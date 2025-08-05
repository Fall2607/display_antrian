<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 bg-gray-800 text-white p-6 fixed h-full transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 z-10">
    <h1 class="text-2xl font-bold mb-8">Admin Panel</h1>
    <nav>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul>
            <li class="mb-4">
                <a href="index.php"
                    class="flex items-center p-2 rounded-md transition-colors <?= ($current_page == 'index.php') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700'; ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
            </li>
            <li class="mb-4">
                <a href="klinik.php"
                    class="flex items-center p-2 rounded-md transition-colors <?= ($current_page == 'klinik.php') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700'; ?>">
                    <i class="fas fa-hospital mr-3"></i>
                    Manajemen Klinik
                </a>
            </li>
            <li class="mb-4">
                <a href="#" class="flex items-center p-2 text-gray-400 hover:bg-gray-700 rounded-md transition-colors">
                    <i class="fas fa-user-md mr-3"></i>
                    Manajemen Dokter
                </a>
            </li>
            <li class="mb-4">
                <a href="#" class="flex items-center p-2 text-gray-400 hover:bg-gray-700 rounded-md transition-colors">
                    <i class="fas fa-cog mr-3"></i>
                    Pengaturan
                </a>
            </li>
        </ul>
    </nav>
    <div class="absolute bottom-4">
        <a href="#" class="flex items-center p-2 text-gray-400 hover:bg-gray-700 rounded-md transition-colors">
            <i class="fas fa-sign-out-alt mr-3"></i>
            Logout
        </a>
    </div>
</aside>