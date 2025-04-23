<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <img src="assets/img/logo.png" alt="Logo" width="70">
        </div>
        <div class="sidebar-brand-text mx-3"><?php echo SITE_NAME; ?></div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=dashboard">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item <?php echo $page == 'cauhinh' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=cauhinh">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Cấu hình</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'cauhinhcot' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=cauhinhcot">
            <i class="fas fa-fw fa-box"></i>
            <span>Cấu hình cột</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'lienhe' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=lienhe">
            <i class="fas fa-fw fa-envelope"></i>
            <span>Liên hệ</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'categories' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=categories">
            <i class="fas fa-fw fa-tags"></i>
            <span>Danh Mục</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'orders' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=orders">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Đơn Hàng</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'products' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=products">
            <i class="fas fa-fw fa-box"></i>
            <span>Sản Phẩm</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'blog' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=blog">
            <i class="fas fa-book-open"></i>
            <span>Nội dung - Blog</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'danhgia' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=danhgia">
            <i class="fas fa-fw fa-star"></i>
            <span>Đánh giá</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'Customerreviews' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=Customerreviews">
            <i class="fas fa-fw fa-comments"></i>
            <span>Ý kiến khách hàng</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'logo' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=logo">
            <i class="fas fa-fw fa-images"></i>
            <span>Logo</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'slideshow' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=slideshow">
            <i class="fas fa-fw fa-images"></i>
            <span>Hình Slide</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'customers' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=customers">
            <i class="fas fa-fw fa-users"></i>
            <span>Khách hàng</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'partner' ? 'active' : ''; ?>">
        <a class="nav-link" href="index.php?page=partner">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Đối tác</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'admins' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="index.php?page=quantri">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Tài khoản</span>
        </a>
    </li>
    <li class="nav-item <?php echo $page == 'logout' ? 'active' : ''; ?>">
        <a class="nav-link collapsed" href="logout.php">
            <i class="fas fa-fw fa-sign-in-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

<style>
/* CSS cho sidebar */
.sidebar {
    -webkit-overflow-scrolling: touch; /* Cuộn mượt trên iOS */
}

/* Đảm bảo sidebar cuộn được trên mobile */
@media (max-width: 767.98px) {
    .sidebar {
        height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
        overflow-y: auto; /* Bật cuộn dọc */
        -webkit-overflow-scrolling: touch; /* Cuộn mượt trên iOS */
    }
}
</style>