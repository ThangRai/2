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
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        .page_404 {
            padding: 40px 0;
            background: #fff;
            font-family: 'Arvo', serif;
        }
        .page_404 img {
            width: 100%;
        }
        .four_zero_four_bg {
            background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
        }
        .four_zero_four_bg h1 {
            font-size: 80px;
            color: #333;
            text-align: center;
            margin: 0;
        }
        .four_zero_four_bg h3 {
            font-size: 80px;
            color: #333;
            text-align: center;
        }
        .contant_box_404 {
            margin-top: -50px;
            text-align: center;
        }
        .contant_box_404 h3.h2 {
            font-size: 30px;
            color: #333;
            margin-bottom: 20px;
        }
        .contant_box_404 p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .link_404 {
            color: #fff !important;
            padding: 10px 20px;
            background: #39ac31;
            margin: 20px 0;
            display: inline-block;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .link_404:hover {
            background: #2d8a26;
        }
        @media (max-width: 768px) {
            .four_zero_four_bg {
                height: 300px;
            }
            .four_zero_four_bg h1 {
                font-size: 60px;
            }
            .four_zero_four_bg h3 {
                font-size: 60px;
            }
            .contant_box_404 h3.h2 {
                font-size: 24px;
            }
            .contant_box_404 p {
                font-size: 16px;
            }
        }
        @media (max-width: 576px) {
            .four_zero_four_bg {
                height: 250px;
            }
            .four_zero_four_bg h1 {
                font-size: 50px;
            }
            .four_zero_four_bg h3 {
                font-size: 50px;
            }
            .contant_box_404 {
                margin-top: -30px;
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

    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-10 col-sm-offset-1 text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center">404</h1>
                        </div>
                        <div class="contant_box_404">
                            <h3 class="h2">Look like you're lost</h3>
                            <p>the page you are looking for not avaible!</p>
                            <a href="/2/public/" class="link_404">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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