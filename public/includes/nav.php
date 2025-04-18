<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?php echo BASE_URL_PUBLIC; ?>">
        <img src="assets/img/logo.png" alt="Logo" width="30"> <?php echo SITE_NAME; ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item <?php echo $page == 'home' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=home">Home</a>
            </li>
            <li class="nav-item <?php echo $page == 'product' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=product">Products</a>
            </li>
            <li class="nav-item <?php echo $page == 'cart' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=cart">Cart</a>
            </li>
            <li class="nav-item <?php echo $page == 'login' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=login">Login</a>
            </li>
        </ul>
    </div>
</nav>