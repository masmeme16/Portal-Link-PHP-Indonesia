<?php
// Wajib: Cek sesi login dan koneksi
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; // Pastikan file ini ada
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-list-alt"></i> Data Manajemen Menu</h3>
    <p>
        <a href="menu_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Menu Baru</a>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </p>

    <?php
    // Tampilkan pesan sukses/error jika ada dari operasi CRUD lain
    if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['msg_type']);
    endif;
    ?>

    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
            <table id="menuTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Menu</th>
                        <th>Dibuat</th>
                        <th>Diperbarui</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Prepared Statement untuk SELECT (Aman)
                    $sql = "SELECT menu_id, menu_name, menu_created, menu_updated FROM menu ORDER BY menu_id DESC";
                    
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['menu_id']) ?></td>
                                    <td><?= htmlspecialchars($row['menu_name']) ?></td>
                                    <td><?= htmlspecialchars($row['menu_created']) ?></td>
                                    <td><?= htmlspecialchars($row['menu_updated']) ?></td>
                                    <td>
                                        <a href="menu_edit.php?id=<?= $row['menu_id'] ?>" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="menu_delete.php?id=<?= $row['menu_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')" title="Hapus"><i class="fas fa-trash-alt"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php
                            endwhile;
                        } else {
                            echo '<tr><td colspan="5" class="text-center">Tidak ada data menu.</td></tr>';
                        }
                        $stmt->close();
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-danger">Error saat menyiapkan statement: ' . $conn->error . '</td></tr>';
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#menuTable').DataTable({
            
        });
    });
</script>

</body>
</html>