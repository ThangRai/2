<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy danh sách Flash Sale
$stmt = $pdo->query("SELECT fs.*, p.name AS product_name 
                     FROM flash_sales fs 
                     JOIN products p ON fs.product_id = p.id 
                     ORDER BY fs.created_at DESC");
$flash_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Flash Sale</h1>
    <a href="?page=flash_sales&subpage=add" class="btn btn-primary btn-sm">Thêm Flash Sale</a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Flash Sale</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Giá Flash Sale</th>
                        <th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($flash_sales)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Chưa có Flash Sale nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($flash_sales as $fs): ?>
                            <tr>
                                <td><?php echo $fs['id']; ?></td>
                                <td><?php echo htmlspecialchars($fs['product_name']); ?></td>
                                <td><?php echo number_format($fs['sale_price'], 0, ',', '.') . ' ₫'; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($fs['start_time'])); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($fs['end_time'])); ?></td>
                                <td><?php echo $fs['is_active'] ? 'Kích hoạt' : 'Không kích hoạt'; ?></td>
                                <td>
                                    <a href="?page=flash_sales&subpage=edit&id=<?php echo $fs['id']; ?>" class="btn btn-warning btn-sm" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/2/admin/pages/flash_sales/process_delete.php?id=<?php echo $fs['id']; ?>" class="btn btn-danger btn-sm" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa Flash Sale này không?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>