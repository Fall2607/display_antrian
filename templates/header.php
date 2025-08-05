<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Custom scrollbar (opsional) */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">

    <div class="flex h-screen bg-gray-200">
        <?php require_once 'sidebar.php'; // Memanggil sidebar dari direktori yang sama ?>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out md:ml-64">
            <!-- Header -->
            <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-5">
                <!-- Tombol Menu Mobile -->
                <button id="menu-btn" class="text-gray-600 md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-700">
                    <?php
                    // Judul dinamis berdasarkan halaman
                    $current_page = basename($_SERVER['PHP_SELF']);
                    if ($current_page == 'index.php') {
                        echo 'Dashboard';
                    } elseif ($current_page == 'klinik.php') {
                        echo 'Manajemen Data Klinik';
                    }
                    ?>
                </h2>
                <div class="flex items-center">
                    <span class="mr-4 text-gray-600">Selamat datang, Agus!</span>
                    <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="Avatar Pengguna"
                        class="w-10 h-10 rounded-full">
                </div>
            </header>