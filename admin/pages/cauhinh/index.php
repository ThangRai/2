<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Lỗi kết nối CSDL: ' . $e->getMessage());
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
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

// Hàm xử lý upload file
function uploadFile($file, $upload_dir = 'uploads/contact/', $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/x-icon']) {
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $filename = uniqid() . '-' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    if (in_array($file['type'], $allowed_types) && $file['size'] <= 5 * 1024 * 1024) {
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
    }
    return false;
}

// Xử lý đóng/mở website
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_site_status'])) {
    $site_status = isset($_POST['site_status']) ? 1 : 0;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES ('site_status', ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->execute([$site_status, $site_status]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật trạng thái website thành công'];
    } catch (Exception $e) {
        error_log('Update site status error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý nút Top
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_scroll_top'])) {
    $scroll_top = isset($_POST['scroll_top']) ? 1 : 0;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES ('scroll_top', ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->execute([$scroll_top, $scroll_top]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật nút Top thành công'];
    } catch (Exception $e) {
        error_log('Update scroll top error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật nút Top: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý favicon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_favicon'])) {
    $favicon = $_FILES['favicon'] ?? null;
    $errors = [];

    if ($favicon && $favicon['size'] > 0) {
        $favicon_path = uploadFile($favicon, 'uploads/favicon/', ['image/x-icon', 'image/png']);
        if ($favicon_path) {
            try {
                $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES ('favicon', ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$favicon_path, $favicon_path]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật favicon thành công'];
            } catch (Exception $e) {
                error_log('Update favicon error: ' . $e->getMessage());
                $errors[] = 'Lỗi khi cập nhật favicon: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'Lỗi khi upload favicon';
        }
    } else {
        $errors[] = 'Vui lòng chọn file favicon';
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý thêm liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_contact'])) {
    $type = trim($_POST['type'] ?? '');
    $value = trim($_POST['value'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $order = (int)($_POST['order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    $icon = $_FILES['icon'] ?? null;

    $errors = [];

    if (empty($type) || empty($value)) {
        $errors[] = 'Loại và giá trị không được để trống';
    }

    $icon_path = null;
    if ($icon && $icon['size'] > 0) {
        $icon_path = uploadFile($icon);
        if (!$icon_path) {
            $errors[] = 'Lỗi khi upload icon';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_info (type, value, link, icon, `order`, status)
                VALUES (:type, :value, :link, :icon, :order, :status)
            ");
            $stmt->execute([
                ':type' => $type,
                ':value' => $value,
                ':link' => $link ?: null,
                ':icon' => $icon_path,
                ':order' => $order,
                ':status' => $status
            ]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Thêm liên hệ thành công'];
        } catch (Exception $e) {
            error_log('Add contact error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi thêm liên hệ: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý sửa liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_contact'])) {
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $type = trim($_POST['type'] ?? '');
    $value = trim($_POST['value'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $order = (int)($_POST['order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    $icon = $_FILES['icon'] ?? null;

    $errors = [];

    if ($edit_id === 0) {
        $errors[] = 'ID không hợp lệ';
    }
    if (empty($type) || empty($value)) {
        $errors[] = 'Loại và giá trị không được để trống';
    }

    $icon_path = null;
    if ($icon && $icon['size'] > 0) {
        $icon_path = uploadFile($icon);
        if (!$icon_path) {
            $errors[] = 'Lỗi khi upload icon';
        }
    }

    if (empty($errors)) {
        try {
            $sql = "
                UPDATE contact_info
                SET type = :type, value = :value, link = :link, `order` = :order, status = :status";
            if ($icon_path) {
                $sql .= ", icon = :icon";
                $stmt = $pdo->prepare("SELECT icon FROM contact_info WHERE id = ?");
                $stmt->execute([$edit_id]);
                $old_icon = $stmt->fetchColumn();
                if ($old_icon) {
                    $old_path = 'uploads/contact/' . $old_icon;
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $params = [
                ':type' => $type,
                ':value' => $value,
                ':link' => $link ?: null,
                ':order' => $order,
                ':status' => $status,
                ':id' => $edit_id
            ];
            if ($icon_path) {
                $params[':icon'] = $icon_path;
            }
            $stmt->execute($params);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật liên hệ thành công'];
        } catch (Exception $e) {
            error_log('Edit contact error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật liên hệ: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý xóa liên hệ
if (isset($_GET['delete_contact']) && is_numeric($_GET['delete_contact'])) {
    $delete_id = (int)$_GET['delete_contact'];
    $errors = [];

    try {
        $stmt = $pdo->prepare("SELECT icon FROM contact_info WHERE id = ?");
        $stmt->execute([$delete_id]);
        $icon = $stmt->fetchColumn();
        if ($icon) {
            $icon_path = 'uploads/contact/' . $icon;
            if (file_exists($icon_path)) {
                unlink($icon_path);
            }
        }
        $stmt = $pdo->prepare("DELETE FROM contact_info WHERE id = ?");
        $stmt->execute([$delete_id]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Xóa liên hệ thành công'];
    } catch (Exception $e) {
        error_log('Delete contact error: ' . $e->getMessage());
        $errors[] = 'Lỗi khi xóa liên hệ: ' . $e->getMessage();
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý màu sắc toàn cục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_colors'])) {
    $bg_color = trim($_POST['bg_color'] ?? '#ffffff');
    $text_color = trim($_POST['text_color'] ?? '#000000');
    $link_color = trim($_POST['link_color'] ?? '#007bff');
    $opacity = floatval($_POST['opacity'] ?? 1);

    $errors = [];

    if ($opacity < 0 || $opacity > 1) {
        $errors[] = 'Độ trong suốt phải từ 0 đến 1';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO settings (name, value)
                VALUES
                    ('default_bg_color', ?),
                    ('default_text_color', ?),
                    ('default_link_color', ?),
                    ('default_opacity', ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)
            ");
            $stmt->execute([$bg_color, $text_color, $link_color, $opacity]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật màu sắc thành công'];
        } catch (Exception $e) {
            error_log('Update colors error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật màu sắc: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý font chữ toàn cục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fonts'])) {
    $font_size = (int)($_POST['font_size'] ?? 16);
    $font_weight = trim($_POST['font_weight'] ?? 'normal');

    $errors = [];

    if ($font_size < 10) {
        $errors[] = 'Kích thước chữ phải lớn hơn 10px';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO settings (name, value)
                VALUES
                    ('default_font_size', ?),
                    ('default_font_weight', ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)
            ");
            $stmt->execute([$font_size, $font_weight]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật font chữ thành công'];
        } catch (Exception $e) {
            error_log('Update fonts error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật font chữ: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý SMTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_smtp'])) {
    $smtp_host = trim($_POST['smtp_host'] ?? '');
    $smtp_port = (int)($_POST['smtp_port'] ?? 0);
    $smtp_username = trim($_POST['smtp_username'] ?? '');
    $smtp_password = trim($_POST['smtp_password'] ?? '');
    $smtp_from = trim($_POST['smtp_from'] ?? '');
    $smtp_from_name = trim($_POST['smtp_from_name'] ?? '');

    $errors = [];

    if (empty($smtp_host) || empty($smtp_port) || empty($smtp_username) || empty($smtp_from)) {
        $errors[] = 'Vui lòng điền đầy đủ thông tin SMTP';
    }

    if (empty($errors)) {
        try {
            $settings = [
                'smtp_host' => $smtp_host,
                'smtp_port' => $smtp_port,
                'smtp_username' => $smtp_username,
                'smtp_password' => $smtp_password,
                'smtp_from' => $smtp_from,
                'smtp_from_name' => $smtp_from_name
            ];
            foreach ($settings as $name => $value) {
                $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
                $stmt->execute([$name, $value, $value]);
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật SMTP thành công'];
        } catch (Exception $e) {
            error_log('Update SMTP error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật SMTP: ' . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = ['type' => 'error', 'text' => implode('<br>', $errors)];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Xử lý mã nhúng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_embed'])) {
    $embed_code = trim($_POST['embed_code'] ?? '');
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES ('embed_code', ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->execute([$embed_code, $embed_code]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật mã nhúng thành công'];
    } catch (Exception $e) {
        error_log('Update embed code error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi cập nhật mã nhúng: ' . $e->getMessage()];
    }
    echo '<script>window.location.href="?page=cauhinh";</script>';
    exit;
}

// Lấy cấu hình
$site_status = 1;
$scroll_top = 1;
$favicon = null;
$embed_code = '';
$default_styles = [
    'bg_color' => '#ffffff',
    'text_color' => '#000000',
    'link_color' => '#007bff',
    'opacity' => 1,
    'font_size' => 16,
    'font_weight' => 'normal'
];
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name IN ('site_status', 'scroll_top', 'favicon', 'embed_code', 'default_bg_color', 'default_text_color', 'default_link_color', 'default_opacity', 'default_font_size', 'default_font_weight')");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        switch ($row['name']) {
            case 'site_status':
                $site_status = (int)$row['value'];
                break;
            case 'scroll_top':
                $scroll_top = (int)$row['value'];
                break;
            case 'favicon':
                $favicon = $row['value'];
                break;
            case 'embed_code':
                $embed_code = $row['value'];
                break;
            case 'default_bg_color':
                $default_styles['bg_color'] = $row['value'];
                break;
            case 'default_text_color':
                $default_styles['text_color'] = $row['value'];
                break;
            case 'default_link_color':
                $default_styles['link_color'] = $row['value'];
                break;
            case 'default_opacity':
                $default_styles['opacity'] = (float)$row['value'];
                break;
            case 'default_font_size':
                $default_styles['font_size'] = (int)$row['value'];
                break;
            case 'default_font_weight':
                $default_styles['font_weight'] = $row['value'];
                break;
        }
    }
} catch (Exception $e) {
    error_log('Fetch settings error: ' . $e->getMessage());
}

// Lấy thông tin liên hệ
$contacts = [];
try {
    $stmt = $pdo->query("SELECT id, type, value, link, icon, `order`, status FROM contact_info ORDER BY `order`, id");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Fetch contacts error: ' . $e->getMessage());
}

// Lấy thông tin SMTP
$smtp_settings = [];
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'smtp_%'");
    $smtp_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    error_log('Fetch SMTP settings error: ' . $e->getMessage());
}

// Lấy liên hệ để sửa
$edit_contact = null;
if (isset($_GET['edit_contact']) && is_numeric($_GET['edit_contact'])) {
    try {
        $edit_id = (int)$_GET['edit_contact'];
        $stmt = $pdo->prepare("SELECT * FROM contact_info WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_contact = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$edit_contact) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Liên hệ không tồn tại'];
            echo '<script>window.location.href="?page=cauhinh";</script>';
            exit;
        }
    } catch (Exception $e) {
        error_log('Fetch edit contact error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Lỗi khi lấy liên hệ: ' . $e->getMessage()];
        echo '<script>window.location.href="?page=cauhinh";</script>';
        exit;
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cấu hình Website</h1>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="configTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab"><i class="fas fa-cog"></i> Tổng quan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"><i class="fas fa-address-book"></i> Liên hệ</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="colors-tab" data-toggle="tab" href="#colors" role="tab"><i class="fas fa-palette"></i> Màu sắc</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="fonts-tab" data-toggle="tab" href="#fonts" role="tab"><i class="fas fa-font"></i> Font chữ</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab"><i class="fas fa-envelope"></i> SMTP</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="embed-tab" data-toggle="tab" href="#embed" role="tab"><i class="fas fa-code"></i> Mã nhúng</a>
    </li>
</ul>

<div class="tab-content" id="configTabsContent">
    <!-- Tab Tổng quan -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cấu hình chung</h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['message'])): ?>
                    <script>
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: '<?php echo $_SESSION['message']['type']; ?>',
                                title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                                html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>: <?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>');
                        }
                    </script>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <!-- Đóng/Mở Website -->
                <form method="POST" class="mb-4">
                    <div class="form-group">
                        <label>Trạng thái Website</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="site_status" name="site_status" <?php echo $site_status ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="site_status">Bật/Tắt Website</label>
                        </div>
                    </div>
                    <button type="submit" name="update_site_status" class="btn btn-primary">Cập nhật</button>
                </form>

                <!-- Nút Top -->
                <form method="POST" class="mb-4">
                    <div class="form-group">
                        <label>Nút cuộn lên đầu</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="scroll_top" name="scroll_top" <?php echo $scroll_top ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="scroll_top">Bật/Tắt nút Top</label>
                        </div>
                    </div>
                    <button type="submit" name="update_scroll_top" class="btn btn-primary">Cập nhật</button>
                </form>

                <!-- Favicon -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="favicon">Favicon</label>
                        <input type="file" class="form-control-file" id="favicon" name="favicon" accept="image/x-icon,image/png">
                        <?php if ($favicon): ?>
                            <img src="Uploads/favicon/<?php echo htmlspecialchars($favicon, ENT_QUOTES); ?>" alt="Favicon" style="max-width: 32px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="update_favicon" class="btn btn-primary">Cập nhật Favicon</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Liên hệ -->
    <div class="tab-pane fade" id="contact" role="tabpanel">
        <!-- Form thêm/sửa liên hệ -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_contact ? 'Sửa Liên hệ' : 'Thêm Liên hệ'; ?></h6>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_contact): ?>
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_contact['id'], ENT_QUOTES); ?>">
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Loại liên hệ <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="hotline" <?php echo ($edit_contact && $edit_contact['type'] === 'hotline') ? 'selected' : ''; ?>>Hotline</option>
                                    <option value="zalo" <?php echo ($edit_contact && $edit_contact['type'] === 'zalo') ? 'selected' : ''; ?>>Zalo</option>
                                    <option value="email" <?php echo ($edit_contact && $edit_contact['type'] === 'email') ? 'selected' : ''; ?>>Email</option>
                                    <option value="facebook" <?php echo ($edit_contact && $edit_contact['type'] === 'facebook') ? 'selected' : ''; ?>>Facebook</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="value">Giá trị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="value" name="value" value="<?php echo htmlspecialchars($edit_contact['value'] ?? '', ENT_QUOTES); ?>" required>
                                <small class="form-text text-muted">VD: 0123 456 789, https://zalo.me/..., info@example.com</small>
                            </div>
                            <div class="form-group">
                                <label for="link">Đường dẫn</label>
                                <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($edit_contact['link'] ?? '', ENT_QUOTES); ?>">
                                <small class="form-text text-muted">VD: tel:0123456789, mailto:info@example.com, https://facebook.com/...</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon">Icon</label>
                                <input type="file" class="form-control-file" id="icon" name="icon" accept="image/*">
                                <?php if ($edit_contact && $edit_contact['icon']): ?>
                                    <img src="Uploads/contact/<?php echo htmlspecialchars($edit_contact['icon'], ENT_QUOTES); ?>" alt="Icon" style="max-width: 50px; margin-top: 10px;">
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="order">Thứ tự</label>
                                <input type="number" class="form-control" id="order" name="order" value="<?php echo htmlspecialchars($edit_contact['order'] ?? 0, ENT_QUOTES); ?>" min="0">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" <?php echo ($edit_contact && $edit_contact['status']) || !$edit_contact ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="status">Hiển thị</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="<?php echo $edit_contact ? 'edit_contact' : 'add_contact'; ?>" class="btn btn-primary"><?php echo $edit_contact ? 'Cập nhật' : 'Thêm Liên hệ'; ?></button>
                    <?php if ($edit_contact): ?>
                        <a href="?page=cauhinh" class="btn btn-secondary">Hủy</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Bảng danh sách liên hệ -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách Liên hệ</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Loại</th>
                                <th>Giá trị</th>
                                <th>Đường dẫn</th>
                                <th>Icon</th>
                                <th>Trạng thái</th>
                                <th>Thứ tự</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Chưa có liên hệ nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contacts as $index => $contact): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($contact['type'], ENT_QUOTES); ?></td>
                                        <td><?php echo htmlspecialchars($contact['value'], ENT_QUOTES); ?></td>
                                        <td><?php echo htmlspecialchars($contact['link'] ?? '-', ENT_QUOTES); ?></td>
                                        <td>
                                            <?php if ($contact['icon']): ?>
                                                <img src="Uploads/contact/<?php echo htmlspecialchars($contact['icon'], ENT_QUOTES); ?>" alt="Icon" style="max-width: 30px;">
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $contact['status'] ? '<span class="badge badge-success">Hiển thị</span>' : '<span class="badge badge-secondary">Ẩn</span>'; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($contact['order'], ENT_QUOTES); ?></td>
                                        <td>
                                            <a href="?page=cauhinh&edit_contact=<?php echo $contact['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="?page=cauhinh&delete_contact=<?php echo $contact['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Màu sắc -->
    <div class="tab-pane fade" id="colors" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cấu hình Màu sắc Website</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bg_color">Màu nền website</label>
                                <input type="color" class="form-control" id="bg_color" name="bg_color" value="<?php echo htmlspecialchars($default_styles['bg_color'], ENT_QUOTES); ?>">
                            </div>
                            <div class="form-group">
                                <label for="text_color">Màu chữ chính</label>
                                <input type="color" class="form-control" id="text_color" name="text_color" value="<?php echo htmlspecialchars($default_styles['text_color'], ENT_QUOTES); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="link_color">Màu liên kết</label>
                                <input type="color" class="form-control" id="link_color" name="link_color" value="<?php echo htmlspecialchars($default_styles['link_color'], ENT_QUOTES); ?>">
                            </div>
                            <div class="form-group">
                                <label for="opacity">Độ trong suốt nền (0-1)</label>
                                <input type="number" step="0.1" min="0" max="1" class="form-control" id="opacity" name="opacity" value="<?php echo htmlspecialchars($default_styles['opacity'], ENT_QUOTES); ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_colors" class="btn btn-primary">Cập nhật Màu sắc</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Font chữ -->
    <div class="tab-pane fade" id="fonts" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cấu hình Font chữ Website</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="font_size">Kích thước chữ (px)</label>
                                <input type="number" min="10" class="form-control" id="font_size" name="font_size" value="<?php echo htmlspecialchars($default_styles['font_size'], ENT_QUOTES); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="font_weight">Độ đậm chữ</label>
                                <select class="form-control" id="font_weight" name="font_weight">
                                    <option value="normal" <?php echo $default_styles['font_weight'] === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                    <option value="bold" <?php echo $default_styles['font_weight'] === 'bold' ? 'selected' : ''; ?>>Bold</option>
                                    <option value="400" <?php echo $default_styles['font_weight'] === '400' ? 'selected' : ''; ?>>400</option>
                                    <option value="700" <?php echo $default_styles['font_weight'] === '700' ? 'selected' : ''; ?>>700</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_fonts" class="btn btn-primary">Cập nhật Font chữ</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab SMTP -->
    <div class="tab-pane fade" id="smtp" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Cấu hình SMTP</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="smtp_host">SMTP Host</label>
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($smtp_settings['smtp_host'] ?? '', ENT_QUOTES); ?>" placeholder="smtp.gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port</label>
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($smtp_settings['smtp_port'] ?? 587, ENT_QUOTES); ?>">
                    </div>
                    <div class="form-group">
                        <label for="smtp_username">SMTP Username</label>
                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($smtp_settings['smtp_username'] ?? '', ENT_QUOTES); ?>" placeholder="your-email@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_password">SMTP Password</label>
                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($smtp_settings['smtp_password'] ?? '', ENT_QUOTES); ?>">
                    </div>
                    <div class="form-group">
                        <label for="smtp_from">Email gửi</label>
                        <input type="email" class="form-control" id="smtp_from" name="smtp_from" value="<?php echo htmlspecialchars($smtp_settings['smtp_from'] ?? '', ENT_QUOTES); ?>" placeholder="your-email@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="smtp_from_name">Tên người gửi</label>
                        <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="<?php echo htmlspecialchars($smtp_settings['smtp_from_name'] ?? '', ENT_QUOTES); ?>" placeholder="Tên Website">
                    </div>
                    <button type="submit" name="update_smtp" class="btn btn-primary">Cập nhật SMTP</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tab Mã nhúng -->
    <div class="tab-pane fade" id="embed" role="tabpanel">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Mã nhúng</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="embed_code">Mã nhúng (Google Analytics, Pixel, v.v.)</label>
                        <textarea class="form-control" id="embed_code" name="embed_code" rows="6"><?php echo htmlspecialchars($embed_code, ENT_QUOTES); ?></textarea>
                    </div>
                    <button type="submit" name="update_embed" class="btn btn-primary">Cập nhật Mã nhúng</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>