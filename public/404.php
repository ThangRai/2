<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gửi header 404
header('HTTP/1.0 404 Not Found');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out;
        }
        h1 {
    line-height: 1.5 !important;
    font-size: 3.5em !important;
    font-weight: 700;
    color: #ff4757;
    margin-bottom: 20px !important;
    background: linear-gradient(90deg, #ff4757, #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
        p {
            font-size: 1.2em;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #3b82f6, #93c5fd);
            color: #fff;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            transform: scale(1.05);
        }
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
        @media (max-width: 576px) {
            .container {
                padding: 20px;
            }
            h1 {
                font-size: 2.5em;
            }
            p {
                font-size: 1em;
            }
            .btn {
                padding: 10px 20px;
                font-size: 1em;
            }
        }
        @media (max-width: 425px) {
            h1 {
                font-size: 2em;
            }
            p {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <?php
    if (file_exists('C:/laragon/www/2/public/includes/header.php')) {
        require_once 'C:/laragon/www/2/public/includes/header.php';
    } else {
        echo '<div style="text-align: center; color: red; padding: 10px;">Lỗi: Không tìm thấy file header.php</div>';
    }
    ?>

    <div class="container">
        <h1>404 - Không Tìm Thấy</h1>
        <p>Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển. Vui lòng kiểm tra lại URL hoặc quay về trang chủ.</p>
        <a href="/2/public/" class="btn"><i class="fas fa-home"></i> Quay Về Trang Chủ</a>
    </div>

    <?php
    if (file_exists('C:/laragon/www/2/public/includes/footer.php')) {
        require_once 'C:/laragon/www/2/public/includes/footer.php';
    } else {
        echo '<div style="text-align: center; color: red; padding: 10px;">Lỗi: Không tìm thấy file footer.php</div>';
    }
    ?>

</body>
</html>
<?php ob_end_flush(); ?>