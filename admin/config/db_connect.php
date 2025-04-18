<?php
// KHÔNG có khoảng trắng trước <?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ecommerce_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    error_log('DB connection error: ' . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Lỗi kết nối cơ sở dữ liệu']));
}

?>