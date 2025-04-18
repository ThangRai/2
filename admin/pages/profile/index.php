<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'C:/laragon/www/2/admin/config/db_connect.php';
// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy thông tin admin
$admin_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy thông tin admin'];
    header('Location: index.php');
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $two_factor_code = trim($_POST['two_factor_code'] ?? '');

    $errors = [];

    // Validate
    if (empty($name)) {
        $errors[] = 'Họ và Tên không được để trống';
    }
    if (!empty($phone) && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    if (empty($username)) {
        $errors[] = 'Tên đăng nhập không được để trống';
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }

    // Kiểm tra username trùng
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
    $stmt->execute([$username, $admin_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Tên đăng nhập đã tồn tại';
    }

    // Xử lý ảnh đại diện
    $avatar = $admin['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Ảnh đại diện chỉ hỗ trợ JPG, JPEG, PNG';
        } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB
            $errors[] = 'Ảnh đại diện không được lớn hơn 2MB';
        } else {
            $avatar_name = 'admin_' . $admin_id . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/avatars/';
            $upload_path = $upload_dir . $avatar_name;
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $avatar = $upload_path;
                // Xóa ảnh cũ nếu có
                if ($admin['avatar'] && file_exists($admin['avatar'])) {
                    unlink($admin['avatar']);
                }
            } else {
                $errors[] = 'Không thể tải lên ảnh đại diện';
            }
        }
    }

    // Nếu không có lỗi, cập nhật
    if (empty($errors)) {
        try {
            $update_data = [
                'name' => $name,
                'phone' => $phone ?: null,
                'email' => $email ?: null,
                'address' => $address ?: null,
                'username' => $username,
                'avatar' => $avatar,
                'two_factor_code' => $two_factor_code ?: null,
                'id' => $admin_id
            ];

            $sql = "UPDATE admins SET name = ?, phone = ?, email = ?, address = ?, username = ?, avatar = ?, two_factor_code = ?";
            if (!empty($password)) {
                $sql .= ", password = ?";
                $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= " WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($update_data));

            // Cập nhật session
            $_SESSION['admin_name'] = $name;
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật thông tin thành công'];
        } catch (Exception $e) {
            error_log('Profile update error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật thông tin'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    header('Location: index.php');
    exit;
}
?>

    <div class="row">
        <div class="col-md-12 content">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hồ sơ cá nhân</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <script>
                            Swal.fire({
                                icon: '<?php echo $_SESSION['message']['type']; ?>',
                                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                                html: '<?php echo htmlspecialchars($_SESSION['message']['text']); ?>',
                                confirmButtonText: 'OK'
                            });
                        </script>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="address">Địa chỉ</label>
                                    <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Mật khẩu mới (để trống nếu không đổi)</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="form-group">
                                    <label for="avatar">Ảnh đại diện</label>
                                    <input type="file" class="form-control-file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png">
                                    <?php if ($admin['avatar']): ?>
                                        <img src="/2/admin/<?php echo htmlspecialchars($admin['avatar']); ?>" alt="Avatar" class="mt-2" style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="two_factor_code">Mã xác thực (2FA)</label>
                                    <input type="text" class="form-control" id="two_factor_code" name="two_factor_code" value="<?php echo htmlspecialchars($admin['two_factor_code'] ?? ''); ?>">
                                    <small class="form-text text-muted">Để trống nếu không sử dụng xác thực hai yếu tố.</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
