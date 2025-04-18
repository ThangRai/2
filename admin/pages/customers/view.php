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
    <h1 class="h3 mb-0 text-gray-800">Chi tiết khách hàng</h1>
    <a href="?page=customers" class="btn btn-secondary">Quay lại</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo $customer['id']; ?></p>
                <p><strong>Tên:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></p>
                <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($customer['created_at'])); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($customer['address'] ?? '-'); ?></p>
                <p><strong>Tỉnh/Thành:</strong> <?php echo htmlspecialchars($customer['province']); ?></p>
                <p><strong>Quận/Huyện:</strong> <?php echo htmlspecialchars($customer['district']); ?></p>
                <p><strong>Phường/Xã:</strong> <?php echo htmlspecialchars($customer['ward']); ?></p>
            </div>
        </div>
    </div>
</div>