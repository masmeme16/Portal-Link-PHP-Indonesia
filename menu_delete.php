<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
require 'db_connect.php'; 

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $menu_id = trim($_GET['id']);

    // Prepared Statement untuk DELETE (Anti SQL Injection)
    $sql = "DELETE FROM menu WHERE menu_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $menu_id); // "i" = integer

        if ($stmt->execute()) {
            $_SESSION['message'] = "Menu ID **" . htmlspecialchars($menu_id) . "** berhasil dihapus.";
            $_SESSION['msg_type'] = "danger";
        } else {
            $_SESSION['message'] = "Gagal menghapus menu.";
            $_SESSION['msg_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error saat menyiapkan statement DELETE: " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "Permintaan hapus tidak valid.";
    $_SESSION['msg_type'] = "danger";
}

$conn->close();
header("location: menu.php");
exit;
?>