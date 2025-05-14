<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy cấu hình hiện tại
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'columns_%' OR name LIKE 'review_columns_%' OR name LIKE 'partner_columns_%' OR name LIKE 'blog_columns_%'  OR name LIKE 'service_columns_%'  OR name LIKE 'project_columns_%'");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }
} catch (Exception $e) {
    error_log('Config columns error: ' . $e->getMessage());
    $settings = [
        'columns_375' => 2,
        'columns_425' => 3,
        'columns_768' => 4,
        'columns_1200' => 5,
        'columns_max' => 6,
        'review_columns_375' => 1,
        'review_columns_425' => 1,
        'review_columns_768' => 2,
        'review_columns_1200' => 3,
        'review_columns_max' => 3,
        'partner_columns_375' => 1,
        'partner_columns_425' => 1,
        'partner_columns_768' => 2,
        'partner_columns_1200' => 3,
        'partner_columns_max' => 4,
        'blog_columns_375' => 1,
        'blog_columns_425' => 1,
        'blog_columns_768' => 2,
        'blog_columns_1200' => 4,
        'blog_columns_max' => 6,
        'service_columns_375' => 2,
        'service_columns_425' => 3,
        'service_columns_768' => 4,
        'service_columns_1200' => 5,
        'service_columns_max' => 6,
        'project_columns_375' => 1,
        'project_columns_425' => 1,
        'project_columns_768' => 2,
        'project_columns_1200' => 3,
        'project_columns_max' => 4
    ];
}

// Xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Cấu hình cột sản phẩm
        $columns_375 = (int)$_POST['columns_375'];
        $columns_425 = (int)$_POST['columns_425'];
        $columns_768 = (int)$_POST['columns_768'];
        $columns_1200 = (int)$_POST['columns_1200'];
        $columns_max = (int)$_POST['columns_max'];

        // Cấu hình cột ý kiến khách hàng
        $review_columns_375 = (int)$_POST['review_columns_375'];
        $review_columns_425 = (int)$_POST['review_columns_425'];
        $review_columns_768 = (int)$_POST['review_columns_768'];
        $review_columns_1200 = (int)$_POST['review_columns_1200'];
        $review_columns_max = (int)$_POST['review_columns_max'];

        // Cấu hình cột đối tác
        $partner_columns_375 = (int)$_POST['partner_columns_375'];
        $partner_columns_425 = (int)$_POST['partner_columns_425'];
        $partner_columns_768 = (int)$_POST['partner_columns_768'];
        $partner_columns_1200 = (int)$_POST['partner_columns_1200'];
        $partner_columns_max = (int)$_POST['partner_columns_max'];

        // Cấu hình cột blog
        $blog_columns_375 = (int)$_POST['blog_columns_375'];
        $blog_columns_425 = (int)$_POST['blog_columns_425'];
        $blog_columns_768 = (int)$_POST['blog_columns_768'];
        $blog_columns_1200 = (int)$_POST['blog_columns_1200'];
        $blog_columns_max = (int)$_POST['blog_columns_max'];

        // Cấu hình cột dịch vụ
        $service_columns_375 = (int)$_POST['service_columns_375'];
        $service_columns_425 = (int)$_POST['service_columns_425'];
        $service_columns_768 = (int)$_POST['service_columns_768'];
        $service_columns_1200 = (int)$_POST['service_columns_1200'];
        $service_columns_max = (int)$_POST['service_columns_max'];

        // Cấu hình cột dịch vụ
        $project_columns_375 = (int)$_POST['project_columns_375'];
        $project_columns_425 = (int)$_POST['project_columns_425'];
        $project_columns_768 = (int)$_POST['project_columns_768'];
        $project_columns_1200 = (int)$_POST['project_columns_1200'];
        $project_columns_max = (int)$_POST['project_columns_max'];

        // Kiểm tra giá trị hợp lệ
        $valid_range = fn($val) => $val >= 1 && $val <= 6;
        if (!$valid_range($columns_375) || !$valid_range($columns_425) || !$valid_range($columns_768) || 
            !$valid_range($columns_1200) || !$valid_range($columns_max) ||
            !$valid_range($review_columns_375) || !$valid_range($review_columns_425) || 
            !$valid_range($review_columns_768) || !$valid_range($review_columns_1200) || 
            !$valid_range($review_columns_max) ||
            !$valid_range($partner_columns_375) || !$valid_range($partner_columns_425) || 
            !$valid_range($partner_columns_768) || !$valid_range($partner_columns_1200) || 
            !$valid_range($partner_columns_max) ||
            !$valid_range($blog_columns_375) || !$valid_range($blog_columns_425) || 
            !$valid_range($blog_columns_768) || !$valid_range($blog_columns_1200) || 
            !$valid_range($blog_columns_max) ||
            !$valid_range($service_columns_375) || !$valid_range($service_columns_425) || 
            !$valid_range($service_columns_768) || !$valid_range($service_columns_1200) || 
            !$valid_range($service_columns_max) ||
            !$valid_range($project_columns_375) || !$valid_range($project_columns_425) || 
            !$valid_range($project_columns_768) || !$valid_range($project_columns_1200) || 
            !$valid_range($project_columns_max)) {
            throw new Exception('Số cột phải từ 1 đến 6.');
        }

        // Cập nhật settings
        $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (:name, :value) ON DUPLICATE KEY UPDATE value = :value");
        
        // Cột sản phẩm
        $stmt->execute(['name' => 'columns_375', 'value' => $columns_375]);
        $stmt->execute(['name' => 'columns_425', 'value' => $columns_425]);
        $stmt->execute(['name' => 'columns_768', 'value' => $columns_768]);
        $stmt->execute(['name' => 'columns_1200', 'value' => $columns_1200]);
        $stmt->execute(['name' => 'columns_max', 'value' => $columns_max]);

        // Cột ý kiến khách hàng
        $stmt->execute(['name' => 'review_columns_375', 'value' => $review_columns_375]);
        $stmt->execute(['name' => 'review_columns_425', 'value' => $review_columns_425]);
        $stmt->execute(['name' => 'review_columns_768', 'value' => $review_columns_768]);
        $stmt->execute(['name' => 'review_columns_1200', 'value' => $review_columns_1200]);
        $stmt->execute(['name' => 'review_columns_max', 'value' => $review_columns_max]);

        // Cột đối tác
        $stmt->execute(['name' => 'partner_columns_375', 'value' => $partner_columns_375]);
        $stmt->execute(['name' => 'partner_columns_425', 'value' => $partner_columns_425]);
        $stmt->execute(['name' => 'partner_columns_768', 'value' => $partner_columns_768]);
        $stmt->execute(['name' => 'partner_columns_1200', 'value' => $partner_columns_1200]);
        $stmt->execute(['name' => 'partner_columns_max', 'value' => $partner_columns_max]);

        // Cột blog
        $stmt->execute(['name' => 'blog_columns_375', 'value' => $blog_columns_375]);
        $stmt->execute(['name' => 'blog_columns_425', 'value' => $blog_columns_425]);
        $stmt->execute(['name' => 'blog_columns_768', 'value' => $blog_columns_768]);
        $stmt->execute(['name' => 'blog_columns_1200', 'value' => $blog_columns_1200]);
        $stmt->execute(['name' => 'blog_columns_max', 'value' => $blog_columns_max]);

         // Cột dịch vụ
        $stmt->execute(['name' => 'service_columns_375', 'value' => $service_columns_375]);
        $stmt->execute(['name' => 'service_columns_425', 'value' => $service_columns_425]);
        $stmt->execute(['name' => 'service_columns_768', 'value' => $service_columns_768]);
        $stmt->execute(['name' => 'service_columns_1200', 'value' => $service_columns_1200]);
        $stmt->execute(['name' => 'service_columns_max', 'value' => $service_columns_max]);

        // Cột dự án
        $stmt->execute(['name' => 'project_columns_375', 'value' => $project_columns_375]);
        $stmt->execute(['name' => 'project_columns_425', 'value' => $project_columns_425]);
        $stmt->execute(['name' => 'project_columns_768', 'value' => $project_columns_768]);
        $stmt->execute(['name' => 'project_columns_1200', 'value' => $project_columns_1200]);
        $stmt->execute(['name' => 'project_columns_max', 'value' => $project_columns_max]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Cập nhật cấu hình thành công'];
        echo '<script>window.location.href="?page=cauhinhcot";</script>';
        exit;
    } catch (Exception $e) {
        error_log('Update columns error: ' . $e->getMessage());
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Có lỗi khi cập nhật cấu hình: ' . $e->getMessage()];
        echo '<script>window.location.href="?page=cauhinhcot";</script>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu hình cột</title>
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card { border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .btn-primary { background: #4e73df; border: none; }
        @media (max-width: 768px) {
            .form-group label, .form-group input { font-size: 0.9em; }
            .btn { font-size: 0.9em; }
        }
    </style>
</head>
<body>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cấu hình cột</h1>
    </div>

    <!-- Form cấu hình cột -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cấu hình số cột hiển thị</h6>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['message'])): ?>
                <script>
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: '<?php echo $_SESSION['message']['type']; ?>',
                            title: '<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>',
                            html: '<?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        console.error('SweetAlert2 not loaded');
                        alert('<?php echo $_SESSION['message']['type'] === 'success' ? 'Thành công' : 'Lỗi'; ?>: <?php echo htmlspecialchars($_SESSION['message']['text'], ENT_QUOTES); ?>');
                    }
                </script>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form method="POST">
                <!-- Cột sản phẩm -->
                <h6 class="m-0 font-weight-bold text-primary mb-3">Sản phẩm</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="columns_375" name="columns_375" value="<?php echo htmlspecialchars($settings['columns_375']); ?>" min="1" max="6" required>
                        </div>
                         <div class="form-group">
                            <label for="columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="columns_768" name="columns_768" value="<?php echo htmlspecialchars($settings['columns_768']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="columns_425" name="columns_425" value="<?php echo htmlspecialchars($settings['columns_425']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="columns_1200" name="columns_1200" value="<?php echo htmlspecialchars($settings['columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="columns_max" name="columns_max" value="<?php echo htmlspecialchars($settings['columns_max']); ?>" min="1" max="6" required>
                        </div>

                    </div>
                </div>

                <!-- Cột ý kiến khách hàng -->
                <hr class="my-4">
                <h6 class="m-0 font-weight-bold text-primary mb-3">Ý kiến khách hàng</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="review_columns_375" name="review_columns_375" value="<?php echo htmlspecialchars($settings['review_columns_375']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="review_columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="review_columns_425" name="review_columns_425" value="<?php echo htmlspecialchars($settings['review_columns_425']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="review_columns_768" name="review_columns_768" value="<?php echo htmlspecialchars($settings['review_columns_768']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="review_columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="review_columns_1200" name="review_columns_1200" value="<?php echo htmlspecialchars($settings['review_columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="review_columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="review_columns_max" name="review_columns_max" value="<?php echo htmlspecialchars($settings['review_columns_max']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                </div>

                <!-- Cột đối tác -->
                <hr class="my-4">
                <h6 class="m-0 font-weight-bold text-primary mb-3">Đối tác</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="partner_columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="partner_columns_375" name="partner_columns_375" value="<?php echo htmlspecialchars($settings['partner_columns_375']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="partner_columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="partner_columns_425" name="partner_columns_425" value="<?php echo htmlspecialchars($settings['partner_columns_425']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="partner_columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="partner_columns_768" name="partner_columns_768" value="<?php echo htmlspecialchars($settings['partner_columns_768']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="partner_columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="partner_columns_1200" name="partner_columns_1200" value="<?php echo htmlspecialchars($settings['partner_columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="partner_columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="partner_columns_max" name="partner_columns_max" value="<?php echo htmlspecialchars($settings['partner_columns_max']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                </div>

                <!-- Cột blog -->
                <hr class="my-4">
                <h6 class="m-0 font-weight-bold text-primary mb-3">Bài viết blog</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blog_columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="blog_columns_375" name="blog_columns_375" value="<?php echo htmlspecialchars($settings['blog_columns_375']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="blog_columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="blog_columns_425" name="blog_columns_425" value="<?php echo htmlspecialchars($settings['blog_columns_425']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blog_columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="blog_columns_768" name="blog_columns_768" value="<?php echo htmlspecialchars($settings['blog_columns_768']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="blog_columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="blog_columns_1200" name="blog_columns_1200" value="<?php echo htmlspecialchars($settings['blog_columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blog_columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="blog_columns_max" name="blog_columns_max" value="<?php echo htmlspecialchars($settings['blog_columns_max']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                </div>

                <!-- Cột Dịch vụ -->
                <hr class="my-4">
                <h6 class="m-0 font-weight-bold text-primary mb-3">Bài viết Dịch vụ</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="service_columns_375" name="service_columns_375" value="<?php echo htmlspecialchars($settings['service_columns_375']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="service_columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="service_columns_425" name="service_columns_425" value="<?php echo htmlspecialchars($settings['service_columns_425']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="service_columns_768" name="service_columns_768" value="<?php echo htmlspecialchars($settings['service_columns_768']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="service_columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="service_columns_1200" name="service_columns_1200" value="<?php echo htmlspecialchars($settings['service_columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="service_columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="service_columns_max" name="service_columns_max" value="<?php echo htmlspecialchars($settings['service_columns_max']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                </div>

                <!-- Cột Dự án -->
                <hr class="my-4">
                <h6 class="m-0 font-weight-bold text-primary mb-3">Bài viết Dự án</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="project_columns_375">Dưới 375px (Mobile nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="project_columns_375" name="project_columns_375" value="<?php echo htmlspecialchars($settings['project_columns_375']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="project_columns_425">Dưới 425px <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="project_columns_425" name="project_columns_425" value="<?php echo htmlspecialchars($settings['project_columns_425']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="project_columns_768">Dưới 768px (Tablet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="project_columns_768" name="project_columns_768" value="<?php echo htmlspecialchars($settings['project_columns_768']); ?>" min="1" max="6" required>
                        </div>
                        <div class="form-group">
                            <label for="project_columns_1200">Dưới 1200px (Desktop nhỏ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="project_columns_1200" name="project_columns_1200" value="<?php echo htmlspecialchars($settings['project_columns_1200']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="project_columns_max">Tối đa (1200px trở lên) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="project_columns_max" name="project_columns_max" value="<?php echo htmlspecialchars($settings['project_columns_max']); ?>" min="1" max="6" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
            </form>
        </div>
    </div>

</body>
</html>