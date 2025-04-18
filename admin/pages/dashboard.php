<?php
require_once '../../config/db_connect.php';

// Thống kê
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'")->fetchColumn() ?? 0;
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$processing_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn();
$delivered_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();
$cancelled_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();

// Dữ liệu cho biểu đồ
$chart_data = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_amount) AS revenue 
                            FROM orders 
                            WHERE status = 'delivered' 
                            GROUP BY month 
                            ORDER BY month DESC 
                            LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
$labels = array_column($chart_data, 'month');
$revenues = array_map('floatval', array_column($chart_data, 'revenue'));
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Customers</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($total_revenue, 2); ?></div>
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
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Orders</div>
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
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Processing Orders</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Delivered Orders</div>
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
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Cancelled Orders</div>
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
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="min-height: 300px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chart loaded:', typeof Chart);
    var ctx = document.getElementById('revenueChart');
    console.log('Canvas found:', !!ctx);
    if (ctx && typeof Chart !== 'undefined') {
        var revenueChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse($labels)); ?>,
                datasets: [{
                    label: 'Revenue',
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
});
</script>