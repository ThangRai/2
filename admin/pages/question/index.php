<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Hàm ghi log hoạt động
function logActivity($pdo, $admin_id, $role_id, $action, $page, $target_id = null, $details = null) {
    $role_id = $role_id ?? 0; // Gán role_id mặc định nếu null
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (admin_id, role_id, action, page, target_id, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$admin_id, $role_id, $action, $page, $target_id, $details, $ip_address]);
    } catch (Exception $e) {
        error_log("Lỗi ghi log vào activity_logs: " . $e->getMessage());
    }
}

// Kiểm tra session
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Phiên đăng nhập không hợp lệ. Vui lòng đăng nhập lại.'];
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Kiểm tra quyền truy cập
$stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed_roles = [1, 2, 3, 4]; // super_admin (1), staff (2), admin (4)
if (!$admin || !isset($admin['role_id']) || !in_array($admin['role_id'], $allowed_roles)) {
    logActivity($pdo, $_SESSION['admin_id'] ?? 0, $admin['role_id'] ?? 0, 'Truy cập bị từ chối', 'question', null, 'Admin ID: ' . ($_SESSION['admin_id'] ?? 'không có') . ', Role ID: ' . ($admin['role_id'] ?? 'không có'));
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này.'];
    echo '<script>window.location.href="index.php?page=dashboard";</script>';
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$faq = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
    $stmt->execute([$id]);
    $faq = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$faq) {
        logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Truy cập FAQ không tồn tại', 'question', $id);
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Câu hỏi không tồn tại.'];
        echo '<script>window.location.href="?page=question";</script>';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== 'duplicate') {
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');
    $seo_keywords = trim($_POST['seo_keywords'] ?? '');

    $errors = [];
    if (empty($question)) $errors[] = 'Câu hỏi không được để trống.';
    if (empty($answer)) $errors[] = 'Câu trả lời không được để trống.';
    if (!empty($seo_title) && strlen($seo_title) > 255) {
        $errors[] = 'Tiêu đề SEO không được vượt quá 255 ký tự.';
    }
    if (!empty($seo_description) && strlen($seo_description) > 160) {
        $errors[] = 'Mô tả SEO không được vượt quá 160 ký tự.';
    }

    if (empty($errors)) {
        try {
            if ($action === 'edit' && isset($_POST['id'])) {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, is_published = ?, seo_title = ?, seo_description = ?, seo_keywords = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$question, $answer, $is_published, $seo_title, $seo_description, $seo_keywords, $id]);
                logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Sửa FAQ', 'question', $id, 'Câu hỏi: ' . substr($question, 0, 100));
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật câu hỏi thành công.'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, is_published, seo_title, seo_description, seo_keywords, created_at) 
                                       VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$question, $answer, $is_published, $seo_title, $seo_description, $seo_keywords]);
                $new_id = $pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Thêm FAQ', 'question', $new_id, 'Câu hỏi: ' . substr($question, 0, 100));
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm câu hỏi thành công.'];
            }
            echo '<script>window.location.href="?page=question";</script>';
            exit;
        } catch (Exception $e) {
            logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Lỗi khi lưu FAQ', 'question', null, 'Lỗi: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lưu câu hỏi: ' . $e->getMessage()];
        }
    } else {
        logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Lỗi xác thực FAQ', 'question', null, 'Lỗi: ' . implode(', ', $errors));
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
}

if ($action === 'duplicate' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt->execute([$id]);
        $faq = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($faq) {
            $new_question = $faq['question'] . ' (Sao chép)';
            $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, is_published, seo_title, seo_description, seo_keywords, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $new_question,
                $faq['answer'],
                $faq['is_published'],
                $faq['seo_title'],
                $faq['seo_description'],
                $faq['seo_keywords']
            ]);
            $new_id = $pdo->lastInsertId();
            logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Nhân bản FAQ', 'question', $new_id, 'FAQ gốc ID: ' . $id);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Nhân bản câu hỏi thành công.'];
        } else {
            logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Lỗi nhân bản FAQ', 'question', $id, 'FAQ không tồn tại');
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Câu hỏi không tồn tại.'];
        }
    } catch (Exception $e) {
        logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Lỗi nhân bản FAQ', 'question', $id, 'Lỗi: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi nhân bản câu hỏi: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=question";</script>';
    exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
        $stmt->execute([$id]);
        logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Xóa FAQ', 'question', $id);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa câu hỏi thành công.'];
    } catch (Exception $e) {
        logActivity($pdo, $_SESSION['admin_id'], $admin['role_id'], 'Lỗi xóa FAQ', 'question', $id, 'Lỗi: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa câu hỏi: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=question";</script>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'edit' ? 'Sửa câu hỏi' : ($action === 'add' ? 'Thêm câu hỏi' : 'Quản lý câu hỏi thường gặp'); ?></title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .btn-secondary { background: #6c757d; border: none; }
        textarea { resize: vertical; min-height: 200px; }
        .answer-column { max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        @media (max-width: 768px) {
            .form-group label, .form-group input, .form-group select, .form-group textarea { font-size: 0.9em; }
            .btn { font-size: 0.9em; }
            .answer-column { max-width: 150px; }
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

    <?php if ($action === 'list'): ?>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Câu hỏi thường gặp</h1>
            <a href="?page=question&action=add" class="btn btn-primary">Thêm câu hỏi</a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách câu hỏi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Câu hỏi</th>
                                <th>Câu trả lời</th>
                                <th>Hiển thị</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM faqs ORDER BY created_at DESC");
                            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($faqs as $index => $item):
                                $answer = strip_tags($item['answer']);
                                $answer = strlen($answer) > 100 ? substr($answer, 0, 100) . '...' : $answer;
                            ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['question']); ?></td>
                                    <td class="answer-column"><?php echo htmlspecialchars($answer); ?></td>
                                    <td><i class="fas <?php echo $item['is_published'] ? 'fa-eye' : 'fa-eye-slash'; ?>" title="<?php echo $item['is_published'] ? 'Hiển thị' : 'Ẩn'; ?>"></i></td>
                                    <td>
                                        <a href="?page=question&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                        <a href="?page=question&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');"><i class="fas fa-trash"></i> Xóa</a>
                                        <a href="?page=question&action=duplicate&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-copy"></i> Nhân bản</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $action === 'edit' ? 'Sửa câu hỏi' : 'Thêm câu hỏi'; ?></h1>
            <a href="?page=question" class="btn btn-secondary">Hủy</a>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $action === 'edit' ? 'Sửa câu hỏi' : 'Thêm câu hỏi'; ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="question">Câu hỏi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="question" name="question" value="<?php echo isset($faq) ? htmlspecialchars($faq['question']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="answer">Câu trả lời <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="answer" name="answer" rows="8" required><?php echo isset($faq) ? htmlspecialchars($faq['answer']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published" <?php echo (isset($faq) && $faq['is_published']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_published">Hiển thị</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="seo_title">Tiêu đề SEO (tối đa 255 ký tự)</label>
                                <input type="text" class="form-control" id="seo_title" name="seo_title" maxlength="255" value="<?php echo isset($faq) ? htmlspecialchars($faq['seo_title']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="seo_description">Mô tả SEO (tối đa 160 ký tự)</label>
                                <textarea class="form-control" id="seo_description" name="seo_description" rows="3" maxlength="160"><?php echo isset($faq) ? htmlspecialchars($faq['seo_description']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="seo_keywords">Từ khóa SEO (phân cách bằng dấu phẩy)</label>
                                <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo isset($faq) ? htmlspecialchars($faq['seo_keywords']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="?page=question" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>