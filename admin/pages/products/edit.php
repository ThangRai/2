<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy sản phẩm để chỉnh sửa.'];
    header("Location: ?page=products");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT id, name, category_id, content, description, image, original_price, current_price, stock, is_active, seo_image, seo_title, seo_description, seo_keywords FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sản phẩm không tồn tại.'];
    header("Location: ?page=products");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $content = trim($_POST['noidung'] ?? ''); // Lấy từ noidung thay vì content
    $description = trim($_POST['description'] ?? '');
    $original_price = (float)($_POST['original_price'] ?? 0);
    $current_price = (float)($_POST['current_price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    // Kiểm tra lỗi
    $errors = [];
    if (empty($name)) $errors[] = 'Tên sản phẩm không được để trống.';
    if (empty($category_id)) $errors[] = 'Vui lòng chọn danh mục.';
    if (!is_numeric($original_price) || $original_price < 0) $errors[] = 'Giá gốc phải là số dương.';
    if (!is_numeric($current_price) || $current_price < 0) $errors[] = 'Giá hiện tại phải là số dương.';
    if (!is_numeric($stock) || $stock < 0) $errors[] = 'Số lượng tồn kho phải là số không âm.';
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = 'Hình ảnh phải là định dạng JPG hoặc PNG.';
        }
        if ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'Hình ảnh không được lớn hơn 2MB.';
        }
    }
    if (!empty($_FILES['seo_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if (!in_array($_FILES['seo_image']['type'], $allowed_types)) {
            $errors[] = 'Ảnh đại diện SEO phải là định dạng JPG hoặc PNG.';
        }
        if ($_FILES['seo_image']['size'] > $max_size) {
            $errors[] = 'Ảnh đại diện SEO không được lớn hơn 2MB.';
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
            // Xử lý ảnh sản phẩm
            $image = $product['image'];
            if (!empty($_FILES['image']['name'])) {
                $target_dir = "uploads/products/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $image = $target_dir . basename($_FILES['image']['name']);
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                    throw new Exception('Lỗi khi tải lên hình ảnh sản phẩm.');
                }
            }

            // Xử lý ảnh đại diện SEO
            $seo_image = $product['seo_image'];
            if (!empty($_FILES['seo_image']['name'])) {
                $target_dir = "uploads/seo_images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $seo_image = $target_dir . basename($_FILES['seo_image']['name']);
                if (!move_uploaded_file($_FILES['seo_image']['tmp_name'], $seo_image)) {
                    throw new Exception('Lỗi khi tải lên ảnh đại diện SEO.');
                }
            }

            // Cập nhật sản phẩm
            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, content = ?, description = ?, image = ?, original_price = ?, current_price = ?, stock = ?, is_active = ?, seo_image = ?, seo_title = ?, seo_description = ?, seo_keywords = ? WHERE id = ?");
            $stmt->execute([$name, $category_id, $content, $description, $image, $original_price, $current_price, $stock, $is_active, $seo_image, $seo_title, $seo_description, $seo_keywords, $id]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật sản phẩm thành công.'];
            echo '<script>window.location.href="?page=products";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Edit product error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="/2/admin/assets/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="/2/admin/assets/vendor/fontawesome/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>
        <style>
        .form-group {
            margin-bottom: 1.5rem;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .img-preview {
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .form-group label, .form-group input, .form-group select, .form-group textarea {
                font-size: 0.9em;
            }
            .btn {
                font-size: 0.9em;
            }
        }
        /* Đảm bảo CKEditor hiển thị tốt trong SB Admin */
        .ck-editor__editable {
            min-height: 200px;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <!-- Hiển thị thông báo SweetAlert2 -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script>
                            Swal.fire({
                                icon: '<?php echo $_SESSION['message']['type']; ?>',
                                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                                html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                                confirmButtonText: 'OK'
                            });
                        </script>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Sửa sản phẩm</h1>
                        <a href="?page=products" class="btn btn-secondary">Hủy</a>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" novalidate>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Danh mục <span class="text-danger">*</span></label>
                                            <select class="form-control" id="category_id" name="category_id" required>
                                                <option value="">Chọn danh mục</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Mô tả</label>
                                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="noidung">Nội dung</label>
                                            <textarea class="form-control" id="noidung" name="noidung" rows="4"><?php echo htmlspecialchars($product['content'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="image">Hình ảnh</label>
                                            <?php if (!empty($product['image'])): ?>
                                                <div class="img-preview">
                                                    <img src="/2/admin/<?php echo htmlspecialchars($product['image']); ?>" alt="Hình ảnh hiện tại" style="width: 100px; height: 100px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control-file" id="image" name="image" accept="image/jpeg,image/png">
                                        </div>
                                        <div class="form-group">
                                            <label for="original_price">Giá gốc (VND) <span class="text-danger">*</span></label>
                                            <input type="number" step="1000" min="0" class="form-control" id="original_price" name="original_price" value="<?php echo (int)$product['original_price']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="current_price">Giá hiện tại (VND) <span class="text-danger">*</span></label>
                                            <input type="number" step="1000" min="0" class="form-control" id="current_price" name="current_price" value="<?php echo (int)$product['current_price']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock">Tồn kho <span class="text-danger">*</span></label>
                                            <input type="number" min="0" class="form-control" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">Kích hoạt</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="seo_image">Ảnh đại diện SEO</label>
                                            <?php if (!empty($product['seo_image'])): ?>
                                                <div class="img-preview">
                                                    <img src="/2/admin/<?php echo htmlspecialchars($product['seo_image']); ?>" alt="Ảnh SEO hiện tại" style="width: 100px; height: 100px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control-file" id="seo_image" name="seo_image" accept="image/jpeg,image/png">
                                        </div>
                                        <div class="form-group">
                                            <label for="seo_title">Tiêu đề SEO (tối đa 255 ký tự)</label>
                                            <input type="text" class="form-control" id="seo_title" name="seo_title" value="<?php echo htmlspecialchars($product['seo_title'] ?? ''); ?>" maxlength="255">
                                        </div>
                                        <div class="form-group">
                                            <label for="seo_description">Mô tả SEO (tối đa 160 ký tự)</label>
                                            <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="160"><?php echo htmlspecialchars($product['seo_description'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="seo_keywords">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                                            <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo htmlspecialchars($product['seo_keywords'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="?page=products" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/2/admin/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/2/admin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/2/admin/assets/js/ckeditor_config.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>