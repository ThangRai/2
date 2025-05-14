<?php
ob_start();
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM faqs WHERE is_published = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Error fetching FAQs: ' . $e->getMessage());
    $faqs = [];
}

$seo_title = !empty($faqs) ? $faqs[0]['seo_title'] : 'Câu hỏi thường gặp';
$seo_description = !empty($faqs) ? $faqs[0]['seo_description'] : 'Danh sách câu hỏi thường gặp và câu trả lời.';
$seo_keywords = !empty($faqs) ? $faqs[0]['seo_keywords'] : 'FAQ, câu hỏi thường gặp, hỗ trợ';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seo_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Nunito -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <style>
        .section-title {
            margin-top: 10px;
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            position: relative;
            animation: slideIn 1s ease-out;
        }
        .faq-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .accordion .card {
            border: none;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .accordion .card-header {
            background-color: #ffffff;
            border: none;
            padding: 0;
        }
        .accordion .card-header button {
            width: 100%;
            text-align: left;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            text-decoration: none;
            background: none;
            border: none;
            outline: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .accordion .card-header button:hover {
            color: #007bff;
        }
        .accordion .card-header button:not(.collapsed) {
            color: #007bff;
        }
        .accordion .card-header button:not(.collapsed) i {
            transform: rotate(0deg);
        }
        .accordion .card-body {
            padding: 20px;
            font-size: 1rem;
            color: #555;
            text-align: left !important;
        }
        .accordion .card-body p {
            margin: 0;
        }
        @media (max-width: 576px) {
            .container {
                margin-top: 20px;
                padding: 0 15px;
            }
            .accordion .card-header button {
                font-size: 1rem;
                padding: 10px;
            }
            .accordion .card-body {
                font-size: 0.9rem;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'C:/laragon/www/2/public/includes/header.php'; ?>

    <div class="container">
        <div class="faq-header">
            <h1 class="section-title">CÂU HỎI THƯỜNG GẶP</h1>
        </div>
        
        <?php if (empty($faqs)): ?>
            <div class="alert alert-info text-center">
                Hiện tại chưa có câu hỏi nào được hiển thị.
            </div>
        <?php else: ?>
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="card">
                        <div class="card-header" id="heading<?php echo $index; ?>">
                            <h2 class="mb-0">
                                <button class="btn btn-link <?php echo $index === 0 ? '' : 'collapsed'; ?>" 
                                        type="button" data-toggle="collapse" 
                                        data-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                        aria-controls="collapse<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($faq['question']); ?>
                                    <i class="fas <?php echo $index === 0 ? 'fa-minus' : 'fa-plus'; ?>"></i>
                                </button>
                            </h2>
                        </div>
                        <div id="collapse<?php echo $index; ?>" 
                             class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                             aria-labelledby="heading<?php echo $index; ?>" 
                             data-parent="#faqAccordion">
                            <div class="card-body">
                                <?php echo $faq['answer']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.accordion .btn-link').on('click', function() {
                // Cập nhật icon cho button được click
                $(this).find('i').toggleClass('fa-plus fa-minus');
                // Đặt lại icon cho các button khác
                $('.accordion .btn-link').not(this).find('i').removeClass('fa-minus').addClass('fa-plus');
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>