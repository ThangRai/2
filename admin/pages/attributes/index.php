<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

$attributes = $pdo->query("SELECT * FROM product_attributes")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO product_attributes (name) VALUES (?)");
        $stmt->execute([$name]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Thuộc tính đã được thêm thành công.'];
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng nhập tên thuộc tính.'];
    }

    echo '<script>window.location.href="?page=products&subpage=manage_attributes";</script>';
    exit;
}
?>

<?php if (isset($_SESSION['message'])): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['message']['type']; ?>',
            title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
            text: '<?php echo htmlspecialchars($_SESSION['message']['text']); ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Quản lý thuộc tính sản phẩm</h6>
    </div>
    <div class="card-body">
        <!-- Form thêm thuộc tính -->
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="name">Tên thuộc tính (VD: Kích thước, Màu sắc)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Thêm thuộc tính</button>
        </form>

        <!-- Danh sách thuộc tính -->
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên thuộc tính</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attributes as $attribute): ?>
                        <tr>
                            <td><?php echo $attribute['id']; ?></td>
                            <td><?php echo htmlspecialchars($attribute['name']); ?></td>
                            <td>
                                <a href="?page=attributes&subpage=edit&id=<?php echo $attribute['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="pages/attributes/process_delete.php?id=<?php echo $attribute['id']; ?>" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa thuộc tính này không?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>