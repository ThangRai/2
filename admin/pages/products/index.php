<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Redirecting to login.php: No admin_id in session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 3]; // super_admin (1), content_manager (3)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Access denied for admin_id: ' . ($_SESSION['admin_id'] ?? 'unknown') . ', role_id: ' . ($admin['role_id'] ?? 'none'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php";</script>';
    exit;
}

// Debug session
error_log('Session data: ' . print_r($_SESSION, true));

// Phân trang
$limit = 5;
$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page - 1) * $limit;

// Bộ lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where = [];
$params = [];
$sql = "SELECT p.* FROM products p WHERE 1=1";

if ($search) {
    $where[] = "p.name LIKE ?";
    $params[] = "%$search%";
}
if ($category_id) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
}
if ($status !== '') {
    $where[] = "p.is_active = ?";
    $params[] = (int)$status;
}

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng số sản phẩm
$count_sql = "SELECT COUNT(*) FROM products p WHERE 1=1";
if ($where) {
    $count_sql .= " AND " . implode(" AND ", $where);
}
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Lấy danh mục cho bộ lọc
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sản phẩm</h1>
    <a href="?page=products&subpage=add" class="btn btn-primary btn-sm">Thêm sản phẩm</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="page" value="products">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên sản phẩm..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>Kích hoạt</option>
                        <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>Không kích hoạt</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Lọc</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Giá gốc</th>
                        <th>Giá hiện tại</th>
                        <th>Mô tả</th>
                        <th>Nội dung</th>
                        <th>Hình ảnh</th>
                        <th>Giảm giá</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo number_format($product['original_price'], 0, ',', '.') . ' ₫'; ?></td>
                            <td><?php echo number_format($product['current_price'], 0, ',', '.') . ' ₫'; ?></td>
                            <td><?php echo htmlspecialchars(substr(strip_tags($product['description'] ?? ''), 0, 50)) . (strlen(strip_tags($product['description'] ?? '')) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars(substr(strip_tags($product['content'] ?? ''), 0, 50)) . (strlen(strip_tags($product['content'] ?? '')) > 50 ? '...' : ''); ?></td>
                            <td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($product['image']); ?>" alt="Hình ảnh sản phẩm" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    Không có hình
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if ($product['original_price'] > 0) {
                                    $discount = (($product['original_price'] - $product['current_price']) / $product['original_price']) * 100;
                                    echo number_format($discount, 0) . '%';
                                } else {
                                    echo '0%';
                                }
                                ?>
                            </td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <button class="btn btn-sm <?php echo $product['is_active'] ? 'btn-success' : 'btn-secondary'; ?> status-btn" data-id="<?php echo $product['id']; ?>" data-status="<?php echo $product['is_active'] ? 0 : 1; ?>">
                                    <?php echo $product['is_active'] ? 'Bật' : 'Tắt'; ?>
                                </button>
                            </td>
                            <td>
                                <a href="?page=products&subpage=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="pages/products/process_clone.php?id=<?php echo $product['id']; ?>" class="btn btn-info btn-sm" title="Sao chép">
                                    <i class="fas fa-copy"></i>
                                </a>
                                <a href="pages/products/process_delete.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm delete-btn" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này không?');">
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
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=products&page_num=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=products&page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=products&page_num=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&status=<?php echo $status; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX cho nút trạng thái -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log('jQuery loaded:', typeof $);
    console.log('Status buttons found:', $('.status-btn').length);

    $('.status-btn').on('click', function() {
        var button = $(this);
        var productId = button.data('id');
        var newStatus = button.data('status');

        console.log('Status button clicked: ID =', productId, 'New Status =', newStatus);

        $.ajax({
            url: '/2/admin/pages/products/update_status.php',
            type: 'POST',
            data: {
                id: productId,
                is_active: newStatus
            },
            dataType: 'json',
            success: function(result) {
                console.log('AJAX response:', result);
                if (result.success) {
                    button.text(newStatus == 1 ? 'Bật' : 'Tắt');
                    button.removeClass('btn-success btn-secondary');
                    button.addClass(newStatus == 1 ? 'btn-success' : 'btn-secondary');
                    button.data('status', newStatus == 1 ? 0 : 1);
                    console.log('Status updated successfully');
                } else {
                    console.log('Update failed:', result.message);
                    alert('Lỗi: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                alert('Lỗi AJAX: ' + xhr.responseText);
            }
        });
    });
});
</script>