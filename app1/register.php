<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Preflight request
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
    // Kiểm tra dữ liệu gửi lên
    if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        throw new Exception("Vui lòng điền đầy đủ thông tin!");
    }

    $username = mysqli_real_escape_string($link, $_POST['username']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = $_POST['password'];

    // Validate username
    if (strlen($username) < 3 || strlen($username) > 20) {
        throw new Exception("Username phải từ 3-20 ký tự!");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email không hợp lệ!");
    }

    // Validate password
    if (strlen($password) < 6) {
        throw new Exception("Mật khẩu phải có ít nhất 6 ký tự!");
    }
    if (!preg_match("/[A-Z]/", $password)) {
        throw new Exception("Mật khẩu phải có ít nhất 1 chữ hoa!");
    }
    if (!preg_match("/[0-9]/", $password)) {
        throw new Exception("Mật khẩu phải có ít nhất 1 số!");
    }

    // Kiểm tra username đã tồn tại chưa
    $sql = "SELECT * FROM User WHERE username = '$username' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Username đã tồn tại!");
    }

    // Kiểm tra email đã tồn tại chưa
    $sql = "SELECT * FROM User WHERE email = '$email' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Email đã được sử dụng!");
    }

    // Thêm người dùng mới
    $sql = "INSERT INTO User (username, email, password) VALUES ('$username', '$email', '$password')";
    
    if (!chayTruyVanKhongTraVeDL($link, $sql)) {
        throw new Exception("Không thể tạo tài khoản. Vui lòng thử lại!");
    }

    $response['success'] = true;
    $response['message'] = "Đăng ký thành công!";

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?> 