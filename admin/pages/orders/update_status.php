<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

error_log('POST data: ' . print_r($_POST, true));

// Kiểm tra dữ liệu đầu vào
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$id = (int)$_POST['id'];
$status = $_POST['status'];
$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if (!in_array($status, $valid_statuses)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $success = $stmt->execute([$status, $id]);

    error_log("Update order ID $id, status $status, Success: " . ($success ? 'true' : 'false'));

    ob_end_clean();
    if ($success && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Trạng thái đã được cập nhật']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng hoặc không có thay đổi']);
    }
} catch (Exception $e) {
    error_log('SQL error: ' . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu']);
}
exit;
?>