<?php
require_once '../../config/database.php';
require_once '../../src/functions.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'shop') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// ดึงข้อมูลร้านค้าจากฐานข้อมูล
$sql = "SELECT * FROM shop_details WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
$stmt->execute();
$shop_details = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop_details) {
    die('ไม่พบร้านค้าที่เกี่ยวข้องกับผู้ใช้นี้');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .dashboard-icon {
            font-size: 40px;
        }
        .card-title {
            font-size: 18px;
        }
        .card-subtitle {
            font-size: 14px;
        }
        .main-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .menu-item {
            width: 90px;
            height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: #f8f9fa;
            text-align: center;
        }
        .menu-item .bi {
            font-size: 40px;
            margin-bottom: 5px;
        }
        .menu-item span {
            font-size: 14px;
        }
        .footer-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f8f9fa;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            padding: 10px 0;
        }
        .footer-menu .nav-item {
            flex: 1;
            text-align: center;
        }
        .footer-menu .nav-link {
            font-size: 14px;
        }
        .footer-menu .bi {
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'layout/navbar.php'; ?>

    <div class="container mt-3">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2>ร้านค้า: <?php echo htmlspecialchars($shop_details['shop_name']); ?></h2>
                <p>สาขาทองหล่อ</p>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">ยอดขายวันนี้</h5>
                        <h6 class="card-subtitle mb-2 text-muted">฿10,000.00</h6>
                        <p class="card-text">ยอดขายในวันนี้</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">คุณภาพออเดอร์สัปดาห์ก่อน</h5>
                        <h6 class="card-subtitle mb-2 text-muted">ออเดอร์ไม่สำเร็จ 0%</h6>
                        <p class="card-text">ผลลายได้: ฿0.00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row main-menu">
            <a href="promotion.php" class="menu-item">
                <i class="bi bi-bullhorn"></i>
                <span>โปรโมชัน</span>
            </a>
            <a href="finance.php" class="menu-item">
                <i class="bi bi-cash-stack"></i>
                <span>การเงิน</span>
            </a>
            <a href="quality.php" class="menu-item">
                <i class="bi bi-star"></i>
                <span>คุณภาพร้าน</span>
            </a>
            <a href="sales_summary.php" class="menu-item">
                <i class="bi bi-graph-up"></i>
                <span>สรุปการขาย</span>
            </a>
            <a href="pos.php" class="menu-item">
                <i class="bi bi-pc-display"></i>
                <span>สมัคร POS</span>
            </a>
            <a href="shop_online.php" class="menu-item">
                <i class="bi bi-cart"></i>
                <span>ช้อปสินค้า</span>
            </a>
            <a href="loan.php" class="menu-item">
                <i class="bi bi-bank"></i>
                <span>เงินด่วน</span>
            </a>
            <a href="support.php" class="menu-item">
                <i class="bi bi-life-preserver"></i>
                <span>ศูนย์ช่วยเหลือ</span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <nav class="navbar navbar-expand navbar-light bg-light footer-menu">
        <ul class="navbar-nav w-100">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-house-door"></i>
                    <br>หน้าหลัก
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-list"></i>
                    <br>เมนู
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-receipt"></i>
                    <br>ออเดอร์
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-clock-history"></i>
                    <br>ประวัติ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-gear"></i>
                    <br>ตั้งค่า
                </a>
            </li>
        </ul>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xkk+8WfRW9jJwrPpGweX0K8AMo4+" crossorigin="anonymous"></script>
</body>
</html>
