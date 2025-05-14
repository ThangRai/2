<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Lấy danh sách danh mục và thuộc tính
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$attributes = $pdo->query("SELECT * FROM product_attributes")->fetchAll(PDO::FETCH_ASSOC);

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
        'È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
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
    
    // Kiểm tra trùng slug
    $baseSlug = $string;
    $counter = 1;
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");
        $stmt->execute([$string]);
        if ($stmt->fetchColumn() == 0) {
            break;
        }
        $string = $baseSlug . '-' . $counter++;
    }
    return $string;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $content = trim($_POST['noidung'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $original_price = (float)($_POST['original_price'] ?? 0);
    $current_price = (float)($_POST['current_price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');
    $sale_price = (float)($_POST['sale_price'] ?? 0);
    $start_time = trim($_POST['start_time'] ?? '');
    $end_time = trim($_POST['end_time'] ?? '');
    $flash_sale_active = isset($_POST['flash_sale_active']) ? 1 : 0;
    $attributes_values = isset($_POST['attributes']) ? $_POST['attributes'] : [];

    // Kiểm tra lỗi
    $errors = [];
    if (empty($name)) $errors[] = 'Tên sản phẩm không được để trống.';
    if (empty($category_id)) $errors[] = 'Vui lòng chọn danh mục.';
    if (!is_numeric($original_price) || $original_price < 0) $errors[] = 'Giá gốc phải là số dương.';
    if (!is_numeric($current_price) || $current_price < 0) $errors[] = 'Giá hiện tại phải là số dương.';
    if ($sale_price > 0 && (!is_numeric($sale_price) || $sale_price >= $current_price)) {
        $errors[] = 'Giá Flash Sale phải nhỏ hơn giá hiện tại.';
    }
    if ($sale_price > 0 && (empty($start_time) || empty($end_time))) {
        $errors[] = 'Vui lòng nhập thời gian bắt đầu và kết thúc cho Flash Sale.';
    }
    if ($sale_price > 0 && strtotime($end_time) <= strtotime($start_time)) {
        $errors[] = 'Thời gian kết thúc Flash Sale phải sau thời gian bắt đầu.';
    }
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
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $target_dir = "uploads/products/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $image = $target_dir . time() . '_' . basename($_FILES['image']['name']);
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                    throw new Exception('Lỗi khi tải lên hình ảnh sản phẩm.');
                }
            }

            // Xử lý ảnh đại diện SEO
            $seo_image = null;
            if (!empty($_FILES['seo_image']['name'])) {
                $target_dir = "uploads/seo_images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $seo_image = $target_dir . time() . '_' . basename($_FILES['seo_image']['name']);
                if (!move_uploaded_file($_FILES['seo_image']['tmp_name'], $seo_image)) {
                    throw new Exception('Lỗi khi tải lên ảnh đại diện SEO.');
                }
            }

            // Tạo slug
            $slug = createSlug($name, $pdo);

            // Thêm sản phẩm
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, content, description, image, original_price, current_price, stock, is_active, seo_image, seo_title, seo_description, seo_keywords, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $slug, $category_id, $content, $description, $image, $original_price, $current_price, $stock, $is_active, $seo_image, $seo_title, $seo_description, $seo_keywords]);

            // Lấy ID sản phẩm vừa thêm
            $product_id = $pdo->lastInsertId();

            // Thêm Flash Sale (nếu có)
            if ($sale_price > 0 && !empty($start_time) && !empty($end_time)) {
                $stmt = $pdo->prepare("INSERT INTO flash_sales (product_id, sale_price, start_time, end_time, is_active) 
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$product_id, $sale_price, $start_time, $end_time, $flash_sale_active]);
            }

            // Thêm thuộc tính sản phẩm
            foreach ($attributes_values as $attr_id => $value) {
                if (!empty($value)) {
                    $stmt = $pdo->prepare("INSERT INTO attribute_values (product_id, attribute_id, value) VALUES (?, ?, ?)");
                    $stmt->execute([$product_id, $attr_id, trim($value)]);
                }
            }

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm sản phẩm thành công.'];
            echo '<script>window.location.href="?page=products";</script>';
            exit;
        } catch (Exception $e) {
            error_Log('Add product error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm sản phẩm: ' . $e->getMessage()];
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
    <title>Thêm sản phẩm</title>
    <!-- SB Admin CSS -->
<link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
<link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
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
<body>
    <!-- Hiển thị thông báo SweetAlert2 -->
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

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm sản phẩm</h1>
        <a href="?page=products" class="btn btn-secondary">Hủy</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm sản phẩm mới</h6>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="noidung">Nội dung</label>
                            <textarea class="form-control" id="noidung" name="noidung" rows="6"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Hình ảnh</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/jpeg,image/png">
                        </div>
                        <div class="form-group">
                            <label for="original_price">Giá gốc <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" required>
                        </div>
                        <div class="form-group">
                            <label for="current_price">Giá hiện tại <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Kích hoạt</label>
                            </div>
                        </div>
                        <!-- Flash Sale -->
                        <div class="form-group">
                            <label for="sale_price">Giá Flash Sale (VND)</label>
                            <input type="number" step="0.01" class="form-control" id="sale_price" name="sale_price">
                        </div>
                        <div class="form-group">
                            <label for="start_time">Thời gian bắt đầu Flash Sale</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time">
                        </div>
                        <div class="form-group">
                            <label for="end_time">Thời gian kết thúc Flash Sale</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="flash_sale_active" name="flash_sale_active">
                                <label class="form-check-label" for="flash_sale_active">Kích hoạt Flash Sale</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="seo_image">Ảnh đại diện SEO</label>
                            <input type="file" class="form-control-file" id="seo_image" name="seo_image" accept="image/jpeg,image/png">
                        </div>
                        <div class="form-group">
                            <label for="seo_title">Tiêu đề SEO (tối đa 255 ký tự)</label>
                            <input type="text" class="form-control" id="seo_title" name="seo_title" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label for="seo_description">Mô tả SEO (tối đa 160 ký tự)</label>
                            <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="160"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="seo_keywords">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                            <input type="text" class="form-control" id="seo_keywords" name="seo_keywords">
                        </div>
                        <!-- Thuộc tính sản phẩm -->
                        <div class="form-group">
                            <label>Thuộc tính sản phẩm</label>
                            <?php foreach ($attributes as $attribute): ?>
                                <div class="form-group">
                                    <label><?php echo htmlspecialchars($attribute['name']); ?></label>
                                    <input type="text" class="form-control" name="attributes[<?php echo $attribute['id']; ?>]" placeholder="Nhập giá trị (VD: S, M, L hoặc Đỏ, Xanh)">
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($attributes)): ?>
                                <p class="text-muted">Chưa có thuộc tính. <a href="?page=attributes&subpage=manage">Quản lý thuộc tính</a>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="?page=products" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>

    <!-- Scripts -->
<script src="/2/admin/assets/js/jquery.min.js"></script>
<script src="/2/admin/assets/js/bootstrap.bundle.min.js"></script>
<script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
    <script src="/2/admin/assets/js/ckeditor_config.js"></script>
</body>
</html>