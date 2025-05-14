<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Sử dụng PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Kiểm tra yêu cầu AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
    exit;
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$order_id = (int)$_POST['id'];

// Lấy thông tin đơn hàng
try {
    $stmt = $pdo->prepare("
        SELECT o.*, c.name AS customer_name, c.email AS customer_email
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Đơn hàng không tồn tại']);
        exit;
    }

    // Lấy chi tiết đơn hàng (nếu có)
    $stmt = $pdo->prepare("
        SELECT od.*, p.name AS product_name
        FROM order_details od
        JOIN products p ON od.product_id = p.id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log('Fetch order error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy thông tin đơn hàng']);
    exit;
}

// Lấy cấu hình SMTP
try {
    $settings = [];
    $stmt = $pdo->prepare("SELECT name, value FROM settings WHERE name LIKE 'smtp_%'");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }

    if (empty($settings['smtp_host']) || empty($settings['smtp_username']) || empty($settings['smtp_from'])) {
        echo json_encode(['success' => false, 'message' => 'Cấu hình SMTP không đầy đủ']);
        exit;
    }
} catch (Exception $e) {
    error_log('Fetch SMTP settings error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy cấu hình SMTP']);
    exit;
}

// Khởi tạo PHPMailer
if (file_exists('C:/laragon/www/2/vendor/autoload.php')) {
    require 'C:/laragon/www/2/vendor/autoload.php';
} else {
    require 'C:/laragon/www/2/public/includes/PHPMailer/src/Exception.php';
    require 'C:/laragon/www/2/public/includes/PHPMailer/src/PHPMailer.php';
    require 'C:/laragon/www/2/public/includes/PHPMailer/src/SMTP.php';
}

$mail = new PHPMailer(true);

try {
    // Cấu hình SMTP
    $mail->isSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $settings['smtp_port'];

    
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // Thiết lập người gửi và người nhận
    $mail->setFrom($settings['smtp_from'], $settings['smtp_from_name']);
    $mail->addAddress($order['customer_email'], $order['customer_name']);

    // Nội dung email
    $mail->isHTML(true);
    $mail->Subject = 'Cập nhật trạng thái đơn hàng #' . $order['id'];

    // Định dạng trạng thái
    $statuses = [
        'pending' => 'Đang chờ',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đã vận chuyển',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy'
    ];
    $status_text = $statuses[$order['status']] ?? $order['status'];

    // Nội dung HTML
    $mail->Body = '
        <h2>Cập nhật trạng thái đơn hàng #' . $order['id'] . '</h2>
        <p>Kính gửi ' . htmlspecialchars($order['customer_name']) . ',</p>
        <p>Đơn hàng của bạn đã được cập nhật với thông tin sau:</p>
        <table border="1" cellpadding="5" style="border-collapse: collapse;">
            <tr>
                <th>Mã đơn hàng</th>
                <td>#' . $order['id'] . '</td>
            </tr>
            <tr>
                <th>Khách hàng</th>
                <td>' . htmlspecialchars($order['customer_name']) . '</td>
            </tr>
            <tr>
                <th>Tổng tiền</th>
                <td>' . number_format($order['total_amount'], 0, ',', '.') . ' ₫</td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td>' . $status_text . '</td>
            </tr>
            <tr>
                <th>Ngày tạo</th>
                <td>' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</td>
            </tr>
        </table>';

    // Thêm chi tiết sản phẩm
    if ($order_details) {
        $mail->Body .= '
            <h3>Chi tiết sản phẩm</h3>
            <table border="1" cellpadding="5" style="border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($order_details as $detail) {
            $mail->Body .= '
                <tr>
                    <td>' . htmlspecialchars($detail['product_name']) . '</td>
                    <td>' . $detail['quantity'] . '</td>
                    <td>' . number_format($detail['price'], 0, ',', '.') . ' ₫</td>
                    <td>' . number_format($detail['quantity'] * $detail['price'], 0, ',', '.') . ' ₫</td>
                </tr>';
        }
        $mail->Body .= '</tbody></table>';
    }

    $mail->Body .= '
        <p>Cảm ơn bạn đã mua sắm với chúng tôi!</p>
        <p>Trân trọng,<br>' . htmlspecialchars($settings['smtp_from_name']) . '</p>';

    // Gửi email
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Email đã được gửi thành công']);
} catch (Exception $e) {
    error_log('PHPMailer error: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Không thể gửi email: ' . $mail->ErrorInfo]);
}

?>
