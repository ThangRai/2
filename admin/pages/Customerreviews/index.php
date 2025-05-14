<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
// $stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
// $stmt->execute([$_SESSION['admin_id']]);
// $admin = $stmt->fetch(PDO::FETCH_ASSOC);

// $allowed_roles = [1, 2]; // super_admin (1), staff (2)
// if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
//     error_log('Từ chối truy cập cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
//     $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
//     echo '<script>window.location.href="index.php?page=dashboard";</script>';
//     exit;
// }

// Debug session
error_log('Dữ liệu session: ' . print_r($_SESSION, true));

// Phân trang
$limit = 5;
$page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page_num - 1) * $limit;

// Bộ lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$params = [];
$sql = "SELECT * FROM customer_reviews WHERE 1=1";

if ($search) {
    $where[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng số ý kiến
$count_sql = "SELECT COUNT(*) FROM customer_reviews WHERE 1=1";
if ($where) {
    $count_sql .= " AND " . implode(" AND ", $where);
}
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_reviews = $count_stmt->fetchColumn();
$total_pages = ceil($total_reviews / $limit);
?>

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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Ý kiến khách hàng</h1>
    <a href="?page=customerreviews&subpage=add" class="btn btn-primary">Thêm ý kiến mới</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách ý kiến khách hàng</h6>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="page" value="customerreviews">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Lọc</button>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách ý kiến -->
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh đại diện</th>
                        <th>Tên</th>
                        <th>Số sao</th>
                        <th>Mô tả</th>
                        <th>Nội dung</th>
                        <th>Hiển thị</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review_item): ?>
                        <tr>
                            <td><?php echo $review_item['id']; ?></td>
                            <td>
                                <?php if ($review_item['avatar']): ?>
                                    <img src="http://localhost/2/admin/uploads/dgkhachhang/<?php echo htmlspecialchars($review_item['avatar']); ?>" alt="Avatar" style="max-width: 50px; max-height: 50px;">
                                <?php else: ?>
                                    Không có
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($review_item['name']); ?></td>
                            <td><?php echo $review_item['rating']; ?> sao</td>
                            <td><?php echo htmlspecialchars($review_item['description']); ?></td>
                            <td><?php echo htmlspecialchars(substr($review_item['content'], 0, 50)) . (strlen($review_item['content']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo $review_item['is_visible'] ? 'Có' : 'Không'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($review_item['created_at'])); ?></td>
                            <td>
                                <a href="?page=customerreviews&subpage=edit&id=<?php echo $review_item['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?page=customerreviews&subpage=delete&id=<?php echo $review_item['id']; ?>" class="btn btn-danger btn-sm delete-btn" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa ý kiến này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page_num > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=customerreviews&page_num=<?php echo $page_num - 1; ?>&search=<?php echo urlencode($search); ?>">Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page_num ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=customerreviews&page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page_num < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=customerreviews&page_num=<?php echo $page_num + 1; ?>&search=<?php echo urlencode($search); ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>