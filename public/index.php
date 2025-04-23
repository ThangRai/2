<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    // Kiểm tra trạng thái website
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'site_status'");
    $stmt->execute();
    $site_status = (int)$stmt->fetchColumn();
    if (!$site_status) {
        if (file_exists('C:/laragon/www/2/public/pages/maintenance.php')) {
            require_once 'C:/laragon/www/2/public/pages/maintenance.php';
        } else {
            header('HTTP/1.0 503 Service Unavailable');
            echo '<h1>503 Service Unavailable</h1><p>Website đang bảo trì. Vui lòng quay lại sau.</p>';
        }
        exit;
    }
    // Lấy slides
    $stmt = $pdo->prepare("SELECT image, title, description, link FROM slides WHERE status = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Lấy sản phẩm nổi bật (ví dụ: 4 sản phẩm mới nhất)
    $stmt = $pdo->prepare("SELECT id, name, image, current_price FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Index error: ' . $e->getMessage());
    $slides = [];
    $featured_products = [];
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Website</title>
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
        /* Carousel */
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
        /* Intro Section */
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
        /* Responsive */
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
            .intro-section h2, .featured-products h2 {
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
            .intro-section h2, .featured-products h2 {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
    <?php
    if (file_exists('C:/laragon/www/2/public/includes/header.php')) {
        require_once 'C:/laragon/www/2/public/includes/header.php';
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
                                <img src="http://localhost/2/admin/<?php echo htmlspecialchars($slide['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($slide['title'], ENT_QUOTES); ?>">
                            </a>
                            <!-- <div class="carousel-caption">
                                <h5><?php echo htmlspecialchars($slide['title'], ENT_QUOTES); ?></h5>
                                <?php if ($slide['description']): ?>
                                    <p><?php echo htmlspecialchars($slide['description'], ENT_QUOTES); ?></p>
                                <?php endif; ?>
                            </div> -->
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

        <!-- Intro Section -->
        <!-- <div class="intro-section">
            <h2>Chào Mừng Đến Với Thắng Raiy</h2>
            <p>
                Khám phá các sản phẩm chất lượng cao với giá cả hợp lý. Chúng tôi cam kết mang đến trải nghiệm mua sắm tuyệt vời!
            </p>
        </div> -->

        <?php
        if (file_exists('C:/laragon/www/2/public/pages/product.php')) {
            require_once 'C:/laragon/www/2/public/pages/product.php';
        } else {
            echo '<p style="color: red;">Lỗi: Không tìm thấy file product.php</p>';
        }
        ?>

        <?php
            if (file_exists('C:/laragon/www/2/public/pages/blog.php')) {
                require_once 'C:/laragon/www/2/public/pages/blog.php';
            } else {
                echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file footer.php</p></div>';
            }
        ?>

        <?php
            if (file_exists('C:/laragon/www/2/public/pages/reviews.php')) {
                require_once 'C:/laragon/www/2/public/pages/reviews.php';
            } else {
                echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file footer.php</p></div>';
            }
        ?>



        <?php
            if (file_exists('C:/laragon/www/2/public/pages/doitac.php')) {
                require_once 'C:/laragon/www/2/public/pages/doitac.php';
            } else {
                echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file footer.php</p></div>';
            }
        ?>
    </div>



    <?php
    if (file_exists('C:/laragon/www/2/public/includes/footer.php')) {
        require_once 'C:/laragon/www/2/public/includes/footer.php';
    } else {
        echo '<div class="container"><p style="color: red;">Lỗi: Không tìm thấy file footer.php</p></div>';
    }
    ?>

</body>
</html>
<?php ob_end_flush(); ?>