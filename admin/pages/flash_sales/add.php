<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$flash_sale = null;

if ($product_id) {
    $stmt = $pdo->prepare("SELECT * FROM flash_sales WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $flash_sale = $stmt->fetch(PDO::FETCH_ASSOC);
}

$products = $pdo->query("SELECT id, name FROM products WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $sale_price = (float)$_POST['sale_price'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($flash_sale) {
        // Cập nhật Flash Sale
        $stmt = $pdo->prepare("UPDATE flash_sales SET sale_price = ?, start_time = ?, end_time = ?, is_active = ? WHERE product_id = ?");
        $stmt->execute([$sale_price, $start_time, $end_time, $is_active, $product_id]);
    } else {
        // Thêm mới Flash Sale
        $stmt = $pdo->prepare("INSERT INTO flash_sales (product_id, sale_price, start_time, end_time, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $sale_price, $start_time, $end_time, $is_active]);
    }

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Flash Sale đã được lưu thành công.'];
    echo '<script>window.location.href="?page=products";</script>';
    exit;
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $flash_sale ? 'Sửa Flash Sale' : 'Thêm Flash Sale'; ?></h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label for="product_id">Sản phẩm</label>
                <select name="product_id" class="form-control" required>
                    <option value="">Chọn sản phẩm</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo $product_id == $product['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="sale_price">Giá Flash Sale (VND)</label>
                <input type="number" name="sale_price" class="form-control" value="<?php echo $flash_sale ? $flash_sale['sale_price'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="start_time">Thời gian bắt đầu</label>
                <input type="datetime-local" name="start_time" class="form-control" value="<?php echo $flash_sale ? date('Y-m-d\TH:i', strtotime($flash_sale['start_time'])) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="end_time">Thời gian kết thúc</label>
                <input type="datetime-local" name="end_time" class="form-control" value="<?php echo $flash_sale ? date('Y-m-d\TH:i', strtotime($flash_sale['end_time'])) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" <?php echo $flash_sale && $flash_sale['is_active'] ? 'checked' : ''; ?>> Kích hoạt
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>
</div>