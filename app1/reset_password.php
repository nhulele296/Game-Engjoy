<?php
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "db_module.php";
$link = null;
taoKetNoi($link);

// Phản hồi mặc định
$response = ["success" => false, "message" => ""];

try {
    // Kiểm tra dữ liệu
    if (!isset($_POST['email'], $_POST['code'], $_POST['password'])) {
        throw new Exception("Thiếu thông tin cần thiết!");
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);
    $code = mysqli_real_escape_string($link, $_POST['code']);
    $password = $_POST['password'];

    // Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        throw new Exception("Mật khẩu phải có ít nhất 6 ký tự!");
    }

    // Kiểm tra mã xác nhận
    $sql = "SELECT * FROM User WHERE email = '$email' AND reset_code = '$code' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception("Mã xác nhận không đúng!");
    }

    $user = mysqli_fetch_assoc($result);

    // Kiểm tra thời gian hết hạn
    if (strtotime($user['reset_code_expiry']) < time()) {
        throw new Exception("Mã xác nhận đã hết hạn!");
    }

    // Cập nhật mật khẩu mới
    $sql = "UPDATE User SET 
            password = '$password',
            reset_code = NULL,
            reset_code_expiry = NULL
            WHERE email = '$email'";

    if (!chayTruyVanKhongTraVeDL($link, $sql)) {
        throw new Exception("Không thể cập nhật mật khẩu!");
    }

    $response['success'] = true;
    $response['message'] = "Mật khẩu đã được cập nhật thành công!";

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?> 