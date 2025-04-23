<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra subpage và ID
if (!isset($_GET['subpage']) || $_GET['subpage'] !== 'delete' || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Yêu cầu không hợp lệ.'];
    echo '<script>window.location.href="?page=blog";</script>';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT title FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$blog) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bài viết không tồn tại.'];
    echo '<script>window.location.href="?page=blog";</script>';
    exit;
}

// Xử lý xóa khi xác nhận
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        error_log('Deleted blog ID: ' . $id . ', Rows affected: ' . $stmt->rowCount());

        if ($stmt->rowCount() === 0) {
            throw new Exception('Không có bài viết nào được xóa.');
        }

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa bài viết thành công.'];
        echo '<script>window.location.href="?page=blog";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Delete blog error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa bài viết: ' . $e->getMessage()];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa bài viết</title>
    <link href="/2/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/2/admin/css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .btn-secondary { background: #6c757d; border: none; }
        .btn-danger { background: #dc3545; border: none; }
        @media (max-width: 768px) {
            .form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }
            .btn { font-size: 0.9em; }
        }
    </style>
</head>
<body>
    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['message']['type']; ?>',
                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                html: '<?php echo htmlspecialchars($_SESSION['message']['text']); ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Xóa bài viết</h1>
        <a href="?page=blog" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Xác nhận xóa bài viết</h6>
        </div>
        <div class="card-body">
            <p>Bạn có chắc chắn muốn xóa bài viết <strong><?php echo htmlspecialchars($blog['title']); ?></strong>? Hành động này không thể hoàn tác.</p>
            <form method="POST">
                <button type="submit" class="btn btn-danger">Xóa</button>
                <a href="?page=blog" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>

    <script src="/2/admin/vendor/jquery/jquery.min.js"></script>
    <script src="/2/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/js/sb-admin-2.min.js"></script>
</body>
</html>