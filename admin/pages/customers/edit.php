<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2]; // super_admin (1), staff (2)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Từ chối truy cập cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($customer_id <= 0) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID khách hàng không hợp lệ.'];
    echo '<script>window.location.href="?page=customers";</script>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Khách hàng không tồn tại.'];
    echo '<script>window.location.href="?page=customers";</script>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $ward = trim($_POST['ward'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Tên không được để trống';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    if (!empty($phone) && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
    if (empty($province)) {
        $errors[] = 'Tỉnh/Thành không được để trống';
    }
    if (empty($district)) {
        $errors[] = 'Quận/Huyện không được để trống';
    }
    if (empty($ward)) {
        $errors[] = 'Phường/Xã không được để trống';
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }

    // Kiểm tra email trùng
    $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ? AND id != ?");
    $stmt->execute([$email, $customer_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Email đã tồn tại';
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE customers SET name = ?, email = ?, phone = ?, address = ?, province = ?, district = ?, ward = ?";
            $params = [$name, $email, $phone ?: null, $address ?: null, $province, $district, $ward];
            if (!empty($password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= " WHERE id = ?";
            $params[] = $customer_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật khách hàng thành công'];
            echo '<script>window.location.href="?page=customers";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Lỗi sửa khách hàng: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật khách hàng: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<?php if (isset($_SESSION['message'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sửa khách hàng</h1>
    <a href="?page=customers" class="btn btn-secondary">Quay lại</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu (để trống nếu không đổi)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="province">Tỉnh/Thành <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="province" name="province" value="<?php echo htmlspecialchars($customer['province']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="district">Quận/Huyện <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($customer['district']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ward">Phường/Xã <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ward" name="ward" value="<?php echo htmlspecialchars($customer['ward']); ?>" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="?page=customers" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>