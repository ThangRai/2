<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Kiểm tra slug
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (empty($slug)) {
    header('Location: /2/public/pages/404.php');
    exit;
}

// Lấy bài viết
$stmt = $pdo->prepare("SELECT id, title, content FROM blogs WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$blog) {
    header('Location: /2/public/pages/404.php');
    exit;
}

// Tăng lượt xem
try {
    $stmt = $pdo->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
    $stmt->execute([$blog['id']]);
} catch (Exception $e) {
    error_log('Update views error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .blog-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 2rem;
        }
        .blog-content {
            font-size: 1rem;
            line-height: 1.8;
            color: #495057;
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }
        .blog-content h2, .blog-content h3, .blog-content h4 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            scroll-margin-top: 80px; /* Khoảng cách khi cuộn */
        }
        .toc {
            background: #ffffff;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .toc h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1.25rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        .toc ul {
            list-style: none;
            padding: 0;
        }
        .toc li {
            margin-bottom: 0.75rem;
            position: relative;
            padding-left: 1.5rem;
        }
        .toc li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #2563eb;
            font-size: 1.2rem;
            line-height: 1.5;
        }
        .toc li.toc-h3 {
            margin-left: 1.5rem;
            padding-left: 1.75rem;
        }
        .toc li.toc-h4 {
            margin-left: 3rem;
            padding-left: 2rem;
        }
        .toc a {
            text-decoration: none;
            color: #343a40;
            font-size: 1rem;
            transition: color 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }
        .toc a:hover {
            color: #2563eb;
            transform: translateX(5px);
        }
        .toc li.toc-h2 a {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .toc li.toc-h3 a {
            font-weight: 500;
            font-size: 0.95rem;
        }
        .toc li.toc-h4 a {
            font-weight: 400;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .blog-title {
                font-size: 2rem;
            }
            .content-container {
                padding: 1rem;
            }
            .toc {
                padding: 1rem;
                position: static;
            }
            .toc h4 {
                font-size: 1.25rem;
            }
            .toc a {
                font-size: 0.9rem;
            }
            .toc li.toc-h2 a {
                font-size: 1rem;
            }
            .toc li.toc-h3 a {
                font-size: 0.85rem;
            }
            .toc li.toc-h4 a {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
<?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="content-container">
        <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>

        <!-- Mục lục -->
        <div class="toc" id="toc"></div>

        <!-- Nội dung bài viết -->
        <div class="blog-content" id="blog-content"><?php echo $blog['content']; ?></div>
    </div>
    
    <?php         require_once 'C:/laragon/www/2/public/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tạo mục lục từ các heading
        function generateTOC() {
            const content = document.getElementById('blog-content');
            const toc = document.getElementById('toc');
            const headings = content.querySelectorAll('h2, h3, h4');
            if (headings.length === 0) {
                toc.style.display = 'none';
                return;
            }

            const ul = document.createElement('ul');
            ul.innerHTML = '<h4>Mục lục</h4>';
            headings.forEach((heading, index) => {
                const id = `heading-${index}`;
                heading.id = id; // Gán ID cho heading
                const li = document.createElement('li');
                li.className = heading.tagName.toLowerCase() === 'h3' ? 'toc-h3' : heading.tagName.toLowerCase() === 'h4' ? 'toc-h4' : 'toc-h2';
                const a = document.createElement('a');
                a.href = `#${id}`;
                a.textContent = heading.textContent;
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
                });
                li.appendChild(a);
                ul.appendChild(li);
            });
            toc.appendChild(ul);
        }

        // Chạy khi trang tải
        document.addEventListener('DOMContentLoaded', generateTOC);
    </script>
</body>
</html>