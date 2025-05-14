<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID thuộc tính không hợp lệ.'];
    header("Location: /2/admin/index.php?page=products&subpage=manage_attributes");
    exit;
}

$id = (int)$_GET['id'];

// Lấy thông tin thuộc tính
$stmt = $pdo->prepare("SELECT id, name FROM product_attributes WHERE id = ?");
$stmt->execute([$id]);
$attribute = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attribute) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Thuộc tính không tồn tại.'];
    header("Location: /2/admin/index.php?page=products&subpage=manage_attributes");
    exit;
}

// Xử lý form chỉnh sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Kiểm tra lỗi
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Tên thuộc tính không được để trống.';
    }

    // Kiểm tra trùng tên thuộc tính
    $stmt = $pdo->prepare("SELECT id FROM product_attributes WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Tên thuộc tính đã tồn tại.';
    }

    if (empty($errors)) {
        try {
            // Cập nhật thuộc tính
            $stmt = $pdo->prepare("UPDATE product_attributes SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật thuộc tính thành công.'];
            echo '<script>window.location.href="?page=attributes";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Edit Attribute error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật thuộc tính: ' . $e->getMessage()];
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
    <title>Chỉnh sửa thuộc tính</title>
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
            .form-group label, .form-group input {
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
                <div class="container-fluid">
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
                        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa thuộc tính</h1>
                        <a href="?page=products&subpage=manage_attributes" class="btn btn-secondary">Hủy</a>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chỉnh sửa thuộc tính</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="name">Tên thuộc tính <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($attribute['name']); ?>" required>
                                    <small class="form-text text-muted">Ví dụ: Màu sắc, Kích thước</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="?page=products&subpage=manage_attributes" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/2/admin/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/2/admin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
</body>
</html>