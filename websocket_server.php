<?php
// websocket_server.php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class KlinikUpdater implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "Server WebSocket berjalan di semua antarmuka (0.0.0.0)...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Koneksi baru! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Menerima pesan: {$msg}\n";
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Koneksi {$conn->resourceId} telah ditutup.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Terjadi error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Jalankan server pada port 8080 dan ikat ke 0.0.0.0
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new KlinikUpdater()
        )
    ),
    8080,
    '0.0.0.0' // <-- Perubahan penting ada di sini
);

$server->run();
