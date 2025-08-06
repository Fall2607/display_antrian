<!-- Footer Sederhana -->
<footer class="text-center p-4 text-sm text-gray-500 mt-auto">
    <div class="flex flex-col items-center gap-2">
        <p>&copy; <?= date("Y") ?> Dibuat oleh Fallen~. Hak Cipta Dilindungi.</p>
        <div class="flex items-center gap-4">
            <a href="https://www.instagram.com/naufal2007/" target="_blank" rel="noopener noreferrer"
                class="text-gray-500 hover:text-gray-800 transition">
                <i class="fab fa-instagram fa-lg"></i>
            </a>
            <a href="https://github.com/Fall2607" target="_blank" rel="noopener noreferrer"
                class="text-gray-500 hover:text-gray-800 transition">
                <i class="fab fa-github fa-lg"></i>
            </a>
        </div>
    </div>
</footer>

</main> <!-- Penutup dari <main> -->
</div> <!-- Penutup dari .flex-1 .flex .flex-col -->
</div> <!-- Penutup dari .flex .h-screen -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarToggleIcon = document.getElementById('sidebar-toggle-icon');
        const mainContent = document.getElementById('main-content');

        // Fungsi untuk menerapkan status ciut dari kelas di <html>
        function applyInitialSidebarState() {
            if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
                // Terapkan kelas final ke elemen sidebar dan konten utama
                sidebar.classList.add('w-20', 'sidebar-collapsed');
                sidebar.classList.remove('w-64');
                mainContent.classList.add('md:ml-20');
                mainContent.classList.remove('md:ml-64');
                sidebarToggleIcon.classList.add('rotate-180');

                // Hapus kelas inisialisasi setelah diterapkan
                document.documentElement.classList.remove('sidebar-collapsed-init');
            }
        }

        applyInitialSidebarState();

        // Fungsi untuk menangani toggle sidebar mobile
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Fungsi untuk menangani ciut/bentang sidebar desktop
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-20');
            sidebar.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('md:ml-64');
            mainContent.classList.toggle('md:ml-20');
            sidebarToggleIcon.classList.toggle('rotate-180');

            // Simpan status ke localStorage
            if (sidebar.classList.contains('w-20')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });
    });
</script>
</body>

</html>