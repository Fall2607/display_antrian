<?php
$serverName = "182.253.37.109";
$connectionOptions = array(
    "Database" => "AVISENA",
    "Uid" => "Agus",
    "PWD" => "1437157",
    "Encrypt" => true,
    "TrustServerCertificate" => true
);
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// ==== HANDLE TAMBAH DATA ====
if (isset($_POST['add'])) {
    $no_klinik = $_POST['NO_KLINIK'];
    $nama_klinik = $_POST['NAMA_KLINIK'];
    $image = $_POST['image'];

    $insertSql = "INSERT INTO klinik (NO_KLINIK, NAMA_KLINIK, image) VALUES (?, ?, ?)";
    $params = array($no_klinik, $nama_klinik, $image);
    sqlsrv_query($conn, $insertSql, $params);
}

// ==== HANDLE HAPUS DATA ====
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $deleteSql = "DELETE FROM klinik WHERE NO_KLINIK = ?";
    sqlsrv_query($conn, $deleteSql, array($deleteId));
    header("Location: index.php");
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
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM klinik";
$stmt = sqlsrv_query($conn, $sql);

if (!$editData) {
    $queryLast = sqlsrv_query($conn, "SELECT MAX(CAST(NO_KLINIK AS INT)) AS max_klinik FROM klinik");
    $rowLast = sqlsrv_fetch_array($queryLast, SQLSRV_FETCH_ASSOC);
    $nextNo = ($rowLast['max_klinik'] ?? 0) + 1;
} else {
    $nextNo = $editData['NO_KLINIK']; // kalau edit, isi dengan data lama
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<div class="container mt-4">
    <h2>Form <?= $editData ? 'Edit' : 'Tambah' ?> Data Klinik</h2>
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">NO_KLINIK</label>
            <input type="text" name="NO_KLINIK" class="form-control" value="<?= $nextNo ?>" readonly required>
        </div>
        <div class="col-md-4">
            <label class="form-label">NAMA_KLINIK</label>
            <input type="text" name="NAMA_KLINIK" class="form-control" value="<?= $editData['NAMA_KLINIK'] ?? '' ?>"
                required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Image URL</label>
            <input type="text" name="image" class="form-control" value="<?= $editData['image'] ?? '' ?>">
        </div>
        <div class="col-12">
            <?php if ($editData): ?>
                <button type="submit" name="update" class="btn btn-warning">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
            <?php endif; ?>
        </div>
    </form>

    <h2>Data Klinik</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Id Klinik</th>
                <th>Nama Klinik</th>
                <th>Image</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)):
                ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= htmlspecialchars($row['NO_KLINIK']) ?></td>
                    <td><?= htmlspecialchars($row['NAMA_KLINIK']) ?></td>
                    <td><img src="<?= htmlspecialchars($row['image']) ?>" width="50"></td>
                    <td>
                        <a href="?edit=<?= urlencode($row['NO_KLINIK']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= urlencode($row['NO_KLINIK']) ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php $no++; endwhile; ?>
        </tbody>
    </table>
</div>

<?php sqlsrv_close($conn); ?>