<!-- Footer Sederhana -->
<footer class="text-center p-4 text-sm text-gray-500 mt-auto">
    &copy; <?= date("Y") ?> Fallen~.
</footer>

</main> <!-- Penutup dari <main> -->
</div> <!-- Penutup dari .flex-1 .flex .flex-col -->
</div> <!-- Penutup dari .flex .h-screen -->

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.flex-1');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
</script>
</body>

</html>