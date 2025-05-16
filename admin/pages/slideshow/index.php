<?php
ob_start(); // Bắt đầu output buffering

require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Xử lý thêm slide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];

    // Validate
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Ảnh slide là bắt buộc';
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
            $image_name = 'slide_' . time() . '.' . $ext;
            $upload_dir = 'uploads/slides/';
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

    // Lưu message và thêm slide
    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO slides (title, image, description, link, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $image, $description ?: null, $link ?: null, $status]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm slide thành công'];
        } catch (Exception $e) {
            error_log('Add slide error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm slide: ' . $e->getMessage()];
        }
    }

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=slideshow";</script>';
    exit;
}

// Xử lý sửa slide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_slide'])) {
    $edit_id = trim($_POST['edit_id'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    $errors = [];

    // Validate
    if (empty($edit_id) || !is_numeric($edit_id)) {
        $errors[] = 'ID slide không hợp lệ';
    }
    if (empty($title)) {
        $errors[] = 'Tiêu đề không được để trống';
    }

    // Lấy thông tin slide hiện tại
    $stmt = $pdo->prepare("SELECT image FROM slides WHERE id = ?");
    $stmt->execute([$edit_id]);
    $current_slide = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$current_slide) {
        $errors[] = 'Slide không tồn tại';
    } else {
        $image = $current_slide['image'];
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
            $image_name = 'slide_' . $edit_id . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/slides/';
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

    // Lưu message và sửa slide
    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE slides
                SET title = ?, image = ?, description = ?, link = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $image, $description ?: null, $link ?: null, $status, $edit_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật slide thành công'];
        } catch (Exception $e) {
            error_log('Edit slide error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật slide: ' . $e->getMessage()];
        }
    }

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=slideshow";</script>';
    exit;
}

// Xử lý xóa slide
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image FROM slides WHERE id = ?");
        $stmt->execute([$delete_id]);
        $slide = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($slide) {
            if ($slide['image'] && file_exists($slide['image'])) {
                unlink($slide['image']);
            }
            $stmt = $pdo->prepare("DELETE FROM slides WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa slide thành công'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Slide không tồn tại'];
        }
    } catch (Exception $e) {
        error_log('Delete slide error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa slide'];
    }
    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=slideshow";</script>';
    exit;
}

// Lấy thông tin slide để sửa hoặc thêm
$edit_slide = null;
if (isset($_GET['action']) && $_GET['action'] === 'add' && !isset($_POST['add_slide']) && !isset($_POST['edit_slide'])) {
    $edit_slide = false; // Thêm mới
} elseif (isset($_GET['edit']) && is_numeric($_GET['edit']) && !isset($_POST['add_slide']) && !isset($_POST['edit_slide'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM slides WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_slide = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$edit_slide) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Slide không tồn tại'];
        echo '<script>window.location.href="?page=slideshow";</script>';
        exit;
    }
}

// Lấy danh sách slides
$stmt = $pdo->query("SELECT * FROM slides ORDER BY created_at DESC");
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Slideshow</h1>
    <a href="?page=slideshow&action=add" class="btn btn-primary">Thêm Slide</a>
</div>

<!-- Form thêm/sửa slide -->
<?php if ($edit_slide !== null): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_slide ? 'Sửa Slide' : 'Thêm Slide'; ?></h6>
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
            <?php if ($edit_slide): ?>
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_slide['id']); ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($edit_slide['title'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Ảnh slide <span class="text-danger"><?php echo $edit_slide ? '' : '*'; ?></span></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept=".jpg,.jpeg,.png" <?php echo $edit_slide ? '' : 'required'; ?>>
                        <?php if ($edit_slide && $edit_slide['image'] && file_exists('C:/laragon/www/2/admin/' . $edit_slide['image'])): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($edit_slide['image']); ?>" alt="Slide" class="mt-2" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_slide['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="link">Liên kết</label>
                        <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($edit_slide['link'] ?? ''); ?>">
                        <small class="form-text text-muted">VD: http://localhost/2/product/123</small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" <?php echo ($edit_slide && $edit_slide['status']) || !$edit_slide ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="status">Hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" name="<?php echo $edit_slide ? 'edit_slide' : 'add_slide'; ?>" class="btn btn-primary"><?php echo $edit_slide ? 'Cập nhật' : 'Thêm Slide'; ?></button>
            <a href="?page=slideshow" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Bảng danh sách slides -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Slide</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Ảnh</th>
                        <th>Mô tả</th>
                        <th>Liên kết</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slides as $index => $slide): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($slide['title']); ?></td>
                            <td>
                                <?php if ($slide['image'] && file_exists('C:/laragon/www/2/admin/' . $slide['image'])): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($slide['image']); ?>" alt="Slide" style="width: 200px;height: auto;object-fit: cover;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($slide['description'] ?? '-'); ?></td>
                            <td>
                                <?php if ($slide['link']): ?>
                                    <a href="<?php echo htmlspecialchars($slide['link']); ?>" target="_blank">Xem</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $slide['status'] ? '<span class="badge badge-success">Hiển thị</span>' : '<span class="badge badge-secondary">Ẩn</span>'; ?>
                            </td>
                            <td>
                                <a href="?page=slideshow&edit=<?php echo $slide['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Sửa</a>
                                <a href="?page=slideshow&delete=<?php echo $slide['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fas fa-trash"></i> Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>