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
$stmt->bindValue(':user_id', $user['id']);
$stmt->execute();
$shop_details = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Shop Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu/manage_menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Promotions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="login/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">ร้านค้าของคุณ</h2>
                    </div>
                    <div class="card-body">
                        <h3>ยินดีต้อนรับ, <?php echo htmlspecialchars($shop_details['owner_name']); ?></h3>
                        <p><strong>ชื่อร้านค้า:</strong> <?php echo htmlspecialchars($shop_details['shop_name']); ?></p>
                        <?php if ($shop_details['logo']): ?>
                            <p><strong>โลโก้ร้าน:</strong> <img src="../../uploads/<?php echo htmlspecialchars($shop_details['logo']); ?>" alt="Logo" style="max-width: 100px;"></p>
                        <?php endif; ?>
                        <p><strong>บัตรประชาชนเจ้าของร้าน:</strong> <?php echo htmlspecialchars($shop_details['id_card']); ?></p>
                        <p><strong>ชื่อธนาคาร:</strong> <?php echo htmlspecialchars($shop_details['bank_name']); ?></p>
                        <p><strong>เลขบัญชี:</strong> <?php echo htmlspecialchars($shop_details['account_number']); ?></p>
                    </div>
                    <div class="card-footer text-center">
                        <a href="login/logout.php" class="btn btn-danger">ออกจากระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xkk+8WfRW9jJwrPpGweX0K8AMo4+" crossorigin="anonymous"></script>
</body>
</html>
