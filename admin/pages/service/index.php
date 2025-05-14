<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Hàm tạo slug
function createSlug($string, $pdo) {
    $search = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ',
        'đ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
        'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ẽ',
        'Ì','Í','Ị','Ỉ','Ĩ',
        'Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
        'Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
        'Ỳ','Ý','Ỵ','Ỷ','Ỹ',
        'Đ'
    ];
    $replace = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
        'E','E','E','E','E','E','E','E','E','E','E',
        'I','I','I','I','I',
        'O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
        'U','U','U','U','U','U','U','U','U','U','U',
        'Y','Y','Y','Y','Y',
        'D'
    ];
    $string = str_replace($search, $replace, $string);
    $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    $string = strtolower(trim(preg_replace('/\s+/', '-', $string), '-'));
    $baseSlug = $string;
    $counter = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE slug = ?");
        $stmt->execute([$string]);
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        $string = $baseSlug . '-' . $counter++;
    }
    return $string;
}

// Hàm ghi log hoạt động (tái sử dụng từ activity_logs.php)
function logActivity($pdo, $admin_id, $role_id, $action, $page, $target_id = null, $details = null) {
    $role_id = $role_id ?? 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$admin_id, $role_id, $action, $page, $target_id, $details, $ip_address]);
    } catch (Exception $e) {
        error_log("Lỗi ghi log vào activity_logs: " . $e->getMessage());
    }
}

// Kiểm tra session
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
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

// Xử lý xóa dịch vụ
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT thumbnail FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        try {
            // Xóa ảnh đại diện nếu tồn tại
            if ($service['thumbnail'] && file_exists("C:/laragon/www/2/admin/" . $service['thumbnail'])) {
                unlink("C:/laragon/www/2/admin/" . $service['thumbnail']);
            }
            // Xóa dịch vụ
            $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $stmt->execute([$id]);
            logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Xóa dịch vụ', 'service', $id, 'Dịch vụ ID: ' . $id);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa dịch vụ thành công.'];
        } catch (Exception $e) {
            error_log('Delete service error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa dịch vụ: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Dịch vụ không tồn tại.'];
    }
    echo '<script>window.location.href="?page=service";</script>';
    exit;
}

// Xử lý thêm/sửa dịch vụ
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$service = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$service) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Dịch vụ không tồn tại.'];
        echo '<script>window.location.href="?page=service";</script>';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['noidung'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    // Kiểm tra lỗi
    $errors = [];
    if (empty($title)) $errors[] = 'Tên dịch vụ không được để trống.';
    if (empty($description)) $errors[] = 'Mô tả không được để trống.';
    if (!empty($_FILES['thumbnail']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (!in_array($_FILES['thumbnail']['type'], $allowed_types)) {
            $errors[] = 'Ảnh đại diện phải là định dạng JPG hoặc PNG.';
        }
        if ($_FILES['thumbnail']['size'] > $max_size) {
            $errors[] = 'Ảnh đại diện không được lớn hơn 2MB.';
        }
    }
    if (!empty($seo_title) && strlen($seo_title) > 255) {
        $errors[] = 'Tiêu đề SEO không được vượt quá 255 ký tự.';
    }
    if (!empty($seo_description) && strlen($seo_description) > 160) {
        $errors[] = 'Mô tả SEO không được vượt quá 160 ký tự.';
    }

    if (empty($errors)) {
        try {
            // Xử lý ảnh đại diện
            $thumbnail = $service['thumbnail'] ?? null;
            if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "Uploads/thumbnails/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $thumbnail = $target_dir . time() . '_' . basename($_FILES['thumbnail']['name']);
                if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail)) {
                    throw new Exception('Lỗi khi tải lên ảnh đại diện.');
                }
                error_log('Thumbnail uploaded: ' . $thumbnail);
            }

            // Chuẩn bị và thực thi SQL
            if ($action === 'edit' && isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, content = ?, thumbnail = ?, is_published = ?, seo_title = ?, seo_description = ?, seo_keywords = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords, $id]);
                logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Sửa dịch vụ', 'service', $id, 'Dịch vụ ID: ' . $id . ', Tên: ' . $title);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật dịch vụ thành công.'];
            } else {
                $slug = createSlug($title, $pdo);
                $stmt = $pdo->prepare("INSERT INTO services (title, slug, description, content, thumbnail, is_published, seo_title, seo_description, seo_keywords, created_at) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $slug, $description, $content, $thumbnail, $is_published, $seo_title, $seo_description, $seo_keywords]);
                $new_id = $pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Thêm dịch vụ', 'service', $new_id, 'Dịch vụ ID: ' . $new_id . ', Tên: ' . $title);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm dịch vụ thành công.'];
            }

            echo '<script>window.location.href="?page=service";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Service save error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lưu dịch vụ: ' . $e->getMessage()];
        }
    } else {
        error_log('Validation errors: ' . implode(', ', $errors));
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}

