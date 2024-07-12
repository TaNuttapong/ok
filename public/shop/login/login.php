<?php
require_once '../../../config/database.php';
require_once '../../../src/functions.php';

session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'shop') {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];

    $user = checkShopLogin($conn, $phone_number);
    if ($user && $user['role'] == 'shop') {
        // Start session and redirect to complete profile if first login
        $_SESSION['user'] = $user;
        // Check if the shop has completed profile
        $sql = "SELECT * FROM shop_details WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':user_id', $user['id']);
        $stmt->execute();
        $shop_details = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($shop_details) {
            header("Location: ../dashboard.php");
        } else {
            header("Location: complete_profile.php");
        }
        exit();
    } else {
        $error = "ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>เข้าสู่ระบบร้านค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="text-center">เข้าสู่ระบบร้านค้า</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="login.php">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">หมายเลขโทรศัพท์:</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="d-grid">
                                <input type="submit" class="btn btn-primary" value="เข้าสู่ระบบ">
                            </div>
                        </form>
                        <?php if (isset($error)) echo "<p class='text-danger mt-3'>$error</p>"; ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="register.php">ลงทะเบียน</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>
