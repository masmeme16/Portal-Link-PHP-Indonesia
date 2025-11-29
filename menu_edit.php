<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

$menu_id = $menu_name = '';
$message = '';
$is_post_request = ($_SERVER["REQUEST_METHOD"] == "POST");

// --- Bagian 1: Ambil Data Lama (GET) atau Tangani POST Request
if (!$is_post_request) {
    // Jika bukan POST, berarti kita hanya ingin menampilkan form edit
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $menu_id = trim($_GET['id']);

        // Prepared Statement untuk SELECT data lama (Anti SQL Injection)
        $sql = "SELECT menu_id, menu_name FROM menu WHERE menu_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $menu_id); // "i" = integer
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $menu_name = $row['menu_name'];
            } else {
                $_SESSION['message'] = "Menu tidak ditemukan.";
                $_SESSION['msg_type'] = "danger";
                header("location: menu.php");
                exit;
            }
            $stmt->close();
        }
    } else {
        $_SESSION['message'] = "ID Menu tidak valid.";
        $_SESSION['msg_type'] = "danger";
        header("location: menu.php");
        exit;
    }
} else {
    // --- Bagian 2: Proses Update Data (POST)
    $menu_id = trim($_POST['menu_id']);
    $menu_name = trim($_POST['menu_name']);

    if (empty($menu_name)) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Nama Menu wajib diisi.</div>';
    } else {
        // Prepared Statement untuk UPDATE (Anti SQL Injection)
        // Kolom menu_updated akan otomatis terisi oleh database
        $sql = "UPDATE menu SET menu_name = ?, menu_updated = NOW() WHERE menu_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $menu_name, $menu_id); // "s" = string, "i" = integer

            if ($stmt->execute()) {
                $_SESSION['message'] = "Menu **" . htmlspecialchars($menu_name) . "** berhasil diperbarui!";
                $_SESSION['msg_type'] = "warning";
                header("location: menu.php");
                exit;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Gagal memperbarui menu. Mungkin nama menu sudah ada.</div>';
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
    <title>Edit Menu</title>
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
        <h3 class="text-center mb-4"><i class="fas fa-edit"></i> Edit Menu ID: <?= htmlspecialchars($menu_id) ?></h3>
        <?php if (isset($message)) echo $message; ?>
        
        <form action="menu_edit.php" method="post">
            <input type="hidden" name="menu_id" value="<?= htmlspecialchars($menu_id) ?>">
            <div class="mb-3">
                <label for="menu_name" class="form-label">Nama Menu:</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                    <input type="text" class="form-control" id="menu_name" name="menu_name" value="<?= htmlspecialchars($menu_name) ?>" required>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save"></i> Perbarui Menu</button>
                <a href="menu.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>