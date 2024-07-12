<?php
require_once '../../../config/database.php';
require_once '../../../src/functions.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'shop') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $owner_name = $_POST['owner_name'];
    $id_card = $_POST['id_card'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];
    
    // อัปโหลดโลโก้ร้าน
    $logo = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = uploadFile($_FILES['logo']);
        if ($logo === false) {
            $error = "การอัปโหลดไฟล์ล้มเหลว";
        }
    }

    if (!isset($error) && updateShopDetails($conn, $user['id'], $shop_name, $owner_name, $logo, $id_card, $bank_name, $account_number)) {
        // อัปเดตข้อมูลใน session
        $_SESSION['user']['shop_name'] = $shop_name;
        $_SESSION['user']['owner_name'] = $owner_name;
        $_SESSION['user']['logo'] = $logo;
        $_SESSION['user']['id_card'] = $id_card;
        $_SESSION['user']['bank_name'] = $bank_name;
        $_SESSION['user']['account_number'] = $account_number;
        
        header("Location: ../dashboard.php");
        exit();
    } else {
        $error = "การอัปเดตข้อมูลล้มเหลว";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>กรอกข้อมูลเพิ่มเติม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">กรอกข้อมูลเพิ่มเติม</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="complete_profile.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="shop_name" class="form-label">ชื่อร้านค้า:</label>
                                <input type="text" class="form-control" id="shop_name" name="shop_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="owner_name" class="form-label">ชื่อเจ้าของร้าน:</label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">โลโก้ร้าน:</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                            </div>
                            <div class="mb-3">
                                <label for="id_card" class="form-label">บัตรประชาชนเจ้าของร้าน:</label>
                                <input type="text" class="form-control" id="id_card" name="id_card" required>
                            </div>
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">ชื่อธนาคาร:</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="account_number" class="form-label">เลขบัญชี:</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" required>
                            </div>
                            <div class="d-grid">
                                <input type="submit" class="btn btn-primary" value="บันทึกข้อมูล">
                            </div>
                        </form>
                        <?php if (isset($error)) echo "<p class='text-danger mt-3'>$error</p>"; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>
