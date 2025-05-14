<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM product_attributes WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Thuộc tính đã được xóa thành công.'];
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID không hợp lệ.'];
}

echo '<script>window.location.href="?page=attributes&subpage=manage";</script>';
exit;
?>