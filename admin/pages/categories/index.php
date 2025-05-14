<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Lỗi kết nối CSDL: ' . $e->getMessage());
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Danh sách modules
$modules = [
    'home' => ['name' => 'Trang chủ', 'link' => '/2/public/home'],
    'about' => ['name' => 'Giới thiệu', 'link' => '/2/public/about'],
    'products' => ['name' => 'Sản phẩm', 'link' => '/2/public/products'],
    'services' => ['name' => 'Dịch vụ', 'link' => '/2/public/services'],
    'projects' => ['name' => 'Dự án', 'link' => '/2/public/projects'],
    'news' => ['name' => 'Tin tức', 'link' => '/2/public/news'],
    'contact' => ['name' => 'Liên hệ', 'link' => '/2/public/contact'],
    'gallery' => ['name' => 'Thư viện ảnh', 'link' => '/2/public/gallery'],
    'faq' => ['name' => 'Hỏi đáp', 'link' => '/2/public/pages/question.php'],
    'testimonials' => ['name' => 'Ý kiến khách hàng', 'link' => '/2/public/testimonials'],
    'partners' => ['name' => 'Đối tác', 'link' => '/2/public/partners'],
];

// Hàm tạo slug hỗ trợ tiếng Việt
function createSlug($string) {
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    ];
    foreach ($unicode as $nonUnicode => $uni) {
        $string = preg_replace("/($uni)/i", $nonUnicode, $string);
    }
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($string)));
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'category';
}

// Hàm kiểm tra danh mục con
function hasChildren($pdo, $category_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
        $stmt->execute([$category_id]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log('Has children error: ' . $e->getMessage());
        return false;
    }
}

