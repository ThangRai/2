<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Truy vấn thông tin đơn hàng và khách hàng
    $stmt = $pdo->prepare("
        SELECT o.*, 
               c.name AS customer_name, 
               c.email, 
               c.phone, 
               c.address, 
               c.province, 
               c.district, 
               c.ward
        FROM orders o 
        JOIN customers c ON o.customer_id = c.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Truy vấn chi tiết sản phẩm trong đơn hàng
    $stmt = $pdo->prepare("
        SELECT od.*, p.name AS product_name, p.image AS product_image
        FROM order_details od
        JOIN products p ON od.product_id = p.id
        WHERE od.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $statuses = [
        'pending' => 'Đang chờ',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đã giao',
        'delivered' => 'Hoàn thành',
        'cancelled' => 'Đã hủy'
    ];

    $payment_methods = [
        'cod' => 'Thanh toán khi nhận hàng',
        'bank_transfer' => 'Chuyển khoản ngân hàng'
    ];

    if (!$order) {
        echo '<p class="text-danger">Không tìm thấy đơn hàng.</p>';
        exit;
    }
} catch (Exception $e) {
    error_log('Order detail error: ' . $e->getMessage());
    echo '<p class="text-danger">Đã có lỗi xảy ra khi tải thông tin đơn hàng.</p>';
    exit;
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-4 text-gray-800">Chi tiết đơn hàng #<?php echo $order['id']; ?>
</h1>
</div>
<div class="card shadow mb-4">

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?php echo $order['id']; ?></p>
                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount'], 0, ',', '.') . ' ₫'; ?></p>
                    <p><strong>Trạng thái:</strong> <?php echo isset($statuses[$order['status']]) ? $statuses[$order['status']] : ucfirst($order['status']); ?></p>
                    <p><strong>Phương thức thanh toán:</strong> <?php echo isset($order['payment_method']) && isset($payment_methods[$order['payment_method']]) ? $payment_methods[$order['payment_method']] : 'Không xác định'; ?></p>
                    <p><strong>Ghi chú:</strong> <?php echo !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có'; ?></p>
                    <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>Tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?: 'Không có'); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address'] . ', ' . $order['ward'] . ', ' . $order['district'] . ', ' . $order['province']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết sản phẩm</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_details as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $item['product_image'] ? 'http://localhost/2/admin/' . htmlspecialchars($item['product_image']) : 'http://localhost/2/admin/uploads/products/default.jpg'; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.') . ' ₫'; ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') . ' ₫'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="?page=orders" class="btn btn-primary btn-sm">Quay lại danh sách</a>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}
.table th, .table td {
    vertical-align: middle;
}
@media (max-width: 768px) {
    .table th, .table td {
        font-size: 0.9em;
    }
    .card-body p {
        font-size: 0.95em;
    }
}
</style>