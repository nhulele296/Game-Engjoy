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
    if (!isset($_POST['email'])) {
        throw new Exception("Thiếu email!");
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);

    // Kiểm tra xem email có phải là Gmail không
    if (!preg_match('/@gmail\.com$/', $email)) {
        throw new Exception("Vui lòng sử dụng địa chỉ Gmail!");
    }

    // Kiểm tra xem user đã tồn tại chưa
    $sql = "SELECT * FROM User WHERE email = '$email' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);

    if (!$result) {
        throw new Exception("Lỗi truy vấn database!");
    }

    if (mysqli_num_rows($result) === 0) {
        // Tạo username từ phần trước @ của email
        $username = explode('@', $email)[0];
        
        // Kiểm tra xem username đã tồn tại chưa
        $check_username = "SELECT id FROM User WHERE username = '$username' LIMIT 1";
        $username_result = chayTruyVanTraVeDL($link, $check_username);
        
        if (mysqli_num_rows($username_result) > 0) {
            // Nếu username đã tồn tại, thêm số ngẫu nhiên vào sau
            $username = $username . rand(100, 999);
        }

        // Tạo user mới
        $sql = "INSERT INTO User (username, email) VALUES ('$username', '$email')";
        if (!chayTruyVanKhongTraVeDL($link, $sql)) {
            throw new Exception("Không thể tạo tài khoản mới!");
        }
        
        $response['message'] = "Tài khoản mới đã được tạo và đăng nhập thành công!";
    } else {
        $response['message'] = "Đăng nhập thành công!";
    }

    // Đăng nhập thành công
    $response['success'] = true;
    $response['redirect_url'] = "/home";

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?> 