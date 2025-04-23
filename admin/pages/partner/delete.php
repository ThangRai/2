<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Lấy ID đối tác
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra đối tác tồn tại
$stmt = $pdo->prepare("SELECT logo FROM partners WHERE id = ?");
$stmt->execute([$id]);
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Đối tác không tồn tại.'];
    echo '<script>window.location.href="?page=partner";</script>';
    exit;
}

// Xóa đối tác
try {
    // Xóa logo nếu có
    if ($partner['logo'] && file_exists($partner['logo'])) {
        unlink($partner['logo']);
    }

    $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa đối tác thành công'];
    echo '<script>window.location.href="?page=partner";</script>';
    exit;
} catch (Exception $e) {
    error_log('Delete partner error: ' . $e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa đối tác: ' . $e->getMessage()];
    echo '<script>window.location.href="?page=partner";</script>';
    exit;
}
?>