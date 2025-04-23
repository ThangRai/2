<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2]; // super_admin (1), staff (2)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

// Phân trang
$limit = 5;
$page_num = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page_num - 1) * $limit;

// Bộ lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$params = [];
$sql = "SELECT * FROM partners WHERE 1=1";

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
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng số đối tác
$count_sql = "SELECT COUNT(*) FROM partners WHERE 1=1";
if ($where) {
    $count_sql .= " AND " . implode(" AND ", $where);
}
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_partners = $count_stmt->fetchColumn();
$total_pages = ceil($total_partners / $limit);
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
    <h1 class="h3 mb-0 text-gray-800">Quản lý đối tác</h1>
    <a href="?page=partner&subpage=add" class="btn btn-primary">Thêm đối tác mới</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách đối tác</h6>
    </div>
    <div class="card-body">
        <!-- Bộ lọc -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="page" value="partner">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Lọc</button>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách đối tác -->
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Link</th>
                        <th>Hiển thị</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partners as $partner): ?>
                        <tr>
                            <td><?php echo $partner['id']; ?></td>
                            <td>
                                <?php if ($partner['logo']): ?>
                                    <img src="/2/admin/uploads/doitac/<?php echo htmlspecialchars($partner['logo']); ?>" alt="Logo" style="max-width: 100px; margin-top: 10px;">
                                    <?php else: ?>
                                    Không có
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($partner['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($partner['description'], 0, 50)) . (strlen($partner['description']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars($partner['link'] ?: 'Không có'); ?></td>
                            <td>
                                <input type="checkbox" class="visible-checkbox" data-id="<?php echo $partner['id']; ?>" <?php echo $partner['is_visible'] ? 'checked' : ''; ?>>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($partner['created_at'])); ?></td>
                            <td>
                                <a href="?page=partner&subpage=edit&id=<?php echo $partner['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?page=partner&subpage=delete&id=<?php echo $partner['id']; ?>" class="btn btn-danger btn-sm delete-btn" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa đối tác này?')">
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
                            <a class="page-link" href="?page=partner&page_num=<?php echo $page_num - 1; ?>&search=<?php echo urlencode($search); ?>">Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page_num ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=partner&page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page_num < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=partner&page_num=<?php echo $page_num + 1; ?>&search=<?php echo urlencode($search); ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('.visible-checkbox').on('change', function() {
        var id = $(this).data('id');
        var is_visible = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: 'partner.php',
            type: 'POST',
            data: {
                action: 'update_visible',
                id: id,
                is_visible: is_visible
            },
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: response.status,
                    title: response.status === 'success' ? 'Thành công' : 'Lỗi',
                    text: response.message,
                    confirmButtonText: 'OK'
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể kết nối đến server.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>
