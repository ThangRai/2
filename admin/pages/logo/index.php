<?php
ob_start(); // Bắt đầu output buffering

require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Xử lý thêm logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_logo'])) {
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];

    // Validate
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Ảnh logo là bắt buộc';
    }

    // Xử lý ảnh
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Ảnh chỉ hỗ trợ JPG, JPEG, PNG';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ảnh không được lớn hơn 2MB';
        } else {
            $image_name = 'logo_' . time() . '.' . $ext;
            $upload_dir = 'uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_path = $upload_dir . $image_name;
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $errors[] = 'Không thể tải lên ảnh';
            } else {
                $image = $upload_path;
            }
        }
    }

    // Lưu message và thêm logo
    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO logos (title, image, link, status)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $image, $link ?: null, $status]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm logo thành công'];
        } catch (Exception $e) {
            error_log('Add logo error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm logo: ' . $e->getMessage()];
        }
    }

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Xử lý sửa logo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_logo'])) {
    $edit_id = trim($_POST['edit_id'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];

    // Validate
    if (empty($edit_id) || !is_numeric($edit_id)) {
        $errors[] = 'ID logo không hợp lệ';
    }
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }

    // Lấy thông tin logo hiện tại
    $stmt = $pdo->prepare("SELECT image FROM logos WHERE id = ?");
    $stmt->execute([$edit_id]);
    $current_logo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$current_logo) {
        $errors[] = 'Logo không tồn tại';
    } else {
        $image = $current_logo['image'];
    }

    // Xử lý ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Ảnh chỉ hỗ trợ JPG, JPEG, PNG';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ảnh không được lớn hơn 2MB';
        } else {
            $image_name = 'logo_' . $edit_id . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/logos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_path = $upload_dir . $image_name;
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                if ($image && file_exists($image)) {
                    unlink($image);
                }
                $image = $upload_path;
            } else {
                $errors[] = 'Không thể tải lên ảnh';
            }
        }
    }

    // Lưu message và sửa logo
    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE logos
                SET title = ?, image = ?, link = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $image, $link ?: null, $status, $edit_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật logo thành công'];
        } catch (Exception $e) {
            error_log('Edit logo error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật logo: ' . $e->getMessage()];
        }
    }

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Xử lý xóa logo
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image FROM logos WHERE id = ?");
        $stmt->execute([$delete_id]);
        $logo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($logo) {
            if ($logo['image'] && file_exists($logo['image'])) {
                unlink($logo['image']);
            }
            $stmt = $pdo->prepare("DELETE FROM logos WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa logo thành công'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Logo không tồn tại'];
        }
    } catch (Exception $e) {
        error_log('Delete logo error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa logo'];
    }
    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=logo";</script>';
    exit;
}

// Lấy thông tin logo để sửa
$edit_logo = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit']) && !isset($_POST['add_logo']) && !isset($_POST['edit_logo'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM logos WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_logo = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$edit_logo) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Logo không tồn tại'];
        echo '<script>window.location.href="?page=logo";</script>';
        exit;
    }
}

// Lấy danh sách logos
$stmt = $pdo->query("SELECT * FROM logos ORDER BY created_at DESC");
$logos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Logo</h1>
</div>

<!-- Form thêm/sửa logo -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_logo ? 'Sửa Logo' : 'Thêm Logo'; ?></h6>
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
            <?php if ($edit_logo): ?>
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_logo['id']); ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($edit_logo['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Ảnh logo <span class="text-danger"><?php echo $edit_logo ? '' : '*'; ?></span></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept=".jpg,.jpeg,.png" <?php echo $edit_logo ? '' : 'required'; ?>>
                        <?php if ($edit_logo && $edit_logo['image'] && file_exists('C:/laragon/www/2/admin/' . $edit_logo['image'])): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($edit_logo['image']); ?>" alt="Logo" class="mt-2" style="width: 100px; height: 100px; object-fit: contain;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="link">Liên kết</label>
                        <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($edit_logo['link'] ?? ''); ?>">
                        <small class="form-text text-muted">VD: http://localhost/2/</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" <?php echo ($edit_logo && $edit_logo['status']) || !$edit_logo ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="status">Hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="<?php echo $edit_logo ? 'edit_logo' : 'add_logo'; ?>" class="btn btn-primary"><?php echo $edit_logo ? 'Cập nhật' : 'Thêm Logo'; ?></button>
            <?php if ($edit_logo): ?>
                <a href="?page=logo" class="btn btn-secondary">Hủy</a>
            <?php endif; ?>
        </form>
    </div>
</div>

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
                                <a href="?page=logo&edit=<?php echo $logo['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="?page=logo&delete=<?php echo $logo['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>