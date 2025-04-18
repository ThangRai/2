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
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2]; // super_admin (1), staff (2)
if (!$admin || !in_array($admin['role_id'], $allowed_roles)) {
    error_log('Từ chối truy cập cho admin_id: ' . ($_SESSION['admin_id'] ?? 'không xác định') . ', role_id: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

// Debug session
error_log('Dữ liệu session: ' . print_r($_SESSION, true));

// Xử lý tab 1: Cập nhật thông tin liên hệ
$contacts_info = $pdo->query("SELECT * FROM contacts_info LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contacts_info'])) {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $working_hours = trim($_POST['working_hours'] ?? '');

    $errors = [];
    if (empty($address)) $errors[] = 'Địa chỉ không được để trống';
    if (empty($phone)) $errors[] = 'Số điện thoại không được để trống';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
    if (empty($working_hours)) $errors[] = 'Giờ làm việc không được để trống';

    if (empty($errors)) {
        try {
            if ($contacts_info) {
                $stmt = $pdo->prepare("UPDATE contacts_info SET address = ?, phone = ?, email = ?, working_hours = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$address, $phone, $email, $working_hours, $contacts_info['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO contacts_info (address, phone, email, working_hours) VALUES (?, ?, ?, ?)");
                $stmt->execute([$address, $phone, $email, $working_hours]);
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật thông tin liên hệ thành công'];
            echo '<script>window.location.href="?page=lienhe";</script>';
            exit;
        } catch (Exception $e) {
            error_log('Lỗi cập nhật thông tin liên hệ: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật thông tin: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}

// Xử lý tab 3: Cập nhật mã iframe bản đồ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_map'])) {
    $map_iframe = trim($_POST['map_iframe'] ?? '');
    try {
        if ($contacts_info) {
            $stmt = $pdo->prepare("UPDATE contacts_info SET map_iframe = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$map_iframe ?: null, $contacts_info['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO contacts_info (map_iframe) VALUES (?)");
            $stmt->execute([$map_iframe ?: null]);
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật bản đồ thành công'];
        echo '<script>window.location.href="?page=lienhe";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Lỗi cập nhật bản đồ: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật bản đồ: ' . $e->getMessage()];
    }
}

// Tab 2: Danh sách liên hệ
$limit = 10;
$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$params = [];
$sql = "SELECT * FROM contacts WHERE 1=1";

if ($search) {
    $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tổng số liên hệ
$count_sql = "SELECT COUNT(*) FROM contacts WHERE 1=1";
if ($where) {
    $count_sql .= " AND " . implode(" AND ", $where);
}
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_contacts = $count_stmt->fetchColumn();
$total_pages = ceil($total_contacts / $limit);

// Xử lý xóa liên hệ
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $contact_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$contact_id]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa tin nhắn liên hệ thành công'];
        echo '<script>window.location.href="?page=lienhe#contacts-tab";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Lỗi xóa liên hệ: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa liên hệ: ' . $e->getMessage()];
    }
}
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
    <h1 class="h3 mb-0 text-gray-800">Quản lý liên hệ</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Cấu hình liên hệ</h6>
    </div>
    <div class="card-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="contactTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info" role="tab">Thông tin liên hệ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" href="#contacts" role="tab">Danh sách liên hệ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="map-tab" data-bs-toggle="tab" href="#map" role="tab">Bản đồ</a>
            </li>
        </ul>

        <!-- Tab nội dung -->
        <div class="tab-content mt-3" id="contactTabsContent">
            <!-- Tab 1: Thông tin liên hệ -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <form method="POST">
                    <input type="hidden" name="update_contacts_info" value="1">
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($contacts_info['address'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($contacts_info['phone'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($contacts_info['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="working_hours" class="form-label">Giờ làm việc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="working_hours" name="working_hours" value="<?php echo htmlspecialchars($contacts_info['working_hours'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>

            <!-- Tab 2: Danh sách liên hệ -->
            <div class="tab-pane fade" id="contacts" role="tabpanel">
                <!-- Bộ lọc -->
                <form method="GET" class="mb-4">
                    <input type="hidden" name="page" value="lienhe">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email, số điện thoại..." value="<?php echo htmlspecialchars($search); ?>">
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
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Nội dung</th>
                                <th>Ngày gửi</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><?php echo $contact['id']; ?></td>
                                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['phone'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . (strlen($contact['message']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                                    <td>
                                        <a href="?page=lienhe&action=delete&id=<?php echo $contact['id']; ?>" class="btn btn-danger btn-sm delete-btn" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=lienhe&page_num=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>#contacts-tab">Trước</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=lienhe&page_num=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>#contacts-tab"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=lienhe&page_num=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>#contacts-tab">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Tab 3: Bản đồ -->
            <div class="tab-pane fade" id="map" role="tabpanel">
                <form method="POST">
                    <input type="hidden" name="update_map" value="1">
                    <div class="mb-3">
                        <label for="map_iframe" class="form-label">Mã iframe Google Maps</label>
                        <textarea class="form-control" id="map_iframe" name="map_iframe" rows="5"><?php echo htmlspecialchars($contacts_info['map_iframe'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <?php if (!empty($contacts_info['map_iframe'])): ?>
                        <button type="button" class="btn btn-danger" onclick="if(confirm('Bạn có chắc muốn xóa bản đồ này?')) { document.getElementById('map_iframe').value = ''; this.form.submit(); }">Xóa</button>
                    <?php endif; ?>
                </form>
                <?php if (!empty($contacts_info['map_iframe'])): ?>
                    <div class="mt-3">
                        <h6>Xem trước bản đồ</h6>
                        <?php echo $contacts_info['map_iframe']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>