<?php
ob_start(); // Bắt đầu output buffering

require_once 'C:/laragon/www/2/admin/config/db_connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'C:/laragon/www/2/public/includes/PHPMailer/src/Exception.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/PHPMailer.php';
require 'C:/laragon/www/2/public/includes/PHPMailer/src/SMTP.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Hàm lấy cấu hình email từ bảng settings
function getEmailSettings($pdo) {
    $settings = [];
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name IN ('smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_from', 'smtp_from_name')");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }
    return $settings;
}

// Lấy danh sách roles để hiển thị trong form
$stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy action từ URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $admin_ids = $_POST['admin_ids'] ?? [];
    $subject = trim($_POST['subject'] ?? '');
    $message_content = trim($_POST['message_content'] ?? '');

    $errors = [];

    if (empty($admin_ids)) {
        $errors[] = 'Vui lòng chọn ít nhất một nhân viên';
    }
    if (empty($subject)) {
        $errors[] = 'Tiêu đề thông báo không được để trống';
    }
    if (empty($message_content)) {
        $errors[] = 'Nội dung thông báo không được để trống';
    }

    if (empty($errors)) {
        // Lấy danh sách admin được chọn
        $placeholders = implode(',', array_fill(0, count($admin_ids), '?'));
        $stmt = $pdo->prepare("SELECT id, name, email FROM admins WHERE id IN ($placeholders)");
        $stmt->execute($admin_ids);
        $selected_admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy cấu hình email từ bảng settings
        $email_settings = getEmailSettings($pdo);

        // Kiểm tra xem cấu hình có đầy đủ không
        $required_settings = ['smtp_host', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_from', 'smtp_from_name'];
        foreach ($required_settings as $key) {
            if (empty($email_settings[$key])) {
                $errors[] = "Thiếu cấu hình email: $key";
            }
        }

        if (empty($errors)) {
            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = 3; // Tăng mức debug
                $mail->Debugoutput = 'echo'; // In lỗi ra màn hình
                $mail->isSMTP();
                $mail->Host = $email_settings['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $email_settings['smtp_username'];
                $mail->Password = $email_settings['smtp_password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Mặc định ssl vì cổng 465
                $mail->Port = (int) $email_settings['smtp_port'];

                // Tạm thời tắt xác minh SSL (xóa sau khi cấu hình openssl.cafile)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                $mail->setFrom($email_settings['smtp_from'], $email_settings['smtp_from_name']);

                foreach ($selected_admins as $admin) {
                    $email = $admin['email'];
                    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Email không hợp lệ hoặc trống: " . ($admin['name'] ?? 'ID ' . $admin['id']);
                        continue;
                    }
                    $mail->addAddress($email, $admin['name']);
                    $mail->Subject = $subject;
                    $mail->Body = nl2br(htmlspecialchars($message_content));
                    $mail->isHTML(true);

                    if (!$mail->send()) {
                        $errors[] = "Không thể gửi email tới $email: " . $mail->ErrorInfo;
                    }
                    $mail->clearAddresses();
                }

                if (empty($errors)) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Gửi thông báo thành công'];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
                }
            } catch (Exception $e) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi gửi thông báo: ' . $mail->ErrorInfo];
                error_log(date('Y-m-d H:i:s') . " PHPMailer Error: " . $mail->ErrorInfo . "\n", 3, 'C:/laragon/logs/email_errors.log');
            }
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }

    echo '<script>window.location.href="?page=quantri&action=notify";</script>';
    exit;
}

// Xử lý thêm admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $two_factor_code = trim($_POST['two_factor_code'] ?? '');
    $role_id = trim($_POST['role_id'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Họ và Tên không được để trống';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    if (empty($username)) {
        $errors[] = 'Tên đăng nhập không được để trống';
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }
    if (!empty($phone) && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
    if (empty($role_id) || !is_numeric($role_id)) {
        $errors[] = 'Vui lòng chọn vai trò';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE id = ?");
        $stmt->execute([$role_id]);
        if (!$stmt->fetch()) {
            $errors[] = 'Vai trò không hợp lệ';
        }
    }

    $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $errors[] = 'Email hoặc tên đăng nhập đã tồn tại';
    }

    $avatar = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Ảnh đại diện chỉ hỗ trợ JPG, JPEG, PNG';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ảnh đại diện không được lớn hơn 2MB';
        } else {
            $avatar_name = 'admin_' . time() . '.' . $ext;
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_path = $upload_dir . $avatar_name;
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                $errors[] = 'Không thể tải lên ảnh đại diện';
            } else {
                $avatar = $upload_path;
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO admins (name, email, username, password, phone, address, avatar, two_factor_code, role_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $email, $username, $hashed_password, $phone ?: null, $address ?: null, $avatar, $two_factor_code ?: null, $role_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm admin thành công'];
        } catch (Exception $e) {
            error_log('Add admin error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi thêm admin: ' . $e->getMessage()];
        }
    }

    echo '<script>window.location.href="?page=quantri&action=add";</script>';
    exit;
}

