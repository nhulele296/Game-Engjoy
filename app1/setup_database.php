<?php
require_once "config.php";

// Kết nối đến MySQL (không chọn database)
$link = mysqli_connect(HOST, USER, PASSWORD);

if (!$link) {
    die("Không thể kết nối đến MySQL: " . mysqli_connect_error());
}

// Tạo database nếu chưa tồn tại
$sql = "CREATE DATABASE IF NOT EXISTS " . DB;
if (mysqli_query($link, $sql)) {
    echo "Database đã được tạo hoặc đã tồn tại<br>";
} else {
    die("Lỗi khi tạo database: " . mysqli_error($link));
}

// Chọn database
mysqli_select_db($link, DB);

// Đọc và thực thi file SQL
$sql = file_get_contents('database.sql');
if (mysqli_multi_query($link, $sql)) {
    do {
        // Xử lý từng kết quả
        if ($result = mysqli_store_result($link)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($link));
    echo "Cấu trúc bảng đã được tạo thành công<br>";
} else {
    die("Lỗi khi tạo bảng: " . mysqli_error($link));
}

// Tạo user test
$email = "test@example.com";
$password = "123456";
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Xóa user cũ nếu tồn tại
mysqli_query($link, "DELETE FROM users WHERE email = '$email'");

// Thêm user mới
$sql = "INSERT INTO users (email, password_hash) VALUES ('$email', '$password_hash')";
if (mysqli_query($link, $sql)) {
    echo "Đã tạo user test:<br>";
    echo "Email: $email<br>";
    echo "Password: $password<br>";
} else {
    die("Lỗi khi tạo user test: " . mysqli_error($link));
}

mysqli_close($link);
?> 