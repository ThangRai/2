<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Lấy cấu hình cột từ settings
$column_settings = [];
$keys = ['project_columns_375', 'project_columns_425', 'project_columns_768', 'project_columns_1200', 'project_columns_max'];
foreach ($keys as $key) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $column_settings[$key] = $result ? (int)$result['value'] : 1;
}

// Lấy danh sách dự án đã xuất bản
$stmt = $pdo->prepare("SELECT id, title, slug, description, thumbnail FROM projects WHERE is_published = 1 ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách dự án</title>
    <!-- Đường dẫn file CSS -->
    <link href="/2/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/2/public/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto; /* Căn giữa container */
            padding-left: 15px;
            padding-right: 15px;
        }
        .project-section-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #1a3c6d;
            margin-bottom: 2rem;
            position: relative;
            text-align: center;
        }
        .project-section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #28a745, #20c997);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        .project-grid {
            display: grid;
            gap: 25px;
            padding: 0 15px;
        }
        .project-card {
            position: relative;
            background: #fff;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }
        .project-card:nth-child(1) { animation-delay: 0.1s; }
        .project-card:nth-child(2) { animation-delay: 0.2s; }
        .project-card:nth-child(3) { animation-delay: 0.3s; }
        .project-card:nth-child(4) { animation-delay: 0.4s; }
        .project-card:nth-child(5) { animation-delay: 0.5s; }
        .project-card:nth-child(6) { animation-delay: 0.6s; }
        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .project-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: opacity 0.3s ease;
        }
        .project-card:hover img {
            opacity: 0.3;
        }
        .project-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            transition: opacity 0.3s ease;
        }
        .project-card:hover .project-card-overlay {
            opacity: 1;
        }
        .project-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
            text-align: center;
        }
        .project-card-text {
            font-size: 0.95rem;
            color: #e9ecef;
            line-height: 1.6;
            margin-bottom: 15px;
            text-align: center;
        }
        .project-card-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #fff;
            background: linear-gradient(to right, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .project-card-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            background: linear-gradient(to right, #1e7e34, #17a589);
        }
        .no-projects {
            font-size: 1.2rem;
            color: #6c757d;
            text-align: center;
            padding: 50px 0;
        }
        /* Responsive grid */
        @media (min-width: 375px) {
            .project-grid {
                grid-template-columns: repeat(<?php echo $column_settings['project_columns_375']; ?>, 1fr);
            }
        }
        @media (min-width: 425px) {
            .project-grid {
                grid-template-columns: repeat(<?php echo $column_settings['project_columns_425']; ?>, 1fr);
            }
        }
        @media (min-width: 768px) {
            .project-grid {
                grid-template-columns: repeat(<?php echo $column_settings['project_columns_768']; ?>, 1fr);
            }
        }
        @media (min-width: 1200px) {
            .project-grid {
                grid-template-columns: repeat(<?php echo $column_settings['project_columns_1200']; ?>, 1fr);
            }
        }
        @media (min-width: 1400px) {
            .project-grid {
                grid-template-columns: repeat(<?php echo $column_settings['project_columns_max']; ?>, 1fr);
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
    <!-- Header -->


    <!-- Nội dung chính -->
    <div class="container my-5">
        <h1 class="project-section-title">Danh sách dự án</h1>
        <?php if (empty($projects)): ?>
            <p class="no-projects">Hiện tại chưa có dự án nào.</p>
        <?php else: ?>
            <div class="project-grid">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <?php if ($project['thumbnail']): ?>
                            <img src="/2/admin/<?php echo htmlspecialchars($project['thumbnail']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <?php else: ?>
                            <img src="/2/public/assets/images/placeholder.jpg" alt="Placeholder">
                        <?php endif; ?>
                        <div class="project-card-overlay">
                            <h5 class="project-card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="project-card-text"><?php echo htmlspecialchars(substr($project['description'], 0, 100)) . (strlen($project['description']) > 100 ? '...' : ''); ?></p>
                            <a href="/2/public/pages/project_detail.php?slug=<?php echo htmlspecialchars($project['slug']); ?>" class="project-card-btn">Xem chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


    <!-- Scripts -->
    <script src="/2/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>