<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy cấu hình số cột
try {
    $stmt = $pdo->query("SELECT name, value FROM settings WHERE name LIKE 'partner_columns_%'");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['name']] = (int)$row['value'];
    }
} catch (Exception $e) {
    error_log('Config partner columns error: ' . $e->getMessage());
    $settings = [
        'partner_columns_375' => 1,
        'partner_columns_425' => 1,
        'partner_columns_768' => 2,
        'partner_columns_1200' => 3,
        'partner_columns_max' => 4
    ];
}

// Lấy danh sách đối tác (chỉ hiển thị is_visible = 1)
$stmt = $pdo->prepare("SELECT * FROM partners WHERE is_visible = 1 ORDER BY created_at DESC");
$stmt->execute();
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đối tác</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .partners-grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(<?php echo $settings['partner_columns_375']; ?>, 1fr);
        }
        .partner-card {
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .partner-card:hover {
            transform: translateY(-5px);
        }
        .partner-card img {
            max-width: 170px;
            max-height: 100px;
            object-fit: contain;
            border-radius: 10px;
        }
        .partner-card a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        .partner-card a:hover {
            text-decoration: underline;
        }

        /* Responsive Grid */
        @media (min-width: 375px) {
            .partners-grid {
                grid-template-columns: repeat(<?php echo $settings['partner_columns_425']; ?>, 1fr);
            }
        }
        @media (min-width: 425px) {
            .partners-grid {
                grid-template-columns: repeat(<?php echo $settings['partner_columns_768']; ?>, 1fr);
            }
        }
        @media (min-width: 768px) {
            .partners-grid {
                grid-template-columns: repeat(<?php echo $settings['partner_columns_1200']; ?>, 1fr);
            }
        }
        @media (min-width: 1200px) {
            .partners-grid {
                grid-template-columns: repeat(<?php echo $settings['partner_columns_max']; ?>, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="section-title">Danh sách đối tác</h1>
        <div class="partners-grid">
            <?php foreach ($partners as $partner): ?>
                <div class="partner-card">
                    <?php if ($partner['logo']): ?>
                        <img src="http://localhost/2/admin/uploads/doitac/<?php echo htmlspecialchars($partner['logo']); ?>" alt="Logo">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/100" alt="Default Logo">
                    <?php endif; ?>
                    <?php if ($partner['link']): ?>
                        <a href="<?php echo htmlspecialchars($partner['link']); ?>" target="_blank">Xem website</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>