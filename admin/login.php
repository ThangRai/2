<?php
session_start();
require_once 'config/db_connect.php';
require_once 'config/constants.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:/laragon/www/2/public/includes/PHPMailer/src/Exception.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/PHPMailer.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/SMTP.php';


// Nếu đã đăng nhập thì chuyển hướng
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
$show_reset_form = isset($_GET['action']) && $_GET['action'] === 'forgot_password';

// Hàm tạo mật khẩu ngẫu nhiên
function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

// Xử lý quên mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vui lòng nhập email hợp lệ.';
    } else {
        // Tìm admin theo email
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Tạo mật khẩu mới
            $new_password = generateRandomPassword();

            // Cập nhật mật khẩu (không mã hóa)
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE email = ?");
            $stmt->execute([$new_password, $email]);

            // Gửi email với PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Cấu hình SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'badaotulong123@gmail.com';
                $mail->Password = 'hisl ytee gyip kzat';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                // Thiết lập người gửi và người nhận
                $mail->setFrom('badaotulong123@gmail.com', 'Thắng Raiy');
                $mail->addAddress($email);

                // Nội dung email
                $mail->isHTML(false);
                $mail->Subject = 'Khôi phục mật khẩu - ' . 'THANGRAI WEBSITE';
                $mail->Body = "Chào " . htmlspecialchars($admin['name']) . ",\n\n";
                $mail->Body .= "Mật khẩu mới của bạn là: $new_password\n";
                $mail->Body .= "Vui lòng đăng nhập và thay đổi mật khẩu nếu cần.\n\n";
                $mail->Body .= "Trân trọng,\n" . 'THANGRAI WEBSITE';

                // Gửi email
                $mail->send();
                $success = 'Mật khẩu mới đã được gửi đến email của bạn.';
                $show_reset_form = false;
            } catch (Exception $e) {
                $error = 'Không thể gửi email. Lỗi: ' . $mail->ErrorInfo;
                error_log('Lỗi gửi email PHPMailer: ' . $mail->ErrorInfo);
            }
        } else {
            $error = 'Email không tồn tại trong hệ thống.';
        }
    }
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Tìm admin theo username
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $password === $admin['password']) {
        // Lưu session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];

        // Ghi lại lịch sử đăng nhập
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $pdo->prepare("INSERT INTO admin_logins (admin_id, admin_name, ip_address) VALUES (?, ?, ?)");
        $log_stmt->execute([$admin['id'], $admin['name'], $ip_address]);

        // Chuyển hướng
        header("Location: index.php");
        exit;
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Đăng nhập</title>
    <link rel="stylesheet" href="assets/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fc;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card login-card shadow">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <img src="assets/img/logo.png" alt="Logo" width="50">
                <h1 class="h4 text-gray-900 mb-0"><?php echo SITE_NAME; ?></h1>
                <p class="text-muted small"><?php echo $show_reset_form ? 'Khôi phục mật khẩu' : 'Đăng nhập để tiếp tục'; ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($show_reset_form): ?>
                <form method="POST" novalidate>
                    <input type="hidden" name="forgot_password" value="1">
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="email" placeholder="Nhập email của bạn">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Gửi mật khẩu</button>
                    <div class="text-center mt-3">
                        <a href="login.php">Quay lại đăng nhập</a>
                    </div>
                </form>
            <?php else: ?>
                <form method="POST" novalidate>
                    <input type="hidden" name="login" value="1">
                    <div class="form-group mb-3">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" required autocomplete="username" placeholder="Nhập tên đăng nhập">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    <div class="text-center mt-3">
                        <a href="login.php?action=forgot_password">Quên mật khẩu?</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>