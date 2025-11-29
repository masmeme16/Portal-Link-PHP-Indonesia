<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

$link_data = [];
$has_data = false;

// 1. Ambil Data Link dengan JOIN ke Tabel Menu
// Menggunakan Prepared Statement untuk SELECT meskipun tidak ada input user
$sql = "SELECT l.link_id, l.link_name, l.link_redirect, l.link_created, m.menu_name 
        FROM link l 
        JOIN menu m ON l.link_menu_id = m.menu_id 
        ORDER BY l.link_id DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $has_data = true;
        while ($row = $result->fetch_assoc()) {
            $link_data[] = $row;
        }
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <?php if ($has_data): ?>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css"/>
    <?php endif; ?>

    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>

<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-link"></i> Data Manajemen Link</h3>
    
    <p>
        <a href="link_create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Link Baru</a>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </p>

    <?php
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
            
            <?php if (!$has_data): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada data link yang tersimpan.
                </div>
            <?php else: ?>
                <table id="linkTable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Menu (Kategori)</th>
                            <th>Nama Link</th>
                            <th>URL Redirect</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($link_data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['link_id']) ?></td>
                                <td><?= htmlspecialchars($row['menu_name']) ?></td>
                                <td><?= htmlspecialchars($row['link_name']) ?></td>
                                <td><a href="<?= htmlspecialchars($row['link_redirect']) ?>" target="_blank"><?= htmlspecialchars($row['link_redirect']) ?></a></td>
                                <td><?= htmlspecialchars($row['link_created']) ?></td>
                                <td>
                                    <a href="link_edit.php?id=<?= $row['link_id'] ?>" class="btn btn-sm btn-info text-white me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="link_delete.php?id=<?= $row['link_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus link ini?')" title="Hapus"><i class="fas fa-trash-alt"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($has_data): ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#linkTable').DataTable({
               
            });
        });
    </script>
<?php endif; ?>

</body>
</html>