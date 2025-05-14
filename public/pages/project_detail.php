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

// Lấy thông tin dự án
$stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("HTTP/1.0 404 Not Found");
    echo '<h1>404 - Dự án không tồn tại hoặc chưa được công bố</h1>';
    exit;
}

// Lấy danh sách dự án liên quan
$stmt = $pdo->prepare("SELECT id, title, slug, thumbnail FROM projects WHERE slug != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$slug]);
$related_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['seo_title'] ?: $project['title']); ?> - THANGRAI WEBSITE</title>
    <meta name="description" content="<?php echo htmlspecialchars($project['seo_description'] ?: substr(strip_tags($project['description']), 0, 160)); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($project['seo_keywords'] ?: ''); ?>">
    <meta name="robots" content="index, follow">
    <!-- Đường dẫn file CSS -->
    <link href="/2/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/2/public/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto; /* Căn giữa container */
            padding-left: 15px;
            padding-right: 15px;
        }
        .project-detail-container {
            padding: 50px 0;
        }
        .project-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 20px;
        }
        .project-description {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 30px;
        }
        .project-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .project-image {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .related-projects {
            margin-top: 50px;
        }
        .related-projects .card {
            transition: transform 0.2s;
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .related-projects .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .related-projects img {
            height: 150px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .related-projects .card-body {
            padding: 15px;
            text-align: center;
        }
        .related-projects .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 10px;
        }
        .related-projects .btn {
            background: linear-gradient(to right, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 8px 15px;
            font-size: 0.9rem;
            color: #fff;
        }
        .related-projects .btn:hover {
            background: linear-gradient(to right, #1e7e34, #17a589);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        @media (max-width: 768px) {
            .project-title {
                font-size: 1.8rem;
            }
            .project-description {
                font-size: 1rem;
            }
            .related-projects img {
                height: 120px;
            }
            .related-projects .card-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->


    <!-- Nội dung chi tiết dự án -->
    <div class="container project-detail-container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h1>
                <p class="project-description"><?php echo htmlspecialchars($project['description']); ?></p>
                <?php if ($project['thumbnail']): ?>
                    <img src="/2/admin/<?php echo htmlspecialchars($project['thumbnail']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-image">
                <?php endif; ?>
                <div class="project-content">
                    <?php echo $project['content']; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="related-projects">
                    <h4 class="mb-4">Dự án liên quan</h4>
                    <?php if ($related_projects): ?>
                        <?php foreach ($related_projects as $related): ?>
                            <div class="card mb-3">
                                <?php if ($related['thumbnail']): ?>
                                    <img src="/2/admin/<?php echo htmlspecialchars($related['thumbnail']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php else: ?>
                                    <img src="/2/public/assets/images/placeholder.jpg" class="card-img-top" alt="Placeholder">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($related['title']); ?></h5>
                                    <a href="/2/public/pages/project_detail.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" class="btn btn-sm">Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Không có dự án liên quan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->


    <!-- Scripts -->
    <script src="/2/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>