// Hàm lấy danh mục dạng cây
function getCategoryTree($pdo, $parent_id = 0, $level = 0, $exclude_id = null) {
    $categories = [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND id != ? ORDER BY `order`, name");
        $stmt->execute([$parent_id, $exclude_id ?? 0]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $category) {
            $category['level'] = $level;
            $categories[] = $category;
            $children = getCategoryTree($pdo, $category['id'], $level + 1, $exclude_id);
            $categories = array_merge($categories, $children);
        }
    } catch (Exception $e) {
        error_log('Get category tree error: ' . $e->getMessage());
    }
    return $categories;
}

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $slug = createSlug($_POST['slug'] ?? $name);
    $parent_id = (int)($_POST['parent_id'] ?? 0);
    $link = trim($_POST['link'] ?? '');
    $module = trim($_POST['module'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $order = (int)($_POST['order'] ?? 0);

    $errors = [];

    // Validate
    if (empty($name)) {
        $errors[] = 'Tên danh mục không được để trống';
    }
    if (empty($slug)) {
        $errors[] = 'Slug không hợp lệ';
    } else {
        // Kiểm tra slug trùng
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() > 0) {
            $slug = $slug . '-' . time();
        }
    }
    if ($parent_id !== 0) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$parent_id]);
            if (!$stmt->fetch()) {
                $errors[] = 'Danh mục cha không tồn tại';
                $parent_id = 0;
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi kiểm tra danh mục cha: ' . $e->getMessage();
            $parent_id = 0;
        }
    }
    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Liên kết không hợp lệ';
    }
    if ($module && !isset($modules[$module])) {
        $errors[] = 'Module không hợp lệ';
    }

    // Lưu danh mục
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO categories (name, slug, parent_id, link, module, status, `order`)
                VALUES (:name, :slug, :parent_id, :link, :module, :status, :order)
            ");
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':parent_id' => $parent_id,
                ':link' => $link ?: null,
                ':module' => $module ?: null,
                ':status' => $status,
                ':order' => $order
            ]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm danh mục thành công'];
        } catch (Exception $e) {
            error_log('Add category error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi thêm danh mục: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=categories";</script>';
    exit;
}

// Xử lý sửa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = createSlug($_POST['slug'] ?? $name);
    $parent_id = (int)($_POST['parent_id'] ?? 0);
    $link = trim($_POST['link'] ?? '');
    $module = trim($_POST['module'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $order = (int)($_POST['order'] ?? 0);

    $errors = [];

    // Validate
    if ($edit_id === 0) {
        $errors[] = 'ID danh mục không hợp lệ';
    }
    if (empty($name)) {
        $errors[] = 'Tên danh mục không được để trống';
    }
    if (empty($slug)) {
        $errors[] = 'Slug không hợp lệ';
    } else {
        // Kiểm tra slug trùng
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $edit_id]);
        if ($stmt->fetchColumn() > 0) {
            $slug = $slug . '-' . time();
        }
    }
    if ($parent_id !== 0) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$parent_id]);
            if (!$stmt->fetch()) {
                $errors[] = 'Danh mục cha không tồn tại';
                $parent_id = 0;
            } else {
                $descendants = getCategoryTree($pdo, $edit_id, 0, null);
                $descendant_ids = array_column($descendants, 'id');
                if (in_array($parent_id, $descendant_ids) || $parent_id == $edit_id) {
                    $errors[] = 'Không thể chọn danh mục này làm danh mục cha';
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi kiểm tra danh mục cha: ' . $e->getMessage();
            $parent_id = 0;
        }
    }
    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Liên kết không hợp lệ';
    }
    if ($module && !isset($modules[$module])) {
        $errors[] = 'Module không hợp lệ';
    }

    // Lưu danh mục
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE categories
                SET name = :name, slug = :slug, parent_id = :parent_id, link = :link, module = :module, status = :status, `order` = :order
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':parent_id' => $parent_id,
                ':link' => $link ?: null,
                ':module' => $module ?: null,
                ':status' => $status,
                ':order' => $order,
                ':id' => $edit_id
            ]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật danh mục thành công'];
        } catch (Exception $e) {
            error_log('Edit category error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật danh mục: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=categories";</script>';
    exit;
}

// Xử lý xóa danh mục
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $errors = [];

    if (hasChildren($pdo, $delete_id)) {
        $errors[] = 'Không thể xóa danh mục này vì nó chứa danh mục con';
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa danh mục thành công'];
        } catch (Exception $e) {
            error_log('Delete category error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi xóa danh mục: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=categories";</script>';
    exit;
}

// Lấy thông tin danh mục để sửa
$edit_category = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit']) && !isset($_POST['add_category']) && !isset($_POST['edit_category'])) {
    try {
        $edit_id = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$edit_category) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Danh mục không tồn tại'];
            echo '<script>window.location.href="?page=categories";</script>';
            exit;
        }
    } catch (Exception $e) {
        error_log('Fetch edit category error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lấy danh mục: ' . $e->getMessage()];
        echo '<script>window.location.href="?page=categories";</script>';
        exit;
    }
}

