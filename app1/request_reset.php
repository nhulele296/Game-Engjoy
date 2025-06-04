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
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$link = null;
taoKetNoi($link);

// Phản hồi mặc định
$response = ["success" => false, "message" => ""];

try {
    // Kiểm tra dữ liệu
    if (!isset($_POST['email'])) {
        throw new Exception("Vui lòng nhập địa chỉ email!");
    }

    $email = mysqli_real_escape_string($link, $_POST['email']);

    // Kiểm tra email có tồn tại không
    $sql = "SELECT * FROM User WHERE email = '$email' LIMIT 1";
    $result = chayTruyVanTraVeDL($link, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        throw new Exception("Email không tồn tại trong hệ thống!");
    }

    // Tạo mã xác nhận ngẫu nhiên 6 chữ số
    $reset_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Lưu mã xác nhận và thời gian hết hạn (15 phút)
    $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $sql = "UPDATE User SET 
            reset_code = '$reset_code',
            reset_code_expiry = '$expiry'
            WHERE email = '$email'";

    if (!chayTruyVanKhongTraVeDL($link, $sql)) {
        throw new Exception("Không thể tạo mã xác nhận!");
    }

    // Cấu hình PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình server
        $mail->SMTPDebug = 4;  // Tăng debug level lên mức cao nhất
        $mail->Debugoutput = 'html'; // Hiển thị debug dưới dạng HTML để dễ đọc
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'agowilt.nhule@gmail.com';
        $mail->Password = 'ymmo xfiw jnll vcyu';
        
        // Cấu hình chi tiết SSL/TLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Thêm timeout dài hơn
        $mail->Timeout = 60; // timeout 60 giây
        $mail->SMTPKeepAlive = true; // giữ kết nối
        
        // Cấu hình SSL cụ thể
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->CharSet = 'UTF-8';

        // Người gửi và người nhận
        $mail->setFrom('agowilt.nhule@gmail.com', 'EngJoy');
        $mail->addAddress($email);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - EngJoy';
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2>Đặt lại mật khẩu</h2>
                <p>Bạn đã yêu cầu đặt lại mật khẩu. Đây là mã xác nhận của bạn:</p>
                <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0;">
                    ' . $reset_code . '
                </div>
                <p>Mã này sẽ hết hạn sau 15 phút.</p>
                <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
            </div>
        ';

        $mail->send();
        $response['success'] = true;
        $response['message'] = "Mã xác nhận đã được gửi đến email của bạn!";

    } catch (Exception $e) {
        throw new Exception("Không thể gửi email: " . $mail->ErrorInfo);
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if ($link) giaiPhongBoNho($link);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?> 