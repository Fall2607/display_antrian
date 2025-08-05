<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json; charset=UTF-8");

require_once '../connection.php';
require_once '../vendor/autoload.php';

function notifyWebSocketServer()
{
    try {
        $client = new \WebSocket\Client("ws://127.0.0.1:8080");
        $client->send(json_encode(['event' => 'data_updated']));
        $client->close();
    } catch (\Exception $e) {
        // Abaikan error jika server WebSocket tidak berjalan
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$response = [];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM Dokter_Poli WHERE Nama_Dr = ?";
                $stmt = sqlsrv_query($conn, $sql, array($id));
                if ($stmt === false)
                    throw new Exception("Query untuk mengambil data tunggal gagal.");
                $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $response['status'] = 'success';
                $response['data'] = $data;
            } else {
                $search = $_GET['search'] ?? '';
                $whereClause = "";
                $params = [];
                if (!empty($search)) {
                    $whereClause = " WHERE Nama_Dr LIKE ?";
                    $params[] = "%" . $search . "%";
                }
                $sql = "SELECT * FROM Dokter_Poli" . $whereClause . " ORDER BY Nama_Dr ASC";
                $stmt = sqlsrv_query($conn, $sql, $params);
                if ($stmt === false)
                    throw new Exception("Query untuk mengambil semua data gagal.");
                $data = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $data[] = $row;
                }
                $response['status'] = 'success';
                $response['data'] = $data;
            }
            break;

        case 'POST':
            if (!isset($input['No_Poli'], $input['Nama_Dr'])) {
                throw new Exception("Input tidak valid. No Poli dan Nama Dokter wajib diisi.");
            }
            $sql = "INSERT INTO Dokter_Poli (No_Poli, Nama_Dr, Jam_Praktek, Minim, Maxim, NoAntri, NoDisplay, NoUrut, Loket) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $input['No_Poli'],
                $input['Nama_Dr'],
                $input['Jam_Praktek'] ?? '',
                $input['Minim'] ?? 0,
                $input['Maxim'] ?? 0,
                $input['NoAntri'] ?? 0,
                empty($input['NoDisplay']) ? null : $input['NoDisplay'], // Handle null untuk Sesi
                $input['NoUrut'] ?? 0,
                $input['Loket'] ?? ''
            ];
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data dokter berhasil ditambahkan.';
                http_response_code(201);
                notifyWebSocketServer();
            } else {
                throw new Exception("Gagal menambahkan data. Pastikan No Poli atau Nama Dokter unik jika diperlukan.");
            }
            break;

        case 'PUT':
            if (!isset($input['original_Nama_Dr'], $input['Nama_Dr'])) {
                throw new Exception("Input tidak valid untuk pembaruan.");
            }
            $sql = "UPDATE Dokter_Poli SET No_Poli = ?, Nama_Dr = ?, Jam_Praktek = ?, Minim = ?, Maxim = ?, NoDisplay = ?, Loket = ? WHERE Nama_Dr = ?";
            $params = [
                $input['No_Poli'],
                $input['Nama_Dr'],
                $input['Jam_Praktek'] ?? '',
                $input['Minim'] ?? 0,
                $input['Maxim'] ?? 0,
                empty($input['NoDisplay']) ? null : $input['NoDisplay'], // Handle null untuk Sesi
                $input['Loket'] ?? '',
                $input['original_Nama_Dr'] // Kunci WHERE adalah nama asli
            ];
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data dokter berhasil diperbarui.';
                notifyWebSocketServer();
            } else {
                throw new Exception("Gagal memperbarui data.");
            }
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception("ID tidak ditemukan untuk penghapusan.");
            }
            $id = $_GET['id'];
            $sql = "DELETE FROM Dokter_Poli WHERE Nama_Dr = ?";
            $stmt = sqlsrv_query($conn, $sql, array($id));
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data dokter berhasil dihapus.';
                notifyWebSocketServer();
            } else {
                throw new Exception("Gagal menghapus data.");
            }
            break;

        default:
            throw new Exception("Metode tidak diizinkan.");
            http_response_code(405);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

sqlsrv_close($conn);
echo json_encode($response);
?>