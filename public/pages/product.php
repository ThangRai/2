<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    // Lấy cấu hình cột
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'columns_%'");
    $columns = [
        'columns_375' => 2,
        'columns_425' => 3,
        'columns_768' => 4,
        'columns_1200' => 5,
        'columns_max' => 6
    ];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[$row['name']] = min((int)$row['value'], 6);
    }

    // Lấy sản phẩm
    $stmt = $pdo->prepare("SELECT id, slug, name, image, description, stock, original_price, current_price FROM products WHERE is_active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý thêm vào giỏ hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        header('Content-Type: application/json'); // Thiết lập header JSON
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        error_log("POST received: product_id=$product_id, quantity=$quantity");

        // Kiểm tra stock
        $stmt = $pdo->prepare("SELECT id, name, current_price, stock, image FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['stock'] >= $quantity && $quantity > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Kiểm tra tổng số lượng trong giỏ hàng
            $current_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
            if ($product['stock'] >= $current_quantity + $quantity) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'name' => $product['name'],
                        'price' => $product['current_price'],
                        'quantity' => $quantity,
                        'stock' => $product['stock'],
                        'image' => $product['image']
                    ];
                }
                echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Số lượng vượt quá tồn kho!']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm hết hàng hoặc không hợp lệ!']);
            exit;
        }
    }
} catch (Exception $e) {
    error_log('Product page error: ' . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    $products = [];
    $columns = [
        'columns_375' => 2,
        'columns_425' => 3,
        'columns_768' => 4,
        'columns_1200' => 5,
        'columns_max' => 6
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Thêm SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Thêm jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #f8f9fa, #dfe6e9);
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        h1 {
            text-align: center;
            font-size: 24px !important;
            color: #000;
            margin-bottom: 40px;
            font-weight: bold !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }
        h1::after {
            content: '';
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #0984e3, #74b9ff);
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        .product-grid {
            margin-top: 40px;
            display: grid;
            gap: 20px;
            grid-auto-rows: minmax(400px, auto);
        }
        @media (max-width: 375px) {
            .product-grid {
                grid-template-columns: repeat(<?php echo $columns['columns_375']; ?>, minmax(150px, 1fr));
            }
        }
        @media (min-width: 375px) and (max-width: 425px) {
            .product-grid {
                grid-template-columns: repeat(<?php echo $columns['columns_425']; ?>, minmax(120px, 1fr));
            }
        }
        @media (min-width: 425px) and (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(<?php echo $columns['columns_768']; ?>, minmax(160px, 1fr));
            }
        }
        @media (min-width: 768px) and (max-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(<?php echo $columns['columns_1200']; ?>, minmax(180px, 1fr));
            }
        }
        @media (min-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(<?php echo $columns['columns_max']; ?>, minmax(200px, 1fr));
            }
        }
        .product-card {
            background: #fff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            min-height: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }
        .product-image {
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            background: #f1f2f6;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        .discount-badge {
            position: absolute;
            background: linear-gradient(135deg, rgb(196, 1, 17), rgb(242, 6, 6));
            color: #fff;
            padding: 5px 12px;
            border-radius: 0 2px 25px 0;
            font-size: 0.85em;
            font-weight: 600;
            z-index: 2;
        }
        .product-info {
            padding: 15px 0 0;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-info h5 {
            font-size: 16px;
            font-weight: 500;
            color: #2d3436;
            margin: 0 0 10px;
            height: auto;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .original-price {
            text-decoration: line-through;
            color: #b2bec3;
            font-size: 0.9em;
            margin-right: 8px;
        }
        .current-price {
            color: rgb(216, 0, 18);
            font-size: 14px;
            font-weight: 600;
        }
        .stock-status {
            font-size: 0.9em;
            margin: 6px 0;
            font-weight: 500;
        }
        .stock-status.in {
            color: #00b894;
        }
        .stock-status.out {
            color: #ff4757;
        }
        .rating {
            margin: 6px 0;
            font-size: 1em;
        }
        .rating i {
            color: #feca57;
            transition: transform 0.2s ease;
        }
        .rating i:hover {
            transform: scale(1.2);
        }
        .add-to-cart-form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            padding: 0 15px;
        }
        .add-to-cart-form input[type="number"] {
            width: 50px;
            padding: 6px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .add-to-cart {
            background: linear-gradient(135deg, rgb(227, 9, 9), rgb(142, 18, 18));
            color: #fff;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            border-radius: 4px;
            flex-grow: 1;
        }
        .add-to-cart:hover {
            background: linear-gradient(135deg, #0652dd, #4dabf7);
            transform: scale(1.02);
        }
        .add-to-cart:disabled {
            background: #b2bec3;
            cursor: not-allowed;
            transform: none;
        }
        .hover-icons {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 3;
        }
        .product-card:hover .hover-icons {
            opacity: 1;
        }
        .hover-icons a {
            background: #fff;
            color: #2d3436;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
            font-size: 1.2em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, background 0.3s ease;
            transform: scale(0);
        }
        .product-card:hover .hover-icons a {
            transform: scale(1);
        }
        .hover-icons a:nth-child(1) { transition-delay: 0.05s; }
        .hover-icons a:nth-child(2) { transition-delay: 0.1s; }
        .hover-icons a:nth-child(3) { transition-delay: 0.15s; }
        .hover-icons a:hover {
            background: #0984e3;
            color: #fff;
        }
        .error, .success {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .error {
            color: #ff4757;
            background: #ffe6e6;
        }
        .success {
            color: #00b894;
            background: #e6fff7;
        }
        .product-description {
            font-size: 0.9em;
            color: #636e72;
            margin: 8px 0;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        @media (max-width: 768px) {
            .product-image {
                height: 180px;
            }
            .product-info h5 {
                font-size: 1.1em;
                height: 44px;
            }
            .current-price {
                font-size: 1.3em;
            }
            .add-to-cart-form input[type="number"] {
                width: 70px;
                padding: 5px;
                font-size: 13px;
            }
            .add-to-cart {
                font-size: 13px;
                padding: 7px 12px;
            }
            .hover-icons a {
                width: 35px;
                height: 35px;
                font-size: 1.1em;
            }
        }
        @media (max-width: 425px) {
            .product-image {
                height: 160px;
            }
            .product-info h5 {
                font-size: 1em;
                height: 40px;
            }
            .current-price {
                font-size: 1.2em;
            }
            .add-to-cart-form input[type="number"] {
                width: 60px;
                padding: 4px;
                font-size: 12px;
            }
            .add-to-cart {
                font-size: 12px;
                padding: 6px 10px;
            }
            .hover-icons a {
                width: 32px;
                height: 32px;
                font-size: 1em;
            }
        }
        @media (max-width: 375px) {
            .product-image {
                height: 140px;
            }
            .product-info h5 {
                font-size: 0.95em;
                height: auto;
            }
            .current-price {
                font-size: 14px;
            }
            .add-to-cart-form input[type="number"] {
                width: 55px;
                padding: 4px;
                font-size: 12px;
            }
            .add-to-cart {
                font-size: 12px;
                padding: 6px 8px;
            }
            .hover-icons a {
                width: 30px;
                height: 30px;
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>
<div class="container">
    <h1>Sản Phẩm</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $discount = $product['original_price'] > $product['current_price'] && $product['original_price'] > 0
                ? round(($product['original_price'] - $product['current_price']) / $product['original_price'] * 100)
                : 0;
            ?>
            <div class="product-card">
                <a href="/2/public/pages/<?php echo htmlspecialchars($product['slug'], ENT_QUOTES); ?>" class="product-link"></a>
                <?php if ($discount > 0): ?>
                    <span class="discount-badge"><?php echo $discount; ?>% OFF</span>
                <?php endif; ?>
                <div class="product-image">
                    <img src="<?php echo $product['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($product['image'], ENT_QUOTES) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
                    <div class="hover-icons">
                        <a href="/2/public/pages/<?php echo htmlspecialchars($product['slug'], ENT_QUOTES); ?>" title="Xem chi tiết" onclick="event.stopPropagation();"><i class="fas fa-eye"></i></a>
                        <a href="#" title="Yêu thích" onclick="event.stopPropagation();"><i class="fas fa-heart"></i></a>
                        <a href="#" title="So sánh" onclick="event.stopPropagation();"><i class="fas fa-balance-scale"></i></a>
                    </div>
                </div>
                <div class="product-info">
                    <h5><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h5>
                    <div class="product-price">
                        <?php if ($product['original_price'] > $product['current_price']): ?>
                            <span class="original-price"><?php echo number_format($product['original_price'], 0, ',', '.'); ?>đ</span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo number_format($product['current_price'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="stock-status <?php echo $product['stock'] > 0 ? 'in' : 'out'; ?>">
                        <?php echo $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng'; ?>
                    </div>
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <form id="cart-form-<?php echo $product['id']; ?>" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="form-control">
                        <button type="submit" name="add_to_cart" class="add-to-cart" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-cart"></i> Mua ngay
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>

<!-- JavaScript xử lý AJAX và SweetAlert2 -->
<script>
$(document).ready(function() {
    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault(); // Ngăn hành vi submit mặc định
        var form = $(this);
        var formData = form.serialize(); // Lấy dữ liệu form

        $.ajax({
            url: '/2/public/pages/product.php',
            type: 'POST',
            data: formData + '&add_to_cart=1', // Thêm tham số add_to_cart
            dataType: 'json',
            success: function(result) {
                Swal.fire({
                    icon: result.success ? 'success' : 'error',
                    title: result.success ? 'Thành công' : 'Lỗi',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (result.success) {
                        window.location.href = 'http://localhost/2/public/pages/cart.php';
                    }
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Lỗi AJAX: ' + xhr.responseText,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>

</body>
</html>
<?php ob_end_flush(); ?>