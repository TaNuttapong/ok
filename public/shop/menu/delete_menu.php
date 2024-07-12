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

// ลบเมนูจากฐานข้อมูล
$sql = "DELETE FROM menus WHERE id = :id AND shop_id = :shop_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $menu_id);
$stmt->bindValue(':shop_id', $user['id']);

if ($stmt->execute()) {
    header("Location: manage_menu.php");
    exit();
} else {
    echo "เกิดข้อผิดพลาดในการลบเมนู";
}
?>
