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

// Debug connection info
error_log("Attempting to connect with: HOST=" . HOST . ", USER=" . USER . ", DB=" . DB);

taoKetNoi($link);

if (!$link) {
    error_log("Failed to connect to database: " . mysqli_connect_error());
    throw new Exception("Lỗi kết nối đến cơ sở dữ liệu!");
}

// Phản hồi mặc định
$response = ["success" => false, "message" => ""];

// Xử lý đăng nhập
try {
    // Debug POST data
    error_log("POST data: " . json_encode($_POST));
    
    // Kiểm tra dữ liệu
    if (!isset($_POST['username'], $_POST['password'])) {
        throw new Exception("Thiếu dữ liệu truyền lên!");
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        throw new Exception("Vui lòng nhập đầy đủ thông tin đăng nhập và mật khẩu!");
    }

    // Escape dữ liệu
    $username = mysqli_real_escape_string($link, $username);

    // Lấy tên bảng và cấu trúc
    $result = mysqli_query($link, "SHOW TABLES");
    error_log("Tables in database:");
    while ($row = mysqli_fetch_row($result)) {
        error_log($row[0]);
    }

    // Tìm user trong database với username hoặc email
    $sql = "SELECT * FROM User WHERE username = '$username' OR email = '$username' LIMIT 1";
    error_log("Executing SQL query: " . $sql);
    
    $result = chayTruyVanTraVeDL($link, $sql);
    
    if (!$result) {
        $error = "SQL Error: " . mysqli_error($link);
        error_log($error);
        throw new Exception("Lỗi truy vấn: " . $error);
    }

    if (mysqli_num_rows($result) === 0) {
        error_log("No user found with username/email: " . $username);
        throw new Exception("Tên đăng nhập/Email hoặc mật khẩu không đúng!");
    }

    $user = mysqli_fetch_assoc($result);
    error_log("Query result: " . json_encode($user));

    // Verify password
    if ($password !== $user['password']) {
        throw new Exception("Tên đăng nhập/Email hoặc mật khẩu không đúng!");
    }

    // Nếu thành công
    $response['success'] = true;
    $response['message'] = "Đăng nhập thành công!";
    $response['redirect_url'] = "/home.php";

    // Ở đây bạn có thể thêm session hoặc JWT token nếu cần
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
