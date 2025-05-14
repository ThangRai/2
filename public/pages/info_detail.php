<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra slug
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: /2/public/pages/info.php');
    exit;
}

$slug = $_GET['slug'];

// Lấy thông tin bài viết
$stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: /2/public/pages/info.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['seo_title'] ?: $article['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($article['seo_description'] ?: substr($article['description'], 0, 160)); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($article['seo_keywords']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Roboto', sans-serif;
        }
        .article-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }
        .article-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .article-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .article-description {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .article-content {
            font-size: 1rem;
            line-height: 1.8;
            color: #333;
        }
        .article-meta {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn-back {
            background: linear-gradient(45deg, #6c757d, #8a959f);
            border: none;
            padding: 8px 20px;
            font-size: 0.9rem;
            color: #fff;
            border-radius: 25px;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-back:hover {
            background: linear-gradient(45deg, #5a6268, #787f87);
            transform: scale(1.05);
        }
        @media (max-width: 768px) {
            .article-container {
                padding: 20px;
            }
            .article-title {
                font-size: 1.5rem;
            }
            .article-description, .article-content {
                font-size: 0.95rem;
            }
            .btn-back {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="article-container">
            <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-meta">
                <i class="fas fa-calendar-alt"></i> Đăng ngày: <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
            </div>
            <?php if ($article['thumbnail'] && file_exists("C:/laragon/www/2/admin/" . $article['thumbnail'])): ?>
                <img src="/2/admin/<?php echo htmlspecialchars($article['thumbnail']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
            <?php endif; ?>
            <p class="article-description"><?php echo htmlspecialchars($article['description']); ?></p>
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>
            <a href="/2/public/pages/info.php" class="btn btn-back mt-4">Quay lại danh sách</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>