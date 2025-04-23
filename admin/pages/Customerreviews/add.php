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

// Xử lý thêm ý kiến
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    $avatar = null;

    // Xử lý tải lên ảnh
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES['avatar']['type'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['avatar']['name']);
        $upload_dir = 'uploads/dgkhachhang/';  // Thư mục lưu ảnh
        $upload_path = $upload_dir . $file_name;

        // Kiểm tra thư mục upload có tồn tại không
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);  // Tạo thư mục nếu chưa có
        }

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $avatar = $upload_path;
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi tải lên ảnh đại diện.'];
                echo '<script>window.location.href="index.php?page=customerreviews&subpage=add";</script>';
                exit;
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Ảnh không hợp lệ (chỉ chấp nhận JPEG, PNG, GIF, tối đa 2MB).'];
            echo '<script>window.location.href="index.php?page=customerreviews&subpage=add";</script>';
            exit;
        }
    }

    // Insert vào cơ sở dữ liệu
    $sql = "INSERT INTO customer_reviews (name, rating, description, content, is_visible, avatar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$name, $rating, $description, $content, $is_visible, $avatar])) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm ý kiến khách hàng thành công.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm ý kiến khách hàng.'];
    }
    echo '<script>window.location.href="index.php?page=customerreviews";</script>';
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
    <h1 class="h3 mb-0 text-gray-800">Thêm ý kiến khách hàng</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thêm ý kiến mới</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Tên khách hàng" required>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-control" required>
                        <option value="">Số sao</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> sao</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" class="form-control" placeholder="Mô tả" required>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input type="checkbox" name="is_visible" class="form-check-input" id="is_visible" checked>
                        <label class="form-check-label" for="is_visible">Hiển thị</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Thêm</button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <textarea name="content" class="form-control" rows="3" placeholder="Nội dung ý kiến" required></textarea>
                </div>
                <div class="col-md-6">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif">
                </div>
            </div>
        </form>
    </div>
</div>
