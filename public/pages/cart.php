<?php
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php'; // Kết nối database

// Xử lý xóa sản phẩm khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $product_id = intval($_POST['product_id']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        header("Location: http://localhost/2/public/pages/cart.php");
        exit();
    }
}

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if (isset($_SESSION['cart'][$product_id]) && $quantity > 0) {
        if ($quantity <= $_SESSION['cart'][$product_id]['stock']) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            $_SESSION['success'] = 'Cập nhật số lượng thành công!';
        } else {
            $_SESSION['error'] = 'Số lượng vượt quá tồn kho!';
        }
    } else {
        $_SESSION['error'] = 'Số lượng không hợp lệ!';
    }
    header("Location: http://localhost/2/public/pages/cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #f8f9fa, #dfe6e9);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        h2 {
            text-align: center;
            color: #000;
            margin-bottom: 40px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .product-img {
            max-width: 50px;
            height: auto;
        }
        .btn-remove {
            background: #ff4757;
            color: #fff;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn-remove:hover {
            background: #e63946;
        }
        .btn-update {
            background: #0984e3;
            color: #fff;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn-update:hover {
            background: #0652dd;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        .cart-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-continue, .btn-checkout {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            text-decoration: none;
            color: #fff;
            display: inline-block;
            text-align: center;
        }
        .btn-continue {
            background: #6c757d;
        }
        .btn-continue:hover {
            background: #5a6268;
            transform: scale(1.02);
        }
        .btn-checkout {
            background: #28a745;
        }
        .btn-checkout:hover {
            background: #218838;
            transform: scale(1.02);
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

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .cart-table {
                display: block;
                overflow-x: auto;
            }
            th, td {
                font-size: 0.9em;
                padding: 8px;
            }
            .product-img {
                max-width: 40px;
            }
            .quantity-input {
                width: 50px;
            }
            .btn-remove, .btn-update {
                padding: 5px 10px;
                font-size: 0.85em;
            }
            .cart-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-continue, .btn-checkout {
                width: 100%;
                padding: 12px;
                font-size: 1em;
            }
        }
        @media (max-width: 425px) {
            .container {
                margin: 20px auto;
                padding: 0 10px;
            }
            h2 {
                font-size: 1.5em;
            }
            th, td {
                font-size: 0.85em;
                padding: 6px;
            }
            .product-img {
                max-width: 30px;
            }
            .quantity-input {
                width: 45px;
            }
        }
    </style>
</head>
<body>
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>
    <div class="container">
        <h2>Giỏ hàng của bạn</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td><img src="<?php echo $item['image'] ? 'http://localhost/2/admin/' . htmlspecialchars($item['image']) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-img"></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                    <button type="submit" name="update_quantity" class="btn-update">Cập nhật</button>
                                </form>
                            </td>
                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                    <button type="submit" name="remove_from_cart" class="btn-remove">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"><strong>Tổng cộng</strong></td>
                        <td><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="cart-actions">
                <a href="http://localhost/2/public/pages/product.php" class="btn-continue">Tiếp tục mua hàng</a>
                <a href="http://localhost/2/public/pages/checkout.php" class="btn-checkout">Thanh toán</a>
            </div>
        <?php else: ?>
            <p class="error">Giỏ hàng của bạn đang trống.</p>
            <div class="cart-actions">
                <a href="http://localhost/2/public/" class="btn-continue">Tiếp tục mua hàng</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?php require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>

</body>
</html>