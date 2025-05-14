<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID thuộc tính không hợp lệ.'];
    echo '<script>window.location.href="/2/admin/index.php?page=products&subpage=manage_attributes";</script>';
    exit;
}

$id = (int)$_GET['id'];

try {
    // Kiểm tra xem thuộc tính có tồn tại không
    $stmt = $pdo->prepare("SELECT id FROM product_attributes WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Thuộc tính không tồn tại.'];
        echo '<script>window.location.href="/2/admin/index.php?page=products&subpage=manage_attributes";</script>';
        exit;
    }

    // Kiểm tra xem thuộc tính có được sử dụng trong attribute_values không
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attribute_values WHERE attribute_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không thể xóa thuộc tính vì đã được sử dụng trong giá trị thuộc tính.'];
        echo '<script>window.location.href="/2/admin/index.php?page=attributes";</script>';
        exit;
    }

    // Xóa thuộc tính
    $stmt = $pdo->prepare("DELETE FROM product_attributes WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa thuộc tính thành công.'];
} catch (Exception $e) {
    error_log('Delete Attribute error: ' . $e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa thuộc tính: ' . $e->getMessage()];
}

echo '<script>window.location.href="/2/admin/index.php?page=attributes";</script>';
exit;
?>