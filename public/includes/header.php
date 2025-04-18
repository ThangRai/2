<?php
try {
    require_once 'C:/laragon/www/2/admin/config/db_connect.php';
    // Lấy logo
    $stmt = $pdo->prepare("SELECT image, link FROM logos WHERE status = 1 ORDER BY created_at DESC LIMIT 1");
    $stmt->execute();
    $logo = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['image' => 'default-logo.png', 'link' => '/2/public'];
    
    // Lấy danh mục
    $stmt = $pdo->query("SELECT id, name, parent_id, link FROM categories WHERE status = 1 ORDER BY `order`, id");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Xây dựng danh mục cha-con
    $menu = [];
    foreach ($categories as $cat) {
        if ($cat['parent_id'] == 0) {
            $menu[$cat['id']] = [
                'id' => $cat['id'],
                'name' => $cat['name'],
                'link' => $cat['link'] ?: '/2/public/' . $cat['id'],
                'children' => []
            ];
        } else {
            if (isset($menu[$cat['parent_id']])) {
                $menu[$cat['parent_id']]['children'][] = [
                    'name' => $cat['name'],
                    'link' => $cat['link'] ?: '/2/public/' . $cat['id']
                ];
            }
        }
    }
    
    // Đếm giỏ hàng
    $cart_count = 0;
    if (isset($_SESSION['cart'])) {
        $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    
    // Kiểm tra đăng nhập
    $user = null;
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log('Header error: ' . $e->getMessage());
    $logo = ['image' => 'default-logo.png', 'link' => '/2/public'];
    $menu = [];
    $cart_count = 0;
    $user = null;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        ul.navbar-nav.me-auto {
            margin: 0 auto;
        }   
        .header .navbar {
            padding: 0.5rem 1rem;
            width: 1200px;
            margin: 0 auto;
        }
        .header .navbar-brand img {
            height: 70px;
            transition: transform 0.3s;
        }
        .header .navbar-brand img:hover {
            transform: scale(1.05);
        }
        .header .nav-link {
            color: #000 !important;
            font-size: 1.1em;
            padding: 0.5rem 1rem;
        }
        .header .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .header .dropdown-menu {
            background:rgb(255, 255, 255);
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .header .dropdown-menu .dropdown-item {
            color: #000;
        }
        .header .dropdown-menu .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        /* Tìm kiếm */
        .header-right .search-icon a {
            color: #000;
            font-size: 1.3em;
            text-decoration: none;
        }
        .header-right .search-icon .dropdown-menu {
            background: #000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            padding: 0.5rem;
        }
        .header-right .search-icon .search-form {
            display: flex;
            align-items: center;
        }
        .header-right .search-icon .search-form input {
            border: 1px solid #eee;
            padding: 0.5rem 1rem;
            font-size: 0.9em;
            outline: none;
            width: 180px;
        }
        .header-right .search-icon .search-form button {
            background: #3498db;
            border: none;
            padding: 0.5rem;
            color: #000;
            cursor: pointer;
            transition: background 0.3s;
        }
        .header-right .search-icon .search-form button:hover {
            background: #2980b9;
        }
        /* Giỏ hàng */
        .header-right .cart-icon {
            position: relative;
            transition: transform 0.3s;
        }
        .header-right .cart-icon:hover {
            transform: scale(1.2);
        }
        .header-right .cart-icon a {
            color: #000;
            font-size: 1.4em;
            text-decoration: none;
        }
        .header-right .cart-icon .badge {
            position: absolute;
            top: -15px;
            right: -20px;
            background: red;
            color: #fff;
            border-radius: 50%;
            padding: 7px 7px;
            font-size: 0.75em;
            font-weight: bold;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }
        .header-right .cart-icon:hover .badge {
            transform: scale(1.1);
        }
        /* Login */
        .header-right .login-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #fff;
            transition: transform 0.3s;
        }
        .header-right .login-icon img:hover {
            transform: scale(1.1);
        }
        .header-right .login-icon a {
            color: #000;
            font-size: 1.3em;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .header-right {
                gap: 15px;
            }
            .header-right .search-icon .dropdown-menu {
                min-width: 200px;
            }
            .header-right .search-icon .search-form input {
                width: 120px;
            }
            .header-right .cart-icon a, .header-right .login-icon a {
                font-size: 1.2em;
            }
            .header-right .login-icon img {
                width: 26px;
                height: 26px;
            }
        }
        @media (max-width: 576px) {
            .header .navbar-brand img {
                height: 40px;
            }
            .header-right {
                gap: 10px;
            }
            .header-right .search-icon .dropdown-menu {
                min-width: 150px;
            }
            .header-right .search-icon .search-form input {
                width: 80px;
            }
            .header-right .cart-icon a, .header-right .login-icon a {
                font-size: 1.1em;
            }
            .header-right .login-icon img {
                width: 24px;
                height: 24px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo htmlspecialchars($logo['link'] ?: '/2/public', ENT_QUOTES); ?>">
                    <img src="http://localhost/2/admin/<?php echo htmlspecialchars($logo['image'], ENT_QUOTES); ?>" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"><i class="fas fa-bars text-white"></i></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/2/public">Trang chủ</a>
                        </li> -->
                        <?php foreach ($menu as $cat): ?>
                            <?php if (!empty($cat['children'])): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="<?php echo htmlspecialchars($cat['link'], ENT_QUOTES); ?>" id="dropdown<?php echo $cat['id']; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdown<?php echo $cat['id']; ?>">
                                        <?php foreach ($cat['children'] as $child): ?>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo htmlspecialchars($child['link'], ENT_QUOTES); ?>">
                                                    <?php echo htmlspecialchars($child['name'], ENT_QUOTES); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo htmlspecialchars($cat['link'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="header-right">
                        <div class="search-icon dropdown">
                            <a href="#" id="searchDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-search"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="searchDropdown">
                                <form class="search-form" action="/2/search" method="GET">
                                    <input type="text" name="q" placeholder="Tìm kiếm..." required>
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-icon">
                            <a href="/2/public/pages/cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="badge"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="login-icon">
                            <?php if ($user): ?>
                                <a href="/2/profile">
                                    <?php if ($user['avatar']): ?>
                                        <img src="/2/admin/uploads/avatars/<?php echo htmlspecialchars($user['avatar'], ENT_QUOTES); ?>" alt="Avatar">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle"></i>
                                    <?php endif; ?>
                                </a>
                            <?php else: ?>
                                <a href="/2/public/login"><i class="fas fa-user"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>