<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['reset_email'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Yêu cầu không hợp lệ!'];
    header("Location: login.php");
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $otp = trim($_POST['otp']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($otp) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng điền đầy đủ thông tin!'];
        header("Location: reset_password.php");
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Mật khẩu xác nhận không khớp!'];
        header("Location: reset_password.php");
        exit;
    }

    if (strlen($new_password) < 8) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Mật khẩu mới phải có ít nhất 8 ký tự!'];
        header("Location: reset_password.php");
        exit;
    }

    // Verify OTP
    $stmt = $pdo->prepare("SELECT otp FROM otp_codes WHERE email = ? AND otp = ? AND expires_at > NOW()");
    $stmt->execute([$email, $otp]);
    if (!$stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Mã OTP không hợp lệ hoặc đã hết hạn!'];
        header("Location: reset_password.php");
        exit;
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    if ($stmt->execute([$hashed_password, $email])) {
        // Clear OTP
        $stmt = $pdo->prepare("DELETE FROM otp_codes WHERE email = ?");
        $stmt->execute([$email]);

        unset($_SESSION['reset_email']);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.'];
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi đặt lại mật khẩu! Vui lòng thử lại.'];
        header("Location: reset_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg login-card">
            <h3 class="text-center mb-4">Đặt lại mật khẩu</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="otp" class="form-label">Mã OTP</label>
                    <input type="text" name="otp" id="otp" class="form-control" maxlength="6" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="reset" class="btn btn-primary w-100">Đặt lại mật khẩu</button>
                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none">Quay lại đăng nhập</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>