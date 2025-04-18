<?php
session_start();
require_once 'config/db_connect.php';
require_once 'config/constants.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Redirecting to login.php: No admin_id in session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

// Debug session
error_log('Index.php session data: ' . print_r($_SESSION, true));

// Kiểm tra quyền cho trang admins
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : 'index';

if ($page === 'admins') {
    $stmt = $pdo->prepare("SELECT permissions FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $permissions = $admin['permissions'] ? json_decode($admin['permissions'], true) : [];

    if (!isset($permissions['manage_admins']) && $_SESSION['admin_name'] !== 'Admin') {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Bạn không có quyền truy cập trang này'];
        error_log('Access denied to admins page for admin_id: ' . $_SESSION['admin_id']);
        echo '<script>window.location.href="index.php?page=dashboard";</script>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <!-- Render session messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <div id="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'includes/topbar.php'; ?>
                <div class="container-fluid">
                    <?php
                    $page_path = "pages/$page/$subpage.php";
                    if (file_exists($page_path)) {
                        include $page_path;
                    } else {
                        echo "<h4>Page not found!</h4>";
                    }
                    ?>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>
    <script src="assets/vendor/chart.js/Chart.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>