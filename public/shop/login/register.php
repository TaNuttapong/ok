<?php
require_once '../../../config/database.php';
require_once '../../../src/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];

    if (createShopUser($conn, $phone_number)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "การลงทะเบียนล้มเหลว";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ลงทะเบียนร้านค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="text-center">ลงทะเบียนร้านค้า</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="register.php">
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">หมายเลขโทรศัพท์:</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="d-grid">
                                <input type="submit" class="btn btn-primary" value="ลงทะเบียน">
                            </div>
                        </form>
                        <?php if (isset($error)) echo "<p class='text-danger mt-3'>$error</p>"; ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="login.php">กลับไปหน้าล็อกอิน</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>
