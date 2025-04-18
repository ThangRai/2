<?php
session_start();

// Lấy dữ liệu từ session
$order_id = $_SESSION['order_id'] ?? null;
$cart_items = $_SESSION['cart_items'] ?? [];
$total_price = $_SESSION['total_price'] ?? 0;
$customer_info = $_SESSION['customer_info'] ?? [];
$payment_method = $_SESSION['payment_method'] ?? 'cod';
$payment_method_text = $payment_method == 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng';

// Chuyển hướng về trang chủ nếu không có order_id
if (!$order_id) {
    $_SESSION['cart_message'] = 'Không tìm thấy thông tin đơn hàng. Vui lòng thử lại!';
    header('Location: /2/public/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 15px;
        }
        .success-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideIn 1s ease-out, glow 2s infinite alternate;
        }
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(59, 130, 246, 0.2), rgba(147, 197, 253, 0.2));
            opacity: 0.5;
            z-index: 0;
            animation: gradientFlow 5s ease infinite;
        }
        .success-icon {
            font-size: 6em;
            color: #10b981;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite;
            position: relative;
            z-index: 1;
        }
        .success-title {
            font-size: 2.8em;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #3b82f6, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textGlow 2s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }
        .success-message {
            font-size: 1.3em;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1.2s ease-out;
        }
        .order-details, .customer-info, .bank-transfer {
            text-align: left;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1.4s ease-out;
        }
        .order-details h3, .customer-info h3, .bank-transfer h3 {
            font-size: 1.5em;
            color: #2d3436;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #3b82f6, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .order-details th, .order-details td {
            border: 1px solid #dfe6e9;
            padding: 10px;
            text-align: left;
        }
        .order-details th {
            background: linear-gradient(90deg, #3b82f6, #93c5fd);
            color: #fff;
        }
        .order-details td.total {
            font-weight: 700;
            color: #ff4757;
        }
        .customer-info p, .bank-transfer p {
            margin: 5px 0;
            color: #2d3436;
            font-size: 1.1em;
        }
        .payment-info {
            font-size: 1.1em;
            color: #1f2937;
            background: rgba(59, 130, 246, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1.4s ease-out;
        }
        .btn {
            background: linear-gradient(135deg, #3b82f6, #93c5fd);
            color: #fff;
            border: none;
            padding: 14px 35px;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        .btn:hover {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.6);
        }
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
        }
        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes glow {
            from { box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); }
            to { box-shadow: 0 20px 50px rgba(59, 130, 246, 0.5); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes textGlow {
            0% { filter: brightness(100%); }
            50% { filter: brightness(120%); }
            100% { filter: brightness(100%); }
        }
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }
            .success-card {
                padding: 30px;
            }
            .success-title {
                font-size: 2.2em;
            }
            .success-icon {
                font-size: 4.5em;
            }
            .success-message, .payment-info, .customer-info p, .bank-transfer p {
                font-size: 1em;
            }
            .order-details th, .order-details td {
                padding: 8px;
                font-size: 0.9em;
            }
            .btn {
                padding: 12px 25px;
                font-size: 1em;
            }
        }
        @media (max-width: 425px) {
            .success-title {
                font-size: 1.8em;
            }
            .success-message {
                font-size: 0.95em;
            }
            .success-icon {
                font-size: 3.5em;
            }
            .order-details th, .order-details td {
                font-size: 0.85em;
            }
            .payment-info, .customer-info p, .bank-transfer p {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="container">
        <div class="success-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="success-title">Chúc mừng! Đặt hàng thành công!</h1>
            <p class="success-message">
                Cảm ơn bạn đã tin tưởng chúng tôi!<br>
                Email xác nhận đã được gửi đến hộp thư của bạn. Chúng tôi sẽ liên hệ sớm để xác nhận đơn hàng.
            </p>
            <div class="payment-info">
                <strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($payment_method_text, ENT_QUOTES); ?>
            </div>

            <!-- Thông tin khách hàng -->
            <div class="customer-info">
                <h3>Thông tin khách hàng</h3>
                <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($customer_info['name'] ?? '', ENT_QUOTES); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['email'] ?? '', ENT_QUOTES); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($customer_info['phone'] ?? '', ENT_QUOTES); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($customer_info['address'] ?? '', ENT_QUOTES); ?></p>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="order-details">
                <h3>Thông tin đơn hàng #<?php echo htmlspecialchars($order_id, ENT_QUOTES); ?></h3>
                <table>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['current_price'], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo number_format($item['current_price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="total">Tổng cộng</td>
                        <td class="total"><?php echo number_format($total_price, 0, ',', '.'); ?> VNĐ</td>
                    </tr>
                </table>
            </div>

            <!-- Thông tin chuyển khoản -->
            <?php if ($payment_method == 'bank_transfer'): ?>
                <div class="bank-transfer">
                    <h3>Thông tin chuyển khoản</h3>
                    <p><strong>Ngân hàng:</strong> Ngân hàng Thương mại Cổ phần Kỹ Thương Việt Nam (Techcombank)</p>
                    <p><strong>Số tài khoản:</strong> 1234-5678-9012-3456</p>
                    <p><strong>Chủ tài khoản:</strong> Công ty TNHH Thắng Raiy</p>
                    <p><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng #<?php echo htmlspecialchars($order_id, ENT_QUOTES); ?></p>
                    <p>Vui lòng chuyển khoản số tiền <strong><?php echo number_format($total_price, 0, ',', '.'); ?> VNĐ</strong> theo thông tin trên để hoàn tất thanh toán.</p>
                </div>
            <?php endif; ?>

            <a href="/2/public/" class="btn"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
        </div>
    </div>

    <?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>