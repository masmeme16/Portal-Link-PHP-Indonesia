<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

$link_id = $link_menu_id = $link_name = $link_redirect = '';
$message = '';
$menus = []; 

// 1. Ambil Daftar Menu (untuk dropdown/select)
$sql_menu = "SELECT menu_id, menu_name FROM menu ORDER BY menu_name ASC";
$result_menu = $conn->query($sql_menu);
if ($result_menu->num_rows > 0) {
    while ($row = $result_menu->fetch_assoc()) {
        $menus[] = $row;
    }
}

// --- Bagian 2: Tangani GET (Ambil data lama) atau POST (Update data)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Proses Update Data (POST) ---
    $link_id = trim($_POST['link_id']);
    $link_menu_id = trim($_POST['link_menu_id']);
    $link_name = trim($_POST['link_name']);
    $link_redirect = trim($_POST['link_redirect']);

    if (empty($link_menu_id) || empty($link_name) || empty($link_redirect)) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Semua kolom wajib diisi.</div>';
    } else {
        // Prepared Statement untuk UPDATE (Anti SQL Injection)
        $sql = "UPDATE link SET link_menu_id = ?, link_name = ?, link_redirect = ? WHERE link_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // "issi" = integer, string, string, integer
            $stmt->bind_param("issi", $link_menu_id, $link_name, $link_redirect, $link_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Link **" . htmlspecialchars($link_name) . "** berhasil diperbarui!";
                $_SESSION['msg_type'] = "warning";
                $stmt->close();
                $conn->close();
                header("location: link.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal memperbarui link. Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-bug"></i> Error prepare statement: ' . $conn->error . '</div>';
        }
    }

} else {
    // --- Tampilkan Form Edit (GET) ---
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $link_id = trim($_GET['id']);

        $sql = "SELECT link_menu_id, link_name, link_redirect FROM link WHERE link_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $link_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $link_menu_id = $row['link_menu_id'];
                $link_name = $row['link_name'];
                $link_redirect = $row['link_redirect'];
            } else {
                $_SESSION['message'] = "Link tidak ditemukan.";
                $_SESSION['msg_type'] = "danger";
                $stmt->close();
                $conn->close();
                header("location: link.php");
                exit;
            }
            $stmt->close();
        }
    } else {
        $_SESSION['message'] = "ID Link tidak valid.";
        $_SESSION['msg_type'] = "danger";
        $conn->close();
        header("location: link.php");
        exit;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Link</title>
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
        <h3 class="text-center mb-4"><i class="fas fa-edit"></i> Edit Link ID: <?= htmlspecialchars($link_id) ?></h3>
        <?php if (isset($message)) echo $message; ?>
        
        <form action="link_edit.php" method="post">
            <input type="hidden" name="link_id" value="<?= htmlspecialchars($link_id) ?>">
            
            <div class="mb-3">
                <label for="link_menu_id" class="form-label">Pilih Kategori Menu:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-bars"></i></span>
                    <select class="form-select" id="link_menu_id" name="link_menu_id" required>
                        <option value="">-- Pilih Menu --</option>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?= htmlspecialchars($menu['menu_id']) ?>" 
                                <?= ($link_menu_id == $menu['menu_id']) ? 'selected' : '' ?>>
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
                    <input type="text" class="form-control" id="link_name" name="link_name" value="<?= htmlspecialchars($link_name) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="link_redirect" class="form-label">URL Redirect:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                    <input type="url" class="form-control" id="link_redirect" name="link_redirect" value="<?= htmlspecialchars($link_redirect) ?>" required>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save"></i> Perbarui Link</button>
                <a href="link.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>