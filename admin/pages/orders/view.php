<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

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
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Không tìm thấy đơn hàng.'];
        echo '<script>window.location.href="?page=orders";</script>';
        exit;
    }

    // Hàm chuyển số thành chữ (tiếng Việt)
    function numberToWordsVN($number) {
        $units = ['', 'nghìn', 'triệu', 'tỷ'];
        $digits = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $number = (int)$number;
        if ($number == 0) return 'không đồng';

        $words = '';
        $unitIndex = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $chunkWords = '';
                $hundreds = floor($chunk / 100);
                $tens = floor(($chunk % 100) / 10);
                $ones = $chunk % 10;

                if ($hundreds > 0) {
                    $chunkWords .= $digits[$hundreds] . ' trăm';
                }
                if ($tens > 0 || $ones > 0) {
                    if ($hundreds > 0) $chunkWords .= ' ';
                    if ($tens == 0 && $ones > 0) {
                        $chunkWords .= $ones == 1 ? 'mốt' : $digits[$ones];
                    } elseif ($tens == 1) {
                        $chunkWords .= 'mười';
                        if ($ones > 0) $chunkWords .= ' ' . ($ones == 5 ? 'lăm' : $digits[$ones]);
                    } else {
                        if ($tens > 0) $chunkWords .= $digits[$tens] . ' mươi';
                        if ($ones > 0) $chunkWords .= ' ' . ($ones == 1 ? 'mốt' : $digits[$ones]);
                    }
                }
                if ($chunkWords != '') {
                    $words = $chunkWords . ' ' . $units[$unitIndex] . ($words != '' ? ' ' . $words : '');
                }
            }
            $number = floor($number / 1000);
            $unitIndex++;
        }

        return trim($words) . ' đồng';
    }

} catch (Exception $e) {
    error_log('Order detail error: ' . $e->getMessage());
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Đã có lỗi xảy ra khi tải thông tin đơn hàng.'];
    echo '<script>window.location.href="?page=orders";</script>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?php echo $order['id']; ?></title>
    <!-- Bootstrap CSS -->
    <!-- Font Awesome -->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            .btn-sm {
                font-size: 0.9em;
            }
        }
        @media (max-width: 575px) {
            .d-sm-flex {
                flex-direction: column;
                align-items: flex-start;
            }
            .btn-sm {
                font-size: 0.9em;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header (giả sử có file header.php) -->
    <?php // require_once 'C:/laragon/www/2/admin/includes/header.php'; ?>

    <!-- Hiển thị thông báo SweetAlert2 -->
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

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-4 text-gray-800">Chi tiết đơn hàng #<?php echo $order['id']; ?></h1>
            <button onclick="printInvoice()" class="btn btn-success btn-sm mt-2 mt-sm-0">In đơn hàng</button>
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

    <!-- Footer (giả sử có file footer.php) -->
    <?php // require_once 'C:/laragon/www/2/admin/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    function printInvoice() {
        let printWindow = window.open('', '_blank');
        if (!printWindow) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                html: 'Không thể mở cửa sổ in. Vui lòng kiểm tra trình duyệt.',
                confirmButtonText: 'OK'
            });
            return;
        }
        printWindow.document.write(`
            <html>
            <head>
                <title>In đơn hàng #<?php echo $order['id']; ?></title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 14px;
                        margin: 0;
                        padding: 20px;
                        line-height: 1.5;
                    }
                    .container {
                        width: 100%;
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .header img {
                        max-width: 100px;
                    }
                    .header h1 {
                        font-size: 24px;
                        margin: 10px 0;
                    }
                    .header p {
                        margin: 5px 0;
                        font-size: 14px;
                    }
                    .order-info, .customer-info {
                        margin-bottom: 20px;
                    }
                    .order-info p, .customer-info p {
                        margin: 5px 0;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background: #f2f2f2;
                        font-weight: bold;
                    }
                    .summary {
                        margin-bottom: 20px;
                    }
                    .summary p {
                        margin: 5px 0;
                        text-align: right;
                    }
                    .summary .total {
                        font-weight: bold;
                        font-size: 16px;
                    }
                    .note {
                        margin-bottom: 20px;
                    }
                    .signatures {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 40px;
                    }
                    .signature-box {
                        text-align: center;
                        width: 20%;
                    }
                    .signature-box p {
                        margin: 5px 0;
                        font-weight: bold;
                    }
                    .signature-box .line {
                        border-top: 1px solid #000;
                        margin-top: 50px;
                    }
                    @media print {
                        @page {
                            size: A4;
                            margin: 10mm;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <img src="http://localhost/2/admin/uploads/logos/logo_1_1744723326.jpg" alt="THANGRAI WEBSITE">
                        <h1>THANGRAI WEBSITE - SHOP ĐIỆN TỬ</h1>
                        <p>"Website Của Thang Raiy"</p>
                        <p><strong>Hotline:</strong> 0914 476 792 | <strong>Website:</strong> THANGRAI WEBSITE</p>
                    </div>
                    <div class="order-info">
                        <p><strong>Mã đơn hàng:</strong> DH<?php echo sprintf('%010d', $order['id']); ?></p>
                        <p><strong>Ngày đặt hàng:</strong> <?php echo date('d/m/Y', strtotime($order['created_at'])); ?></p>
                        <p><strong>Ngày giao hàng:</strong> <?php echo isset($order['delivery_date']) ? date('d/m/Y', strtotime($order['delivery_date'])) : date('d/m/Y', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div class="customer-info">
                        <p><strong>Họ Tên Người Nhận:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?: 'Không có'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address'] . ', ' . $order['ward'] . ', ' . $order['district'] . ', ' . $order['province']); ?></p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; foreach ($order_details as $item): ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.') . ' ₫'; ?></td>
                                    <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') . ' ₫'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="summary">
                        <p>Tổng cộng (<?php echo count($order_details); ?>): <?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $order_details)), 0, ',', '.') . ' ₫'; ?></p>
                        <p>Giảm giá: <?php echo isset($order['discount']) ? number_format($order['discount'], 0, ',', '.') . ' ₫' : '- 0 ₫'; ?></p>
                        <p>Phí dịch vụ giao hàng: <?php echo isset($order['shipping_fee']) ? number_format($order['shipping_fee'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></p>
                        <p class="total">TỔNG TIỀN PHẢI THANH TOÁN: <?php echo number_format($order['total_amount'], 0, ',', '.') . ' ₫'; ?></p>
                        <p>(Số tiền bằng chữ: <?php echo ucfirst(numberToWordsVN($order['total_amount'])); ?>)</p>
                    </div>
                    <div class="note">
                        <p><strong>Ghi chú:</strong> <?php echo !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có'; ?></p>
                    </div>
                    <div class="signatures">
                        <div class="signature-box">
                            <p>PKD</p>
                            <div class="line"></div>
                        </div>
                        <div class="signature-box">
                            <p>Thủ kho</p>
                            <div class="line"></div>
                        </div>
                        <div class="signature-box">
                            <p>Người giao</p>
                            <div class="line"></div>
                        </div>
                        <div class="signature-box">
                            <p>Người nhận</p>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            html: 'Đơn hàng đã được in!',
            confirmButtonText: 'OK'
        });
    }
    </script>
</body>
</html>