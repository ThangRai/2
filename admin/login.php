<?php
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once 'config/db_connect.php';
require_once 'config/constants.php';

// Nhập các lớp PHPMailer
require 'C:/laragon/www/2/public/includes/PHPMailer/src/Exception.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/PHPMailer.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Thiết lập múi giờ MySQL
$pdo->exec("SET time_zone = '+07:00';");

// Chuyển hướng nếu đã đăng nhập
if (isset($_SESSION['admin_id'])) {
    if (!headers_sent()) {
        header("Location: index.php");
        exit;
    }
    echo '<script>window.location.href="index.php";</script>';
    exit;
}

$errors = [];
$success = '';
$show_reset_form = isset($_GET['action']) && $_GET['action'] === 'forgot_password';

$recaptchaSecret = '6LcueTQrAAAAADs2jcO4qTppgmwETcvyEIFSVPkr';
$telegramBotToken = '6608663537:AAExeC77L9XmTSK3lpW0Q3zt_kGfC1qKZfA';
$telegramChatId = '5901907211';

// Hàm gửi thông báo Telegram
function sendTelegramMessage($botToken, $chatId, $message) {
    if (!extension_loaded('curl')) {
        error_log('Lỗi gửi thông báo Telegram: Tiện ích cURL không được bật trong PHP');
        return false;
    }

    // Kiểm tra token bot bằng API getMe
    $ch = curl_init("https://api.telegram.org/bot$botToken/getMe");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($result === false || $httpCode !== 200) {
        $response = json_decode($result, true);
        $errorMsg = $error ?: ($response['description'] ?? 'Không thể xác minh bot Telegram');
        error_log("Lỗi xác minh bot Telegram: HTTP $httpCode - $errorMsg");
        return false;
    }

    // Gửi thông báo
    $ch = curl_init("https://api.telegram.org/bot$botToken/sendMessage");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ],
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($result === false || $httpCode !== 200) {
        $response = json_decode($result, true);
        $errorMsg = $error ?: ($response['description'] ?? 'Unknown Telegram API error');
        error_log("Lỗi gửi thông báo Telegram: HTTP $httpCode - $errorMsg - Phản hồi: " . substr($result, 0, 200));
        return false;
    }
    return true;
}

// Hàm tạo mật khẩu ngẫu nhiên
function generateRandomPassword($length = 8) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Hàm lấy cấu hình email từ bảng settings
function getEmailSettings($pdo) {
    $settings = [];
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name IN ('smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_from', 'smtp_from_name')");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }
    return $settings;
}

