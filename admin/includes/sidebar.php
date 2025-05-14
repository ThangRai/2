<?php
// Kết nối cơ sở dữ liệu
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra session và lấy role_id
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy role_id từ session hoặc truy vấn cơ sở dữ liệu
if (!isset($_SESSION['role_id'])) {
    $stmt = $pdo->prepare("SELECT role_id FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        error_log('Không tìm thấy admin với ID: ' . $_SESSION['admin_id']);
        header('Location: login.php');
        exit;
    }
    
    $_SESSION['role_id'] = $admin['role_id'];
}

// Định nghĩa quyền truy cập cho từng trang
$permissions = [
    'dashboard' => [1, 2, 3, 4], // Tất cả role đều thấy Dashboard
    'cauhinh' => [1, 2, 4],      // Tất cả role đều thấy
    'cauhinhcot' => [1, 4],
    'lienhe' => [1, 2, 3, 4],      
    'categories' => [1, 2, 3],      // Tất cả role đều thấy
    'info' => [1, 2, 3, 4],
    'orders' => [1, 2, 3],       // Tất cả role đều thấy
    'products' => [1, 2, 3],     // Tất cả role đều thấy
    'flash_sales' => [1, 2],
    'attributes' => [1, 2],
    'blog' => [1, 2, 3],
    'service' =>[1, 2, 3, 4],
    'project' => [1, 2, 3, 4],
    'danhgia' => [1, 2, 3],
    'Customerreviews' => [1, 2, 3],
    'logo' => [1, 2, 3],
    'slideshow' => [1, 2, 3],
    'image' => [1, 2, 3, 4],
    'customers' => [1, 2, 3],
    'question' => [1, 2, 3, 4],     // Hỏi đáp
    'partner' => [1, 2, 3],
    'quantri' => [1, 4],         // Chỉ super_admin (role 1)
    'activity_logs' => [1, 3],
    'faicon' => [1],
    'logout' => [1, 2, 3, 4],    // Tất cả role đều thấy Đăng xuất
];

// Hàm kiểm tra quyền truy cập
function hasAccess($page, $role_id, $permissions) {
    return isset($permissions[$page]) && in_array($role_id, $permissions[$page]);
}
?>

<style>

    @media (max-width: 767.98px) {
        .sidebar {
            height: 100vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar .dropdown-menu {
            position: relative;
            width: 100%;
            background-color: #fff;
            border: none;
            box-shadow: none;
        }

        .sidebar .dropdown-item {
            padding-left: 2rem;
        }
    }

    .sidebar {
        -webkit-overflow-scrolling: touch;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="http://localhost/2/admin/">
        <div class="sidebar-brand-icon">
            <img src="assets/img/logo.png" alt="Logo" width="70">
        </div>
        <div class="sidebar-brand-text mx-3"><?php echo SITE_NAME; ?></div>
    </a>
    <hr class="sidebar-divider my-0">

    <?php if (hasAccess('dashboard', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
        <a class="nav-link" href="http://localhost/2/admin/">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('cauhinh', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'cauhinh' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=cauhinh">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Cấu hình</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('cauhinhcot', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'cauhinhcot' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=cauhinhcot">
            <i class="fas fa-fw fa-box"></i>
            <span>Cấu hình cột</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('lienhe', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'lienhe' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=lienhe">
            <i class="fas fa-fw fa-envelope"></i>
            <span>Liên hệ</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('categories', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'categories' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=categories">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh Mục</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('info', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'info' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=info">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Giới thiệu</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('orders', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'orders' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=orders">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Đơn Hàng</span>
        </a>
    </li>
    <?php endif; ?>

        <?php if (hasAccess('blog', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=blog">
            <i class="fas fa-book-open"></i>
            <span>Nội dung - Blog</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('products', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'products' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=products">
            <i class="fas fa-fw fa-box"></i>
            <span>Sản phẩm</span>
        </a>
    </li>
    <?php endif; ?>

        <?php if (hasAccess('service', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'service' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=service">
            <i class="fas fa-concierge-bell"></i>
            <span>Dịch vụ</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('project', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'project' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=project">
            <i class="fas fa-folder"></i>
            <span>Dự án</span>
        </a>
    </li>
    <?php endif; ?>


    <?php if (hasAccess('danhgia', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'danhgia' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=danhgia">
            <i class="fas fa-fw fa-star"></i>
            <span>Đánh giá</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('Customerreviews', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'Customerreviews' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=Customerreviews">
            <i class="fas fa-fw fa-comments"></i>
            <span>Ý kiến khách hàng</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('logo', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'logo' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=logo">
            <i class="fas fa-fw fa-images"></i>
            <span>Logo</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('slideshow', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'slideshow' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=slideshow">
            <i class="fas fa-fw fa-images"></i>
            <span>Hình Slide</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('image', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'image' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=image">
            <i class="fas fa-fw fa-image	"></i>
            <span>Thư viện ảnh</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('customers', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'customers' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=customers">
            <i class="fas fa-fw fa-users"></i>
            <span>Khách hàng</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('question', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'question' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=question">
            <i class="fas fa-question-circle"></i>
            <span>Hỏi đáp</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('partner', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'partner' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=partner">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Đối tác</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('quantri', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'admins' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="index.php?page=quantri">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Tài khoản</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (hasAccess('activity_logs', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'activity_logs' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="index.php?page=activity_logs">
            <i class="fas fa-fw fa-clock"></i>
            <span>Lịch sử hoạt động</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('faicon', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'faicon' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="index.php?page=faicon">
            <i class="fas fa-fw fa-align-justify"></i>
            <span>Awesome icon</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (hasAccess('logout', $_SESSION['role_id'], $permissions)): ?>
    <li class="nav-item <?php echo $page == 'logout' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="logout.php">
            <i class="fas fa-fw fa-sign-in-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </li>
    <?php endif; ?>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>