<?php
require_once '../../../config/database.php';
require_once '../../../src/functions.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'shop') {
    header("Location: ../../../login.php");
    exit();
}

$user = $_SESSION['user'];
$menu_id = $_GET['id'];

// ตรวจสอบว่า shop_id มีอยู่ในฐานข้อมูล
$sql = "SELECT id FROM shop_details WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
$stmt->execute();
$shop_details = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop_details) {
    die('ไม่พบร้านค้าที่เกี่ยวข้องกับผู้ใช้นี้');
}

// ดึงข้อมูลเมนูจากฐานข้อมูล
$sql = "SELECT * FROM menus WHERE id = :id AND shop_id = :shop_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $menu_id, PDO::PARAM_INT);
$stmt->bindValue(':shop_id', $shop_details['id'], PDO::PARAM_INT);
$stmt->execute();
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    die('ไม่พบเมนูที่ต้องการแก้ไข');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_name = $_POST['menu_name'];
    $menu_description = $_POST['menu_description'];
    $menu_price = $_POST['menu_price'];
    $menu_category = $_POST['menu_category'];
    $menu_status = $_POST['menu_status'];
    $menu_image = $_FILES['menu_image']['name'];
    $upload_dir = '../../../uploads/';
    $target_file = $upload_dir . basename($menu_image);

    if (empty($menu_name) || empty($menu_description) || empty($menu_price) || empty($menu_category) || empty($menu_status)) {
        $error_message = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // อัปโหลดรูปภาพใหม่ถ้ามีการเปลี่ยนแปลง
        if (!empty($menu_image)) {
            if (move_uploaded_file($_FILES['menu_image']['tmp_name'], $target_file)) {
                $sql = "UPDATE menus SET name = :name, description = :description, price = :price, category = :category, image = :image, status = :status WHERE id = :id AND shop_id = :shop_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':image', $menu_image, PDO::PARAM_STR);
            } else {
                $error_message = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
            }
        } else {
            $sql = "UPDATE menus SET name = :name, description = :description, price = :price, category = :category, status = :status WHERE id = :id AND shop_id = :shop_id";
            $stmt = $conn->prepare($sql);
        }

        $stmt->bindValue(':name', $menu_name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $menu_description, PDO::PARAM_STR);
        $stmt->bindValue(':price', $menu_price, PDO::PARAM_STR);
        $stmt->bindValue(':category', $menu_category, PDO::PARAM_STR);
        $stmt->bindValue(':status', $menu_status, PDO::PARAM_INT);
        $stmt->bindValue(':id', $menu_id, PDO::PARAM_INT);
        $stmt->bindValue(':shop_id', $shop_details['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success_message = "แก้ไขเมนูเรียบร้อยแล้ว";
            // ดึงข้อมูลเมนูที่แก้ไขใหม่
            $sql = "SELECT * FROM menus WHERE id = :id AND shop_id = :shop_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $menu_id, PDO::PARAM_INT);
            $stmt->bindValue(':shop_id', $shop_details['id'], PDO::PARAM_INT);
            $stmt->execute();
            $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error_message = "เกิดข้อผิดพลาดในการแก้ไขเมนู";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>แก้ไขเมนู</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">แก้ไขเมนู</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <form action="edit_menu.php?id=<?php echo $menu_id; ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="menu_name" class="form-label">ชื่อเมนู</label>
                            <input type="text" class="form-control" id="menu_name" name="menu_name" value="<?php echo htmlspecialchars($menu['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="menu_description" class="form-label">รายละเอียดเมนู</label>
                            <textarea class="form-control" id="menu_description" name="menu_description" rows="3" required><?php echo htmlspecialchars($menu['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="menu_price" class="form-label">ราคา</label>
                            <input type="number" class="form-control" id="menu_price" name="menu_price" value="<?php echo htmlspecialchars($menu['price']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="menu_category" class="form-label">ประเภทเมนู</label>
                            <input type="text" class="form-control" id="menu_category" name="menu_category" value="<?php echo htmlspecialchars($menu['category']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="menu_image" class="form-label">รูปภาพเมนู</label>
                            <input type="file" class="form-control" id="menu_image" name="menu_image">
                            <?php if ($menu['image']): ?>
                                <img src="../../../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="Menu Image" style="max-width: 100px;" class="mt-2">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="menu_status" class="form-label">สถานะเมนู</label>
                            <select class="form-control" id="menu_status" name="menu_status" required>
                                <option value="1" <?php if ($menu['status'] == 1) echo 'selected'; ?>>เปิด</option>
                                <option value="0" <?php if ($menu['status'] == 0) echo 'selected'; ?>>ปิด</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">แก้ไขเมนู</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xkk+8WfRW9jJwrPpGweX0K8AMo4+" crossorigin="anonymous"></script>
</body>
</html>
