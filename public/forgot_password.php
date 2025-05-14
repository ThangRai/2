<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';
require_once 'includes/PHPMailer/src/Exception.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Email không tồn tại!'];
        header("Location: forgot_password.php");
        exit;
    }

    $otp = rand(100000, 999999);
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $pdo->prepare("INSERT INTO otp_codes (email, otp, expires_at) VALUES (?, ?, ?)");
    if ($stmt->execute([$email, $otp, $expires_at])) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'badaotulong123@gmail.com';
            $mail->Password = 'hisl ytee gyip kzat';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom('badaotulong123@gmail.com', 'Hệ thống OTP');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Mã OTP để đặt lại mật khẩu';
            $mail->Body = "Mã OTP của bạn là: <b>$otp</b>. Mã này có hiệu lực trong 15 phút.";

            $mail->send();
            $_SESSION['reset_email'] = $email;
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Mã OTP đã được gửi đến email của bạn!'];
            header("Location: reset_password.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gửi email thất bại: ' . $mail->ErrorInfo];
            header("Location: forgot_password.php");
            exit;
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lưu OTP! Vui lòng thử lại.'];
        header("Location: forgot_password.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quên mật khẩu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
      font-family: 'Segoe UI', sans-serif;
    }
    .forgot-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    .btn-warning {
      background-color: #fcb045;
      border: none;
    }
    .btn-warning:hover {
      background-color: #f39628;
    }
    a {
      color: #6c63ff;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="forgot-card">
      <h3 class="text-center mb-4 text-primary">Quên mật khẩu</h3>
      <form method="POST" action="">
        <div class="mb-3">
          <label for="email" class="form-label">Nhập email đã đăng ký</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="abc@example.com" required>
        </div>
        <button type="submit" name="forgot" class="btn btn-warning w-100">Gửi mã OTP</button>
        <div class="text-center mt-3">
          <a href="login.php">← Quay lại đăng nhập</a>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <script>
      Swal.fire({
        icon: '<?php echo $_SESSION['message']['type']; ?>',
        title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
        text: '<?php echo $_SESSION['message']['text']; ?>'
      });
    </script>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>
</body>
</html>
