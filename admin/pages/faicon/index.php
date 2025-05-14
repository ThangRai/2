<?php
// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: /2/admin/login.php');
    exit;
}

// Đọc file JSON
$json_path = 'C:/laragon/www/2/admin/assets/vendor/fontawesome/icons.json';
if (!file_exists($json_path)) {
    die('File icons.json không tồn tại. Vui lòng tải từ https://raw.githubusercontent.com/FortAwesome/Font-Awesome/5.15.4/metadata/icons.json và lưu vào C:/laragon/www/2/admin/assets/vendor/fontawesome/icons.json');
}
$json = file_get_contents($json_path);
$icons = json_decode($json, true);
if ($icons === null) {
    die('Lỗi phân tích JSON. Vui lòng kiểm tra file icons.json.');
}

// Lọc icon theo kiểu
$style = isset($_GET['style']) && in_array($_GET['style'], ['solid', 'regular', 'brands']) ? $_GET['style'] : 'solid';
$style_icons = array_filter($icons, function($icon) use ($style) {
    return in_array($style, $icon['styles']) && isset($icon['free']) && in_array($style, $icon['free']);
});

// Phân trang
$per_page_options = [10, 20, 50, 192, 'all'];
$per_page = isset($_GET['per_page']) && (in_array($_GET['per_page'], $per_page_options) || $_GET['per_page'] === 'all') 
    ? $_GET['per_page'] 
    : 192;

if ($per_page === 'all') {
    $per_page = count($style_icons);
}

$total_icons = count($style_icons);
$total_pages = ceil($total_icons / $per_page);
$page = isset($_GET['icon_page']) ? max(1, min((int)$_GET['icon_page'], $total_pages)) : 1;
$offset = ($page - 1) * $per_page;
$paged_icons = array_slice($style_icons, $offset, $per_page, true);

// Class theo kiểu
$style_class = $style === 'solid' ? 'fas' : ($style === 'regular' ? 'far' : 'fab');

// Hàm tạo URL giữ các tham số hiện tại
function build_pagination_url($params) {
    $current = $_GET;
    $new_params = array_merge($current, $params);
    return '?' . http_build_query($new_params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Font Awesome Icons</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/2/admin/assets/vendor/fontawesome/css/all.min.css" rel="stylesheet">
    <style>
        .card {
            background: linear-gradient(145deg, #e6f0fa, #ffffff);
            border: 1px solid #d1e0ff;
        }
        .copy-code {
            cursor: pointer;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            transition: background 0.2s;
        }
        .copy-code:hover {
            background: #e9ecef;
        }
        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s;
        }
        @media (max-width: 767.98px) {
            .card-body {
                font-size: 0.9rem;
            }
            .fa-3x {
                font-size: 2rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Font Awesome Icons</h1>
        <p class="mb-4">Danh sách icon Font Awesome 5 Free. Nhấp vào mã để copy.</p>

        <!-- Bộ lọc, chọn kiểu và chọn số lượng -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-4 mb-2">
                <input type="text" id="icon-search" class="form-control" placeholder="Tìm kiếm icon (ví dụ: star)">
            </div>
            <div class="col-md-4 mb-2">
                <select id="icon-style" class="form-control">
                    <option value="solid" <?php echo $style === 'solid' ? 'selected' : ''; ?>>Solid</option>
                    <option value="regular" <?php echo $style === 'regular' ? 'selected' : ''; ?>>Regular</option>
                    <option value="brands" <?php echo $style === 'brands' ? 'selected' : ''; ?>>Brands</option>
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <select id="per-page" class="form-control">
                    <?php foreach ($per_page_options as $option): ?>
                        <option value="<?php echo $option; ?>" <?php echo $per_page == $option ? 'selected' : ''; ?>>
                            <?php echo $option === 'all' ? 'Tất cả' : $option; ?> icon
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Icon Grid -->
        <div class="row" id="icon-grid">
            <?php foreach ($paged_icons as $name => $data): ?>
                <div class="col-lg-2 col-md-3 col-sm-6 mb-4 icon-card" data-name="fa-<?php echo htmlspecialchars($name); ?>">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="<?php echo $style_class; ?> fa-<?php echo htmlspecialchars($name); ?> fa-3x mb-2 text-primary"></i>
                            <p class="mb-1"><strong>fa-<?php echo htmlspecialchars($name); ?></strong></p>
                            <code class="copy-code" data-code='<i class="<?php echo $style_class; ?> fa-<?php echo htmlspecialchars($name); ?>"></i>'>
                                <i class="<?php echo $style_class; ?> fa-<?php echo htmlspecialchars($name); ?>"></i>
                            </code>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Icon pagination">
                <ul class="pagination justify-content-center">
                    <!-- Nút Trước -->
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo build_pagination_url(['icon_page' => $page - 1]); ?>">Trước</a>
                    </li>

                    <?php
                    // Tính toán các trang hiển thị
                    $range = 2; // Số trang hiển thị trước và sau trang hiện tại
                    $show_pages = [];
                    
                    // Luôn hiển thị trang đầu
                    $show_pages[] = 1;
                    
                    // Thêm dấu ... nếu cần
                    if ($page - $range > 2) {
                        $show_pages[] = '...';
                    }
                    
                    // Thêm các trang gần trang hiện tại
                    for ($i = max(2, $page - $range); $i <= min($total_pages - 1, $page + $range); $i++) {
                        $show_pages[] = $i;
                    }
                    
                    // Thêm dấu ... nếu cần
                    if ($page + $range < $total_pages - 1) {
                        $show_pages[] = '...';
                    }
                    
                    // Luôn hiển thị trang cuối
                    if ($total_pages > 1) {
                        $show_pages[] = $total_pages;
                    }
                    
                    // Hiển thị các trang
                    foreach ($show_pages as $i): ?>
                        <?php if ($i === '...'): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php else: ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo build_pagination_url(['icon_page' => $i]); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Nút Sau -->
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo build_pagination_url(['icon_page' => $page + 1]); ?>">Sau</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- Toast thông báo -->
        <div class="toast" id="copyToast" style="position: fixed; bottom: 20px; right: 20px;" data-delay="2000">
            <div class="toast-body bg-success text-white">
                Đã copy mã icon!
            </div>
        </div>
    </div>

    <!-- jQuery và Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Copy mã icon
        document.querySelectorAll('.copy-code').forEach(code => {
            code.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-code');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const toast = document.getElementById('copyToast');
                    $(toast).toast('show');
                }).catch(err => {
                    console.error('Lỗi khi copy: ', err);
                });
            });
        });

        // Tìm kiếm icon
        document.getElementById('icon-search').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.icon-card').forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                card.style.display = name.includes(search) ? '' : 'none';
            });
        });

        // Chọn kiểu icon
        document.getElementById('icon-style').addEventListener('change', function() {
            window.location.href = `?page=faicon&style=${this.value}&icon_page=1&per_page=<?php echo $per_page; ?>`;
        });

        // Chọn số lượng hiển thị
        document.getElementById('per-page').addEventListener('change', function() {
            window.location.href = `?page=faicon&style=<?php echo $style; ?>&icon_page=1&per_page=${this.value}`;
        });
    </script>
</body>
</html>