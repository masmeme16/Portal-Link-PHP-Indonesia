<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_name = trim($_POST['menu_name']);

    if (empty($menu_name)) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Nama Menu wajib diisi.</div>';
    } else {
        // Prepared Statement untuk INSERT (Anti SQL Injection)
        // Kita hanya memasukkan menu_name; created_at dan updated_at diisi otomatis oleh database
        $sql = "INSERT INTO menu (menu_name, menu_created, menu_updated) VALUES (?, NOW(), NOW())";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $menu_name);

            if ($stmt->execute()) {
                // Simpan pesan sukses di session dan redirect
                $_SESSION['message'] = "Menu **" . htmlspecialchars($menu_name) . "** berhasil ditambahkan!";
                $_SESSION['msg_type'] = "success";
                header("location: menu.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal menambahkan menu. Mungkin nama menu sudah ada.</div>';
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
    <title>Tambah Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 500px; margin-top: 50px; }
    </style>
</head>
<body>

<div class="container form-container">
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4"><i class="fas fa-plus-circle"></i> Tambah Menu Baru</h3>
        <?php if (isset($message)) echo $message; ?>
        
        <form action="menu_create.php" method="post">
            <div class="mb-3">
                <label for="menu_name" class="form-label">Nama Menu:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                    <input type="text" class="form-control" id="menu_name" name="menu_name" required>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Menu</button>
                <a href="menu.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>