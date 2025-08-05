<?php
// Menyertakan file koneksi database
require_once 'connection.php';

// ==== HANDLE TAMBAH DATA ====
if (isset($_POST['add'])) {
    $no_klinik = $_POST['NO_KLINIK'];
    $nama_klinik = $_POST['NAMA_KLINIK'];
    $image = $_POST['image'];

    $insertSql = "INSERT INTO klinik (NO_KLINIK, NAMA_KLINIK, image) VALUES (?, ?, ?)";
    $params = array($no_klinik, $nama_klinik, $image);
    sqlsrv_query($conn, $insertSql, $params);
    header("Location: klinik.php");
    exit;
}

// ==== HANDLE HAPUS DATA ====
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $deleteSql = "DELETE FROM klinik WHERE NO_KLINIK = ?";
    sqlsrv_query($conn, $deleteSql, array($deleteId));
    header("Location: klinik.php");
    exit;
}

// ==== HANDLE EDIT FORM ====
$editData = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editQuery = sqlsrv_query($conn, "SELECT * FROM klinik WHERE NO_KLINIK = ?", array($editId));
    $editData = sqlsrv_fetch_array($editQuery, SQLSRV_FETCH_ASSOC);
}

// ==== HANDLE UPDATE ====
if (isset($_POST['update'])) {
    $no_klinik = $_POST['NO_KLINIK'];
    $nama_klinik = $_POST['NAMA_KLINIK'];
    $image = $_POST['image'];

    $updateSql = "UPDATE klinik SET NAMA_KLINIK = ?, image = ? WHERE NO_KLINIK = ?";
    $params = array($nama_klinik, $image, $no_klinik);
    sqlsrv_query($conn, $updateSql, $params);
    header("Location: klinik.php");
    exit;
}

// ==== AMBIL DATA UNTUK DITAMPILKAN & AUTO INCREMENT ====
$sql = "SELECT * FROM klinik";
$stmt = sqlsrv_query($conn, $sql);

if (!$editData) {
    $queryLast = sqlsrv_query($conn, "SELECT MAX(CAST(NO_KLINIK AS INT)) AS max_klinik FROM klinik");
    $rowLast = sqlsrv_fetch_array($queryLast, SQLSRV_FETCH_ASSOC);
    $nextNo = ($rowLast['max_klinik'] ?? 0) + 1;
} else {
    $nextNo = $editData['NO_KLINIK'];
}

// Memanggil header dari folder templates
require_once 'templates/header.php';
?>

<!-- Content Area -->
<main class="flex-1 p-6 overflow-y-auto">
    <!-- FORMULIR ANDA DIMULAI DI SINI -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6">Form <?= $editData ? 'Edit' : 'Tambah' ?> Data Klinik</h2>
        <form method="POST" action="klinik.php" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div>
                <label class="block mb-1 font-medium text-gray-700">NO_KLINIK</label>
                <input type="text" name="NO_KLINIK" value="<?= htmlspecialchars($nextNo) ?>" readonly required
                    class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-100 cursor-not-allowed" />
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-700">NAMA_KLINIK</label>
                <input type="text" name="NAMA_KLINIK" required
                    value="<?= htmlspecialchars($editData['NAMA_KLINIK'] ?? '') ?>"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block mb-1 font-medium text-gray-700">Image URL</label>
                <input type="text" name="image" value="<?= htmlspecialchars($editData['image'] ?? '') ?>"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div class="md:col-span-3 flex space-x-4">
                <?php if ($editData): ?>
                    <button type="submit" name="update"
                        class="px-5 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Update</button>
                    <a href="klinik.php"
                        class="inline-block px-5 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">Batal</a>
                <?php else: ?>
                    <button type="submit" name="add"
                        class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Tambah</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <!-- FORMULIR ANDA BERAKHIR DI SINI -->


    <!-- TABEL DATA ANDA DIMULAI DI SINI -->
    <div class="bg-white p-6 rounded-lg shadow-md mt-8">
        <h2 class="text-2xl font-semibold mb-4">Data Klinik</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-medium">No</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Id Klinik</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Nama Klinik</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Image</th>
                        <th class="py-3 px-4 text-left text-sm font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    if (sqlsrv_has_rows($stmt)) {
                        $no = 1;
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)):
                            ?>
                            <tr class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="py-2 px-4 text-sm align-middle"><?= $no ?></td>
                                <td class="py-2 px-4 text-sm align-middle"><?= htmlspecialchars($row['NO_KLINIK']) ?></td>
                                <td class="py-2 px-4 text-sm align-middle"><?= htmlspecialchars($row['NAMA_KLINIK']) ?></td>
                                <td class="py-2 px-4 align-middle">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Image Klinik"
                                            class="w-12 h-12 object-cover rounded"
                                            onerror="this.onerror=null;this.src='https://placehold.co/48x48/E2E8F0/4A5568?text=Error';" />
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-4 text-sm align-middle space-x-2">
                                    <a href="?edit=<?= urlencode($row['NO_KLINIK']) ?>"
                                        class="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 rounded hover:bg-yellow-500 transition text-xs">Edit</a>
                                    <a href="?delete=<?= urlencode($row['NO_KLINIK']) ?>"
                                        onclick="return confirm('Yakin ingin menghapus?')"
                                        class="inline-block px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs">Hapus</a>
                                </td>
                            </tr>
                            <?php
                            $no++;
                        endwhile;
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data untuk ditampilkan.</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- TABEL DATA ANDA BERAKHIR DI SINI -->
    <?php
    // Menutup koneksi database dan memanggil footer
    sqlsrv_close($conn);
    require_once 'templates/footer.php';
    ?>