<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Vui lòng đăng nhập để truy cập.'];
    error_log('Chuyển hướng đến login.php: Không có admin_id trong session');
    echo '<script>window.location.href="login.php";</script>';
    exit;
}

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';

    // Xử lý các hành động: duyệt/hủy duyệt/từ chối/xóa đánh giá, sửa/xóa phản hồi
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        // Xử lý đánh giá
        if (in_array($action, ['approve', 'unapprove', 'reject'])) {
            $review_id = (int)$_POST['review_id'];
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
                $stmt->execute([$review_id]);
                $_SESSION['success'] = 'Đánh giá đã được duyệt!';
            } elseif ($action === 'unapprove') {
                $stmt = $pdo->prepare("UPDATE reviews SET is_approved = 0 WHERE id = ?");
                $stmt->execute([$review_id]);
                $_SESSION['success'] = 'Đã hủy duyệt đánh giá!';
            } elseif ($action === 'reject') {
                $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
                $stmt->execute([$review_id]);
                // Xóa các phản hồi liên quan
                $stmt = $pdo->prepare("DELETE FROM review_replies WHERE review_id = ?");
                $stmt->execute([$review_id]);
                $_SESSION['success'] = 'Đã xóa đánh giá và các phản hồi!';
            }
        }

        // Xử lý xóa phản hồi
        if ($action === 'delete_reply') {
            $reply_id = (int)$_POST['reply_id'];
            $stmt = $pdo->prepare("DELETE FROM review_replies WHERE id = ?");
            $stmt->execute([$reply_id]);
            $_SESSION['success'] = 'Đã xóa phản hồi!';
        }

        // Xử lý sửa phản hồi
        if ($action === 'edit_reply') {
            $reply_id = (int)$_POST['reply_id'];
            $reply_content = trim($_POST['reply_content']);
            $reply_by = trim($_POST['reply_by']);

            if (empty($reply_content) || empty($reply_by)) {
                $_SESSION['error'] = 'Nội dung và người phản hồi không được để trống!';
            } else {
                $stmt = $pdo->prepare("UPDATE review_replies SET reply_by = ?, reply_content = ? WHERE id = ?");
                $stmt->execute([$reply_by, $reply_content, $reply_id]);
                $_SESSION['success'] = 'Đã cập nhật phản hồi!';
            }
        }

        echo '<script>window.location.href="index.php?page=danhgia";</script>';
        exit;
    }

    // Lấy tất cả đánh giá
    $stmt = $pdo->prepare("
        SELECT r.id, r.customer_name, r.rating, r.comment, r.is_approved, r.created_at, p.name as product_name 
        FROM reviews r 
        JOIN products p ON r.product_id = p.id 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tất cả phản hồi từ bảng review_replies
    $stmt = $pdo->prepare("
        SELECT rr.id, rr.review_id, rr.reply_by, rr.reply_content, rr.created_at 
        FROM review_replies rr 
        ORDER BY rr.created_at ASC
    ");
    $stmt->execute();
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Nhóm phản hồi theo review_id
    $replies_by_review = [];
    foreach ($replies as $reply) {
        $replies_by_review[$reply['review_id']][] = $reply;
    }

} catch (Exception $e) {
    error_log('Admin reviews error: ' . $e->getMessage());
    $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đánh giá</title>
    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .table {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-approve, .btn-reject, .btn-unapprove, .btn-edit-reply, .btn-delete-reply {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 6px;
        }
        .btn-approve {
            background: #00b894;
            color: #fff;
        }
        .btn-reject {
            background: #ff4757;
            color: #fff;
        }
        .btn-unapprove {
            background: #ffc107;
            color: #fff;
        }
        .btn-edit-reply {
            background: #007bff;
            color: #fff;
        }
        .btn-delete-reply {
            background: #dc3545;
            color: #fff;
        }
        .reply-row {
            background-color: #f8f9fa;
            font-size: 0.9em;
        }
        .reply-row td {
            padding-left: 30px;
        }
        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 0.9em;
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
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo isset($_SESSION['success']) ? 'success' : 'error'; ?>',
                title: '<?php echo isset($_SESSION['success']) ? 'Thành công' : 'Lỗi'; ?>',
                html: '<?php echo htmlspecialchars(isset($_SESSION['success']) ? $_SESSION['success'] : $_SESSION['error']); ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php unset($_SESSION['success'], $_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đánh giá</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đánh giá</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Khách hàng</th>
                            <th>Đánh giá</th>
                            <th>Bình luận/Phản hồi</th>
                            <th>Ngày gửi</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['product_name'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($review['customer_name'], ENT_QUOTES); ?></td>
                                <td><?php echo $review['rating']; ?> sao</td>
                                <td><?php echo htmlspecialchars($review['comment'], ENT_QUOTES); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></td>
                                <td><?php echo $review['is_approved'] ? 'Đã duyệt' : 'Chưa duyệt'; ?></td>
                                <td>
                                    <?php if (!$review['is_approved']): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm btn-approve">Duyệt</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm btn-reject" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?')">Từ chối</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <input type="hidden" name="action" value="unapprove">
                                            <button type="submit" class="btn btn-warning btn-sm btn-unapprove" onclick="return confirm('Bạn có chắc muốn hủy duyệt đánh giá này không?')">Hủy duyệt</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm btn-reject" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?')">Xóa</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <!-- Hiển thị các phản hồi của đánh giá này -->
                            <?php if (isset($replies_by_review[$review['id']])): ?>
                                <?php foreach ($replies_by_review[$review['id']] as $reply): ?>
                                    <tr class="reply-row">
                                        <td colspan="2"></td>
                                        <td colspan="2">
                                            <strong><?php echo htmlspecialchars($reply['reply_by'], ENT_QUOTES); ?>:</strong>
                                            <?php echo htmlspecialchars($reply['reply_content'], ENT_QUOTES); ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($reply['created_at'])); ?></td>
                                        <td colspan="2">
                                            <!-- Nút sửa phản hồi -->
                                            <button class="btn btn-primary btn-sm btn-edit-reply" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editReplyModal"
                                                    data-reply-id="<?php echo $reply['id']; ?>"
                                                    data-reply-by="<?php echo htmlspecialchars($reply['reply_by'], ENT_QUOTES); ?>"
                                                    data-reply-content="<?php echo htmlspecialchars($reply['reply_content'], ENT_QUOTES); ?>">
                                                Sửa
                                            </button>
                                            <!-- Nút xóa phản hồi -->
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="reply_id" value="<?php echo $reply['id']; ?>">
                                                <input type="hidden" name="action" value="delete_reply">
                                                <button type="submit" class="btn btn-danger btn-sm btn-delete-reply" 
                                                        onclick="return confirm('Bạn có chắc muốn xóa phản hồi này không?')">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal chỉnh sửa phản hồi -->
    <div class="modal fade" id="editReplyModal" tabindex="-1" aria-labelledby="editReplyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReplyModalLabel">Chỉnh sửa phản hồi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="reply_id" id="edit_reply_id">
                        <input type="hidden" name="action" value="edit_reply">
                        <div class="mb-3">
                            <label for="edit_reply_by" class="form-label">Người phản hồi</label>
                            <input type="text" class="form-control" id="edit_reply_by" name="reply_by" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_reply_content" class="form-label">Nội dung phản hồi</label>
                            <textarea class="form-control" id="edit_reply_content" name="reply_content" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer (giả sử có file footer.php) -->
    <?php // require_once 'C:/laragon/www/2/admin/includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script để điền dữ liệu vào modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editButtons = document.querySelectorAll('.btn-edit-reply');
            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var replyId = this.getAttribute('data-reply-id');
                    var replyBy = this.getAttribute('data-reply-by');
                    var replyContent = this.getAttribute('data-reply-content');

                    document.getElementById('edit_reply_id').value = replyId;
                    document.getElementById('edit_reply_by').value = replyBy;
                    document.getElementById('edit_reply_content').value = replyContent;
                });
            });
        });
    </script>
</body>
</html>