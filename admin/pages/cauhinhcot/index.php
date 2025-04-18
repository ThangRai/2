<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy cấu hình hiện tại
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'columns_%'");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }
} catch (Exception $e) {
    error_log('Config columns error: ' . $e->getMessage());
    $settings = [
        'columns_375' => 2,
        'columns_425' => 3,
        'columns_768' => 4,
        'columns_1200' => 5,
        'columns_max' => 6
    ];
}

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $columns_375 = (int)$_POST['columns_375'];
        $columns_425 = (int)$_POST['columns_425'];
        $columns_768 = (int)$_POST['columns_768'];
        $columns_1200 = (int)$_POST['columns_1200'];
        $columns_max = (int)$_POST['columns_max'];

        // Cập nhật settings
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value");
        $stmt->execute(['name' => 'columns_375', 'value' => $columns_375]);
        $stmt->execute(['name' => 'columns_425', 'value' => $columns_425]);
        $stmt->execute(['name' => 'columns_768', 'value' => $columns_768]);
        $stmt->execute(['name' => 'columns_1200', 'value' => $columns_1200]);
        $stmt->execute(['name' => 'columns_max', 'value' => $columns_max]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật cấu hình thành công'];
        echo '<script>window.location.href="?page=cauhinhcot";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Update columns error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Có lỗi khi cập nhật cấu hình: ' . $e->getMessage()];
        echo '<script>window.location.href="?page=cauhinhcot";</script>';
        exit;
    }
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cấu hình cột sản phẩm</h1>
</div>

<!-- Form cấu hình cột -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cấu hình số cột hiển thị sản phẩm</h6>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['message'])): ?>
            <script>
                // Kiểm tra Swal tồn tại
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: '<?php echo $_SESSION['message']['type']; ?>',
                        title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                        html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                        confirmButtonText: 'OK'
                    });
                } else {
                    console.error('SweetAlert2 not loaded');
                    alert('<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>: <?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>');
                }
            </script>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="columns_375" name="columns_375" value="<?php echo htmlspecialchars($settings['columns_375']); ?>" min="1" max="6" required>
                    </div>
                    <div class="form-group">
                        <label for="columns_425">Dưới 425px <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="columns_425" name="columns_425" value="<?php echo htmlspecialchars($settings['columns_425']); ?>" min="1" max="6" required>
                    </div>
                    <div class="form-group">
                        <label for="columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="columns_768" name="columns_768" value="<?php echo htmlspecialchars($settings['columns_768']); ?>" min="1" max="6" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="columns_1200" name="columns_1200" value="<?php echo htmlspecialchars($settings['columns_1200']); ?>" min="1" max="6" required>
                    </div>
                    <div class="form-group">
                        <label for="columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="columns_max" name="columns_max" value="<?php echo htmlspecialchars($settings['columns_max']); ?>" min="1" max="6" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
        </form>
    </div>
</div>