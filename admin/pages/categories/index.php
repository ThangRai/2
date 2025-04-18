<?php
ob_start(); // Bắt đầu output buffering

// Bật hiển thị lỗi để debug
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

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $parent_id = (int)($_POST['parent_id'] ?? 0);
    $link = trim($_POST['link'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $order = (int)($_POST['order'] ?? 0);

    $errors = [];

    // Validate
    if (empty($name)) {
        $errors[] = 'Tên danh mục không được để trống';
    }
    if ($parent_id !== 0) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$parent_id]);
            if (!$stmt->fetch()) {
                $errors[] = 'Danh mục cha không tồn tại';
                $parent_id = 0; // Đặt lại về 0 nếu không hợp lệ
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi kiểm tra danh mục cha: ' . $e->getMessage();
            $parent_id = 0;
        }
    }
    if (!empty($link) && !filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Liên kết không hợp lệ';
    }

    // Lưu danh mục
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO categories (name, parent_id, link, status, `order`)
                VALUES (:name, :parent_id, :link, :status, :order)
            ");
            $stmt->execute([
                ':name' => $name,
                ':parent_id' => $parent_id,
                ':link' => $link ?: null,
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

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=categories";</script>';
    exit;
}

// Xử lý sửa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $parent_id = (int)($_POST['parent_id'] ?? 0);
    $link = trim($_POST['link'] ?? '');
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
    if ($parent_id !== 0) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute([$parent_id]);
            if (!$stmt->fetch()) {
                $errors[] = 'Danh mục cha không tồn tại';
                $parent_id = 0;
            } else {
                // Kiểm tra không cho chọn chính nó hoặc con của nó làm cha
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

    // Lưu danh mục
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE categories
                SET name = :name, parent_id = :parent_id, link = :link, status = :status, `order` = :order
                WHERE id = :id
            ");
            $stmt->execute([
                ':name' => $name,
                ':parent_id' => $parent_id,
                ':link' => $link ?: null,
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

    // Chuyển hướng bằng JS
    echo '<script>window.location.href="?page=categories";</script>';
    exit;
}

// Xử lý xóa danh mục
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $errors = [];

    // Kiểm tra danh mục con
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

    // Chuyển hướng bằng JS
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
                // Kiểm tra Swal tồn tại
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

<?php ob_end_flush(); ?>