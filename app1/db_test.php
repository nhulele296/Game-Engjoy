<?php
$link = mysqli_connect("localhost", "root", "", "game");
if (!$link) {
    die("❌ Lỗi kết nối: " . mysqli_connect_error());
}
echo "✅ Kết nối thành công!";
mysqli_close($link);
?>
