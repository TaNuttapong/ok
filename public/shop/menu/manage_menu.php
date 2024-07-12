<?php
require_once '../../../config/database.php';
require_once '../../../src/functions.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'shop') {
    header("Location: ../../../login.php");
    exit();
}

$user = $_SESSION['user'];

// ดึงข้อมูล shop_id จากตาราง shop_details
$sql = "SELECT id FROM shop_details WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
$stmt->execute();
$shop_details = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop_details) {
    die('ไม่พบร้านค้าที่เกี่ยวข้องกับผู้ใช้นี้');
}

// ดึงข้อมูลเมนูจากฐานข้อมูล
$sql = "SELECT * FROM menus WHERE shop_id = :shop_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':shop_id', $shop_details['id'], PDO::PARAM_INT);
$stmt->execute();
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">จัดการเมนู</h2>
        <div class="d-flex justify-content-end mb-3">
            <a href="add_menu.php" class="btn btn-primary">เพิ่มเมนูใหม่</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ชื่อเมนู</th>
                        <th>รายละเอียด</th>
                        <th>ราคา</th>
                        <th>ประเภท</th>
                        <th>รูปภาพ</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($menus)): ?>
                        <tr>
                            <td colspan="7" class="text-center">ไม่มีเมนู</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($menus as $menu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($menu['name']); ?></td>
                                <td><?php echo htmlspecialchars($menu['description']); ?></td>
                                <td><?php echo htmlspecialchars($menu['price']); ?></td>
                                <td><?php echo htmlspecialchars($menu['category']); ?></td>
                                <td><img src="../../../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="Menu Image" style="max-width: 100px;"></td>
                                <td><?php echo $menu['status'] == 1 ? 'เปิด' : 'ปิด'; ?></td>
                                <td>
                                    <a href="edit_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                    <a href="delete_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือว่าต้องการลบเมนูนี้?');">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xkk+8WfRW9jJwrPpGweX0K8AMo4+" crossorigin="anonymous"></script>
</body>
</html>
