<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID Flash Sale không hợp lệ.'];
    header("Location: /2/admin/index.php?page=flash_sales");
    exit;
}

$id = (int)$_GET['id'];

try {
    // Kiểm tra xem Flash Sale có tồn tại không
    $stmt = $pdo->prepare("SELECT id FROM flash_sales WHERE id = ?");
    $stmt->execute([$id]);
    $flash_sale = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flash_sale) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Flash Sale không tồn tại.'];
        header("Location: /2/admin/index.php?page=flash_sales");
        exit;
    }

    // Xóa Flash Sale
    $stmt = $pdo->prepare("DELETE FROM flash_sales WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa Flash Sale thành công.'];
    header("Location: /2/admin/index.php?page=flash_sales");
    exit;
} catch (Exception $e) {
    error_log('Delete Flash Sale error: ' . $e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa Flash Sale: ' . $e->getMessage()];
    header("Location: /2/admin/index.php?page=flash_sales");
    exit;
}
?>