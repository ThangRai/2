<?php
session_start();
require_once '../../config/db_connect.php'; // Đường dẫn đến tệp kết nối cơ sở dữ liệu

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để thực hiện thao tác này.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="../../login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 3]; // super_admin (1), content_manager (3)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Truy cập bị từ chối cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền thực hiện thao tác này.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID sản phẩm không hợp lệ.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

$product_id = (int)$_GET['id'];

// Kiểm tra sản phẩm tồn tại
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sản phẩm không tồn tại.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

// Xóa sản phẩm
try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa sản phẩm thành công.'];
    error_log('Xóa sản phẩm thành công: ID = ' . $product_id);
} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa sản phẩm: ' . $e->getMessage()];
    error_log('Lỗi xóa sản phẩm ID ' . $product_id . ': ' . $e->getMessage());
}

// Chuyển hướng về trang danh sách sản phẩm
echo '<script>window.location.href="../../index.php?page=products";</script>';
exit;
?>