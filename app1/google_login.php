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
    if (!isset($_POST['google_token'], $_POST['email'], $_POST['name'])) {
        throw new Exception("Thiếu dữ liệu từ Google!");
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $google_token = $_POST['google_token'];

    // Kiểm tra xem user đã tồn tại chưa
    $sql = "SELECT * FROM User WHERE email = '$email' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);

    if (!$result) {
        throw new Exception("Lỗi truy vấn database!");
    }

    if (mysqli_num_rows($result) === 0) {
        // Tạo user mới nếu chưa tồn tại
        $username = explode('@', $email)[0]; // Tạo username từ email
        $sql = "INSERT INTO User (username, email, name) VALUES ('$username', '$email', '$name')";
        if (!chayTruyVanKhongTraVeDL($link, $sql)) {
            throw new Exception("Không thể tạo tài khoản mới!");
        }
    }

    // Đăng nhập thành công
    $response['success'] = true;
    $response['message'] = "Đăng nhập bằng Google thành công!";
    $response['redirect_url'] = "/home.php";

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?> 