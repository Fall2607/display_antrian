<?php
// Pengaturan koneksi database
$serverName = "tcp:182.253.37.109,56526";
$connectionOptions = [
    "Database" => "AVISENA",
    "Uid" => "Agus",
    "PWD" => "1437157",
    "Encrypt" => false,
    "TrustServerCertificate" => true,
    "LoginTimeout" => 30,
];

// Membuat koneksi ke database
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Memeriksa apakah koneksi berhasil
if ($conn === false) {
    // Menampilkan pesan error jika koneksi gagal
    die(print_r(sqlsrv_errors(), true));
}
?>