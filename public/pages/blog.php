<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy cấu hình cột
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'blog_columns_%'");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = $row['value'];
    }
} catch (Exception $e) {
    error_log('Fetch blog columns error: ' . $e->getMessage());
    $settings = [
        'blog_columns_375' => 1,
        'blog_columns_425' => 1,
        'blog_columns_768' => 2,
        'blog_columns_1200' => 4,
        'blog_columns_max' => 6
    ];
}

// Lấy danh sách blog (chỉ hiển thị bài viết đã publish, giới hạn 6 bài đầu)
$sql = "SELECT id, title, slug, description, thumbnail, created_at, views FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 6";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bài viết</title>
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #1e40af;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --background: #f9fafb;
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(255, 255, 255, 0.2);
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #3b82f6, #a5b4fc);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        a {
            text-decoration: none !important;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            margin-bottom: 4rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            position: relative;
            animation: slideIn 1s ease-out;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        .blog-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 4rem;
        }

        .blog-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            overflow: hidden;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            transition: var(--transition);
            opacity: 0;
            animation: fadeInUp 0.7s ease-out forwards;
            position: relative;
        }

        .blog-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .blog-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            transition: var(--transition);
        }

        .blog-card:hover img {
            transform: scale(1.08);
            filter: brightness(1.05);
        }

        .blog-card-content {
            padding: 10px;
        }

        .blog-card-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.3s ease;
        }

        .blog-card:hover .blog-card-title {
            color: var(--primary-color);
        }

        .blog-card-description {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: start;
        }

        .blog-card-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
            font-weight: 500;
        }

        .blog-card-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .blog-card-meta .icon {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .quick-view-btn {
            display: inline-flex;
            align-items: center;
            background: transparent;
            color: var(--primary-color);
            padding: 3px 15px;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .quick-view-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-color);
            transition: left 0.5s ease;
            z-index: -1;
        }

        .quick-view-btn:hover::before {
            left: 0;
        }

        .quick-view-btn:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .quick-view-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .no-results {
            text-align: center;
            font-size: 1.5rem;
            color: var(--text-muted);
            padding: 3rem;
            font-weight: 500;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Grid */
        @media (max-width: 375px) {
            .blog-grid {
                grid-template-columns: repeat(<?php echo $settings['blog_columns_375']; ?>, 1fr);
            }
            .section-title {
                font-size: 2.25rem;
            }
            .blog-card img {
                height: 200px;
            }
            .blog-card-content {
                padding: 1.5rem;
            }
            .blog-card-title {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 376px) and (max-width: 425px) {
            .blog-grid {
                grid-template-columns: repeat(<?php echo $settings['blog_columns_425']; ?>, 1fr);
            }
        }

        @media (min-width: 426px) and (max-width: 768px) {
            .blog-grid {
                grid-template-columns: repeat(<?php echo $settings['blog_columns_768']; ?>, 1fr);
            }
            .blog-card img {
                height: 220px;
            }
        }

        @media (min-width: 769px) and (max-width: 1200px) {
            .blog-grid {
                grid-template-columns: repeat(<?php echo $settings['blog_columns_1200']; ?>, 1fr);
            }
        }

        @media (min-width: 1201px) {
            .blog-grid {
                grid-template-columns: repeat(<?php echo $settings['blog_columns_max']; ?>, 1fr);
            }
        }
    </style>
</head>

<body>
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="container">
        <h1 class="section-title">TIN TỨC</h1>

        <!-- Danh sách blog -->
        <?php if (empty($blogs)): ?>
            <p class="no-results">Không tìm thấy bài viết nào.</p>
        <?php else: ?>
            <div class="blog-grid" id="blog-grid">
                <?php foreach ($blogs as $index => $blog): ?>
                    <div class="blog-card" style="animation-delay: <?php echo $index * 0.2; ?>s">
                        <a href="/2/public/pages/blog_detail.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>">
                            <?php if ($blog['thumbnail']): ?>
                                <img src="/2/admin/<?php echo htmlspecialchars($blog['thumbnail']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x240" alt="Default Thumbnail">
                            <?php endif; ?>
                            <div class="blog-card-content">
                                <h3 class="blog-card-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <p class="blog-card-description"><?php echo htmlspecialchars($blog['description']); ?></p>
                                <div class="blog-card-meta">
                                    <span><span class="icon">📅</span><?php echo date('d/m/Y', strtotime($blog['created_at'])); ?></span>
                                    <span><span class="icon">👁️</span><?php echo $blog['views']; ?> lượt xem</span>
                                </div>
                                <a href="/2/public/pages/blog_detail.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="quick-view-btn">Xem nhanh</a>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>