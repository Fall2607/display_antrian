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
                $sql = "SELECT * FROM Poliklinik WHERE No_Poli = ?";
                $stmt = sqlsrv_query($conn, $sql, array($id));
                if ($stmt === false)
                    throw new Exception("Query untuk mengambil data tunggal gagal.");
                $data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $response['status'] = 'success';
                $response['data'] = $data;
            } else {
                $search = $_GET['search'] ?? '';
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
                $offset = ($page - 1) * $limit;
                $data = [];
                $totalRecords = 0;
                $whereClause = "";
                $searchParams = [];

                if (!empty($search)) {
                    $whereClause = " WHERE Poli LIKE ?";
                    $searchParams[] = "%" . $search . "%";
                }

                $countSql = "SELECT COUNT(*) as total FROM Poliklinik" . $whereClause;
                $countStmt = sqlsrv_query($conn, $countSql, $searchParams);
                if ($countStmt) {
                    $totalRecords = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['total'];
                }

                $dataSql = "
                    WITH NumberedPoli AS (
                        SELECT *, ROW_NUMBER() OVER (ORDER BY No_Poli ASC) as rownum
                        FROM Poliklinik
                        " . $whereClause . "
                    )
                    SELECT * FROM NumberedPoli WHERE rownum > ? AND rownum <= ?
                ";
                $params = array_merge($searchParams, [$offset, $offset + $limit]);
                $stmt = sqlsrv_query($conn, $dataSql, $params);
                if ($stmt === false)
                    throw new Exception("Query untuk mengambil semua data gagal.");

                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    unset($row['rownum']);
                    $data[] = $row;
                }

                $response['status'] = 'success';
                $response['data'] = $data;
                $response['pagination'] = ['page' => $page, 'limit' => $limit, 'totalRecords' => $totalRecords, 'totalPages' => ceil($totalRecords / $limit)];
            }
            break;

        case 'POST':
            if (!isset($input['No_Poli'], $input['Poli'])) {
                throw new Exception("Input tidak valid. No Poli dan Nama Poli wajib diisi.");
            }
            $sql = "INSERT INTO Poliklinik (No_Poli, Poli, Batas, Kode_klinik) VALUES (?, ?, ?, ?)";
            $params = [
                $input['No_Poli'],
                $input['Poli'],
                $input['Batas'] ?? null,
                $input['Kode_klinik'] ?? null
            ];
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data poliklinik berhasil ditambahkan.';
                http_response_code(201);
                notifyWebSocketServer();
            } else {
                throw new Exception("Gagal menambahkan data. Pastikan No Poli unik.");
            }
            break;

        case 'PUT':
            if (!isset($input['No_Poli'], $input['Poli'])) {
                throw new Exception("Input tidak valid untuk pembaruan.");
            }
            $sql = "UPDATE Poliklinik SET Poli = ?, Batas = ?, Kode_klinik = ? WHERE No_Poli = ?";
            $params = [
                $input['Poli'],
                $input['Batas'] ?? null,
                $input['Kode_klinik'] ?? null,
                $input['No_Poli']
            ];
            $stmt = sqlsrv_query($conn, $sql, $params);
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data poliklinik berhasil diperbarui.';
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
            $sql = "DELETE FROM Poliklinik WHERE No_Poli = ?";
            $stmt = sqlsrv_query($conn, $sql, array($id));
            if ($stmt) {
                $response['status'] = 'success';
                $response['message'] = 'Data poliklinik berhasil dihapus.';
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