<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy cấu hình số cột
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'review_columns_%'");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = (int)$row['value'];
    }
} catch (Exception $e) {
    error_log('Config review columns error: ' . $e->getMessage());
    $settings = [
        'review_columns_375' => 1,
        'review_columns_425' => 1,
        'review_columns_768' => 2,
        'review_columns_1200' => 3,
        'review_columns_max' => 3
    ];
}

// Lấy danh sách ý kiến khách hàng (chỉ hiển thị is_visible = 1)
$stmt = $pdo->prepare("SELECT * FROM customer_reviews WHERE is_visible = 1 ORDER BY created_at DESC");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ý kiến khách hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #343a40;
            margin-bottom: 40px;
        }
        .reviews-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(<?php echo $settings['review_columns_375']; ?>, 1fr);
        }
        .review-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
        }
        .review-card:hover {
            transform: translateY(-5px);
        }
        .review-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .review-card h3 {
            margin: 0;
            color: #343a40;
            font-size: 1.2rem;
        }
        .review-card .rating {
            color: #f1c40f;
            margin: 10px 0;
        }
        .review-card .description {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .review-card p {
            color: #495057;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Responsive Grid */
        @media (min-width: 375px) {
            .reviews-grid {
                grid-template-columns: repeat(<?php echo $settings['review_columns_425']; ?>, 1fr);
            }
        }
        @media (min-width: 425px) {
            .reviews-grid {
                grid-template-columns: repeat(<?php echo $settings['review_columns_768']; ?>, 1fr);
            }
        }
        @media (min-width: 768px) {
            .reviews-grid {
                grid-template-columns: repeat(<?php echo $settings['review_columns_1200']; ?>, 1fr);
            }
        }
        @media (min-width: 1200px) {
            .reviews-grid {
                grid-template-columns: repeat(<?php echo $settings['review_columns_max']; ?>, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="section-title">Ý kiến khách hàng</h1>
        <div class="reviews-grid">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <?php if ($review['avatar']): ?>
                        <img src="http://localhost/2/admin/uploads/dgkhachhang/<?php echo htmlspecialchars($review['avatar']); ?>" alt="Avatar">
                        <?php else: ?>
                        <img src="https://via.placeholder.com/80" alt="Default Avatar">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="description"><?php echo htmlspecialchars($review['description']); ?></div>
                    <p><?php echo htmlspecialchars($review['content']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        .fa-star.filled {
            color: #f1c40f;
        }
        .fa-star {
            color: #e0e0e0;
        }
    </style>
</body>
</html>