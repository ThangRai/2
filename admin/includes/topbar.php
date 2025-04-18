<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

$admin_id = $_SESSION['admin_id'] ?? null;
$avatar = null;
$new_orders_count = 0;
$new_orders = [];

if ($admin_id) {
    // Lấy avatar
    $stmt = $pdo->prepare("SELECT avatar FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    $avatar = $admin['avatar'] ?? null;

    // Lấy số đơn hàng mới (status = 'pending')
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
    $stmt->execute();
    $new_orders_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Lấy 5 đơn hàng mới nhất để hiển thị trong dropdown
    $stmt = $pdo->prepare("SELECT id, customer_id, total_amount, created_at FROM orders WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $new_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Nút toggle sidebar cho mobile -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Ô tìm kiếm -->
    <form class="container search-container">
        <div class="input-group search-input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Tìm kiếm..." id="search-input">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button" id="search-button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
        <!-- Icon tìm kiếm cho mobile -->
        <button class="btn btn-link search-icon-mobile d-md-none" id="search-icon-mobile">
            <i class="fas fa-search"></i>
        </button>
    </form>

    <!-- Phần phải navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Icon thông báo đơn hàng mới -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="ordersDropdown" role="button" data-toggle="dropdown">
                <i class="fas fa-bell fa-fw"></i>
                <?php if ($new_orders_count > 0): ?>
                    <span class="badge badge-danger badge-counter"><?php echo $new_orders_count > 9 ? '9+' : $new_orders_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow">
                <h6 class="dropdown-header">Đơn hàng mới</h6>
                <?php if (empty($new_orders)): ?>
                    <a class="dropdown-item text-gray-500" href="#">Không có đơn hàng mới</a>
                <?php else: ?>
                    <?php foreach ($new_orders as $order): ?>
                        <a class="dropdown-item d-flex align-items-center" href="?page=orders&view=<?php echo $order['id']; ?>">
                            <div>
                                <div class="text-truncate">
                                    <strong><?php echo htmlspecialchars($order['customer_id']); ?></strong>
                                    - <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ
                                </div>
                                <div class="small text-gray-500">
                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center" href="?page=orders">Xem tất cả đơn hàng</a>
                <?php endif; ?>
            </div>
        </li>

        <!-- Icon toàn màn hình và link website -->
        <li class="nav-item no-arrow mx-1">
            <a class="nav-link" href="#" id="fullscreenToggle">
                <i class="fas fa-expand fa-fw"></i>
            </a>
        </li>
        <li class="nav-item no-arrow mx-1">
            <a class="nav-link" href="http://localhost/2/public/" target="_blank">
                <i class="fas fa-external-link-alt fa-fw"></i>
            </a>
        </li>

        <!-- Username và avatar -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                </span>
                <?php if ($avatar): ?>
                    <img src="/2/admin/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                <?php else: ?>
                    <img src="/2/admin/images/default-avatar.png" alt="Default Avatar" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow">
                <a class="dropdown-item" href="?page=profile">Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
        </li>
    </ul>
</nav>

<style>
/* CSS cho ô tìm kiếm trên mobile */
@media (max-width: 767.98px) {
    .search-container {
        position: relative;
    }

    .search-input-group {
        display: none; /* Ẩn ô tìm kiếm mặc định trên mobile */
        position: absolute;
        top: 100%;
        left: 0;
        width: 200px;
        background: #fff;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .search-input-group.active {
        display: flex; /* Hiển thị ô tìm kiếm khi active */
    }

    .search-icon-mobile {
        font-size: 1.2rem;
        color: #4e73df; /* Màu giống nút tìm kiếm */
    }
}

/* Đảm bảo ô tìm kiếm hoạt động bình thường trên desktop */
@media (min-width: 768px) {
    .search-icon-mobile {
        display: none; /* Ẩn icon tìm kiếm trên desktop */
    }
}
</style>

<script>
// JavaScript cho toàn màn hình
document.getElementById('fullscreenToggle').addEventListener('click', function () {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.error(`Lỗi khi kích hoạt toàn màn hình: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
});

// JavaScript cho ô tìm kiếm trên mobile
document.getElementById('search-icon-mobile').addEventListener('click', function () {
    const searchInputGroup = document.querySelector('.search-input-group');
    searchInputGroup.classList.toggle('active');
});
</script>