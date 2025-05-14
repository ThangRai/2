<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';

    // Base URL (thay bằng domain thật khi triển khai)
    $base_url = 'http://localhost/2';

    // Kiểm tra cột updated_at
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'updated_at'");
    $has_updated_at = $stmt->rowCount() > 0;

    // Lấy tất cả sản phẩm active
    $stmt = $pdo->prepare("SELECT slug, " . ($has_updated_at ? 'updated_at' : 'created_at') . " AS lastmod 
                           FROM products 
                           WHERE is_active = 1");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Định dạng XML
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    // Trang danh sách sản phẩm
    echo '    <url>' . PHP_EOL;
    echo '        <loc>' . htmlspecialchars($base_url . '/public/pages/product') . '</loc>' . PHP_EOL;
    echo '        <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
    echo '        <changefreq>daily</changefreq>' . PHP_EOL;
    echo '        <priority>1.0</priority>' . PHP_EOL;
    echo '    </url>' . PHP_EOL;

    // Trang chi tiết sản phẩm
    foreach ($products as $product) {
        echo '    <url>' . PHP_EOL;
        echo '        <loc>' . htmlspecialchars($base_url . '/public/pages/' . $product['slug']) . '</loc>' . PHP_EOL;
        echo '        <lastmod>' . date('Y-m-d', strtotime($product['lastmod'])) . '</lastmod>' . PHP_EOL;
        echo '        <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '        <priority>0.8</priority>' . PHP_EOL;
        echo '    </url>' . PHP_EOL;
    }

    echo '</urlset>' . PHP_EOL;

} catch (Exception $e) {
    error_log('Sitemap error: ' . $e->getMessage());
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    echo '    <url>' . PHP_EOL;
    echo '        <loc>' . htmlspecialchars($base_url . '/public/pages/product') . '</loc>' . PHP_EOL;
    echo '        <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
    echo '        <changefreq>daily</changefreq>' . PHP_EOL;
    echo '        <priority>1.0</priority>' . PHP_EOL;
    echo '    </url>' . PHP_EOL;
    echo '</urlset>' . PHP_EOL;
}

ob_end_flush();
?>
