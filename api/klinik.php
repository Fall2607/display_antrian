<?php
// Mengatur header untuk memberitahu klien bahwa responsnya adalah JSON
header("Content-Type: application/json; charset=UTF-8");

// Memanggil koneksi dari direktori root
require_once '../connection.php';
// Memanggil library klien WebSocket
require_once '../vendor/autoload.php';

// Fungsi untuk mengirim notifikasi ke server WebSocket
function notifyWebSocketServer()
{
    try {
        // Pastikan Anda menggunakan library yang benar (textalk/websocket)
        $client = new \WebSocket\Client("ws://127.0.0.1:8080");
        // Kirim pesan sederhana yang menandakan ada pembaruan
        $client->send(json_encode(['event' => 'data_updated']));
        $client->close();
    } catch (\Exception $e) {
        // Abaikan error jika server WebSocket tidak berjalan, agar API tetap berfungsi.
        // Anda bisa menambahkan logging di sini jika perlu.
    }
}

// Mendapatkan metode request (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Mengambil data input untuk metode POST dan PUT
$input = json_decode(file_get_contents('php://input'), true);

// Variabel untuk menyimpan respons
$response = [];

switch ($method) {
    case 'GET':
        // Handle request untuk satu data spesifik berdasarkan ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM klinik WHERE NO_KLINIK = ?";
            $stmt = sqlsrv_query($conn, $sql, array($id));
            $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $response['status'] = 'success';
            $response['data'] = $data;
        } else {
            // Handle request untuk list dengan search dan pagination
            $search = $_GET['search'] ?? '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5; // Menampilkan 5 data per halaman
            $offset = ($page - 1) * $limit;

            $data = [];
            $totalRecords = 0;

            $whereClause = "";
            $searchParams = [];

            // Logika pencarian berdasarkan NAMA_KLINIK
            if (!empty($search)) {
                $whereClause = " WHERE NAMA_KLINIK LIKE ?";
                $searchParams[] = "%" . $search . "%";
            }

            // Query untuk menghitung total data yang cocok (untuk pagination)
            $countSql = "SELECT COUNT(*) as total FROM klinik" . $whereClause;
            $countStmt = sqlsrv_query($conn, $countSql, $searchParams);
            if ($countStmt) {
                $totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'];
            }

            // Query yang lebih kompatibel untuk paginasi menggunakan ROW_NUMBER()
            $dataSql = "
                WITH NumberedKlinik AS (
                    SELECT *, ROW_NUMBER() OVER (ORDER BY NO_KLINIK ASC) as rownum
                    FROM klinik
                    " . $whereClause . "
                )
                SELECT * FROM NumberedKlinik WHERE rownum > ? AND rownum <= ?
            ";

            // Gabungkan parameter pencarian dengan parameter paginasi
            $params = array_merge($searchParams, [$offset, $offset + $limit]);

            $stmt = sqlsrv_query($conn, $dataSql, $params);
            if ($stmt) {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    // Hapus kolom 'rownum' dari hasil akhir
                    unset($row['rownum']);
                    $data[] = $row;
                }
            } else {
                // Jika query gagal, kirim pesan error
                $response['status'] = 'error';
                $response['message'] = 'Gagal menjalankan query data.';
                echo json_encode($response);
                sqlsrv_close($conn);
                exit;
            }

            $response['status'] = 'success';
            $response['data'] = $data;
            $response['pagination'] = [
                'page' => $page,
                'limit' => $limit,
                'totalRecords' => $totalRecords,
                'totalPages' => ceil($totalRecords / $limit)
            ];
        }
        break;

    case 'POST':
        if (isset($input['NAMA_KLINIK'])) {
            $queryLast = sqlsrv_query($conn, "SELECT MAX(CAST(NO_KLINIK AS INT)) AS max_klinik FROM klinik");
            $rowLast = sqlsrv_fetch_array($queryLast, SQLSRV_FETCH_ASSOC);
            $nextNo = ($rowLast['max_klinik'] ?? 0) + 1;

            $nama_klinik = $input['NAMA_KLINIK'];
            $image = $input['image'] ?? '';

            $sql = "INSERT INTO klinik (NO_KLINIK, NAMA_KLINIK, image) VALUES (?, ?, ?)";
            $params = array($nextNo, $nama_klinik, $image);
            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data klinik berhasil ditambahkan.';
                http_response_code(201);
                notifyWebSocketServer(); // Panggil notifikasi
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Gagal menambahkan data.';
                http_response_code(500);
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Input tidak valid.';
            http_response_code(400);
        }
        break;

    case 'PUT':
        if (isset($input['NO_KLINIK']) && isset($input['NAMA_KLINIK'])) {
            $no_klinik = $input['NO_KLINIK'];
            $nama_klinik = $input['NAMA_KLINIK'];
            $image = $input['image'] ?? '';

            $sql = "UPDATE klinik SET NAMA_KLINIK = ?, image = ? WHERE NO_KLINIK = ?";
            $params = array($nama_klinik, $image, $no_klinik);
            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data klinik berhasil diperbarui.';
                notifyWebSocketServer(); // Panggil notifikasi
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Gagal memperbarui data.';
                http_response_code(500);
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Input tidak valid.';
            http_response_code(400);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "DELETE FROM klinik WHERE NO_KLINIK = ?";
            $stmt = sqlsrv_query($conn, $sql, array($id));

            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data klinik berhasil dihapus.';
                notifyWebSocketServer(); // Panggil notifikasi
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Gagal menghapus data.';
                http_response_code(500);
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'ID tidak ditemukan.';
            http_response_code(400);
        }
        break;

    default:
        $response['status'] = 'error';
        $response['message'] = 'Metode tidak diizinkan.';
        http_response_code(405);
        break;
}

sqlsrv_close($conn);
echo json_encode($response);
?>