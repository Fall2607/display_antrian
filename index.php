<?php
// Memanggil koneksi database
require_once 'connection.php';

// Menghitung jumlah total klinik
$sqlKlinik = "SELECT COUNT(*) as total_klinik FROM klinik";
$stmtKlinik = sqlsrv_query($conn, $sqlKlinik);
$totalKlinik = 0;
if ($stmtKlinik) {
    $totalKlinik = sqlsrv_fetch_array($stmtKlinik, SQLSRV_FETCH_ASSOC)['total_klinik'];
}

// Menghitung jumlah total dokter
$sqlDokter = "SELECT COUNT(*) as total_dokter FROM Dokter_Poli";
$stmtDokter = sqlsrv_query($conn, $sqlDokter);
$totalDokter = 0;
if ($stmtDokter) {
    $totalDokter = sqlsrv_fetch_array($stmtDokter, SQLSRV_FETCH_ASSOC)['total_dokter'];
}

// Menutup koneksi setelah selesai mengambil data
sqlsrv_close($conn);

// Memanggil header dari folder templates
require_once 'templates/header.php';
?>

<!-- Content Area -->
<main class="flex-1 p-6 overflow-y-auto">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Selamat Datang di Dashboard Admin</h1>
        <p class="text-gray-600">Gunakan menu di samping untuk mengelola data website Anda.</p>
    </div>

    <!-- Widget Statistik Dinamis -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <i class="fas fa-hospital text-4xl text-blue-500 mr-4"></i>
            <div>
                <h3 class="text-2xl font-bold"><?= $totalKlinik ?></h3>
                <p class="text-gray-500">Total Klinik</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <i class="fas fa-user-md text-4xl text-green-500 mr-4"></i>
            <div>
                <h3 class="text-2xl font-bold"><?= $totalDokter ?></h3>
                <p class="text-gray-500">Total Dokter</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <i class="fas fa-users text-4xl text-yellow-500 mr-4"></i>
            <div>
                <h3 class="text-2xl font-bold">1,204</h3>
                <p class="text-gray-500">Total Pasien</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <i class="fas fa-chart-line text-4xl text-red-500 mr-4"></i>
            <div>
                <h3 class="text-2xl font-bold">78%</h3>
                <p class="text-gray-500">Kenaikan Bulan Ini</p>
            </div>
        </div>
    </div>

</main>

<?php
// Memanggil footer dari folder templates
require_once 'templates/footer.php';
?>