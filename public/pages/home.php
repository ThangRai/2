<?php
require_once '../admin/config/db_connect.php';

$stmt = $pdo->query("SELECT * FROM products LIMIT 6");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mt-4">
    <h2 class="mb-4">Featured Products</h2>
    <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="assets/img/product/<?php echo $product['image'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>