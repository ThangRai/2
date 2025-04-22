<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Hàm tạo slug từ chuỗi
function createSlug($string) {
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
    return $string;
}

// Hàm kiểm tra và tạo slug duy nhất
function getUniqueSlug($pdo, $baseSlug) {
    $slug = $baseSlug;
    $counter = 1;
    
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() == 0) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $counter++;
    }
}

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để thực hiện thao tác này.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="../../login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 3]; // super_admin (1), content_manager (3)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Truy cập bị từ chối cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền thực hiện thao tác này.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID sản phẩm không hợp lệ.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

$product_id = (int)$_GET['id'];

// Lấy sản phẩm để sao chép
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sản phẩm không tồn tại.'];
    echo '<script>window.location.href="../../index.php?page=products";</script>';
    exit;
}

// Chuẩn bị dữ liệu cho sản phẩm được sao chép
$cloned_product = $product;
unset($cloned_product['id']); // Xóa ID để tạo bản ghi mới
$cloned_product['name'] = $product['name'] . ' (Copy)'; // Thêm "Copy" vào tên

// Tạo slug mới từ tên sản phẩm
$base_slug = createSlug($cloned_product['name']);
$cloned_product['slug'] = getUniqueSlug($pdo, $base_slug); // Đảm bảo slug duy nhất

// Xử lý sao chép ảnh sản phẩm
if (!empty($product['image'])) {
    $original_image = 'C:/laragon/www/2/admin/' . $product['image'];
    $image_ext = pathinfo($product['image'], PATHINFO_EXTENSION);
    $new_image_name = 'Uploads/products/' . $cloned_product['slug'] . '-' . time() . '.' . $image_ext;
    $new_image_path = 'C:/laragon/www/2/admin/' . $new_image_name;
    if (file_exists($original_image)) {
        if (!is_dir('C:/laragon/www/2/admin/Uploads/products/')) {
            mkdir('C:/laragon/www/2/admin/Uploads/products/', 0755, true);
        }
        if (copy($original_image, $new_image_path)) {
            $cloned_product['image'] = $new_image_name;
        } else {
            error_log('Lỗi sao chép ảnh sản phẩm cho ID ' . $product_id . ': ' . $product['image']);
            $cloned_product['image'] = null;
        }
    } else {
        $cloned_product['image'] = null;
    }
}

// Xử lý sao chép ảnh SEO
if (!empty($product['seo_image'])) {
    $original_seo_image = 'C:/laragon/www/2/admin/' . $product['seo_image'];
    $seo_image_ext = pathinfo($product['seo_image'], PATHINFO_EXTENSION);
    $new_seo_image_name = 'Uploads/seo_images/' . $cloned_product['slug'] . '-' . time() . '.' . $seo_image_ext;
    $new_seo_image_path = 'C:/laragon/www/2/admin/' . $new_seo_image_name;
    if (file_exists($original_seo_image)) {
        if (!is_dir('C:/laragon/www/2/admin/Uploads/seo_images/')) {
            mkdir('C:/laragon/www/2/admin/Uploads/seo_images/', 0755, true);
        }
        if (copy($original_seo_image, $new_seo_image_path)) {
            $cloned_product['seo_image'] = $new_seo_image_name;
        } else {
            error_log('Lỗi sao chép ảnh SEO cho ID ' . $product_id . ': ' . $product['seo_image']);
            $cloned_product['seo_image'] = null;
        }
    } else {
        $cloned_product['seo_image'] = null;
    }
}

// Xóa các trường thời gian để sử dụng giá trị mặc định của cơ sở dữ liệu
unset($cloned_product['created_at']);
unset($cloned_product['updated_at']);

// Thêm sản phẩm được sao chép vào cơ sở dữ liệu
try {
    $columns = array_keys($cloned_product);
    $placeholders = array_map(fn($col) => ":$col", $columns);
    $sql = "INSERT INTO products (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    
    $stmt = $pdo->prepare($sql);
    foreach ($cloned_product as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    $stmt->execute();
    
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Sao chép sản phẩm thành công.'];
    error_log('Sao chép sản phẩm thành công: ID gốc = ' . $product_id . ', Tên mới = ' . $cloned_product['name'] . ', Slug mới = ' . $cloned_product['slug']);
} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi sao chép sản phẩm: ' . $e->getMessage()];
    error_log('Lỗi sao chép sản phẩm ID ' . $product_id . ': ' . $e->getMessage());
}

// Chuyển hướng về trang danh sách sản phẩm bằng JavaScript
echo '<script>window.location.href="../../index.php?page=products";</script>';
exit;
?>