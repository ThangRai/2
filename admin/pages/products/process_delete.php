<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ?page=products");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ?page=products");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: ?page=products");
exit;
?>