// Xử lý quên mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verify);

    if (!$responseData->success) {
        $errors[] = 'Vui lòng xác nhận bạn không phải robot.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập email hợp lệ.';
    } else {
        $stmt = $pdo->prepare("SELECT name, email FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $new_password = generateRandomPassword();

            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE email = ?");
            $stmt->execute([$new_password, $email]);

            // Lấy cấu hình email từ bảng settings
            $email_settings = getEmailSettings($pdo);

            // Kiểm tra xem cấu hình có đầy đủ không
            $required_settings = ['smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_from', 'smtp_from_name'];
            foreach ($required_settings as $key) {
                if (empty($email_settings[$key])) {
                    $errors[] = "Thiếu cấu hình email: $key";
                }
            }

            if (empty($errors)) {
                $mail = new PHPMailer(true);
                try {
                    $mail->SMTPDebug = 0; // Tăng mức debug
                    $mail->Debugoutput = 'error_log'; // In lỗi ra màn hình
                    $mail->isSMTP();
                    $mail->Host = $email_settings['smtp_host'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $email_settings['smtp_username'];
                    $mail->Password = $email_settings['smtp_password'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Mặc định ssl vì cổng 465
                    $mail->Port = (int) $email_settings['smtp_port'];

                    // Tạm thời tắt xác minh SSL
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    $mail->setFrom($email_settings['smtp_from'], $email_settings['smtp_from_name']);
                    $mail->addAddress($email);
                    $mail->isHTML(false);
                    $mail->Subject = 'Khôi phục mật khẩu - THANGRAI WEBSITE';
                    $mail->Body = "Chào " . htmlspecialchars($admin['name']) . ",\n\nMật khẩu mới của bạn là: $new_password\nVui lòng đăng nhập và thay đổi mật khẩu nếu cần.\n\nTrân trọng,\nTHANGRAI WEBSITE";

                    $mail->send();
                    $success = 'Mật khẩu mới đã được gửi đến email của bạn.';
                    $show_reset_form = false;
                } catch (Exception $e) {
                    $errors[] = 'Không thể gửi email. Lỗi: ' . $mail->ErrorInfo;
                    error_log('Lỗi gửi email PHPMailer: ' . $mail->ErrorInfo);
                }
            }
        } else {
            $errors[] = 'Email không tồn tại trong hệ thống.';
        }
    }
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verify);

    if (!$responseData->success) {
        $errors[] = 'Vui lòng xác nhận bạn không phải robot.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $login_time = date('Y-m-d H:i:s');
            try {
                $log_stmt = $pdo->prepare("INSERT INTO admin_logins (admin_id, admin_name, ip_address, login_time) VALUES (?, ?, ?, ?)");
                $log_stmt->execute([$admin['id'], $admin['name'], $ip_address, $login_time]);

                // Gửi thông báo Telegram
                $message = "<b>Đăng nhập thành công</b>\n";
                $message .= "Tên: " . htmlentities($admin['name'], ENT_QUOTES, 'UTF-8') . "\n";
                $message .= "Tên đăng nhập: " . htmlentities($username, ENT_QUOTES, 'UTF-8') . "\n";
                $message .= "IP: " . htmlentities($ip_address, ENT_QUOTES, 'UTF-8') . "\n";
                $message .= "Thời gian: " . htmlentities($login_time, ENT_QUOTES, 'UTF-8');
                if (!sendTelegramMessage($telegramBotToken, $telegramChatId, $message)) {
                    error_log("Lỗi gửi thông báo Telegram: Vui lòng kiểm tra token bot, chat ID, và kết nối mạng");
                }
            } catch (PDOException $e) {
                error_log('Lỗi lưu lịch sử đăng nhập: ' . $e->getMessage());
                $errors[] = 'Đăng nhập thành công, nhưng không thể lưu lịch sử đăng nhập.';
            }

            if (!headers_sent()) {
                header("Location: index.php");
                exit;
            }
            echo '<script>window.location.href="index.php";</script>';
            exit;
        } else {
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Đăng nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css">
    <style>.login-container{min-height:100vh;display:flex;align-items:center;justify-content:center;background-color:#f8f9fc}.login-card{max-width:400px;width:100%}</style>
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

            <?php if ($errors): ?>
                <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php if ($show_reset_form): ?>
                    <input type="hidden" name="forgot_password" value="1">
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="email" placeholder="Nhập email">
                    </div>
                    <div class="g-recaptcha mb-3" data-sitekey="6LcueTQrAAAAAB1Enm0TDzLy7UO8DWeXiizVO4FB"></div>
                    <button type="submit" class="btn btn-primary btn-block">Gửi mật khẩu</button>
                    <div class="text-center mt-3"><a href="login.php">Quay lại đăng nhập</a></div>
                <?php else: ?>
                    <input type="hidden" name="login" value="1">
                    <div class="form-group mb-3">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" required autocomplete="username" placeholder="Nhập tên đăng nhập">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Mật khẩu</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu">
                            <div class="input-group-append">
                                <span class="input-group-text" style="cursor:pointer" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="g-recaptcha mb-3" data-sitekey="6LcueTQrAAAAAB1Enm0TDzLy7UO8DWeXiizVO4FB"></div>
                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    <div class="text-center mt-3"><a href="login.php?action=forgot_password">Quên mật khẩu?</a></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
function togglePassword() {
    const e = document.getElementById("password"),
          t = document.getElementById("togglePasswordIcon");
    if (e.type === "password") {
        e.type = "text";
        t.classList.remove("fa-eye");
        t.classList.add("fa-eye-slash");
    } else {
        e.type = "password";
        t.classList.remove("fa-eye-slash");
        t.classList.add("fa-eye");
    }
}
</script>
</body>
</html>
<?php ob_end_flush(); ?>