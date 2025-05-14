<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra ID Flash Sale
if (!isset($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy Flash Sale để chỉnh sửa.'];
    echo '<script>window.location.href="?page=flash_sales";</script>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT fs.*, p.name AS product_name 
                       FROM flash_sales fs 
                       JOIN products p ON fs.product_id = p.id 
                       WHERE fs.id = ?");
$stmt->execute([$id]);
$flash_sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flash_sale) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Flash Sale không tồn tại.'];
    echo '<script>window.location.href="?page=flash_sales";</script>';
    exit;
}

// Lấy danh sách sản phẩm
$products = $pdo->query("SELECT id, name FROM products WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $sale_price = (float)$_POST['sale_price'];
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Kiểm tra lỗi
    $errors = [];
    if (empty($product_id)) $errors[] = 'Vui lòng chọn sản phẩm.';
    if ($sale_price <= 0) $errors[] = 'Giá Flash Sale phải lớn hơn 0.';
    if (empty($start_time) || empty($end_time)) $errors[] = 'Vui lòng nhập thời gian bắt đầu và kết thúc.';
    if (strtotime($end_time) <= strtotime($start_time)) $errors[] = 'Thời gian kết thúc phải sau thời gian bắt đầu.';
    
    // Kiểm tra giá Flash Sale so với giá hiện tại của sản phẩm
    $stmt = $pdo->prepare("SELECT current_price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sale_price >= $product['current_price']) {
        $errors[] = 'Giá Flash Sale phải nhỏ hơn giá hiện tại của sản phẩm.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE flash_sales SET product_id = ?, sale_price = ?, start_time = ?, end_time = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$product_id, $sale_price, $start_time, $end_time, $is_active, $id]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật Flash Sale thành công.'];
            echo '<script>window.location.href="?page=flash_sales";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Edit Flash Sale error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật Flash Sale: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Flash Sale</title>
    <link rel="stylesheet" href="/2/admin/assets/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="/2/admin/assets/vendor/fontawesome/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 1.5rem;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        @media (max-width: 768px) {
            .form-group label, .form-group input, .form-group select {
                font-size: 0.9em;
            }
            .btn {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                    <!-- Hiển thị thông báo SweetAlert2 -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script>
                            Swal.fire({
                                icon: '<?php echo $_SESSION['message']['type']; ?>',
                                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                                html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                                confirmButtonText: 'OK'
                            });
                        </script>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Sửa Flash Sale</h1>
                        <a href="?page=flash_sales" class="btn btn-secondary">Hủy</a>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa Flash Sale</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="product_id">Sản phẩm <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control" required>
                                        <option value="">Chọn sản phẩm</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" <?php echo $product['id'] == $flash_sale['product_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sale_price">Giá Flash Sale (VND) <span class="text-danger">*</span></label>
                                    <input type="number" step="1000" min="0" name="sale_price" id="sale_price" class="form-control" value="<?php echo (int)$flash_sale['sale_price']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="start_time">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($flash_sale['start_time'])); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_time">Thời gian kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($flash_sale['end_time'])); ?>" required>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $flash_sale['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Kích hoạt</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="?page=flash_sales" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <script src="/2/admin/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/2/admin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
<?php ob_end_flush(); ?>