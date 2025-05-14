<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra session và quyền truy cập
// session_start();
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập.'];
    echo '<script>window.location.href="index.php?page=login";</script>';
    exit;
}

$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || $admin['role_id'] != 1) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

// Xử lý tìm kiếm và bộ lọc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
$action = isset($_GET['action']) ? trim($_GET['action']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

// Xử lý phân trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 10; // 10 bản ghi mỗi trang
$offset = ($page - 1) * $per_page;

// Tạo truy vấn SQL
$sql = "SELECT al.*, a.name, r.name as role_name
        FROM activity_logs al
        JOIN admins a ON al.admin_id = a.id
        LEFT JOIN roles r ON al.role_id = r.id
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (LOWER(a.name) LIKE LOWER(?) OR LOWER(al.action) LIKE LOWER(?) OR LOWER(al.page) LIKE LOWER(?) OR LOWER(al.details) LIKE LOWER(?))";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($role_id) {
    $sql .= " AND al.role_id = ?";
    $params[] = $role_id;
}

if ($action) {
    $sql .= " AND al.action = ?";
    $params[] = $action;
}

if ($date_from) {
    $sql .= " AND al.created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
}

if ($date_to) {
    $sql .= " AND al.created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
}

// Đếm tổng số bản ghi
$count_sql = "SELECT COUNT(*) as total FROM activity_logs al
              JOIN admins a ON al.admin_id = a.id
              LEFT JOIN roles r ON al.role_id = r.id
              WHERE 1=1";
$count_params = [];

if ($search) {
    $count_sql .= " AND (LOWER(a.name) LIKE LOWER(?) OR LOWER(al.action) LIKE LOWER(?) OR LOWER(al.page) LIKE LOWER(?) OR LOWER(al.details) LIKE LOWER(?))";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
}

if ($role_id) {
    $count_sql .= " AND al.role_id = ?";
    $count_params[] = $role_id;
}

if ($action) {
    $count_sql .= " AND al.action = ?";
    $count_params[] = $action;
}

if ($date_from) {
    $count_sql .= " AND al.created_at >= ?";
    $count_params[] = $date_from . ' 00:00:00';
}

if ($date_to) {
    $count_sql .= " AND al.created_at <= ?";
    $count_params[] = $date_to . ' 23:59:59';
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($count_params);
$total_logs = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_logs / $per_page);

// Lấy danh sách log
$sql .= " ORDER BY al.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
foreach ($params as $index => $param) {
    $stmt->bindValue($index + 1, $param);
}
$stmt->bindValue(count($params) + 1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách vai trò
$role_stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $role_stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách hành động
$action_stmt = $pdo->query("SELECT DISTINCT action FROM activity_logs");
$actions = $action_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch sử hoạt động</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
        .filter-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .filter-form .form-control,
        .filter-form .btn {
            border-radius: 25px;
            height: 38px;
        }
        .btn-search {
            background: linear-gradient(45deg, #007bff, #00aaff);
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-search:hover {
            background: linear-gradient(45deg, #0056b3, #0088cc);
        }
        .btn-reset {
            background: linear-gradient(45deg, #6c757d, #8a959f);
            border: none;
            color: #fff;
            transition: background 0.3s ease;
        }
        .btn-reset:hover {
            background: linear-gradient(45deg, #5a6268, #787f87);
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .page-link {
            border-radius: 50%;
            margin: 0 5px;
            color: #007bff;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .page-link:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .page-item.active .page-link {
            background: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
        }
        @media (max-width: 768px) {
            .table {
                font-size: 0.85em;
            }
            .filter-form .form-group {
                margin-bottom: 15px;
            }
            .filter-form .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            .filter-form .col-md-4, .filter-form .col-md-2 {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
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
            <h1 class="h3 mb-0 text-gray-800">Lịch sử hoạt động</h1>
        </div>

        <!-- Form tìm kiếm và bộ lọc -->
        <div class="filter-form">
            <form method="GET" action="">
                <input type="hidden" name="page" value="activity_logs">
                <div class="row">
                    <!-- Tìm kiếm -->
                    <div class="col-md-4 col-sm-12 form-group">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm nhân viên, hành động, trang, chi tiết..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <!-- Bộ lọc vai trò -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <select name="role_id" class="form-control">
                            <option value="0">Tất cả vai trò</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>" <?php echo $role_id == $role['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Bộ lọc hành động -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <select name="action" class="form-control">
                            <option value="">Tất cả hành động</option>
                            <?php foreach ($actions as $act): ?>
                                <option value="<?php echo htmlspecialchars($act); ?>" <?php echo $action == $act ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($act); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Bộ lọc ngày -->
                    <div class="col-md-2 col-sm-6 form-group">
                        <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="col-md-2 col-sm-6 form-group">
                        <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 col-sm-12">
                        <button type="submit" class="btn btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </div>
                    <div class="col-md-6 col-sm-12 text-md-right">
                        <a href="index.php?page=activity_logs" class="btn btn-reset"><i class="fas fa-sync-alt"></i> Đặt lại</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bảng lịch sử hoạt động -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách hoạt động</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Nhân viên</th>
                                <th>Vai trò</th>
                                <th>Hành động</th>
                                <th>Trang</th>
                                <th>ID bản ghi</th>
                                <th>Chi tiết</th>
                                <th>Thời gian</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Không tìm thấy bản ghi nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $index => $log): ?>
                                    <tr>
                                        <td><?php echo $offset + $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($log['name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($log['role_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($log['action'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($log['page'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($log['target_id'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($log['details'] ?? ''); ?></td>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=activity_logs&p=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&action=<?php echo urlencode($action); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=activity_logs&p=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&action=<?php echo urlencode($action); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=activity_logs&p=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role_id=<?php echo $role_id; ?>&action=<?php echo urlencode($action); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug trạng thái form
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submitted with values:', {
                search: document.querySelector('input[name="search"]').value,
                role_id: document.querySelector('select[name="role_id"]').value,
                action: document.querySelector('select[name="action"]').value,
                date_from: document.querySelector('input[name="date_from"]').value,
                date_to: document.querySelector('input[name="date_to"]').value
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>