<?php
// websocket_server.php

// Menggunakan __DIR__ untuk memastikan path selalu benar relatif terhadap file ini.
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Kelas ini akan menangani semua logika WebSocket
class KlinikUpdater implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        // SplObjectStorage digunakan untuk menyimpan semua koneksi klien
        $this->clients = new \SplObjectStorage;
        echo "Server WebSocket berjalan...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Tambahkan klien baru ke dalam storage saat koneksi dibuka
        $this->clients->attach($conn);
        echo "Koneksi baru! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Fungsi ini akan dipanggil ketika server menerima pesan dari klien.
        // Dalam kasus ini, pesan datang dari API kita.
        echo "Menerima pesan: {$msg}\n";

        // Kirim pesan yang diterima ke semua klien lain yang terhubung, kecuali pengirimnya.
        // Ini akan memberitahu semua halaman web yang terbuka untuk memuat ulang data mereka.
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Hapus klien dari storage saat koneksi ditutup
        $this->clients->detach($conn);
        echo "Koneksi {$conn->resourceId} telah ditutup.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Tangani error dan tutup koneksi
        echo "Terjadi error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Jalankan server pada port 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new KlinikUpdater()
        )
    ),
    8080
);

$server->run();