// Lấy danh sách danh mục
$categories = getCategoryTree($pdo, 0, 0, $edit_category ? $edit_category['id'] : null);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 4px;
        }
        .btn-primary {
            background: linear-gradient(45deg, #007bff, #00aaff);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0088cc);
        }
        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #8a959f);
            border: none;
        }
        .btn-secondary:hover {
            background: linear-gradient(45deg, #5a6268, #787f87);
        }
        .table-responsive {
            margin-top: 20px;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        @media (max-width: 768px) {
            .form-group {
                margin-bottom: 15px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Quản lý Danh mục</h1>
        </div>

        <!-- Form thêm/sửa danh mục -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_category ? 'Sửa Danh mục' : 'Thêm Danh mục'; ?></h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['message'])): ?>
                    <script>
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: '<?php echo $_SESSION['message']['type']; ?>',
                                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                                html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            console.error('SweetAlert2 not loaded');
                            alert('<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>: <?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>');
                        }
                    </script>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <form method="POST">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_category['id'], ENT_QUOTES); ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_category['name'] ?? '', ENT_QUOTES); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($edit_category['slug'] ?? '', ENT_QUOTES); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="parent_id">Danh mục cha</label>
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="0" <?php echo ($edit_category && $edit_category['parent_id'] == 0) ? 'selected' : ''; ?>>Không có</option>
                                    <?php
                                    $all_categories = getCategoryTree($pdo, 0, 0, $edit_category ? $edit_category['id'] : null);
                                    foreach ($all_categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_category && $edit_category['parent_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo str_repeat('— ', $cat['level']) . htmlspecialchars($cat['name'], ENT_QUOTES); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="module">Module</label>
                                <select class="form-control" id="module" name="module">
                                    <option value="" <?php echo ($edit_category && !$edit_category['module']) ? 'selected' : ''; ?>>Không chọn</option>
                                    <?php foreach ($modules as $key => $mod): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($edit_category && $edit_category['module'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($mod['name'], ENT_QUOTES); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="link">Liên kết</label>
                                <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($edit_category['link'] ?? '', ENT_QUOTES); ?>">
                                <small class="form-text text-muted">VD: http://localhost/2/category/123</small>
                            </div>
                            <div class="form-group">
                                <label for="order">Thứ tự</label>
                                <input type="number" class="form-control" id="order" name="order" value="<?php echo htmlspecialchars($edit_category['order'] ?? 0, ENT_QUOTES); ?>" min="0">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" <?php echo ($edit_category && $edit_category['status']) || !$edit_category ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="status">Hiển thị</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="<?php echo $edit_category ? 'edit_category' : 'add_category'; ?>" class="btn btn-primary"><?php echo $edit_category ? 'Cập nhật' : 'Thêm Danh mục'; ?></button>
                    <?php if ($edit_category): ?>
                        <a href="?page=categories" class="btn btn-secondary">Hủy</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách danh mục -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Danh mục</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên danh mục</th>
                                <th>Slug</th>
                                <th>Module</th>
                                <th>Liên kết</th>
                                <th>Trạng thái</th>
                                <th>Thứ tự</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $index => $category): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo str_repeat('— ', $category['level']) . htmlspecialchars($category['name'], ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug'], ENT_QUOTES); ?></td>
                                    <td><?php echo $category['module'] && isset($modules[$category['module']]) ? htmlspecialchars($modules[$category['module']]['name'], ENT_QUOTES) : '-'; ?></td>
                                    <td>
                                        <?php if ($category['link']): ?>
                                            <a href="<?php echo htmlspecialchars($category['link'], ENT_QUOTES); ?>" target="_blank">Xem</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $category['status'] ? '<span class="badge badge-success">Hiển thị</span>' : '<span class="badge badge-secondary">Ẩn</span>'; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($category['order'], ENT_QUOTES); ?></td>
                                    <td>
                                        <a href="?page=categories&edit=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="?page=categories&delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tạo slug tự động từ tên danh mục, hỗ trợ tiếng Việt
        function createSlug(str) {
            const unicodeMap = {
                'a': 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
                'd': 'đ',
                'e': 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
                'i': 'í|ì|ỉ|ĩ|ị',
                'o': 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
                'u': 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
                'y': 'ý|ỳ|ỷ|ỹ|ỵ',
                'A': 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
                'D': 'Đ',
                'E': 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
                'I': 'Í|Ì|Ỉ|Ĩ|Ị',
                'O': 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
                'U': 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
                'Y': 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
            };
            for (let [nonUnicode, uni] of Object.entries(unicodeMap)) {
                const regex = new RegExp(uni, 'gi');
                str = str.replace(regex, nonUnicode);
            }
            return str
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9-]+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '') || 'category';
        }

        $('#name').on('input', function() {
            var name = $(this).val();
            var slug = createSlug(name);
            $('#slug').val(slug);
        });

        // Cập nhật liên kết khi chọn module
        const modules = <?php echo json_encode($modules); ?>;
        $('#module').change(function() {
            var module = $(this).val();
            var link = module && modules[module] ? modules[module].link : '';
            $('#link').val(link);
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>