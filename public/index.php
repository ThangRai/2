<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Kết nối cơ sở dữ liệu
try {
    require_once '../admin/config/db_connect.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    echo '<h1>500 - Lỗi kết nối cơ sở dữ liệu</h1>';
    exit;
}

// Kiểm tra trạng thái website
try {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'site_status'");
    $stmt->execute();
    $site_status = (int)$stmt->fetchColumn();
    if (!$site_status) {
        if (file_exists('pages/maintenance.php')) {
            require_once 'pages/maintenance.php';
        } else {
            header('HTTP/1.0 503 Service Unavailable');
            echo '<h1>503 Service Unavailable</h1><p>Website đang bảo trì. Vui lòng quay lại sau.</p>';
        }
        exit;
    }
} catch (Exception $e) {
    error_log('Site status check error: ' . $e->getMessage());
}

// Xử lý route
$request_uri = trim($_SERVER['REQUEST_URI'], '/');
$base_path = trim('/2/public', '/');
$path = trim(substr($request_uri, strlen($base_path)), '/');

// Danh sách module cố định
$modules = [
    'home' => 'home.php',
    'about' => 'about.php',
    'products' => 'products.php',
    'services' => 'services.php',
    'projects' => 'projects.php',
    'news' => 'news.php',
    'contact' => 'contact.php',
    'gallery' => 'gallery.php',
    'testimonials' => 'testimonials.php',
    'partners' => 'partners.php',
];

// Lấy dữ liệu chung
try {
    // Lấy slides
    $stmt = $pdo->prepare("SELECT image, title, description, link FROM slides WHERE status = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy sản phẩm nổi bật
    $stmt = $pdo->prepare("SELECT id, name, image, current_price FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy meta SEO cho trang chủ
    $stmt = $pdo->prepare("SELECT seo_title, seo_description, seo_tags FROM settings WHERE name = 'home_seo'");
    $stmt->execute();
    $seo = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['seo_title' => 'Trang Chủ - Website', 'seo_description' => '', 'seo_tags' => ''];
} catch (Exception $e) {
    error_log('Data fetch error: ' . $e->getMessage());
    $slides = [];
    $featured_products = [];
    $seo = ['seo_title' => 'Trang Chủ - Website', 'seo_description' => '', 'seo_tags' => ''];
}

// Xử lý route
if (empty($path) || $path === 'home') {
    // Trang chủ
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($seo['seo_title'], ENT_QUOTES); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($seo['seo_description'], ENT_QUOTES); ?>">
        <meta name="keywords" content="<?php echo htmlspecialchars($seo['seo_tags'], ENT_QUOTES); ?>">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background: #f4f4f4;
            }
            .container {
                max-width: 1200px !important;
                margin: 0 auto;
                padding: 20px;
                text-align: center;
            }
            h1, h2 {
                color: #333;
            }
            p {
                font-size: 1.2em;
            }
            .carousel {
                margin-bottom: 20px;
            }
            .carousel-inner img {
                width: 100%;
                height: 500px;
                object-fit: cover;
            }
            .carousel-caption {
                background: rgba(0, 0, 0, 0.6);
                border-radius: 5px;
                padding: 15px;
                bottom: 20px;
            }
            .carousel-caption h5 {
                font-size: 1.5em;
                color: #fff;
                margin-bottom: 10px;
            }
            .carousel-caption p {
                font-size: 1em;
                color: #ddd;
                margin-bottom: 0;
            }
            .carousel-control-prev, .carousel-control-next {
                width: 5%;
                background: rgba(0, 0, 0, 0.3);
                transition: background 0.3s;
            }
            .carousel-control-prev:hover, .carousel-control-next:hover {
                background: unset;
            }
            .carousel-control-prev-icon, .carousel-control-next-icon {
                font-size: 2em;
            }
            .fa-chevron-right:before, .fa-chevron-left:before {
                display: none;
            }
            .intro-section {
                margin: 40px 0;
                padding: 20px;
                background: linear-gradient(135deg, #ffffff, #e8f0fe);
                border-radius: 10px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            }
            .intro-section h2 {
                font-size: 2em;
                margin-bottom: 15px;
                background: linear-gradient(90deg, #3b82f6, #93c5fd);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .intro-section p {
                font-size: 1.1em;
                color: #4b5563;
            }
            @media (max-width: 768px) {
                .carousel-inner img {
                    height: 300px;
                }
                .carousel-caption h5 {
                    font-size: 1.2em;
                }
                .carousel-caption p {
                    font-size: 0.9em;
                }
                .intro-section h2 {
                    font-size: 1.5em;
                }
                .intro-section p {
                    font-size: 1em;
                }
            }
            @media (max-width: 576px) {
                .carousel-inner img {
                    height: 200px;
                }
                .carousel-caption h5 {
                    font-size: 1em;
                }
                .carousel-caption p {
                    font-size: 0.8em;
                }
                .intro-section h2 {
                    font-size: 1.3em;
                }
            }
        </style>
    </head>
    <body>
        <?php
        if (file_exists('includes/header.php')) {
            require_once 'includes/header.php';
        } else {
            echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file header.php</p></div>';
        }
        ?>

        <div class="container">
            <?php if (!empty($slides)): ?>
                <div id="slideCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($slides as $index => $slide): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <a href="<?php echo htmlspecialchars($slide['link'] ?: '#', ENT_QUOTES); ?>">
                                    <img src="/2/admin/<?php echo htmlspecialchars($slide['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($slide['title'], ENT_QUOTES); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#slideCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#slideCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            <?php else: ?>
                <p>Không có slide nào để hiển thị.</p>
            <?php endif; ?>

            <?php
            $sections = ['info', 'product', 'blog', 'reviews', 'question', 'doitac'];
            foreach ($sections as $section) {
                $file_path = "pages/$section.php";
                if (file_exists($file_path)) {
                    require_once $file_path;
                } else {
                    echo "<p style='color: red;'>Lỗi: Không tìm thấy file $section.php</p>";
                }
            }
            ?>
        </div>

        <?php
        if (file_exists('includes/footer.php')) {
            require_once 'includes/footer.php';
        } else {
            echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file footer.php</p></div>';
        }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
} elseif (isset($modules[$path])) {
    // Module cố định
    if (file_exists($modules[$path])) {
        include $modules[$path];
    } else {
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 - Không tìm thấy trang</h1>';
    }
} else {
    // Kiểm tra danh mục theo slug
    try {
        $stmt = $pdo->prepare("SELECT slug FROM categories WHERE slug = ? AND status = 1 LIMIT 1");
        $stmt->execute([$path]);
        if ($stmt->fetch()) {
            $_GET['slug'] = $path;
            if (file_exists('category.php')) {
                include 'category.php';
            } else {
                header('HTTP/1.0 404 Not Found');
                echo '<h1>404 - Không tìm thấy file category.php</h1>';
            }
        } else {
            header('HTTP/1.0 404 Not Found');
            echo '<h1>404 - Không tìm thấy trang</h1>';
        }
    } catch (Exception $e) {
        error_log('Slug check error: ' . $e->getMessage());
        header('HTTP/1.0 500 Internal Server Error');
        echo '<h1>500 - Lỗi server</h1>';
    }
}

ob_end_flush();
?>