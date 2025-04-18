<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy sản phẩm để sao chép.'];
    header("Location: ?page=products");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sản phẩm không tồn tại.'];
    header("Location: ?page=products");
    exit;
}

// Sao chép sản phẩm
$name = $product['name'] . ' (Copy)';
$category_id = $product['category_id'];
$content = $product['content'];
$description = $product['description'];
$image = $product['image'];
$original_price = $product['original_price'];
$current_price = $product['current_price'];
$stock = $product['stock'];
$is_active = $product['is_active'];

$stmt = $pdo->prepare("INSERT INTO products (name, category_id, content, description, image, original_price, current_price, stock, is_active, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->execute([$name, $category_id, $content, $description, $image, $original_price, $current_price, $stock, $is_active]);

$_SESSION['message'] = ['type' => 'success', 'text' => 'Sao chép sản phẩm thành công.'];
header("Location: ?page=products");
exit;
?>