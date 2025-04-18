<?php
// KHÔNG có khoảng trắng trước <?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

error_log('POST data: ' . print_r($_POST, true));

// Tạm bỏ kiểm tra session để test
// if (!isset($_SESSION['admin_id'])) {
//     ob_end_clean();
//     echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
//     exit;
// }

if (!isset($_POST['id']) || !isset($_POST['is_active'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$id = (int)$_POST['id'];
$is_active = (int)$_POST['is_active'];

try {
    $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
    $success = $stmt->execute([$is_active, $id]);

    error_log("Update ID $id, is_active $is_active, Success: " . ($success ? 'true' : 'false'));

    ob_end_clean();
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Trạng thái đã được cập nhật']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm hoặc không có thay đổi']);
    }
} catch (Exception $e) {
    error_log('SQL error: ' . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu']);
}
exit;
?>