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

$allowed_roles = [1, 2]; // super_admin (1), staff (2)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Access denied for admin_id: ' . ($_SESSION['admin_id'] ?? 'unknown') . ', role_id: ' . ($admin['role_id'] ?? 'none'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
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
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where = [];
$params = [];
$sql = "SELECT o.*, c.name AS customer_name 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.id 
        WHERE 1=1";

if ($search) {
    $where[] = "c.name LIKE ?";
    $params[] = "%$search%";
}
if ($status !== '') {
    $where[] = "o.status = ?";
    $params[] = $status;
}

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$sql .= " ORDER BY o.created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng số đơn hàng
$count_sql = "SELECT COUNT(*) 
              FROM orders o 
              JOIN customers c ON o.customer_id = c.id 
              WHERE 1=1";
if ($where) {
    $count_sql .= " AND " . implode(" AND ", $where);
}
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_orders = $count_stmt->fetchColumn();
$total_pages = ceil($total_orders / $limit);

// Lấy danh sách trạng thái
$statuses = ['pending' => 'Đang chờ', 'processing' => 'Đang xử lý', 'shipped' => 'Đã vận chuyển', 'delivered' => 'Đã giao', 'cancelled' => 'Đã hủy'];
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
    <h1 class="h3 mb-0 text-gray-800">Đơn hàng</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="page" value="orders">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên khách hàng..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $status === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
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
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo number_format($order['total_amount'], 0, ',', '.') . ' ₫'; ?></td>
                            <td>
                                <select class="form-control status-select" data-id="<?php echo $order['id']; ?>">
                                    <?php foreach ($statuses as $key => $label): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $order['status'] === $key ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="?page=orders&subpage=view&id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?page=orders&subpage=update&id=<?php echo $order['id']; ?>" class="btn btn-warning btn-sm" title="Cập nhật">
                                    <i class="fas fa-edit"></i>
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
                            <a class="page-link" href="?page=orders&page_num=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=orders&page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=orders&page_num=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX cho thay đổi trạng thái -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log('jQuery loaded:', typeof $);
    console.log('Status selects found:', $('.status-select').length);

    $('.status-select').on('change', function() {
        var select = $(this);
        var orderId = select.data('id');
        var newStatus = select.val();

        console.log('Status changed: ID =', orderId, 'New Status =', newStatus);

        $.ajax({
            url: '/2/admin/pages/orders/update_status.php',
            type: 'POST',
            data: {
                id: orderId,
                status: newStatus
            },
            dataType: 'json',
            success: function(result) {
                console.log('AJAX response:', result);
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Trạng thái đơn hàng đã được cập nhật',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: result.message,
                        confirmButtonText: 'OK'
                    });
                    select.val(select.data('original-status')); // Hoàn nguyên nếu lỗi
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error, 'Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể cập nhật trạng thái',
                    confirmButtonText: 'OK'
                });
                select.val(select.data('original-status')); // Hoàn nguyên nếu lỗi
            }
        });

        select.data('original-status', newStatus); // Lưu trạng thái hiện tại
    });

    // Lưu trạng thái ban đầu
    $('.status-select').each(function() {
        $(this).data('original-status', $(this).val());
    });
});
</script>