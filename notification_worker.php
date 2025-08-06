<?php
// notification_worker.php

// Script ini akan berjalan selamanya, jadi kita butuh waktu eksekusi tak terbatas
set_time_limit(0);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/connection.php'; // Menggunakan koneksi database yang sama

echo "Worker notifikasi dimulai...\n";

while (true) {
    try {
        // 1. Periksa "kotak surat" (tabel notifikasi)
        $sql = "SELECT TOP 1 id, table_name FROM tbl_notifications";
        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt && sqlsrv_has_rows($stmt)) {
            $notification = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $notificationId = $notification['id'];
            $tableName = $notification['table_name'];

            echo "Perubahan terdeteksi pada tabel: " . $tableName . ". Mengirim notifikasi...\n";

            // 2. Hubungi "pengeras suara" (WebSocket Server)
            try {
                $client = new \WebSocket\Client("ws://127.0.0.1:8080");
                $client->send(json_encode(['event' => 'data_updated']));
                $client->close();
                echo "Notifikasi berhasil dikirim.\n";

                // 3. Hapus "surat" agar tidak diproses lagi
                $deleteSql = "DELETE FROM tbl_notifications WHERE id = ?";
                sqlsrv_query($conn, $deleteSql, array($notificationId));
                echo "Notifikasi ID " . $notificationId . " telah dihapus.\n";

            } catch (\Exception $e) {
                echo "Gagal terhubung ke server WebSocket: " . $e->getMessage() . "\n";
                // Jangan hapus notifikasi jika gagal, coba lagi di iterasi berikutnya
            }
        }
    } catch (\Exception $e) {
        echo "Error pada worker: " . $e->getMessage() . "\n";
        // Mungkin koneksi database terputus, coba sambungkan kembali jika perlu
    }

    // Tunggu 1 detik sebelum memeriksa lagi untuk mengurangi beban CPU
    sleep(1);
}
