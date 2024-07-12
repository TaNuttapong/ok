<?php

// ฟังก์ชันสำหรับการสร้างผู้ใช้ใหม่
function createUser($conn, $username, $identifier, $password, $role) {
    $sql = "INSERT INTO users (username, email, phone_number, password, role) VALUES (:username, :email, :phone_number, :password, :role)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':email', ($role == 'shop') ? NULL : $identifier);
    $stmt->bindValue(':phone_number', ($role == 'shop') ? $identifier : NULL);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
    $stmt->bindValue(':role', $role);
    return $stmt->execute();
}

// ฟังก์ชันสำหรับการตรวจสอบการล็อกอิน
function checkLogin($conn, $identifier, $password) {
    $sql = "SELECT * FROM users WHERE (email = :identifier OR phone_number = :identifier)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':identifier', $identifier);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// ฟังก์ชันสำหรับการสร้างผู้ใช้ใหม่สำหรับร้านค้า
function createShopUser($conn, $phone_number) {
    $username = 'shop_' . $phone_number; // ตั้งชื่อผู้ใช้เป็น 'shop_' + หมายเลขโทรศัพท์
    return createUser($conn, $username, $phone_number, '', 'shop');
}

// ฟังก์ชันสำหรับการตรวจสอบการล็อกอินของร้านค้า
function checkShopLogin($conn, $phone_number) {
    return checkLogin($conn, $phone_number, '');
}

// ฟังก์ชันสำหรับการอัปเดตข้อมูลร้านค้า
function updateShopDetails($conn, $user_id, $shop_name, $owner_name, $logo, $id_card, $bank_name, $account_number) {
    $sql = "INSERT INTO shop_details (user_id, shop_name, owner_name, logo, id_card, bank_name, account_number) VALUES (:user_id, :shop_name, :owner_name, :logo, :id_card, :bank_name, :account_number) ON DUPLICATE KEY UPDATE shop_name = VALUES(shop_name), owner_name = VALUES(owner_name), logo = VALUES(logo), id_card = VALUES(id_card), bank_name = VALUES(bank_name), account_number = VALUES(account_number)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':shop_name', $shop_name);
    $stmt->bindValue(':owner_name', $owner_name);
    $stmt->bindValue(':logo', $logo);
    $stmt->bindValue(':id_card', $id_card);
    $stmt->bindValue(':bank_name', $bank_name);
    $stmt->bindValue(':account_number', $account_number);
    return $stmt->execute();
}

// ฟังก์ชันสำหรับการอัปโหลดไฟล์
function uploadFile($file) {
    $target_dir = "../../../uploads/";
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // ตรวจสอบว่าเป็นไฟล์รูปภาพจริงหรือไม่
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // ตรวจสอบว่ามีไฟล์ที่มีชื่อเดียวกันหรือไม่
    if (file_exists($target_file)) {
        $uploadOk = 0;
    }

    // ตรวจสอบขนาดของไฟล์
    if ($file["size"] > 500000) {
        $uploadOk = 0;
    }

    // อนุญาตให้อัปโหลดเฉพาะบางชนิดของไฟล์
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        $uploadOk = 0;
    }

    // ตรวจสอบว่าผ่านการตรวจสอบทั้งหมดหรือไม่
    if ($uploadOk == 0) {
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return basename($file["name"]);
        } else {
            return false;
        }
    }
}

?>
