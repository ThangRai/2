<?php
require_once 'C:/laragon/www/2/admin/config/db_connect.php';

// Xử lý phân trang
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 12; // 12 bài viết mỗi trang (2 hàng x 6 bài)
$offset = ($page - 1) * $per_page;

// Lấy tổng số bài viết công khai
$count_sql = "SELECT COUNT(*) as total FROM articles WHERE is_published = 1";
$count_stmt = $pdo->query($count_sql);
$total_articles = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_articles / $per_page);

// Lấy danh sách bài viết công khai
$sql = "SELECT id, title, slug, description, thumbnail, created_at 
        FROM articles 
        WHERE is_published = 1 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Chia bài viết thành 2 hàng (mỗi hàng 6 bài)
$rows = array_chunk($articles, 6);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bài viết</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 40px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            
        }
        .article-card {
            border-radius: 12px;
            padding: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }
        .placeholder-image {
            width: 150px;
            height: 100px;
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
        }
        .article-title {
            font-size: 2.1rem;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            text-align: left !important;
        }
        .article-description {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.6;
            text-align: left !important;
        }
        .btn-detail {
            background: linear-gradient(45deg, #007bff, #00aaff);
            border: none;
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #fff;
            border-radius: 25px;
            transition: background 0.3s ease, transform 0.2s ease;
            width: auto;
            text-align: center;
        }
        .btn-detail:hover {
            background: linear-gradient(45deg, #0056b3, #0088cc);
            transform: scale(1.05);
        }
        .pagination {
            justify-content: center;
            margin-top: 40px;
        }
        .page-link {
            border-radius: 50%;
            margin: 0 5px;
            font-weight: 500;
            color: #007bff;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .page-link:hover {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .page-item.active .page-link {
            background: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background: #f8f9fa;
        }
        /* Custom 6 cột trong mỗi hàng */
        .col-md-2 {
            flex: 0 0 16.66667%;
            max-width: 16.66667%;
        }
        @media (max-width: 1200px) {
            .col-md-2 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
        @media (max-width: 992px) {
            .col-md-2 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
        }
        @media (max-width: 768px) {
            .col-md-2 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            .article-image, .placeholder-image {
                width: 100%;
                height: 120px;
            }
            .article-title {
                font-size: 1rem;
            }
            .article-description {
                font-size: 0.85rem;
            }
        }
        @media (max-width: 576px) {
            .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .article-image, .placeholder-image {
                width: 100%;
                height: 150px;
            }
            .article-card .row {
                flex-direction: column;
                align-items: center;
            }
            .article-card .col-6 {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Danh sách bài viết</h1>
        <div class="row">
            <div class="col-12">
                <?php if (empty($articles)): ?>
                    <div class="text-center">
                        <p class="text-muted fs-5">Hiện tại không có bài viết nào.</p>
                    </div>
                <?php else: ?>
                    <!-- Hàng 1: 6 bài viết -->
                    <?php foreach ($rows as $row_index => $row_articles): ?>
                        <div class="row">
                            <?php foreach ($row_articles as $article): ?>
                                <div class="col-12">
                                    <div class="article-card">
                                        <div class="row align-items-start">
                                            <!-- Cột con 1: Ảnh đại diện -->
                                            <div class="col-6">
                                                <?php if ($article['thumbnail'] && file_exists("C:/laragon/www/2/admin/" . $article['thumbnail'])): ?>
                                                    <img src="/2/admin/<?php echo htmlspecialchars($article['thumbnail']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-image">
                                                <?php else: ?>
                                                    <div class="placeholder-image">No Image</div>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Cột con 2: Tiêu đề, mô tả đầy đủ, nút -->
                                            <div class="col-6">
                                                <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                                <p class="article-description"><?php echo htmlspecialchars($article['description']); ?></p>
                                                <a href="/2/public/pages/info_detail.php?slug=<?php echo urlencode($article['slug']); ?>" class="btn btn-detail">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?p=<?php echo $page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?p=<?php echo $page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>