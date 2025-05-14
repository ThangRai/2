<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng điền đầy đủ thông tin!'];
        header("Location: register.php");
        exit;
    }

    if (!preg_match('/^[0-9]{9,15}$/', $phone)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Số điện thoại không hợp lệ!'];
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Mật khẩu xác nhận không khớp!'];
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Mật khẩu phải có ít nhất 8 ký tự!'];
        header("Location: register.php");
        exit;
    }

    // Check if email or username exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Email hoặc tên người dùng đã tồn tại!'];
        header("Location: register.php");
        exit;
    }

    // Insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $phone, $hashed_password])) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Đăng ký thành công! Vui lòng đăng nhập.'];
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Đăng ký thất bại! Vui lòng thử lại.'];
        header("Location: register.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng ký</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(135deg, #89f7fe, #66a6ff);
    }
    .register-card {
      background: #fff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
      max-width: 450px;
      width: 100%;
    }
    .input-icon {
      position: absolute;
      left: 15px;
      top: 69%;
      transform: translateY(-50%);
      color: #0d6efd;
    }
    .form-control {
      padding-left: 40px;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="register-card">
      <h3 class="text-center mb-4">Đăng ký tài khoản</h3>
      <form method="POST" action="">
        <div class="mb-3 position-relative">
          <label for="username" class="form-label">Tên người dùng</label>
          <i class="bi bi-person-fill input-icon"></i>
          <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
          <label for="email" class="form-label">Email</label>
          <i class="bi bi-envelope-fill input-icon"></i>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
          <label for="phone" class="form-label">Số điện thoại</label>
          <i class="bi bi-telephone-fill input-icon"></i>
          <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
          <label for="password" class="form-label">Mật khẩu</label>
          <i class="bi bi-lock-fill input-icon"></i>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
          <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
          <i class="bi bi-lock-fill input-icon"></i>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" name="register" class="btn btn-success w-100">Đăng ký</button>

        <div class="text-center mt-3">
          <a href="login.php">Đã có tài khoản? Đăng nhập</a>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($_SESSION['message'])): ?>
    <script>
      Swal.fire({
        icon: '<?php echo $_SESSION['message']['type']; ?>',
        title: '<?php echo $_SESSION['message']['type'] === "success" ? "Thành công" : "Lỗi"; ?>',
        text: '<?php echo $_SESSION['message']['text']; ?>'
      });
    </script>
    <?php unset($_SESSION['message']); ?>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
