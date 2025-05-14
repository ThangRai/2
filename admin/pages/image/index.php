<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra session và quyền truy cập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập.'];
    echo '<script>window.location.href="index.php?page=login";</script>';
    exit;
}

$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2, 3, 4]; // super_admin (1), staff (2), admin (4)
if (!$admin || !isset($admin['role_id']) || !in_array($admin['role_id'], $allowed_roles)) {
    logActivity($pdo, $_SESSION['admin_id'] ?? 0, $admin['role_id'] ?? 0, 'Truy cập bị từ chối', 'question', null, 'Admin ID: ' . ($_SESSION['admin_id'] ?? 'không có') . ', Role ID: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

// Xử lý thêm/sửa/xóa/ẩn hiện ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';
    $admin_id = $_SESSION['admin_id'];
    $role_id = $admin['role_id'];

    if ($action === 'add') {
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Tiêu đề là bắt buộc.']);
            exit;
        }

        // Xử lý upload nhiều ảnh
        $upload_dir = 'C:/laragon/www/2/admin/uploads/image/';
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $filenames = [];

        if (!empty($_FILES['file']['name'])) {
            $name = $_FILES['file']['name'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $size = $_FILES['file']['size'];
            $error = $_FILES['file']['error'];

            if ($error === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => "Ảnh $name: Chỉ hỗ trợ định dạng JPG, PNG, GIF."]);
                    exit;
                }
                if ($size > $max_size) {
                    echo json_encode(['success' => false, 'message' => "Ảnh $name: Kích thước tối đa 2MB."]);
                    exit;
                }
                $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '', basename($name));
                $target_path = $upload_dir . $filename;
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $filenames[] = $filename;
                    // Lưu tạm vào session để gộp sau
                    $_SESSION['temp_filenames'] = isset($_SESSION['temp_filenames']) ? array_merge($_SESSION['temp_filenames'], [$filename]) : [$filename];
                    echo json_encode(['success' => true, 'message' => "Thêm ảnh $name thành công."]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Lỗi khi upload ảnh $name."]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Lỗi upload ảnh $name."]);
            }
            exit;
        } elseif (!empty($_POST['save']) && !empty($_SESSION['temp_filenames'])) {
            // Lưu bản ghi khi nhấn "Lưu"
            $filenames = $_SESSION['temp_filenames'];
            $stmt = $pdo->prepare("INSERT INTO images (admin_id, role_id, title, description, filenames, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$admin_id, $role_id, $title, $description, json_encode($filenames), $status]);
            // Ghi log
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->execute([$admin_id, $role_id, 'Thêm', 'image', $pdo->lastInsertId(), "Thêm " . count($filenames) . " ảnh", $_SERVER['REMOTE_ADDR']]);
            unset($_SESSION['temp_filenames']);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm ' . count($filenames) . ' ảnh thành công.'];
            echo '<script>window.location.href="index.php?page=image";</script>';
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn ít nhất một ảnh.']);
            exit;
        }
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Tiêu đề là bắt buộc.']);
            exit;
        }
        // Lấy danh sách ảnh hiện tại
        $stmt = $pdo->prepare("SELECT filenames FROM images WHERE id = ? AND admin_id = ?");
        $stmt->execute([$id, $admin_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        $filenames = json_decode($image['filenames'], true) ?: [];

        // Xử lý xóa ảnh
        if (!empty($_POST['delete_images'])) {
            $delete_images = $_POST['delete_images'];
            foreach ($delete_images as $filename) {
                if (in_array($filename, $filenames)) {
                    $file_path = $upload_dir . $filename;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                    $filenames = array_diff($filenames, [$filename]);
                }
            }
        }

        // Xử lý ảnh mới
        $upload_dir = 'C:/laragon/www/2/admin/uploads/image/';
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024;
        if (!empty($_FILES['file']['name'])) {
            $name = $_FILES['file']['name'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $size = $_FILES['file']['size'];
            $error = $_FILES['file']['error'];

            if ($error === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => "Ảnh $name: Chỉ hỗ trợ định dạng JPG, PNG, GIF."]);
                    exit;
                }
                if ($size > $max_size) {
                    echo json_encode(['success' => false, 'message' => "Ảnh $name: Kích thước tối đa 2MB."]);
                    exit;
                }
                $filename = uniqid() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '', basename($name));
                $target_path = $upload_dir . $filename;
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $filenames[] = $filename;
                    $_SESSION['temp_filenames'] = isset($_SESSION['temp_filenames']) ? array_merge($_SESSION['temp_filenames'], [$filename]) : [$filename];
                    echo json_encode(['success' => true, 'message' => "Thêm ảnh $name thành công."]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Lỗi khi upload ảnh $name."]);
                }
            }
            exit;
        } elseif (!empty($_POST['save']) && (!empty($_SESSION['temp_filenames']) || !empty($filenames))) {
            // Lưu bản ghi khi nhấn "Lưu"
            if (!empty($_SESSION['temp_filenames'])) {
                $filenames = array_merge($filenames, $_SESSION['temp_filenames']);
                unset($_SESSION['temp_filenames']);
            }
            $stmt = $pdo->prepare("UPDATE images SET title = ?, description = ?, filenames = ?, status = ?, updated_at = NOW() WHERE id = ? AND admin_id = ?");
            $stmt->execute([$title, $description, json_encode($filenames), $status, $id, $admin_id]);
            // Ghi log
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->execute([$admin_id, $role_id, 'Sửa', 'image', $id, "Sửa bản ghi ID: $id, " . count($filenames) . " ảnh", $_SERVER['REMOTE_ADDR']]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật bản ghi thành công.'];
            echo '<script>window.location.href="index.php?page=image";</script>';
            exit;
        }
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT filenames FROM images WHERE id = ? AND admin_id = ?");
        $stmt->execute([$id, $admin_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($image) {
            $filenames = json_decode($image['filenames'], true) ?: [];
            foreach ($filenames as $filename) {
                $file_path = 'C:/laragon/www/2/admin/uploads/image/' . $filename;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $stmt = $pdo->prepare("DELETE FROM images WHERE id = ? AND admin_id = ?");
            $stmt->execute([$id, $admin_id]);
            // Ghi log
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->execute([$admin_id, $role_id, 'Xóa', 'image', $id, "Xóa bản ghi: " . count($filenames) . " ảnh", $_SERVER['REMOTE_ADDR']]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa bản ghi thành công.'];
        }
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT status FROM images WHERE id = ? AND admin_id = ?");
        $stmt->execute([$id, $admin_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($image) {
            $new_status = $image['status'] === 'active' ? 'inactive' : 'active';
            $stmt = $pdo->prepare("UPDATE images SET status = ?, updated_at = NOW() WHERE id = ? AND admin_id = ?");
            $stmt->execute([$new_status, $id, $admin_id]);
            // Ghi log
            $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->execute([$admin_id, $role_id, 'Ẩn/Hiện', 'image', $id, "Chuyển trạng thái bản ghi ID: $id sang $new_status", $_SERVER['REMOTE_ADDR']]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật trạng thái thành công.'];
        }
    }
    echo '<script>window.location.href="index.php?page=image";</script>';
    exit;
}

// Xử lý tìm kiếm và bộ lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Xử lý phân trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Tạo truy vấn SQL
$sql = "SELECT i.*, a.name, r.name as role_name
        FROM images i
        JOIN admins a ON i.admin_id = a.id
        LEFT JOIN roles r ON i.role_id = r.id
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (LOWER(i.title) LIKE LOWER(?) OR LOWER(i.description) LIKE LOWER(?))";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($role_id) {
    $sql .= " AND i.role_id = ?";
    $params[] = $role_id;
}

if ($status) {
    $sql .= " AND i.status = ?";
    $params[] = $status;
}

// Đếm tổng số bản ghi
$count_sql = "SELECT COUNT(*) as total FROM images i
              JOIN admins a ON i.admin_id = a.id
              LEFT JOIN roles r ON i.role_id = r.id
              WHERE 1=1";
$count_params = [];

if ($search) {
    $count_sql .= " AND (LOWER(i.title) LIKE LOWER(?) OR LOWER(i.description) LIKE LOWER(?))";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
}

if ($role_id) {
    $count_sql .= " AND i.role_id = ?";
    $count_params[] = $role_id;
}

if ($status) {
    $count_sql .= " AND i.status = ?";
    $count_params[] = $status;
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($count_params);
$total_images = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_images / $per_page);

// Lấy danh sách ảnh
$sql .= " ORDER BY i.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
foreach ($params as $index => $param) {
    $stmt->bindValue($index + 1, $param);
}
$stmt->bindValue(count($params) + 1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách vai trò
$role_stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $role_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thư viện ảnh</title>
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
        .filter-form, .upload-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .filter-form .form-control,
        .filter-form .btn,
        .upload-form .form-control,
        .upload-form .btn {
            border-radius: 25px;
            height: 38px;
        }
        .btn-action {
            background: linear-gradient(45deg, #007bff, #00aaff);
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-action:hover {
            background: linear-gradient(45deg, #0056b3, #0088cc);
        }
        .btn-reset {
            background: linear-gradient(45deg, #6c757d, #8a959f);
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-reset:hover {
            background: linear-gradient(45deg, #5a6268, #787f87);
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .page-link {
            border-radius: 50%;
            margin: 0 5px;
            color: #007bff;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-link:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .page-item.active .page-link {
            background: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
        }
        .thumbnail {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 20px;
            min-height: 200px;
        }
        .dropzone .dz-message {
            text-align: center;
            margin: 2em 0;
        }
        .image-preview {
            margin-top: 20px;
        }
        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 5px;
            border-radius: 4px;
        }
        .image-preview .delete-btn {
            position: relative;
            top: -30px;
            right: 15px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            line-height: 25px;
            text-align: center;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .table {
                font-size: 0.85em;
            }
            .filter-form .form-group, .upload-form .form-group {
                margin-bottom: 15px;
            }
            .filter-form .btn, .upload-form .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            .filter-form .col-md-4, .filter-form .col-md-2,
            .upload-form .col-md-4, .upload-form .col-md-2 {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
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

    <!-- <div class="container-fluid"> -->
        <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
            <?php
            $edit_mode = $_GET['action'] === 'edit' && isset($_GET['id']);
            $edit_id = $edit_mode ? (int)$_GET['id'] : 0;
            $edit_data = ['title' => '', 'description' => '', 'status' => 'active', 'filenames' => []];
            if ($edit_mode) {
                $stmt = $pdo->prepare("SELECT * FROM images WHERE id = ? AND admin_id = ?");
                $stmt->execute([$edit_id, $admin_id]);
                $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $edit_data['filenames'] = json_decode($edit_data['filenames'], true) ?: [];
            }
            ?>
            <!-- Form thêm/sửa ảnh với Dropzone -->
            <h1 class="h3 mb-4 text-gray-800"><?php echo $edit_mode ? 'Sửa bản ghi ảnh' : 'Thêm ảnh mới'; ?></h1>
            <div class="upload-form">
                <form action="" method="POST" class="dropzone" id="imageUpload" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="title">Tiêu đề</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($edit_data['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" <?php echo $edit_data['status'] === 'active' ? 'selected' : ''; ?>>Hiển thị</option>
                            <option value="inactive" <?php echo $edit_data['status'] === 'inactive' ? 'selected' : ''; ?>>Ẩn</option>
                        </select>
                    </div>
                    <?php if ($edit_mode && !empty($edit_data['filenames'])): ?>
                        <div class="form-group">
                            <label>Ảnh hiện tại</label>
                            <div class="image-preview">
                                <?php foreach ($edit_data['filenames'] as $filename): ?>
                                    <div style="display: inline-block; position: relative;">
                                        <img src="/2/admin/uploads/image/<?php echo htmlspecialchars($filename); ?>" alt="Image">
                                        <button type="button" class="delete-btn" onclick="deleteImage('<?php echo htmlspecialchars($filename); ?>', this)">×</button>
                                        <input type="hidden" name="delete_images[]" class="delete-image-input" value="">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Khu vực upload ảnh (JPG, PNG, GIF, tối đa 2MB)</label>
                        <div class="dz-message">Kéo thả ảnh vào đây hoặc nhấn để chọn ảnh</div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-sm-12">
                            <button type="button" id="submitBtn" class="btn btn-action">Lưu</button>
                            <input type="hidden" name="save" value="1">
                        </div>
                        <div class="col-md-6 col-sm-12 text-md-right">
                            <a href="index.php?page=image" class="btn btn-reset">Hủy</a>
                        </div>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Trang danh sách ảnh -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Thư viện ảnh</h1>
                <a href="index.php?page=image&action=add" class="btn btn-action"><i class="fas fa-plus"></i> Thêm ảnh</a>
            </div>

            <!-- Form tìm kiếm và bộ lọc -->
            <div class="filter-form">
                <form method="GET" action="">
                    <input type="hidden" name="page" value="image">
                    <div class="row">
                        <div class="col-md-4 col-sm-12 form-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tiêu đề, mô tả..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2 col-sm-6 form-group">
                            <select name="role_id" class="form-control">
                                <option value="0">Tất cả vai trò</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo $role_id == $role['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 form-group">
                            <select name="status" class="form-control">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Hiển thị</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-sm-12">
                            <button type="submit" class="btn btn-action"><i class="fas fa-search"></i> Tìm kiếm</button>
                        </div>
                        <div class="col-md-6 col-sm-12 text-md-right">
                            <a href="index.php?page=image" class="btn btn-reset"><i class="fas fa-sync-alt"></i> Đặt lại</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bảng danh sách ảnh -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách ảnh</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Ảnh</th>
                                    <th>Tiêu đề</th>
                                    <th>Mô tả</th>
                                    <th>Nhân viên</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($images)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Không tìm thấy bản ghi nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($images as $index => $image): ?>
                                        <?php $filenames = json_decode($image['filenames'], true) ?: []; ?>
                                        <tr>
                                            <td><?php echo $offset + $index + 1; ?></td>
                                            <td>
                                                <?php if (!empty($filenames)): ?>
                                                    <img src="/2/admin/uploads/image/<?php echo htmlspecialchars($filenames[0]); ?>" class="thumbnail" alt="Thumbnail">
                                                <?php else: ?>
                                                    Không có ảnh
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($image['title'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($image['description'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($image['name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($image['role_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge <?php echo $image['status'] === 'active' ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo $image['status'] === 'active' ? 'Hiển thị' : 'Ẩn'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($image['created_at'])); ?></td>
                                            <td>
                                                <a href="index.php?page=image&action=edit&id=<?php echo $image['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                                <form action="" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bản ghi này?');"><i class="fas fa-trash"></i></button>
                                                </form>
                                                <form action="" method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-eye<?php echo $image['status'] === 'active' ? '-slash' : ''; ?>"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=image&p=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&status=<?php echo urlencode($status); ?>" aria-label="Previous">
                                        <span aria-hidden="true">«</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=image&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=image&p=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&status=<?php echo urlencode($status); ?>" aria-label="Next">
                                        <span aria-hidden="true">»</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script>
        <?php if (isset($_GET['action']) && ($_GET['action'] === 'add' || $_GET['action'] === 'edit')): ?>
            // Cấu hình Dropzone
            Dropzone.options.imageUpload = {
                url: window.location.href,
                paramName: "file",
                maxFilesize: 2, // MB
                acceptedFiles: "image/jpeg,image/png,image/gif",
                addRemoveLinks: true,
                dictDefaultMessage: "Kéo thả ảnh vào đây hoặc nhấn để chọn ảnh",
                dictRemoveFile: "Xóa",
                dictFileTooBig: "Ảnh quá lớn ({{filesize}}MB). Tối đa: {{maxFilesize}}MB.",
                dictInvalidFileType: "Chỉ hỗ trợ định dạng JPG, PNG, GIF.",
                autoProcessQueue: false,
                parallelUploads: 20,
                init: function() {
                    var myDropzone = this;
                    // Gửi thêm các trường title, description, status
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("title", $("#title").val());
                        formData.append("description", $("#description").val());
                        formData.append("status", $("#status").val());
                        formData.append("action", "<?php echo $edit_mode ? 'edit' : 'add'; ?>");
                        <?php if ($edit_mode): ?>
                            formData.append("id", "<?php echo $edit_id; ?>");
                        <?php endif; ?>
                    });
                    // Xử lý phản hồi từ server
                    this.on("success", function(file, response) {
                        response = JSON.parse(response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                html: response.message,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                html: response.message,
                                confirmButtonText: 'OK'
                            });
                            myDropzone.removeFile(file);
                        }
                    });
                    this.on("error", function(file, errorMessage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });
                        myDropzone.removeFile(file);
                    });
                    // Nút Lưu để gửi tất cả ảnh
                    $("#submitBtn").click(function() {
                        if (!$("#title").val()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                html: 'Tiêu đề là bắt buộc.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                        if (myDropzone.getQueuedFiles().length === 0 && <?php echo $edit_mode ? '!' . json_encode($edit_data['filenames']) : 'true'; ?>) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                html: 'Vui lòng chọn ít nhất một ảnh.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }
                        if (myDropzone.getQueuedFiles().length > 0) {
                            myDropzone.processQueue();
                        } else {
                            // Gửi form để lưu mà không upload ảnh mới
                            var formData = new FormData(document.querySelector('#imageUpload'));
                            formData.append('save', '1');
                            $.ajax({
                                url: window.location.href,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function() {
                                    window.location.href = "index.php?page=image";
                                }
                            });
                        }
                    });
                    // Chuyển hướng sau khi upload xong
                    this.on("queuecomplete", function() {
                        var formData = new FormData(document.querySelector('#imageUpload'));
                        formData.append('save', '1');
                        $.ajax({
                            url: window.location.href,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function() {
                                window.location.href = "index.php?page=image";
                            }
                        });
                    });
                }
            };

            // Xử lý xóa ảnh hiện tại
            function deleteImage(filename, button) {
                Swal.fire({
                    title: 'Bạn có chắc?',
                    text: 'Ảnh này sẽ bị xóa và không thể khôi phục!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(button).siblings('.delete-image-input').val(filename);
                        $(button).parent().hide();
                    }
                });
            }
        <?php endif; ?>
    </script>
</body>
</html>
<?php ob_end_flush(); ?>