<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Thống kê
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'")->fetchColumn() ?? 0;
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$processing_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
$delivered_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
$cancelled_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
$login_history = $pdo->query("SELECT admin_name, login_time, ip_address FROM admin_logins ORDER BY login_time DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Dữ liệu cho biểu đồ
$chart_data = $pdo->query("SELECT DATE(created_at) AS day, SUM(total_amount) AS revenue 
                            FROM orders 
                            WHERE status = 'delivered' 
                            GROUP BY day 
                            ORDER BY day DESC 
                            LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
$labels = array_column($chart_data, 'day');
$revenues = array_map('floatval', array_column($chart_data, 'revenue'));

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng sản phẩm</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_products; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng số đơn hàng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng số khách hàng</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_customers; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng doanh thu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($total_revenue); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Đơn hàng đang chờ xử lý</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_orders; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-start fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Đang xử lý đơn hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $processing_orders; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cog fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đơn hàng đã giao
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $delivered_orders; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Đơn hàng đã hủy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $cancelled_orders; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col col-12 col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tổng quan về doanh thu
                </h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="min-height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col col-12 col-md-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">Lịch sử đăng nhập gần đây</h6>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php if (!empty($login_history)): ?>
                    <?php foreach ($login_history as $login): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($login['admin_name']); ?>
                            <span class="badge badge-primary badge-pill">
                                <?php echo date('d/m/Y H:i', strtotime($login['login_time'])); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">Không có lịch sử đăng nhập</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

</div>

<!-- New Row for Inline Calendar and Clock -->
<div class="row">
    <div class="col col-12 col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lịch</h6>
            </div>
            <div class="card-body-lich" style="padding: 20px 10px;">
                <div id="inlineCalendar"></div>
                <input type="hidden" id="selectedDate" value="<?php echo date('d/m/Y'); ?>">
                <div class="text-center mt-2">
                    <strong>Ngày được chọn: </strong><span id="displaySelectedDate"><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col col-12 col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bản đồ</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3894.832426986445!2d108.30534958539566!3d12.527257892891637!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317193b603af7f3b%3A0x71c85315931a80b0!2zVGjhuq9uZyBSYWk!5e0!3m2!1svi!2s!4v1744961247747!5m2!1svi!2s" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr in inline mode
    flatpickr("#inlineCalendar", {
        inline: true, // Display calendar directly
        dateFormat: "d/m/Y",
        enableTime: false,
        locale: {
            firstDayOfWeek: 1 // Start week on Monday
        },
        defaultDate: "<?php echo date('d/m/Y'); ?>",
        onChange: function(selectedDates, dateStr) {
            // Update hidden input and display selected date
            document.getElementById('selectedDate').value = dateStr;
            document.getElementById('displaySelectedDate').textContent = dateStr;
        }
    });

    // Chart.js for Revenue Chart
    console.log('Chart loaded:', typeof Chart);
    var ctx = document.getElementById('revenueChart');
    console.log('Canvas found:', !!ctx);
    if (ctx && typeof Chart !== 'undefined') {
        var revenueChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse($labels)); ?>,
                datasets: [{
                    label: 'Doanh thu',
                    data: <?php echo json_encode(array_reverse($revenues), JSON_NUMERIC_CHECK); ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(255, 255, 255, 0.8)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    }
                }
            }
        });
    } else {
        console.error('Canvas or Chart.js not found');
    }

    // Update Clock
    // function updateClock() {
    //     const now = new Date();
    //     const hours = String(now.getHours()).padStart(2, '0');
    //     const minutes = String(now.getMinutes()).padStart(2, '0');
    //     const seconds = String(now.getSeconds()).padStart(2, '0');
    //     document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    // }

    // // Initial call to display time
    // updateClock();
    // // Update time every second
    // setInterval(updateClock, 1000);
});
</script>