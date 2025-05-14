<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "WHERE title LIKE ?" : '';
$sql = "SELECT * FROM blogs $search_query ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
if ($search) {
    $stmt->execute(["%$search%"]);
} else {
    $stmt->execute();
}
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý xóa bài viết
$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa bài viết thành công.'];
    } catch (Exception $e) {
        error_log('Delete blog error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa bài viết: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=blog";</script>';
    exit;
}
?>

<!-- Hiển thị thông báo -->
<?php if (isset($_SESSION['message'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
<!-- 
<link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
<link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet"> -->
<style>
    .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
    .form-group { margin-bottom: 1.5rem; }
    .btn-secondary { background: #6c757d; border: none; }
    @media (max-width: 768px) {
        .form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }
        .btn { font-size: 0.9em; }
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản lý Blog</h1>
</div>

<!-- Form tìm kiếm -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm bài viết</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="?page=blog">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm theo tiêu đề...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Tìm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Nút thêm bài viết -->
<div class="mb-4">
    <a href="?page=blog&subpage=add" class="btn btn-primary">Thêm bài viết</a>
</div>

<!-- Danh sách bài viết -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách bài viết</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Mô tả</th>
                        <th>Lượt xem</th>
                        <th>Ảnh đại diện</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $blog): ?>
                        <tr>
                            <td><?php echo $blog['id']; ?></td>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                            <td><?php echo htmlspecialchars($blog['description']); ?></td>
                            <td><?php echo $blog['views']; ?></td>
                            <td>
                                <?php if ($blog['thumbnail']): ?>
                                    <img src="/2/admin/<?php echo $blog['thumbnail']; ?>" width="50" alt="Thumbnail">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $blog['is_published'] ? 'Hiển thị' : 'Ẩn'; ?></td>
                            <td>
                                <a href="?page=blog&subpage=edit&id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-primary" title="Sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="?page=blog&subpage=delete&id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa bài viết này?');" title="Xóa">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
