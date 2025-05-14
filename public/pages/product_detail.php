<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Tích hợp HTMLPurifier
require_once 'C:/laragon/www/2/admin/vendor/htmlpurifier/library/HTMLPurifier.auto.php';
$htmlPurifierConfig = HTMLPurifier_Config::createDefault();
$htmlPurifierConfig->set('HTML.Allowed', 'p,br,b,i,u,strike,ul,ol,li,table,thead,tbody,tr,th,td,blockquote,a[href],img[src|alt],strong,em,iframe[src|width|height|frameborder|allow|allowfullscreen]');
$htmlPurifierConfig->set('HTML.SafeIframe', true);
$htmlPurifierConfig->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');
$purifier = new HTMLPurifier($htmlPurifierConfig);

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    
    // Lấy slug từ tham số GET
    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
    if (empty($slug)) {
        $_SESSION['error'] = 'Không tìm thấy sản phẩm! (Slug rỗng)';
        header('Location: /2/public/pages/product');
        exit;
    }

    // Kiểm tra cấu trúc bảng
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'detailed_description'");
    $hasDetailedDescription = $stmt->rowCount() > 0;

    // Lấy thông tin sản phẩm và Flash Sale
    $query = "SELECT p.id, p.slug, p.name, p.image, p.description, p.content, p.stock, 
                     p.original_price, p.current_price, p.seo_image, p.seo_title, 
                     p.seo_description, p.seo_keywords";
    if ($hasDetailedDescription) {
        $query .= ", p.detailed_description";
    }
    $query .= ", fs.sale_price, fs.start_time, fs.end_time, fs.is_active AS flash_sale_active 
               FROM products p 
               LEFT JOIN flash_sales fs ON p.id = fs.product_id 
               WHERE p.slug = ? AND p.is_active = 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error'] = "Sản phẩm không tồn tại hoặc đã bị xóa! (Slug: $slug)";
        header('Location: /2/public/pages/product');
        exit;
    }

    // Lấy thuộc tính sản phẩm
    $stmt = $pdo->prepare("SELECT pa.name, av.value 
                           FROM attribute_values av 
                           JOIN product_attributes pa ON av.attribute_id = pa.id 
                           WHERE av.product_id = ?");
    $stmt->execute([$product['id']]);
    $product_attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra Flash Sale
    $is_flash_sale_active = $product['flash_sale_active'] && 
                            strtotime($product['start_time']) <= time() && 
                            strtotime($product['end_time']) >= time();

    // Xử lý thêm vào giỏ hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        error_log("POST received: product_id=$product_id, quantity=$quantity");

        // Kiểm tra stock
        $stmt = $pdo->prepare("SELECT id, name, current_price, stock, image FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $cart_product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart_product && $cart_product['stock'] >= $quantity && $quantity > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Kiểm tra tổng số lượng trong giỏ hàng
            $current_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
            if ($cart_product['stock'] >= $current_quantity + $quantity) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'name' => $cart_product['name'],
                        'price' => $is_flash_sale_active ? $product['sale_price'] : $cart_product['current_price'],
                        'quantity' => $quantity,
                        'stock' => $cart_product['stock'],
                        'image' => $cart_product['image']
                    ];
                }
                $_SESSION['success'] = 'Đã thêm vào giỏ hàng!';
                header('Location: /2/public/pages/cart');
                exit;
            } else {
                $_SESSION['error'] = 'Số lượng vượt quá tồn kho!';
            }
        } else {
            $_SESSION['error'] = 'Sản phẩm hết hàng hoặc không hợp lệ!';
        }
        header('Location: /2/public/pages/' . $slug);
        exit;
    }

    // Xử lý gửi đánh giá
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
        $customer_name = trim($_POST['customer_name']);
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        if (empty($customer_name) || empty($comment) || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin và chọn đánh giá từ 1-5 sao!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (product_id, customer_name, rating, comment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$product['id'], $customer_name, $rating, $comment]);
            $_SESSION['success'] = 'Đánh giá đã được gửi và đang chờ duyệt!';
        }
        header('Location: /2/public/pages/' . $slug);
        exit;
    }

    // Xử lý gửi phản hồi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
        $review_id = (int)$_POST['review_id'];
        $reply_by = trim($_POST['reply_by']);
        $reply_content = trim($_POST['reply_content']);

        if (empty($reply_by) || empty($reply_content)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin phản hồi!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO review_replies (review_id, reply_by, reply_content) VALUES (?, ?, ?)");
            $stmt->execute([$review_id, $reply_by, $reply_content]);
            $_SESSION['success'] = 'Phản hồi đã được gửi!';
        }
        header('Location: /2/public/pages/' . $slug);
        exit;
    }

    // Lấy đánh giá đã duyệt
    $stmt = $pdo->prepare("SELECT DISTINCT id, customer_name, rating, comment, created_at FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY created_at DESC");
    $stmt->execute([$product['id']]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy phản hồi cho từng đánh giá
    foreach ($reviews as $key => $review) {
        $stmt = $pdo->prepare("SELECT reply_by, reply_content, created_at FROM review_replies WHERE review_id = ? ORDER BY created_at ASC");
        $stmt->execute([$review['id']]);
        $reviews[$key]['replies'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lọc đánh giá trùng lặp
    $filtered_reviews = [];
    $seen_ids = [];
    foreach ($reviews as $review) {
        if (!in_array($review['id'], $seen_ids)) {
            $filtered_reviews[] = $review;
            $seen_ids[] = $review['id'];
        }
    }
    $reviews = $filtered_reviews;

    // Lấy sản phẩm liên quan (ngẫu nhiên)
    $stmt = $pdo->prepare("SELECT id, slug, name, image, current_price FROM products WHERE is_active = 1 AND id != ? ORDER BY RAND() LIMIT 4");
    $stmt->execute([$product['id']]);
    $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log('Product detail error: ' . $e->getMessage() . ' | Slug: ' . $slug);
    $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
    header('Location: /2/public/pages/product');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($product['seo_description'] ?: $product['description'], ENT_QUOTES); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($product['seo_keywords'], ENT_QUOTES); ?>">
    <meta name="title" content="<?php echo htmlspecialchars($product['seo_title'] ?: $product['name'], ENT_QUOTES); ?>">
    <meta property="og:image" content="<?php echo $product['seo_image'] ? 'http://localhost/2/admin/' . htmlspecialchars($product['seo_image'], ENT_QUOTES) : ($product['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($product['image'], ENT_QUOTES) : 'http://localhost/2/admin/uploads/products/default.jpg'); ?>">
    <title><?php echo htmlspecialchars($product['seo_title'] ?: $product['name'], ENT_QUOTES); ?> - Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #f8f9fa, #dee2e6);
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
        }
        h1 {
            font-size: 34px;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            text-align: center;
        }
        .breadcrumb {
            background: #fff;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 8px;
            padding: 12px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            font-size: 16px;
        }
        .breadcrumb-item a {
            color: #e03131;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .breadcrumb-item a:hover {
            color: #0984e3;
        }
        .breadcrumb-item.active {
            color: #2d3436;
            font-weight: 500;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            color: #b2bec3;
        }
        .product-image img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transition: transform 0.4s ease;
        }
        .product-image img:hover {
            transform: scale(1.03);
        }
        .product-details {
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .product-details h2 {
            font-size: 30px;
            color: #2d3436;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .product-price .flash-sale-price {
            font-size: 34px;
            color: #ff4757;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
        }
        .product-price .flash-sale-price .end-time {
            font-size: 16px;
            color: #2d3436;
            font-weight: 400;
        }
        .product-price .current-price {
            font-size: 32px;
            color: #e03131;
            font-weight: 700;
        }
        .product-price .original-price {
            font-size: 22px;
            color: #b2bec3;
            text-decoration: line-through;
            margin-left: 15px;
        }
        .stock-status {
            font-size: 18px;
            margin: 20px 0;
            font-weight: 500;
        }
        .stock-status.in {
            color: #00b894;
        }
        .stock-status.out {
            color: #ff4757;
        }
        .product-description, .product-detailed-description, .product-content {
            font-size: 16px;
            color: #636e72;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .product-description h3, .product-detailed-description h3, .product-content h3 {
            font-size: 22px;
            color: #2d3436;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .product-attributes {
            margin-top: 20px;
        }
        .product-attributes ul {
            list-style: none;
            padding: 0;
        }
        .product-attributes li {
            font-size: 16px;
            color: #2d3436;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .product-attributes li::before {
            content: '\f058';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e03131;
            margin-right: 10px;
        }
        .add-to-cart-form {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 25px;
        }
        .add-to-cart-form input[type="number"] {
            width: 100px;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }
        .add-to-cart-form input[type="number"]:focus {
            border-color: #e03131;
            outline: none;
        }
        .add-to-cart {
            background: linear-gradient(135deg, #e03131, #c0392b);
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.4s ease, transform 0.3s ease;
        }
        .add-to-cart:hover {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            transform: scale(1.05);
        }
        .add-to-cart:disabled {
            background: #b2bec3;
            cursor: not-allowed;
            transform: none;
        }
        .product-content {
            margin-top: 50px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .product-content h3 {
            font-size: 26px;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .reviews-section {
            margin-top: 50px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .reviews-section h3 {
            font-size: 26px;
            color: #2d3436;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .review-item {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .review-item:hover {
            background: #f8f9fa;
            transform: translateY(-3px);
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .review-header .customer-name {
            font-size: 18px;
            font-weight: 600;
            color: #2d3436;
        }
        .review-header .customer-name::before {
            content: '\f007';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
            color: #e03131;
        }
        .review-header .review-date {
            font-size: 14px;
            color: #b2bec3;
        }
        .review-header .review-date::before {
            content: '\f017';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
            color: #b2bec3;
        }
        .review-rating {
            margin-bottom: 15px;
        }
        .review-rating i {
            color: #ffd43b;
            font-size: 18px;
            margin-right: 3px;
        }
        .review-comment {
            font-size: 16px;
            color: #636e72;
            line-height: 1.8;
        }
        .review-replies {
            margin-top: 20px;
            padding-left: 25px;
            border-left: 4px solid #e03131;
        }
        .reply-item {
            margin-bottom: 15px;
        }
        .reply-item .reply-by {
            font-size: 16px;
            font-weight: 600;
            color: #2d3436;
        }
        .reply-item .reply-by::before {
            content: '\f3e5';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 8px;
            color: #0984e3;
        }
        .reply-item .reply-content {
            font-size: 15px;
            color: #636e72;
            line-height: 1.8;
        }
        .reply-item .reply-date {
            font-size: 13px;
            color: #b2bec3;
        }
        .reply-form {
            margin-top: 25px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: none;
        }
        .reply-form.active {
            display: block;
        }
        .reply-form h4 {
            font-size: 22px;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .reply-form label {
            font-size: 16px;
            color: #2d3436;
            margin-bottom: 8px;
            display: block;
            font-weight: 500;
        }
        .reply-form input, .reply-form textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }
        .reply-form input:focus, .reply-form textarea:focus {
            border-color: #e03131;
            outline: none;
        }
        .reply-form button {
            background: linear-gradient(135deg, #e03131, #ff6b6b);
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.4s ease, transform 0.3s ease;
        }
        .reply-form button:hover {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            transform: scale(1.05);
        }
        .reply-toggle {
            cursor: pointer;
            color: #e03131;
            font-size: 16px;
            font-weight: 500;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease;
        }
        .reply-toggle:hover {
            color: #0984e3;
        }
        .reply-toggle i {
            margin-right: 8px;
        }
        .review-form {
            margin-top: 25px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .review-form h4 {
            font-size: 22px;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .review-form .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .review-form .form-group {
            flex: 1;
            min-width: 150px;
        }
        .review-form label {
            font-size: 16px;
            color: #2d3436;
            margin-bottom: 8px;
            display: block;
            font-weight: 500;
        }
        .review-form input, .review-form textarea, .review-form select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }
        .review-form input:focus, .review-form textarea:focus, .review-form select:focus {
            border-color: #e03131;
            outline: none;
        }
        .review-form textarea {
            margin-bottom: 20px;
        }
        .review-form button {
            background: linear-gradient(135deg, #e03131, #ff6b6b);
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.4s ease, transform 0.3s ease;
        }
        .review-form button:hover {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            transform: scale(1.05);
        }
        .related-products {
            margin-top: 50px;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .related-products h3 {
            font-size: 26px;
            color: #2d3436;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .related-product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }
        .related-product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .related-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .related-product-image {
            position: relative;
            overflow: hidden;
        }
        .related-product-image img {
            width: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .related-product-card:hover .related-product-image img {
            transform: scale(1.05);
        }
        .related-product-info {
            padding: 20px;
            text-align: center;
        }
        .related-product-info h5 {
            font-size: 18px;
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 12px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .related-product-info .current-price {
            font-size: 18px;
            color: #e03131;
            font-weight: 700;
        }
        .error, .success {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 12px;
        }
        .error {
            color: #ff4757;
            background: #ffe6e6;
        }
        .success {
            color: #00b894;
            background: #e6fff7;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 30px;
            }
            .breadcrumb {
                font-size: 15px;
                padding: 10px 15px;
            }
            .product-details h2 {
                font-size: 26px;
            }
            .product-price .flash-sale-price {
                font-size: 30px;
            }
            .product-price .current-price {
                font-size: 28px;
            }
            .product-price .original-price {
                font-size: 20px;
            }
            .add-to-cart-form input[type="number"] {
                width: 90px;
                padding: 10px;
            }
            .add-to-cart {
                padding: 10px 25px;
                font-size: 15px;
            }
            .related-product-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
            .product-content, .reviews-section, .related-products {
                padding: 20px;
            }
            .review-form .form-row {
                flex-direction: column;
            }
        }
        @media (max-width: 425px) {
            h1 {
                font-size: 26px;
            }
            .breadcrumb {
                font-size: 14px;
                padding: 8px 12px;
                overflow-x: auto;
                white-space: nowrap;
            }
            .product-details h2 {
                font-size: 22px;
            }
            .product-price .flash-sale-price {
                font-size: 26px;
            }
            .product-price .current-price {
                font-size: 24px;
            }
            .product-price .original-price {
                font-size: 18px;
            }
            .add-to-cart-form {
                flex-direction: column;
                align-items: stretch;
            }
            .add-to-cart-form input[type="number"] {
                width: 100%;
            }
            .add-to-cart {
                width: 100%;
                padding: 12px;
            }
            .related-product-grid {
                grid-template-columns: 1fr;
            }
            .product-content, .reviews-section, .related-products {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/2/public">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/2/public/pages/product">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></li>
        </ol>
    </nav>
    <div class="container">
        <h1><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h1>
        <!-- Hiển thị thông báo SweetAlert2 -->
        <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
            <script>
                Swal.fire({
                    icon: '<?php echo isset($_SESSION['success']) ? 'success' : 'error'; ?>',
                    title: '<?php echo isset($_SESSION['success']) ? 'Thành công' : 'Lỗi'; ?>',
                    html: '<?php echo htmlspecialchars(isset($_SESSION['success']) ? $_SESSION['success'] : $_SESSION['error'], ENT_QUOTES); ?>',
                    confirmButtonText: 'OK'
                });
            </script>
            <?php unset($_SESSION['success'], $_SESSION['error']); ?>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="product-image">
                    <img src="<?php echo $product['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($product['image'], ENT_QUOTES) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h2><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h2>
                    <div class="product-price">
                        <?php if ($is_flash_sale_active): ?>
                            <span class="flash-sale-price">
                                <?php echo number_format($product['sale_price'], 0, ',', '.'); ?>đ
                                <span class="end-time">(Kết thúc: <?php echo date('d/m/Y H:i', strtotime($product['end_time'])); ?>)</span>
                            </span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo number_format($product['current_price'], 0, ',', '.'); ?>đ</span>
                        <?php if ($product['original_price'] > $product['current_price']): ?>
                            <span class="original-price"><?php echo number_format($product['original_price'], 0, ',', '.'); ?>đ</span>
                        <?php endif; ?>
                    </div>
                    <div class="stock-status <?php echo $product['stock'] > 0 ? 'in' : 'out'; ?>">
                        <?php echo $product['stock'] > 0 ? 'Còn hàng (' . $product['stock'] . ' sản phẩm)' : 'Hết hàng'; ?>
                    </div>
                    <div class="product-description">
                        <h3>Mô tả</h3>
                        <?php echo $purifier->purify($product['description']); ?>
                        <?php if (!empty($product_attributes)): ?>
                            <div class="product-attributes">
                                <h4>Thuộc tính</h4>
                                <ul>
                                    <?php foreach ($product_attributes as $attr): ?>
                                        <li><?php echo htmlspecialchars($attr['name'], ENT_QUOTES); ?>: <?php echo htmlspecialchars($attr['value'], ENT_QUOTES); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($hasDetailedDescription && !empty($product['detailed_description'])): ?>
                        <div class="product-detailed-description">
                            <h3>Chi tiết</h3>
                            <?php echo $purifier->purify($product['detailed_description']); ?>
                        </div>
                    <?php endif; ?>
                    <form class="add-to-cart-form" method="POST" action="/2/public/pages/<?php echo htmlspecialchars($product['slug'], ENT_QUOTES); ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <button type="submit" name="add_to_cart" class="add-to-cart" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="product-content">
            <h3>Thông tin sản phẩm</h3>
            <?php echo $purifier->purify($product['content']); ?>
        </div>
        <div class="reviews-section">
            <h3>Đánh giá khách hàng</h3>
            <?php if (empty($reviews)): ?>
                <p>Chưa có đánh giá nào cho sản phẩm này.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="customer-name"><?php echo htmlspecialchars($review['customer_name'], ENT_QUOTES); ?></span>
                            <span class="review-date"><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'far'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="review-comment">
                            <?php echo htmlspecialchars($review['comment'], ENT_QUOTES); ?>
                        </div>
                        <?php if (!empty($review['replies'])): ?>
                            <div class="review-replies">
                                <?php foreach ($review['replies'] as $reply): ?>
                                    <div class="reply-item">
                                        <div class="reply-by"><?php echo htmlspecialchars($reply['reply_by'], ENT_QUOTES); ?></div>
                                        <div class="reply-content"><?php echo htmlspecialchars($reply['reply_content'], ENT_QUOTES); ?></div>
                                        <div class="reply-date"><?php echo date('d/m/Y H:i', strtotime($reply['created_at'])); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="reply-toggle" data-review-id="<?php echo $review['id']; ?>">
                            <i class="fas fa-comment-dots"></i> Phản hồi
                        </div>
                        <form class="reply-form" id="reply-form-<?php echo $review['id']; ?>" method="POST" action="/2/public/pages/<?php echo htmlspecialchars($product['slug'], ENT_QUOTES); ?>">
                            <h4>Phản hồi</h4>
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <label for="reply_by_<?php echo $review['id']; ?>">Tên:</label>
                            <input type="text" name="reply_by" id="reply_by_<?php echo $review['id']; ?>" required>
                            <label for="reply_content_<?php echo $review['id']; ?>">Nội dung:</label>
                            <textarea name="reply_content" id="reply_content_<?php echo $review['id']; ?>" rows="3" required></textarea>
                            <button type="submit" name="submit_reply">Gửi phản hồi</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form class="review-form" method="POST" action="/2/public/pages/<?php echo htmlspecialchars($product['slug'], ENT_QUOTES); ?>">
                <h4>Viết đánh giá của bạn</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Tên:</label>
                        <input type="text" name="customer_name" id="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="rating">Đánh giá:</label>
                        <select name="rating" id="rating" required>
                            <option value="">Chọn số sao</option>
                            <option value="1">1 sao</option>
                            <option value="2">2 sao</option>
                            <option value="3">3 sao</option>
                            <option value="4">4 sao</option>
                            <option value="5">5 sao</option>
                        </select>
                    </div>
                </div>
                <label for="comment">Bình luận:</label>
                <textarea name="comment" id="comment" rows="5" required></textarea>
                <button type="submit" name="submit_review">Gửi đánh giá</button>
            </form>
        </div>
        <?php if (!empty($related_products)): ?>
            <div class="related-products">
                <h3>Sản phẩm liên quan</h3>
                <div class="related-product-grid">
                    <?php foreach ($related_products as $related_product): ?>
                        <div class="related-product-card">
                            <a href="/2/public/pages/<?php echo htmlspecialchars($related_product['slug'], ENT_QUOTES); ?>">
                                <div class="related-product-image">
                                    <img src="<?php echo $related_product['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($related_product['image'], ENT_QUOTES) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($related_product['name'], ENT_QUOTES); ?>">
                                </div>
                                <div class="related-product-info">
                                    <h5><?php echo htmlspecialchars($related_product['name'], ENT_QUOTES); ?></h5>
                                    <div class="current-price"><?php echo number_format($related_product['current_price'], 0, ',', '.'); ?>đ</div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.querySelectorAll('.reply-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const replyForm = document.getElementById(`reply-form-${reviewId}`);
            replyForm.classList.toggle('active');
        });
    });
</script>
</body>
</html>
<?php ob_end_flush(); ?>