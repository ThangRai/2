<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Lấy thông tin đối tác
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
$stmt->execute([$id]);
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Đối tác không tồn tại.'];
    echo '<script>window.location.href="?page=partner";</script>';
    exit;
}

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    // Xử lý upload logo
$logo = $partner['logo'];
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/doitac/';
    $logo_name = time() . '_' . basename($_FILES['logo']['name']);
    $logo_path = $upload_dir . $logo_name;

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
        // Xóa logo cũ nếu có
        if ($logo && file_exists($logo)) {
            unlink($logo);
        }
        $logo = $logo_path;
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi upload logo.'];
        echo '<script>window.location.href="?page=partner&subpage=edit&id=' . $id . '";</script>';
        exit;
    }
}


    // Kiểm tra dữ liệu
    if (empty($name)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Tên đối tác là bắt buộc.'];
        echo '<script>window.location.href="?page=partner&subpage=edit&id=' . $id . '";</script>';
        exit;
    }

    // Cập nhật cơ sở dữ liệu
    try {
        $sql = "UPDATE partners SET name = ?, description = ?, link = ?, is_visible = ?, logo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $link, $is_visible, $logo, $id]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật đối tác thành công'];
        echo '<script>window.location.href="?page=partner";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Edit partner error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật đối tác: ' . $e->getMessage()];
        echo '<script>window.location.href="?page=partner&subpage=edit&id=' . $id . '";</script>';
        exit;
    }
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa đối tác</h1>
    <a href="?page=partner" class="btn btn-secondary">Quay lại</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin đối tác</h6>
    </div>
    <div class="card-body">
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

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Tên đối tác <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="<?php echo htmlspecialchars($partner['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" class="form-control" id="description" rows="4"><?php echo htmlspecialchars($partner['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="link">Link website</label>
                        <input type="url" name="link" class="form-control" id="link" value="<?php echo htmlspecialchars($partner['link']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo">Logo đối tác</label>
                        <input type="file" name="logo" class="form-control-file" id="logo" accept="image/*">
                        <?php if ($partner['logo']): ?>
                            <img src="/2/admin/uploads/doitac/<?php echo htmlspecialchars($partner['logo']); ?>" alt="Logo" style="max-width: 100px; margin-top: 10px;">
                            <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_visible" class="form-check-input" id="is_visible" <?php echo $partner['is_visible'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_visible">Hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật đối tác</button>
        </form>
    </div>
</div>