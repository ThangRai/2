<?php
session_start();
ob_start();

// Kết nối cơ sở dữ liệu
try {
    require_once '../admin/config/db_connect.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage());
}

// Lấy slug từ URL
$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>404 - Không tìm thấy trang</h1>';
    exit;
}

// Lấy thông tin danh mục
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$category) {
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 - Không tìm thấy danh mục</h1>';
        exit;
    }
} catch (Exception $e) {
    error_log('Lỗi lấy danh mục: ' . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    echo '<h1>500 - Lỗi server</h1>';
    exit;
}

// Hàm lọc HTML để tránh XSS
function purifyHTML($html) {
    require_once 'C:/laragon/www/2/admin/assets/lib/htmlpurifier/library/HTMLPurifier.auto.php';
    $config = HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'UTF-8');
    $config->set('HTML.Allowed', 'p,br,b,i,u,strike,ul,ol,li,table,thead,tbody,tr,th,td,img[src|alt],a[href|title],blockquote,div,span,h1,h2,h3,h4,h5,h6');
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^https?://(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($html);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['seo_title'] ?? $category['name'], ENT_QUOTES); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($category['seo_description'] ?? '', ENT_QUOTES); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($category['seo_tags'] ?? '', ENT_QUOTES); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .category-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .category-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .category-content {
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="category-container">
        <h1><?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?></h1>
        <?php if ($category['show_image'] && $category['image'] && file_exists('C:/laragon/www/2/admin/' . $category['image'])): ?>
            <img src="/2/admin/<?php echo htmlspecialchars($category['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>" class="category-image">
        <?php endif; ?>
        <?php if ($category['show_description'] && $category['description']): ?>
            <div class="category-description">
                <?php echo purifyHTML($category['description']); ?>
            </div>
        <?php endif; ?>
        <?php if ($category['show_content'] && $category['content']): ?>
            <div class="category-content">
                <?php echo purifyHTML($category['content']); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>