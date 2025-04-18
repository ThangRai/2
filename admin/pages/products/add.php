<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $content = $_POST['content'];
    $description = $_POST['description'];
    $original_price = $_POST['original_price'];
    $current_price = $_POST['current_price'];
    $stock = $_POST['stock'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, content, description, image, original_price, current_price, stock, is_active, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $category_id, $content, $description, $image, $original_price, $current_price, $stock, $is_active]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm sản phẩm thành công.'];
    header("Location: ?page=products");
    exit;
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Product</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Product</h6>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label for="original_price">Original Price</label>
                <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" required>
            </div>
            <div class="form-group">
                <label for="current_price">Current Price</label>
                <input type="number" step="0.01" class="form-control" id="current_price" name="current_price" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
                <label for="is_active">Status</label>
                <input type="checkbox" id="is_active" name="is_active" checked>
                <label for="is_active">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="?page=products" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>