<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Hàm xử lý upload ảnh
function handleImageUpload($file, $existingImage = null, $editId = null) {
    $errors = [];
    $uploadDir = 'uploads/logos/';
    $allowed = ['jpg', 'jpeg', 'png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, ['Ảnh logo là bắt buộc']];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $errors[] = 'Ảnh chỉ hỗ trợ JPG, JPEG, PNG';
    } elseif ($file['size'] > $maxSize) {
        $errors[] = 'Ảnh không được lớn hơn 2MB';
    } else {
        $imageName = 'logo_' . ($editId ?: time()) . '.' . $ext;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadPath = $uploadDir . $imageName;
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            if ($existingImage && file_exists($existingImage)) {
                unlink($existingImage);
            }
            return [$uploadPath, []];
        }
        $errors[] = 'Không thể tải lên ảnh';
    }

    return [null, $errors];
}

// Xử lý thêm logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_logo'])) {
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }

    [$image, $imageErrors] = handleImageUpload($_FILES['image'] ?? []);
    $errors = array_merge($errors, $imageErrors);

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO logos (title, image, link, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $image, $link ?: null, $status]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm logo thành công'];
        } catch (Exception $e) {
            error_log('Add logo error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm logo: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Xử lý sửa logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_logo'])) {
    $editId = trim($_POST['edit_id'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];
    if (empty($editId) || !is_numeric($editId)) {
        $errors[] = 'ID logo không hợp lệ';
    }
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }

    $stmt = $pdo->prepare("SELECT image FROM logos WHERE id = ?");
    $stmt->execute([$editId]);
    $currentLogo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$currentLogo) {
        $errors[] = 'Logo không tồn tại';
        $image = null;
    } else {
        $image = $currentLogo['image'];
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        [$image, $imageErrors] = handleImageUpload($_FILES['image'], $currentLogo['image'], $editId);
        $errors = array_merge($errors, $imageErrors);
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE logos SET title = ?, image = ?, link = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $image, $link ?: null, $status, $editId]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật logo thành công'];
        } catch (Exception $e) {
            error_log('Edit logo error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật logo: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Xử lý xóa logo
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image FROM logos WHERE id = ?");
        $stmt->execute([$deleteId]);
        $logo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($logo) {
            if ($logo['image'] && file_exists($logo['image'])) {
                unlink($logo['image']);
            }
            $stmt = $pdo->prepare("DELETE FROM logos WHERE id = ?");
            $stmt->execute([$deleteId]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa logo thành công'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Logo không tồn tại'];
        }
    } catch (Exception $e) {
        error_log('Delete logo error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa logo'];
    }
    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Lấy thông tin logo để sửa
$editLogo = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit']) && !isset($_POST['add_logo']) && !isset($_POST['edit_logo'])) {
    $editId = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM logos WHERE id = ?");
    $stmt->execute([$editId]);
    $editLogo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editLogo) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Logo không tồn tại'];
        echo '<script>window.location.href="?page=logo";</script>';
        exit;
    }
}

// Xác định hiển thị form
$showAddForm = isset($_GET['action']) && $_GET['action'] === 'add';
$showEditForm = $editLogo !== null;

// Lấy danh sách logos
$stmt = $pdo->query("SELECT * FROM logos ORDER BY created_at DESC");
$logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Logo</h1>
    <a href="?page=logo&action=add" class="btn <?php echo ($showAddForm || $showEditForm) ? 'btn-primary' : 'btn-outline-primary'; ?>">
        <i class="fas fa-plus"></i> Thêm Logo
    </a>
</div>

<!-- Form thêm/sửa logo -->
<?php if ($showAddForm || $showEditForm): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $showEditForm ? 'Sửa Logo' : 'Thêm Logo'; ?></h6>
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
            <?php if ($showEditForm): ?>
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($editLogo['id']); ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($editLogo['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Ảnh logo <span class="text-danger"><?php echo $showEditForm ? '' : '*'; ?></span></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept=".jpg,.jpeg,.png" <?php echo $showEditForm ? '' : 'required'; ?>>
                        <?php if ($showEditForm && $editLogo['image'] && file_exists('C:/laragon/www/2/admin/' . $editLogo['image'])): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($editLogo['image']); ?>" alt="Logo" class="mt-2" style="width: 100px; height: 100px; object-fit: contain;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="link">Liên kết</label>
                        <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($editLogo['link'] ?? ''); ?>">
                        <small class="form-text text-muted">VD: http://localhost/2/</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" <?php echo ($editLogo && $editLogo['status']) || !$showEditForm ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="status">Hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="<?php echo $showEditForm ? 'edit_logo' : 'add_logo'; ?>" class="btn btn-primary"><?php echo $showEditForm ? 'Cập nhật' : 'Thêm Logo'; ?></button>
            <a href="?page=logo" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bảng danh sách logos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Logo</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Ảnh</th>
                        <th>Liên kết</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logos as $index => $logo): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($logo['title']); ?></td>
                            <td>
                                <?php if ($logo['image'] && file_exists('C:/laragon/www/2/admin/' . $logo['image'])): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($logo['image']); ?>" alt="Logo" style="width: 50px; height: 50px; object-fit: contain;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($logo['link']): ?>
                                    <a href="<?php echo htmlspecialchars($logo['link']); ?>" target="_blank">Xem</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $logo['status'] ? '<span class="badge badge-success">Hiển thị</span>' : '<span class="badge badge-secondary">Ẩn</span>'; ?>
                            </td>
                            <td>
                                <a href="?page=logo&edit=<?php echo $logo['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Sửa</a>
                                <a href="?page=logo&delete=<?php echo $logo['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fas fa-trash"></i> Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- 
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<?php ob_end_flush(); ?>