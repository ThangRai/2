<?php
try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'site_status'");
    $stmt->execute();
    $site_status = (int)$stmt->fetchColumn();
    $favicon = null;
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'favicon'");
    $stmt->execute();
    $favicon = $stmt->fetchColumn();
} catch (Exception $e) {
    error_log('Maintenance page error: ' . $e->getMessage());
    $site_status = 0; // Mặc định mở nếu lỗi
    $favicon = null;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Đang Bảo Trì</title>
    <?php if ($favicon): ?>
        <link rel="icon" href="/2/admin/uploads/favicon/<?php echo htmlspecialchars($favicon, ENT_QUOTES); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        .maintenance-container img.logo {
            max-width: 100px;
            margin-bottom: 20px;
        }
        .maintenance-container h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #e74c3c;
        }
        .maintenance-container p {
            font-size: 1.2em;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .maintenance-container .contact-btn {
            display: inline-block;
            padding: 12px 25px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s;
        }
        .maintenance-container .contact-btn:hover {
            background: #2980b9;
        }
        .maintenance-container .icon {
            font-size: 3em;
            margin-bottom: 20px;
            color: #3498db;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            .maintenance-container {
                margin: 20px;
                padding: 20px;
            }
            .maintenance-container h1 {
                font-size: 1.8em;
            }
            .maintenance-container p {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <?php if ($favicon): ?>
            <img src="/2/admin/uploads/favicon/<?php echo htmlspecialchars($favicon, ENT_QUOTES); ?>" alt="Logo" class="logo">
        <?php else: ?>
            <i class="fas fa-tools icon"></i>
        <?php endif; ?>
        <h1>Website Đang Bảo Trì</h1>
        <p>Chúng tôi đang nâng cấp hệ thống để mang đến trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!</p>
        <a href="mailto:support@example.com" class="contact-btn">Liên hệ hỗ trợ</a>
    </div>
</body>
</html>