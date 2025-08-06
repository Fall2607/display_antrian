<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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
        <?php require_once 'sidebar.php'; ?>

        <!-- Wrapper Konten Utama dengan Margin untuk Sidebar -->
        <div class="flex-1 flex flex-col md:ml-64">
            <!-- Header -->
            <header
                class="bg-white/80 backdrop-blur-sm shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
                <!-- Tombol Menu Mobile & Judul Halaman -->
                <div class="flex items-center gap-4">
                    <button id="menu-btn" class="text-gray-600 md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-700">
                        <?php
                        $page_titles = [
                            'index.php' => 'Dashboard',
                            'poliklinik.php' => 'Manajemen Poliklinik',
                            'klinik.php' => 'Manajemen Klinik',
                            'dokter_poli.php' => 'Manajemen Dokter',
                            'display_antrian.php' => 'Display Antrian'
                        ];
                        $current_page = basename($_SERVER['PHP_SELF']);
                        echo $page_titles[$current_page] ?? 'Admin Panel';
                        ?>
                    </h2>
                </div>
                <!-- Profil Pengguna -->
                <div class="flex items-center gap-4">
                    <span class="hidden md:block font-semibold text-gray-600">Selamat datang, Agus!</span>
                    <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=A" alt="Avatar Pengguna"
                        class="w-10 h-10 rounded-full border-2 border-blue-500">
                </div>
            </header>