<?php
if (!isset($_SESSION['user'])) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
?>

<nav class="navbar navbar-light bg-light">
    <div class="container-fluid d-flex justify-content-between align-items-center navbar-375">
        <div class="d-flex align-items-center">
            <img src="../../uploads/<?php echo htmlspecialchars($shop_details['logo']); ?>" alt="Shop Logo" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
            <div>
                <div class="shop-name"><?php echo htmlspecialchars($shop_details['shop_name']); ?></div>
            </div>
        </div>
        <a class="nav-link" href="#">
            <i class="bi bi-bell" style="font-size: 24px;"></i>
        </a>
    </div>
</nav>

<style>
    .navbar-375 {
        width: 100%;
        margin: auto;
        padding: 10px 15px;
    }

    .shop-name {
        font-size: 16px;
    }

    .navbar-light .navbar-nav .nav-link {
        font-size: 16px;
    }
</style>
