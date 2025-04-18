<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2]; // super_admin (1), staff (2)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Từ chối truy cập cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($customer_id <= 0) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID khách hàng không hợp lệ.'];
    echo '<script>window.location.href="?page=customers";</script>';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không thể xóa khách hàng có đơn hàng liên quan.'];
        echo '<script>window.location.href="?page=customers";</script>';
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$customer_id]);
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa khách hàng thành công'];
} catch (Exception $e) {
    error_log('Lỗi xóa khách hàng: ' . $e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa khách hàng: ' . $e->getMessage()];
}

echo '<script>window.location.href="?page=customers";</script>';
exit;
?>