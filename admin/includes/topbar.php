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
    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Thời gian đếm giờ -->
<div class="clock-box d-flex align-items-center px-3 py-1 shadow-sm mr-3" id="countdown-box" data-tooltip="">
    <i class="fas fa-fire mr-2 text-danger"></i>
    <span id="countdown" class="font-monospace text-dark">00:00:00</span>
</div>


<div class="gtranslate_wrapper"></div>
<script>window.gtranslateSettings = {"default_language":"vi","native_language_names":true,"detect_browser_language":true,"wrapper_selector":".gtranslate_wrapper","flag_size":24,"flag_style":"3d"}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>


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

<div class="ml-3 d-none d-md-flex align-items-center" id="clock-container">
    <div class="clock-box d-flex align-items-center px-3 py-1 shadow-sm">
        <i class="fas fa-clock mr-2 text-primary"></i>
        <span id="clock" class="font-monospace text-dark"></span>
    </div>
</div>



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
    form.container.search-container {
    max-width: 40%;
}
#countdown-box {
    background: linear-gradient(145deg, #ffffff, #f0f0f0);
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
    border: 1px solid #e3e6f0;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}
#countdown-box:hover {
    background: #e9ecef;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}
#countdown {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}
/* Tooltip */
#countdown-box:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    top: 100%;
    left: 70%;
    transform: translateX(-50%);
    background: #333;
    color: #fff;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    white-space: nowrap;
    z-index: 1000;
    opacity: 1;
    transition: opacity 0.3s ease;
}
#countdown-box::after {
    opacity: 0;
}
#countdown-box:hover::before {
    content: '';
    position: absolute;
    top: calc(100% - 6px);
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-bottom-color: #333;
    z-index: 1000;
}
@media (max-width: 767.98px) {
    #countdown-box {
        display: none !important;
    }
}

#clock-container .clock-box {
    background: linear-gradient(145deg, #ffffff, #f0f0f0);
    border-radius: 30px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #333;
    border: 1px solid #e3e6f0;
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

#clock-container .clock-box:hover {
    background: #e9ecef;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

#clock {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

/* Ẩn trên mobile */
@media (max-width: 767.98px) {
    #clock-container {
        display: none !important;
    }
}


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
<script>
let startTime = localStorage.getItem('startTime');
if (!startTime) {
    startTime = Date.now();
    localStorage.setItem('startTime', startTime);
}
let countdownElement = document.getElementById('countdown');
let countdownBox = document.getElementById('countdown-box');
function updateCountdown() {
    const elapsed = Date.now() - startTime;
    const hours = Math.floor(elapsed / 3600000);
    const minutes = Math.floor((elapsed % 3600000) / 60000);
    const seconds = Math.floor((elapsed % 60000) / 1000);
    countdownElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    countdownBox.setAttribute('data-tooltip', `Thời gian hoạt động của bạn là ${hours} giờ ${minutes} phút ${seconds} giây`);
}
setInterval(updateCountdown, 1000);
updateCountdown();

// Cập nhật đồng hồ hiện tại (giờ, phút, giây)
function updateClock() {
    const now = new Date();

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
}

setInterval(updateClock, 1000);
updateClock(); // chạy ngay lần đầu

</script>
