<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy cấu hình cột từ settings
$column_settings = [];
$keys = ['service_columns_375', 'service_columns_425', 'service_columns_768', 'service_columns_1200', 'service_columns_max'];
foreach ($keys as $key) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $column_settings[$key] = $result ? (int)$result['value'] : 1;
}

// Lấy danh sách dịch vụ đã xuất bản
$stmt = $pdo->prepare("SELECT id, title, slug, description, thumbnail FROM services WHERE is_published = 1 ORDER BY created_at DESC");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách dịch vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1400px;
        }
        .service-section-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 2rem;
            position: relative;
            text-align: center;
        }
        .service-section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #007bff, #00d4ff);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .service-grid {
            display: grid;
            gap: 25px;
            padding: 0 15px;
        }
        .service-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }
        .service-card:nth-child(5) { animation-delay: 0.5s; }
        .service-card:nth-child(6) { animation-delay: 0.6s; }
        .service-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        .service-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .service-card:hover img {
            transform: scale(1.1);
        }
        .service-card-body {
            padding: 20px;
            text-align: center;
        }
        .service-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }
        .service-card:hover .service-card-title {
            color: #007bff;
        }
        .service-card-text {
            font-size: 0.95rem;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .service-card-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #fff;
            background: linear-gradient(to right, #007bff, #00d4ff);
            border: none;
            border-radius: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-card-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
            background: linear-gradient(to right, #0056b3, #0096cc);
        }
        .no-services {
            font-size: 1.2rem;
            color: #6c757d;
            text-align: center;
            padding: 50px 0;
        }
        /* Responsive grid */
        @media (min-width: 375px) {
            .service-grid {
                grid-template-columns: repeat(<?php echo $column_settings['service_columns_375']; ?>, 1fr);
            }
        }
        @media (min-width: 425px) {
            .service-grid {
                grid-template-columns: repeat(<?php echo $column_settings['service_columns_425']; ?>, 1fr);
            }
        }
        @media (min-width: 768px) {
            .service-grid {
                grid-template-columns: repeat(<?php echo $column_settings['service_columns_768']; ?>, 1fr);
            }
        }
        @media (min-width: 1200px) {
            .service-grid {
                grid-template-columns: repeat(<?php echo $column_settings['service_columns_1200']; ?>, 1fr);
            }
        }
        @media (min-width: 1400px) {
            .service-grid {
                grid-template-columns: repeat(<?php echo $column_settings['service_columns_max']; ?>, 1fr);
            }
        }
        /* Animation keyframes */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="service-section-title">Danh sách dịch vụ</h1>
        <?php if (empty($services)): ?>
            <p class="no-services">Hiện tại chưa có dịch vụ nào.</p>
        <?php else: ?>
            <div class="service-grid">
                <?php foreach ($services as $service): ?>
                    <div class="service-card">
                        <?php if ($service['thumbnail']): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($service['thumbnail']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                        <?php else: ?>
                            <img src="/2/public/assets/images/placeholder.jpg" alt="Placeholder">
                        <?php endif; ?>
                        <div class="service-card-body">
                            <h5 class="service-card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                            <p class="service-card-text"><?php echo htmlspecialchars(substr($service['description'], 0, 100)) . (strlen($service['description']) > 100 ? '...' : ''); ?></p>
                            <a href="/2/public/pages/dichvu_detail.php?slug=<?php echo htmlspecialchars($service['slug']); ?>" class="service-card-btn">Xem chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>