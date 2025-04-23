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

// Kiểm tra ID ý kiến
if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy ý kiến khách hàng.'];
    echo '<script>window.location.href="index.php?page=customerreviews";</script>';
    exit;
}

$id = (int)$_GET['id'];

// Lấy thông tin ý kiến để xóa ảnh
$stmt = $pdo->prepare("SELECT avatar FROM customer_reviews WHERE id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

// Xử lý xóa ý kiến
$stmt = $pdo->prepare("DELETE FROM customer_reviews WHERE id = ?");
if ($stmt->execute([$id])) {
    // Xóa ảnh nếu có
    if ($review && $review['avatar'] && file_exists($review['avatar'])) {
        unlink($review['avatar']);
    }
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa ý kiến khách hàng thành công.'];
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa ý kiến khách hàng.'];
}
echo '<script>window.location.href="index.php?page=customerreviews";</script>';
exit;
?>