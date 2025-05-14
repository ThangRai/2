<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra slug
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (empty($slug)) {
    header("HTTP/1.0 404 Not Found");
    echo '<h1>404 - Không tìm thấy trang</h1>';
    exit;
}

// Lấy thông tin dịch vụ
$stmt = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("HTTP/1.0 404 Not Found");
    echo '<h1>404 - Dịch vụ không tồn tại hoặc chưa được công bố</h1>';
    exit;
}

// Lấy danh sách dịch vụ khác (liên quan)
$stmt = $pdo->prepare("SELECT id, title, slug, thumbnail FROM services WHERE slug != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$slug]);
$related_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['seo_title'] ?: $service['title']); ?> - THANGRAI WEBSITE</title>
    <meta name="description" content="<?php echo htmlspecialchars($service['seo_description'] ?: substr(strip_tags($service['description']), 0, 160)); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($service['seo_keywords'] ?: ''); ?>">
    <meta name="robots" content="index, follow">
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/2/admin/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .service-detail-container { padding: 50px 0; }
        .service-title { font-size: 2.5rem; font-weight: bold; margin-bottom: 20px; }
        .service-description { font-size: 1.1rem; color: #555; margin-bottom: 30px; }
        .service-content img { max-width: 100%; height: auto; }
        .service-image { max-width: 100%; border-radius: 8px; margin-bottom: 20px; }
        .related-services { margin-top: 50px; }
        .related-services .card { transition: transform 0.2s; }
        .related-services .card:hover { transform: translateY(-5px); }
        .related-services img { height: 150px; object-fit: cover; }
        @media (max-width: 768px) {
            .service-title { font-size: 1.8rem; }
            .service-description { font-size: 1rem; }
            .related-services img { height: 120px; }
        }
    </style>
</head>
<body>
    <!-- Header (có thể include file header.php nếu có) -->
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <!-- Nội dung chi tiết dịch vụ -->
    <div class="container service-detail-container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="service-title"><?php echo htmlspecialchars($service['title']); ?></h1>
                <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                <?php if ($service['thumbnail']): ?>
                    <img src="/2/admin/<?php echo htmlspecialchars($service['thumbnail']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="service-image">
                <?php endif; ?>
                <div class="service-content">
                    <?php echo $service['content']; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="related-services">
                    <h4 class="mb-4">Dịch vụ liên quan</h4>
                    <?php if ($related_services): ?>
                        <?php foreach ($related_services as $related): ?>
                            <div class="card mb-3">
                                <?php if ($related['thumbnail']): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($related['thumbnail']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($related['title']); ?></h5>
                                    <a href="/2/public/pages/dichvu_detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Không có dịch vụ liên quan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (có thể include file footer.php nếu có) -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 THANGRAI WEBSITE. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="/2/admin/assets/js/jquery.min.js"></script>
    <script src="/2/admin/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/2/admin/assets/js/sb-admin-2.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>