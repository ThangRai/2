<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/laragon/www/2/public/includes/PHPMailer/src/Exception.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/PHPMailer.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/SMTP.php';

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    error_log('Database included');

    // Kiểm tra giỏ hàng
    if (empty($_SESSION['cart'])) {
        $_SESSION['error'] = 'Giỏ hàng trống! Vui lòng thêm sản phẩm trước khi thanh toán.';
        error_log('Empty cart');
        header('Location: /2/public/pages/cart.php');
        exit;
    }

    // Lấy danh sách sản phẩm trong giỏ
    $cart_items = [];
    $total_price = 0;
    error_log('Cart data: ' . print_r($_SESSION['cart'], true));
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT id, name, image, current_price FROM products WHERE id IN ($placeholders) AND is_active = 1");
            error_log('SQL query: ' . $stmt->queryString);
            error_log('SQL params: ' . print_r($ids, true));
            $stmt->execute($ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Products fetched: ' . print_r($products, true));

            foreach ($_SESSION['cart'] as $product_id => $cart_item) {
                foreach ($products as $product) {
                    if ($product['id'] == $product_id) {
                        $cart_items[$product_id] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'image' => $product['image'],
                            'current_price' => $product['current_price'],
                            'quantity' => $cart_item['quantity']
                        ];
                        $total_price += $product['current_price'] * $cart_item['quantity'];
                    }
                }
            }
        }
    }
    error_log('Cart items: ' . print_r($cart_items, true));

    // Xử lý đặt hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log('POST data: ' . print_r($_POST, true));
        if (isset($_POST['place_order'])) {
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $province = trim($_POST['province'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $ward = trim($_POST['ward'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $payment_method = trim($_POST['payment_method'] ?? '');

            $errors = [];
            if (empty($full_name)) $errors[] = "Họ và tên là bắt buộc.";
            if (empty($phone) || !preg_match('/^[0-9]{10,11}$/', $phone)) $errors[] = "Số điện thoại không hợp lệ.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
            if (empty($address)) $errors[] = "Địa chỉ là bắt buộc.";
            if (empty($province)) $errors[] = "Vui lòng chọn tỉnh/thành.";
            if (empty($district)) $errors[] = "Vui lòng chọn quận/huyện.";
            if (empty($ward)) $errors[] = "Vui lòng chọn phường/xã.";
            if (empty($payment_method)) $errors[] = "Vui lòng chọn phương thức thanh toán.";
            if (empty($cart_items)) $errors[] = "Giỏ hàng trống.";

            error_log('Validation errors: ' . print_r($errors, true));
            if (empty($errors)) {
                // Kiểm tra khách hàng đã tồn tại
                $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
                $stmt->execute([$email]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($customer) {
                    // Cập nhật thông tin khách hàng
                    $stmt = $pdo->prepare("
                        UPDATE customers 
                        SET name = ?, phone = ?, address = ?, province = ?, district = ?, ward = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$full_name, $phone, $address, $province, $district, $ward, $customer['id']]);
                    $customer_id = $customer['id'];
                    error_log('Customer updated, ID: ' . $customer_id);
                } else {
                    // Thêm khách hàng mới
                    $stmt = $pdo->prepare("
                        INSERT INTO customers (name, email, phone, address, province, district, ward)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$full_name, $email, $phone, $address, $province, $district, $ward]);
                    $customer_id = $pdo->lastInsertId();
                    error_log('Customer added, ID: ' . $customer_id);
                }

                // Lưu vào bảng orders
                $stmt = $pdo->prepare("
                    INSERT INTO orders (customer_id, total_amount, status, note, payment_method, created_at)
                    VALUES (?, ?, 'pending', ?, ?, NOW())
                ");
                $stmt->execute([$customer_id, $total_price, $note, $payment_method]);
                $order_id = $pdo->lastInsertId();
                error_log('Order saved, ID: ' . $order_id);

                // Lưu vào bảng order_details
                foreach ($cart_items as $item) {
                    $stmt = $pdo->prepare("
                        INSERT INTO order_details (order_id, product_id, quantity, price)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['current_price']]);
                    error_log('Order detail saved for product_id: ' . $item['id']);
                }

                // Lưu thông tin vào session để sử dụng ở order_success.php
                $_SESSION['order_id'] = $order_id;
                $_SESSION['cart_items'] = $cart_items;
                $_SESSION['total_price'] = $total_price;
                $_SESSION['customer_info'] = [
                    'name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address . ', ' . $ward . ', ' . $district . ', ' . $province
                ];
                $_SESSION['payment_method'] = $payment_method;
                $_SESSION['cart_message'] = 'Đặt hàng thành công!';

                // Gửi email
                $mail = new PHPMailer(true);
                try {
                    $mail->SMTPDebug = 2;
                    $mail->Debugoutput = function($str, $level) {
                        error_log("PHPMailer: $str");
                    };
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'badaotulong123@gmail.com';
                    $mail->Password = 'hisl ytee gyip kzat';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';

                    $mail->setFrom('badaotulong123@gmail.com', 'Thắng Raiy');
                    $mail->addAddress($email);
                    $mail->addAddress('badaotulong123@gmail.com');

                    $mail->isHTML(true);
                    $mail->Subject = 'Đơn hàng mới #' . $order_id;
                    $mail->Body = '
                        <h2>Xác nhận đơn hàng #' . $order_id . '</h2>
                        <p><strong>Khách hàng:</strong> ' . htmlspecialchars($full_name) . '</p>
                        <p><strong>Số điện thoại:</strong> ' . htmlspecialchars($phone) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p><strong>Địa chỉ:</strong> ' . htmlspecialchars($address) . ', ' . htmlspecialchars($ward) . ', ' . htmlspecialchars($district) . ', ' . htmlspecialchars($province) . '</p>
                        <p><strong>Ghi chú:</strong> ' . (empty($note) ? 'Không có' : htmlspecialchars($note)) . '</p>
                        <p><strong>Phương thức thanh toán:</strong> ' . ($payment_method == 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng') . '</p>
                        <h3>Chi tiết đơn hàng</h3>
                        <table border="1" cellpadding="5" style="border-collapse: collapse;">
                            <tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Tổng</th></tr>';
                    foreach ($cart_items as $item) {
                        $mail->Body .= '
                            <tr>
                                <td>' . htmlspecialchars($item['name']) . '</td>
                                <td>' . $item['quantity'] . '</td>
                                <td>' . number_format($item['current_price'], 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($item['current_price'] * $item['quantity'], 0, ',', '.') . ' VNĐ</td>
                            </tr>';
                    }
                    $mail->Body .= '
                            <tr><td colspan="3"><strong>Tổng cộng</strong></td><td><strong>' . number_format($total_price, 0, ',', '.') . ' VNĐ</strong></td></tr>
                        </table>';

                    $mail->send();
                    error_log('Email sent successfully');
                } catch (Exception $e) {
                    error_log('Email sending error: ' . $mail->ErrorInfo);
                    $_SESSION['cart_message'] = 'Đặt hàng thành công nhưng gửi email thất bại!';
                }

                unset($_SESSION['cart']);
                $response = ['success' => true, 'message' => 'Đặt hàng thành công!'];
                error_log('Order completed');

                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                }

                $_SESSION['cart_message'] = $response['message'];
                header('Location: /2/public/pages/order_success.php');
                exit;
            } else {
                $response = ['success' => false, 'message' => implode(' ', $errors)];
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                }
                $_SESSION['cart_message'] = $response['message'];
            }
        }
    }

} catch (Exception $e) {
    error_log('Checkout page error: ' . $e->getMessage());
    $cart_items = [];
    $total_price = 0;
    $response = ['success' => false, 'message' => 'Đã có lỗi xảy ra!'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            background: linear-gradient(to bottom, #f8f9fa, #dfe6e9);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #2d3436;
            margin-bottom: 40px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }
        h1::after {
            content: '';
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #0984e3, #74b9ff);
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        .checkout-container {
            display: flex;
            gap: 20px;
        }
        .customer-info {
            flex: 8;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .order-summary {
            flex: 4;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .customer-info h2, .order-summary h2 {
            font-size: 1.5em;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 500;
            background: linear-gradient(90deg, #0984e3, #74b9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: 500;
            color: #2d3436;
            margin-bottom: 5px;
            display: block;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #dfe6e9;
            border-radius: 25px;
            font-size: 1em;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .col-half {
            flex: 1;
            min-width: 150px;
        }
        .col-third {
            flex: 1;
            min-width: 120px;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dfe6e9;
        }
        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        .order-item-details {
            flex: 1;
        }
        .order-item-details .name {
            font-weight: 500;
            color: #2d3436;
            font-size: 1.1em;
        }
        .order-item-details .quantity {
            color: #636e72;
            font-size: 0.95em;
        }
        .order-item-details .price {
            color: #ff4757;
            font-weight: 600;
            font-size: 1.1em;
        }
        .total-price {
            font-size: 1.6em;
            font-weight: 700;
            background: linear-gradient(90deg, #ff4757, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: right;
            margin: 20px 0;
        }
        .payment-method {
            margin: 20px 0;
        }
        .payment-method label {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #2d3436;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .payment-method input {
            margin-right: 10px;
        }
        .btn {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }
        .btn:hover {
            background: linear-gradient(135deg, #0652dd, #4dabf7);
            transform: scale(1.03);
        }
        .btn-edit-cart {
            background: linear-gradient(135deg, #ff4757, #ff6b6b);
            text-decoration: none;
            margin-bottom: 20px;
        }
        .btn-edit-cart:hover {
            background: linear-gradient(135deg, #e84118, #ff4757);
        }
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }
        @media (max-width: 992px) {
            .checkout-container {
                flex-direction: column;
            }
            .customer-info, .order-summary {
                flex: 1;
            }
        }
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }
            h1 {
                font-size: 2em;
            }
            .customer-info, .order-summary {
                padding: 15px;
            }
            .row {
                flex-direction: column;
                gap: 10px;
            }
            .col-half, .col-third {
                flex: 1;
                min-width: 100%;
            }
            .order-item img {
                width: 50px;
                height: 50px;
            }
            .total-price {
                font-size: 1.4em;
            }
            .btn {
                padding: 10px 20px;
                font-size: 1em;
            }
        }
        @media (max-width: 425px) {
            .order-item-details .name, .order-item-details .price {
                font-size: 1em;
            }
            .order-item-details .quantity {
                font-size: 0.9em;
            }
            .form-group input, .form-group select, .form-group textarea {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="container">
        <h1>Thanh Toán</h1>
        <?php if (isset($_SESSION['cart_message'])): ?>
            <div class="toast-container">
                <div class="toast show bg-<?php echo strpos($_SESSION['cart_message'], 'lỗi') !== false ? 'danger' : 'success'; ?>" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Thông báo</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body"><?php echo htmlspecialchars($_SESSION['cart_message'], ENT_QUOTES); ?></div>
                </div>
            </div>
            <?php unset($_SESSION['cart_message']); ?>
        <?php endif; ?>
        <div class="checkout-container">
            <div class="customer-info">
                <h2>Thông Tin Khách Hàng</h2>
                <form id="checkout-form" method="POST">
                    <input type="hidden" name="ajax" value="1">
                    <div class="form-group">
                        <label for="full_name">Họ và Tên *</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                    </div>
                    <div class="form-group row">
                        <div class="col-half">
                            <label for="phone">Số Điện Thoại *</label>
                            <input type="text" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                        </div>
                        <div class="col-half">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Địa Chỉ *</label>
                        <input type="text" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
                    </div>
                    <div class="form-group row">
                        <div class="col-third">
                            <label for="province">Tỉnh/Thành *</label>
                            <select id="province" name="province" required>
                                <option value="">Chọn tỉnh/thành</option>
                            </select>
                        </div>
                        <div class="col-third">
                            <label for="district">Quận/Huyện *</label>
                            <select id="district" name="district" required>
                                <option value="">Chọn quận/huyện</option>
                            </select>
                        </div>
                        <div class="col-third">
                            <label for="ward">Phường/Xã *</label>
                            <select id="ward" name="ward" required>
                                <option value="">Chọn phường/xã</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">Ghi Chú</label>
                        <textarea id="note" name="note"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
                    </div>
                    <div class="payment-method">
                        <h2>Phương Thức Thanh Toán</h2>
                        <label>
                            <input type="radio" name="payment_method" value="cod" required>
                            Thanh toán khi nhận hàng
                        </label>
                        <label>
                            <input type="radio" name="payment_method" value="bank_transfer">
                            Thanh toán chuyển khoản
                        </label>
                    </div>
                    <button type="submit" name="place_order" class="btn">
                        <i class="fas fa-check"></i> Đặt Hàng
                    </button>
                </form>
            </div>
            <div class="order-summary">
                <h2>Đơn Hàng Của Bạn</h2>
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo $item['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($item['image'], ENT_QUOTES) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>">
                        <div class="order-item-details">
                            <div class="name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></div>
                            <div class="quantity">Số lượng: <?php echo $item['quantity']; ?></div>
                            <div class="price"><?php echo number_format($item['current_price'] * $item['quantity'], 0, ',', '.'); ?>đ</div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="total-price">Tổng cộng: <?php echo number_format($total_price, 0, ',', '.'); ?>đ</div>
                <a href="/2/public/pages/cart.php" class="btn btn-edit-cart">
                    <i class="fas fa-shopping-cart"></i> Chỉnh sửa giỏ hàng
                </a>
            </div>
        </div>
    </div>

    <div class="toast-container">
        <div class="toast" id="checkoutToast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Thông báo</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        console.log('Script loaded');
        const form = document.getElementById('checkout-form');
        if (form) {
            console.log('Form found');
            form.addEventListener('submit', function(e) {
                console.log('Form submitted');
                e.preventDefault();
                const button = this.querySelector('button[name="place_order"]');
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đặt hàng...';
                button.disabled = true;

                const formData = new FormData(this);
                formData.append('place_order', '1');
                console.log('Form data:', Array.from(formData.entries()));
                fetch('/2/public/pages/checkout.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    button.innerHTML = '<i class="fas fa-check"></i> Đặt Hàng';
                    button.disabled = false;

                    const toastEl = document.getElementById('checkoutToast');
                    const toastBody = toastEl.querySelector('.toast-body');
                    toastBody.textContent = data.message;
                    toastEl.classList.remove('bg-success', 'bg-danger');
                    toastEl.classList.add(data.success ? 'bg-success' : 'bg-danger');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();

                    if (data.success) {
                        setTimeout(() => window.location.href = '/2/public/pages/order_success.php', 1000);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    button.innerHTML = '<i class="fas fa-check"></i> Đặt Hàng';
                    button.disabled = false;
                    const toastEl = document.getElementById('checkoutToast');
                    const toastBody = toastEl.querySelector('.toast-body');
                    toastBody.textContent = 'Đã có lỗi xảy ra!';
                    toastEl.classList.remove('bg-success');
                    toastEl.classList.add('bg-danger');
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });
            });
        } else {
            console.log('Form not found');
        }

        fetch('https://provinces.open-api.vn/api/p/')
            .then(response => response.json())
            .then(data => {
                const provinceSelect = document.getElementById('province');
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.name;
                    option.textContent = province.name;
                    option.dataset.code = province.code;
                    provinceSelect.appendChild(option);
                });
            });

        document.getElementById('province')?.addEventListener('change', function() {
            const code = this.selectedOptions[0]?.dataset.code;
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (code) {
                fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name;
                            option.textContent = district.name;
                            option.dataset.code = district.code;
                            districtSelect.appendChild(option);
                        });
                    });
            }
        });

        document.getElementById('district')?.addEventListener('change', function() {
            const code = this.selectedOptions[0]?.dataset.code;
            const wardSelect = document.getElementById('ward');
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (code) {
                fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.name;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
        <?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>

</body>
</html>
<?php ob_end_flush(); ?>