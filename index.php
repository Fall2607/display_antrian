<?php
// Memanggil koneksi database
require_once 'connection.php';

// Menghitung jumlah total poliklinik
$sqlPoli = "SELECT COUNT(*) as total_poli FROM Poliklinik";
$stmtPoli = sqlsrv_query($conn, $sqlPoli);
$totalPoli = 0;
if ($stmtPoli) {
    $totalPoli = sqlsrv_fetch_array($stmtPoli, SQLSRV_FETCH_ASSOC)['total_poli'];
}

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
    <!-- Header Konten -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Selamat Datang di Dashboard Admin</h1>
            <p class="text-lg text-gray-500 mt-1">Ringkasan data dan akses cepat ke fitur utama.</p>
        </div>
        <!-- Tombol Aksi Cepat -->
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="pages/poliklinik.php"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-transform hover:scale-105 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Poli</span>
            </a>
            <a href="pages/dokter_poli.php"
                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition-transform hover:scale-105 flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Dokter</span>
            </a>
        </div>
    </div>

    <!-- Widget Statistik Dinamis -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card Total Poliklinik -->
        <a href="pages/poliklinik.php"
            class="block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-clinic-medical text-2xl text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-3xl font-bold"><?= $totalPoli ?></h3>
                    <p class="text-gray-500">Total Poliklinik</p>
                </div>
            </div>
        </a>
        <!-- Card Total Klinik -->
        <a href="pages/klinik.php"
            class="block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-hospital text-2xl text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-3xl font-bold"><?= $totalKlinik ?></h3>
                    <p class="text-gray-500">Total Klinik</p>
                </div>
            </div>
        </a>
        <!-- Card Total Dokter -->
        <a href="pages/dokter_poli.php"
            class="block bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-user-md text-2xl text-green-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-3xl font-bold"><?= $totalDokter ?></h3>
                    <p class="text-gray-500">Total Dokter</p>
                </div>
            </div>
        </a>
        <!-- Card Total Pasien (Statis) -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-users text-2xl text-yellow-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-3xl font-bold">1,204</h3>
                    <p class="text-gray-500">Total Pasien</p>
                </div>
            </div>
        </div>
    </div>

</main>

<?php
// Memanggil footer dari folder templates
require_once 'templates/footer.php';
?>