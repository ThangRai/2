<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy sản phẩm để chỉnh sửa.'];
    header("Location: ?page=products");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sản phẩm không tồn tại.'];
    header("Location: ?page=products");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = (int)$_POST['category_id'];
    $content = $_POST['content'];
    $description = $_POST['description'];
    $original_price = (float)$_POST['original_price'];
    $current_price = (float)$_POST['current_price'];
    $stock = (int)$_POST['stock'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, content = ?, description = ?, image = ?, original_price = ?, current_price = ?, stock = ?, is_active = ? WHERE id = ?");
    $stmt->execute([$name, $category_id, $content, $description, $image, $original_price, $current_price, $stock, $is_active, $id]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật sản phẩm thành công.'];
    // Chuyển hướng bằng JS nếu header thất bại
    echo '<script>window.location.href="?page=products";</script>';
    exit;
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
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Sửa sản phẩm</h1>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" novalidate>
                                <div class="form-group">
                                    <label for="name">Tên sản phẩm</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="category_id">Danh mục</label>
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
                                    <label for="content">Nội dung</label>
                                    <textarea class="form-control" id="content" name="content" rows="4"><?php echo htmlspecialchars($product['content'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="image">Hình ảnh</label>
                                    <?php if (!empty($product['image'])): ?>
                                        <div>
                                            <img src="/2/admin/<?php echo htmlspecialchars($product['image']); ?>" alt="Hình ảnh hiện tại" style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                </div>
                                <div class="form-group">
                                    <label for="original_price">Giá gốc (VND)</label>
                                    <input type="number" step="1000" min="0" class="form-control" id="original_price" name="original_price" value="<?php echo (int)$product['original_price']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="current_price">Giá hiện tại (VND)</label>
                                    <input type="number" step="1000" min="0" class="form-control" id="current_price" name="current_price" value="<?php echo (int)$product['current_price']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stock">Tồn kho</label>
                                    <input type="number" min="0" class="form-control" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="is_active">Trạng thái</label>
                                    <input type="checkbox" id="is_active" name="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                    <label for="is_active">Kích hoạt</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="?page=products" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php ob_end_flush(); ?>