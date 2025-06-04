<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$smtp_server = 'smtp.gmail.com';
$port = 587;

echo "Testing connection to $smtp_server:$port...<br>";

// Thử kết nối socket
$socket = @fsockopen($smtp_server, $port, $errno, $errstr, 30);

if (!$socket) {
    echo "Connection failed: $errno - $errstr<br>";
} else {
    echo "Connection successful!<br>";
    $response = fgets($socket, 515);
    echo "Server response: " . htmlspecialchars($response) . "<br>";
    fclose($socket);
}

// Kiểm tra SSL/TLS
echo "<br>Checking SSL/TLS support:<br>";
echo "OpenSSL installed: " . (extension_loaded('openssl') ? 'Yes' : 'No') . "<br>";
echo "OpenSSL version: " . OPENSSL_VERSION_TEXT . "<br>";

// Kiểm tra các stream wrappers được hỗ trợ
echo "<br>Supported protocols:<br>";
print_r(stream_get_transports());
?> 