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
    <!-- Navbar -->
    <?php include '../layout/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">จัดการเมนู</h2>
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">เพิ่มเมนูใหม่</button>
            <button class="btn btn-secondary ms-2" data-bs-toggle="modal" data-bs-target="#manageCategoryModal">จัดการหมวดหมู่</button>
        </div>
        <div class="row">
            <?php if (empty($menus)): ?>
                <div class="col-12 text-center">
                    <p>ไม่มีเมนู</p>
                </div>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="../../../uploads/<?php echo htmlspecialchars($menu['image']); ?>" class="card-img-top" alt="Menu Image" style="max-height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($menu['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($menu['description']); ?></p>
                                <p class="card-text"><strong>ราคา:</strong> <?php echo htmlspecialchars($menu['price']); ?> บาท</p>
                                <p class="card-text"><strong>ประเภท:</strong> <?php echo htmlspecialchars($menu['category']); ?></p>
                                <p class="card-text"><strong>สถานะ:</strong> <?php echo $menu['status'] == 1 ? 'เปิด' : 'ปิด'; ?></p>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMenuModal<?php echo $menu['id']; ?>">แก้ไข</button>
                                <a href="delete_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือว่าต้องการลบเมนูนี้?');">ลบ</a>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Menu Modal -->
                    <div class="modal fade" id="editMenuModal<?php echo $menu['id']; ?>" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editMenuModalLabel">แก้ไขเมนู</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="edit_menu.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $menu['id']; ?>">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">ชื่อเมนู</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($menu['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">รายละเอียด</label>
                                            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($menu['description']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">ราคา</label>
                                            <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($menu['price']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">ประเภท</label>
                                            <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($menu['category']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">รูปภาพ</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                            <img src="../../../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="Menu Image" style="max-width: 100px;" class="mt-2">
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">สถานะ</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?php echo $menu['status'] == 1 ? 'selected' : ''; ?>>เปิด</option>
                                                <option value="0" <?php echo $menu['status'] == 0 ? 'selected' : ''; ?>>ปิด</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Menu Modal -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMenuModalLabel">เพิ่มเมนูใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_menu.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">ชื่อเมนู</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">รายละเอียด</label>
                            <textarea class="form-control" id="description" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">ราคา</label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">ประเภท</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">รูปภาพ</label>
                            <input type="file" class="form-control" id="image" name="image" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">สถานะ</label>
                            <select class="form-control" id="status" name="status">
                                <option value="1">เปิด</option>
                                <option value="0">ปิด</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">เพิ่มเมนู</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Category Modal -->
    <div class="modal fade" id="manageCategoryModal" tabindex="-1" aria-labelledby="manageCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageCategoryModalLabel">จัดการหมวดหมู่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for managing categories -->
                    <form action="manage_category.php" method="post">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">เพิ่มหมวดหมู่</button>
                    </form>
                    <hr>
                    <h5 class="mt-4">หมวดหมู่ทั้งหมด</h5>
                    <ul class="list-group">
                        <!-- Loop through categories and display them -->
                        <?php
                        // ดึงข้อมูลหมวดหมู่จากฐานข้อมูล
                        $sql = "SELECT * FROM categories WHERE shop_id = :shop_id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(':shop_id', $shop_details['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($categories)): 
                            foreach ($categories as $category):
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($category['name']); ?>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory(<?php echo $category['id']; ?>)">ลบ</button>
                            </li>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <li class="list-group-item text-center">ไม่มีหมวดหมู่</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteCategory(categoryId) {
            if (confirm('คุณแน่ใจหรือว่าต้องการลบหมวดหมู่นี้?')) {
                window.location.href = 'delete_category.php?id=' + categoryId;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+Xkk+8WfRW9jJwrPpGweX0K8AMo4+" crossorigin="anonymous"></script>
</body>
</html>
