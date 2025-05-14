<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';



// Xử lý khi người dùng bấm nút Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Xác minh reCAPTCHA
    $recaptchaSecret = '6LcueTQrAAAAADs2jcO4qTppgmwETcvyEIFSVPkr';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $captchaSuccess = json_decode($verify);

    if (!$captchaSuccess->success) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng xác minh bạn không phải robot.'];
    } else {
        // Kiểm tra tài khoản
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Ghi nhớ đăng nhập (cookie lưu 7 ngày)
            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 7), "/");
            } else {
                setcookie('remember_email', '', time() - 3600, "/"); // Xóa nếu bỏ chọn
            }

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Đăng nhập thành công!'];
            header("Location: http://localhost/2/public");
            exit;
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Email hoặc mật khẩu không đúng!'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng nhập</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #9face6);
    }
    .login-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }
    .input-icon {
      position: absolute;
      left: 15px;
      top: 69%;
      transform: translateY(-50%);
      color: #6c63ff;
      font-size: 1.2rem;
    }
    .position-relative input {
      padding-left: 40px !important;
    }
    .eye-icon {
      position: absolute;
      right: 15px;
      top: 69%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c63ff;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-card">
      <h3 class="text-center mb-4">Đăng nhập</h3>
      <form method="POST" action="">
        <div class="mb-3 position-relative">
          <label for="email" class="form-label">Email</label>
          <i class="bi bi-envelope-fill input-icon"></i>
          <input type="email" name="email" id="email" class="form-control" required value="<?php echo isset($_COOKIE['remember_email']) ? htmlspecialchars($_COOKIE['remember_email']) : ''; ?>">
        </div>

        <div class="mb-3 position-relative">
          <label for="password" class="form-label">Mật khẩu</label>
          <i class="bi bi-lock-fill input-icon"></i>
          <input type="password" name="password" id="password" class="form-control" required>
          <i class="bi bi-eye-fill eye-icon" id="togglePassword"></i>
        </div>

        <div class="mb-3 form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember"
            <?php if (isset($_COOKIE['remember_email'])) echo 'checked'; ?>>
          <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <div class="mb-3">
          <div class="g-recaptcha" data-sitekey="6LcueTQrAAAAAB1Enm0TDzLy7UO8DWeXiizVO4FB"></div>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">Đăng nhập</button>

        <div class="text-center mt-3">
          <a href="register.php">Đăng ký tài khoản</a> |
          <a href="forgot_password.php">Quên mật khẩu?</a>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <script>
      Swal.fire({
        icon: '<?php echo $_SESSION['message']['type']; ?>',
        title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
        text: '<?php echo $_SESSION['message']['text']; ?>',
      });
    </script>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <script>
    // Toggle mật khẩu
    document.getElementById('togglePassword').addEventListener('click', function () {
      const input = document.getElementById('password');
      const icon = this;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
      }
    });
  </script>
</body>
</html>
