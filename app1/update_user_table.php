<?php
require_once "db_module.php";
$link = null;
taoKetNoi($link);

try {
    // Thêm cột reset_code và reset_code_expiry
    $sql = "ALTER TABLE User 
            ADD COLUMN reset_code VARCHAR(6) NULL,
            ADD COLUMN reset_code_expiry DATETIME NULL";

    if (mysqli_query($link, $sql)) {
        echo "Đã cập nhật cấu trúc bảng User thành công!";
    } else {
        echo "Lỗi khi cập nhật bảng: " . mysqli_error($link);
    }
} finally {
    if ($link) giaiPhongBoNho($link);
}
?> 