// Xử lý sửa admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_admin'])) {
    $edit_id = trim($_POST['edit_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $two_factor_code = trim($_POST['two_factor_code'] ?? '');
    $role_id = trim($_POST['role_id'] ?? '');

    $errors = [];

    if (empty($edit_id) || !is_numeric($edit_id)) {
        $errors[] = 'ID admin không hợp lệ';
    }
    if ($edit_id == 1) {
        $errors[] = 'Không thể sửa tài khoản chính';
    }
    if (empty($name)) {
        $errors[] = 'Họ và Tên không được để trống';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    if (empty($username)) {
        $errors[] = 'Tên đăng nhập không được để trống';
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }
    if (!empty($phone) && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }
    if (empty($role_id) || !is_numeric($role_id)) {
        $errors[] = 'Vui lòng chọn vai trò';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE id = ?");
        $stmt->execute([$role_id]);
        if (!$stmt->fetch()) {
            $errors[] = 'Vai trò không hợp lệ';
        }
    }

    $stmt = $pdo->prepare("SELECT id FROM admins WHERE (email = ? OR username = ?) AND id != ?");
    $stmt->execute([$email, $username, $edit_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Email hoặc tên đăng nhập đã tồn tại';
    }

    $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
    $stmt->execute([$edit_id]);
    $current_admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$current_admin) {
        $errors[] = 'Admin không tồn tại';
    } else {
        $avatar = $current_admin['avatar'];
    }

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar'];
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Ảnh đại diện chỉ hỗ trợ JPG, JPEG, PNG';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ảnh đại diện không được lớn hơn 2MB';
        } else {
            $avatar_name = 'admin_' . $edit_id . '_' . time() . '.' . $ext;
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $upload_path = $upload_dir . $avatar_name;
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                if ($avatar && file_exists($avatar)) {
                    unlink($avatar);
                }
                $avatar = $upload_path;
            } else {
                $errors[] = 'Không thể tải lên ảnh đại diện';
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    } else {
        try {
            $sql = "UPDATE admins SET name = ?, email = ?, username = ?, phone = ?, address = ?, avatar = ?, two_factor_code = ?, role_id = ?";
            $params = [$name, $email, $username, $phone ?: null, $address ?: null, $avatar, $two_factor_code ?: null, $role_id];
            if (!empty($password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= " WHERE id = ?";
            $params[] = $edit_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật admin thành công'];
        } catch (Exception $e) {
            error_log('Edit admin error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật admin: ' . $e->getMessage()];
        }
    }

    echo '<script>window.location.href="?page=quantri&action=add";</script>';
    exit;
}

// Xử lý xóa admin
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    if ($delete_id == 1) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không thể xóa tài khoản chính'];
    } elseif ($delete_id != $_SESSION['admin_id']) {
        try {
            $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
            $stmt->execute([$delete_id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin['avatar'] && file_exists($admin['avatar'])) {
                unlink($admin['avatar']);
            }
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->execute([$delete_id]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa admin thành công'];
        } catch (Exception $e) {
            error_log('Delete admin error: ' . $e->getMessage());
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi xóa admin'];
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không thể xóa tài khoản của chính bạn'];
    }
    echo '<script>window.location.href="?page=quantri&action=list";</script>';
    exit;
}

// Lấy thông tin admin để sửa
$edit_admin = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit']) && !isset($_POST['add_admin']) && !isset($_POST['edit_admin'])) {
    $edit_id = $_GET['edit'];
    if ($edit_id != 1) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$edit_admin) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Admin không tồn tại'];
            echo '<script>window.location.href="?page=quantri&action=list";</script>';
            exit;
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không thể sửa tài khoản chính'];
        echo '<script>window.location.href="?page=quantri&action=list";</script>';
        exit;
    }
}

// Lấy danh sách admin (loại trừ id = 1) với tên vai trò
$stmt = $pdo->query("
    SELECT a.*, r.name AS role_name 
    FROM admins a 
    LEFT JOIN roles r ON a.role_id = r.id 
    WHERE a.id != 1
");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Quản trị</h1>
</div>

<!-- Navigation Buttons -->
<div class="mb-4">
    <a href="?page=quantri&action=notify" class="btn btn-primary mr-2">Gửi Thông Báo</a>
    <a href="?page=quantri&action=add" class="btn btn-primary">Thêm Admin</a>
</div>

<?php if ($action === 'notify'): ?>
<!-- Form gửi thông báo nội bộ -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Gửi Thông Báo Nội Bộ</h6>
    </div>
    <div class="card-body">
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
        <form method="POST">
            <div class="form-group">
                <label>Chọn Nhân Viên <span class="text-danger">*</span></label>
                <div class="row">
                    <?php foreach ($admins as $admin): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="admin_ids[]" id="admin_<?php echo htmlspecialchars($admin['id']); ?>" value="<?php echo htmlspecialchars($admin['id']); ?>">
                                <label class="form-check-label" for="admin_<?php echo htmlspecialchars($admin['id']); ?>">
                                    <?php echo htmlspecialchars($admin['name'] . ' (' . $admin['email'] . ')'); ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="form-text text-muted">Chọn các nhân viên để gửi thông báo.</small>
            </div>
            <div class="form-group">
                <label for="subject">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message_content">Nội dung <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message_content" name="message_content" rows="5" required></textarea>
            </div>
            <button type="submit" name="send_notification" class="btn btn-primary">Gửi Thông Báo</button>
        </form>
    </div>
</div>

<?php elseif ($action === 'add' || isset($_GET['edit'])): ?>
<!-- Form thêm/sửa admin -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_admin ? 'Sửa Admin' : 'Thêm Admin'; ?></h6>
    </div>
    <div class="card-body">
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
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_admin): ?>
                <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_admin['id']); ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_admin['name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($edit_admin['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($edit_admin['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu <?php echo $edit_admin ? '(để trống nếu không đổi)' : '<span class="text-danger">*</span>'; ?></label>
                        <input type="password" class="form-control" id="password" name="password" <?php echo $edit_admin ? '' : 'required'; ?>>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($edit_admin['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ</label>
                        <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($edit_admin['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="avatar">Ảnh đại diện</label>
                        <input type="file" class="form-control-file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png">
                        <?php if ($edit_admin && $edit_admin['avatar'] && file_exists('C:/laragon/www/2/admin/' . $edit_admin['avatar'])): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($edit_admin['avatar']); ?>" alt="Avatar" class="mt-2" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="two_factor_code">Mã xác thực (2FA)</label>
                        <input type="text" class="form-control" id="two_factor_code" name="two_factor_code" value="<?php echo htmlspecialchars($edit_admin['two_factor_code'] ?? ''); ?>">
                        <small class="form-text text-muted">Để trống nếu không sử dụng.</small>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">Chọn vai trò</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['id']); ?>" <?php echo ($edit_admin && $edit_admin['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" name="<?php echo $edit_admin ? 'edit_admin' : 'add_admin'; ?>" class="btn btn-primary"><?php echo $edit_admin ? 'Cập nhật' : 'Thêm Admin'; ?></button>
            <?php if ($edit_admin): ?>
                <a href="?page=quantri&action=list" class="btn btn-secondary">Hủy</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php else: ?>
<!-- Bảng danh sách admin -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Admin</h6>
    </div>
    <div class="card-body">
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
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Tên đăng nhập</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Vai trò</th>
                        <th>Ảnh</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $index => $admin): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($admin['name']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td><?php echo htmlspecialchars($admin['phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($admin['address'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($admin['role_name'] ?? '-'); ?></td>
                            <td>
                                <?php if ($admin['avatar'] && file_exists('C:/laragon/www/2/admin/' . $admin['avatar'])): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($admin['avatar']); ?>" alt="Avatar" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?page=quantri&action=add&edit=<?php echo $admin['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="?page=quantri&action=list&delete=<?php echo $admin['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php ob_end_flush(); ?>