// Xử lý danh sách dịch vụ
if ($action === 'list') {
    $search = isset($_GET['s']) ? trim($_GET['s']) : '';
    $filter_status = isset($_GET['status']) ? $_GET['status'] : ''; // Bộ lọc trạng thái
    $filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : ''; // Ngày bắt đầu
    $filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : ''; // Ngày kết thúc
    $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    // Xây dựng truy vấn danh sách
    $sql = "SELECT * FROM services WHERE 1=1";
    $count_sql = "SELECT COUNT(*) as total FROM services WHERE 1=1";
    $params = [];

    // Thêm điều kiện tìm kiếm
    if ($search) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $count_sql .= " AND (title LIKE ? OR description LIKE ?)";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
    }

    // Thêm bộ lọc trạng thái
    if ($filter_status !== '' && in_array($filter_status, ['0', '1'])) {
        $sql .= " AND is_published = ?";
        $count_sql .= " AND is_published = ?";
        $params[] = $filter_status;
    }

    // Thêm bộ lọc ngày
    if ($filter_date_from) {
        $sql .= " AND created_at >= ?";
        $count_sql .= " AND created_at >= ?";
        $params[] = $filter_date_from . ' 00:00:00';
    }
    if ($filter_date_to) {
        $sql .= " AND created_at <= ?";
        $count_sql .= " AND created_at <= ?";
        $params[] = $filter_date_to . ' 23:59:59';
    }

    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;

    // Đếm tổng số bản ghi
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute(array_slice($params, 0, count($params) - 2));
    $total_services = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_services / $per_page);

    // Lấy danh sách dịch vụ
    $stmt = $pdo->prepare($sql);
    foreach ($params as $index => $param) {
        $stmt->bindValue($index + 1, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ</title>
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <?php endif; ?>
    <style>
        .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .btn-secondary { background: #6c757d; border: none; }
        .ck-editor__editable { min-height: 300px; }
        .table-responsive { margin-top: 20px; }
        .pagination { justify-content: center; margin-top: 20px; }
        .page-item.disabled .page-link { pointer-events: none; opacity: 0.6; }
        @media (max-width: 768px) {
            .table { font-size: 0.9em; }
            .form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }
            .btn { font-size: 0.9em; }
            .pagination { font-size: 0.9em; }
        }
    </style>
</head>
<body>
    <!-- Hiển thị thông báo -->
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

    <?php if ($action === 'list'): ?>
        <!-- Danh sách dịch vụ -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Danh sách dịch vụ</h1>
            <a href="?page=service&action=add" class="btn btn-primary">Thêm dịch vụ</a>
        </div>

        <!-- Form tìm kiếm -->
        <div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="?page=service" class="form-inline flex-wrap">
            <input type="hidden" name="page" value="service">
            <div class="form-group mb-2 mr-2">
                <input type="text" class="form-control" name="s" placeholder="Tìm theo tên dịch vụ hoặc mô tả..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group mb-2 mr-2">
                <select class="form-control" name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?php echo $filter_status === '1' ? 'selected' : ''; ?>>Hiển thị</option>
                    <option value="0" <?php echo $filter_status === '0' ? 'selected' : ''; ?>>Ẩn</option>
                </select>
            </div>
            <div class="form-group mb-2 mr-2">
                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($filter_date_from); ?>" placeholder="Từ ngày">
            </div>
            <div class="form-group mb-2 mr-2">
                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($filter_date_to); ?>" placeholder="Đến ngày">
            </div>
            <div class="form-group mb-2">
                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
                <a href="?page=service" class="btn btn-secondary ml-2"><i class="fas fa-undo"></i> Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách dịch vụ</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên dịch vụ</th>
                                <th>Mô tả</th>
                                <th>Ảnh đại diện</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr><td colspan="7" class="text-center">Không tìm thấy dịch vụ nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($services as $index => $svc): ?>
                                    <tr>
                                        <td><?php echo $index + 1 + $offset; ?></td>
                                        <td><?php echo htmlspecialchars($svc['title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($svc['description'], 0, 100)) . (strlen($svc['description']) > 100 ? '...' : ''); ?></td>
                                        <td>
                                            <?php if ($svc['thumbnail']): ?>
                                                <img src="/2/admin/<?php echo $svc['thumbnail']; ?>" width="50" alt="Thumbnail">
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $svc['is_published'] ? 'Hiển thị' : 'Ẩn'; ?></td>
                                        <td><?php echo $svc['created_at']; ?></td>
                                        <td>
                                            <a href="?page=service&action=edit&id=<?php echo $svc['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                            <a href="#" class="btn btn-sm btn-danger delete-service" data-id="<?php echo $svc['id']; ?>" data-title="<?php echo htmlspecialchars($svc['title']); ?>"><i class="fas fa-trash"></i> Xóa</a>
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
                                <a class="page-link" href="?page=service&s=<?php echo urlencode($search); ?>&p=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=service&s=<?php echo urlencode($search); ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=service&s=<?php echo urlencode($search); ?>&p=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Form thêm/sửa dịch vụ -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $action === 'edit' ? 'Sửa dịch vụ' : 'Thêm dịch vụ'; ?></h1>
            <a href="?page=service" class="btn btn-secondary">Hủy</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $action === 'edit' ? 'Sửa dịch vụ' : 'Thêm dịch vụ'; ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Tên dịch vụ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($service) ? htmlspecialchars($service['title']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Mô tả <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo isset($service) ? htmlspecialchars($service['description']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="noidung">Nội dung</label>
                                <textarea class="form-control" id="noidung" name="noidung"><?php echo isset($service) ? htmlspecialchars($service['content']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="thumbnail">Ảnh đại diện</label>
                                <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png">
                                <?php if (isset($service) && $service['thumbnail']): ?>
                                    <img src="/2/admin/<?php echo $service['thumbnail']; ?>" width="100" alt="Thumbnail" class="mt-2">
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" <?php echo (isset($service) && $service['is_published']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_published">Hiển thị</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seo_title">Tiêu đề SEO (tối đa 255 ký tự)</label>
                                <input type="text" class="form-control" id="seo_title" name="seo_title" maxlength="255" value="<?php echo isset($service) ? htmlspecialchars($service['seo_title']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="seo_description">Mô tả SEO (tối đa 160 ký tự)</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="160"><?php echo isset($service) ? htmlspecialchars($service['seo_description']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="seo_keywords">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                                <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo isset($service) ? htmlspecialchars($service['seo_keywords']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="?page=service" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script src="/2/admin/assets/js/jquery.min.js"></script>
    <script src="/2/admin/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ClassicEditor
                    .create(document.querySelector('#noidung'), {
                        language: 'vi',
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'fontSize', 'fontColor', 'fontBackgroundColor', 'alignment', '|',
                            'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                            'insertTable', 'imageUpload', 'imageResize', 'linkImage', 'mediaEmbed', '|',
                            'undo', 'redo'
                        ],
                        placeholder: 'Nhập nội dung dịch vụ...',
                        height: '400px',
                        image: {
                            toolbar: [
                                'imageTextAlternative',
                                'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
                                'imageResize',
                                'linkImage'
                            ],
                            resizeOptions: [
                                { name: 'resizeImage:original', value: null, label: 'Kích thước gốc' },
                                { name: 'resizeImage:50', value: '50', label: '50%' },
                                { name: 'resizeImage:75', value: '75', label: '75%' }
                            ],
                            styles: ['alignLeft', 'alignCenter', 'alignRight']
                        },
                        fontSize: { options: [10, 12, 14, 'default', 18, 20, 24, 30, 36] },
                        alignment: { options: ['left', 'center', 'right', 'justify'] },
                        ckfinder: {
                            uploadUrl: '/2/admin/pages/products/upload_ckeditor.php'
                        },
                        mediaEmbed: { previewsInData: true }
                    })
                    .then(editor => {
                        console.log('CKEditor initialized for noidung');
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error for noidung:', error);
                    });
            });
        </script>
    <?php endif; ?>
    <?php if ($action === 'list'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-service').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const id = this.getAttribute('data-id');
                        const title = this.getAttribute('data-title');
                        Swal.fire({
                            title: 'Xác nhận xóa',
                            html: `Bạn có chắc muốn xóa dịch vụ "<strong>${title}</strong>"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Xóa',
                            cancelButtonText: 'Hủy'
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = `?page=service&action=delete&id=${id}`;
                            }
                        });
                    });
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
<?php ob_end_flush(); ?>