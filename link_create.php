<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

$message = '';
$menus = []; // Untuk menyimpan daftar menu

// 1. Ambil Daftar Menu (untuk dropdown/select)
$sql_menu = "SELECT menu_id, menu_name FROM menu ORDER BY menu_name ASC";
$result_menu = $conn->query($sql_menu);
if ($result_menu->num_rows > 0) {
    while ($row = $result_menu->fetch_assoc()) {
        $menus[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $link_menu_id = trim($_POST['link_menu_id']);
    $link_name = trim($_POST['link_name']);
    $link_redirect = trim($_POST['link_redirect']);

    if (empty($link_menu_id) || empty($link_name) || empty($link_redirect)) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Semua kolom wajib diisi.</div>';
    } else {
        // Prepared Statement untuk INSERT (Anti SQL Injection)
        $sql = "INSERT INTO link (link_menu_id, link_name, link_redirect) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // "iss" = integer, string, string
            $stmt->bind_param("iss", $link_menu_id, $link_name, $link_redirect);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Link **" . htmlspecialchars($link_name) . "** berhasil ditambahkan!";
                $_SESSION['msg_type'] = "success";
                $stmt->close();
                $conn->close();
                header("location: link.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal menambahkan link. Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-bug"></i> Error prepare statement: ' . $conn->error . '</div>';
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Link Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body>
<div class="container form-container">
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4"><i class="fas fa-plus-circle"></i> Tambah Link Baru</h3>
        <?php if (isset($message)) echo $message; ?>
        
        <form action="link_create.php" method="post">
            <div class="mb-3">
                <label for="link_menu_id" class="form-label">Pilih Kategori Menu:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-bars"></i></span>
                    <select class="form-select" id="link_menu_id" name="link_menu_id" required>
                        <option value="">-- Pilih Menu --</option>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?= htmlspecialchars($menu['menu_id']) ?>">
                                <?= htmlspecialchars($menu['menu_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="link_name" class="form-label">Nama Link:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                    <input type="text" class="form-control" id="link_name" name="link_name" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="link_redirect" class="form-label">URL Redirect (Contoh: https://google.com):</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                    <input type="url" class="form-control" id="link_redirect" name="link_redirect" placeholder="Contoh: https://..." required>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Link</button>
                <a href="link.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>