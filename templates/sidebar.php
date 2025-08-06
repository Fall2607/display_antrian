<?php
$is_in_pages_folder = basename(dirname($_SERVER['PHP_SELF'])) == 'pages';
$dashboard_path = $is_in_pages_folder ? '../index.php' : 'index.php';
$pages_path = $is_in_pages_folder ? '' : 'pages/';
?>
<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 bg-gray-800 text-white fixed h-full transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 z-20 flex flex-col">
    <!-- Logo/Header Sidebar -->
    <div class="p-6 text-center border-b border-gray-700">
        <h1 class="text-2xl font-bold text-white">Admin Panel</h1>
        <p class="text-sm text-gray-400">RSU Avisena</p>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-grow p-4">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="space-y-2">
            <li>
                <a href="<?= $dashboard_path ?>"
                    class="flex items-center gap-3 p-3 rounded-lg transition-colors <?= ($current_page == 'index.php') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas fa-tachometer-alt fa-fw w-6 text-center"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= $pages_path ?>poliklinik.php"
                    class="flex items-center gap-3 p-3 rounded-lg transition-colors <?= ($current_page == 'poliklinik.php') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas fa-clinic-medical fa-fw w-6 text-center"></i>
                    <span>Manajemen Poli</span>
                </a>
            </li>
            <li>
                <a href="<?= $pages_path ?>klinik.php"
                    class="flex items-center gap-3 p-3 rounded-lg transition-colors <?= ($current_page == 'klinik.php') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas fa-hospital fa-fw w-6 text-center"></i>
                    <span>Manajemen Klinik</span>
                </a>
            </li>
            <li>
                <a href="<?= $pages_path ?>dokter_poli.php"
                    class="flex items-center gap-3 p-3 rounded-lg transition-colors <?= ($current_page == 'dokter_poli.php') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas fa-user-md fa-fw w-6 text-center"></i>
                    <span>Manajemen Dokter</span>
                </a>
            </li>
            <li>
                <a href="<?= $pages_path ?>display_antrian.php"
                    class="flex items-center gap-3 p-3 rounded-lg transition-colors <?= ($current_page == 'display_antrian.php') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-700 hover:text-white'; ?>">
                    <i class="fas fa-desktop fa-fw w-6 text-center"></i>
                    <span>Display Antrian</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-gray-700">
        <a href="#"
            class="flex items-center gap-3 p-3 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition-colors">
            <i class="fas fa-sign-out-alt fa-fw w-6 text-center"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>