<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

function send_local_email($to, $from, $subject, $message) {
    $smtp_server = '127.0.0.1';  // Local SMTP server
    $smtp_port = 25;             // Default SMTP port
    
    // Create socket connection
    $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
    
    if (!$socket) {
        throw new Exception("Could not connect to mail server: $errstr ($errno)");
    }
    
    // Read greeting
    $response = fgets($socket);
    echo "Server: $response";
    
    // Send HELO command
    fputs($socket, "HELO " . $_SERVER['SERVER_NAME'] . "\r\n");
    $response = fgets($socket);
    echo "HELO: $response";
    
    // Send MAIL FROM command
    fputs($socket, "MAIL FROM:<$from>\r\n");
    $response = fgets($socket);
    echo "FROM: $response";
    
    // Send RCPT TO command
    fputs($socket, "RCPT TO:<$to>\r\n");
    $response = fgets($socket);
    echo "TO: $response";
    
    // Send DATA command
    fputs($socket, "DATA\r\n");
    $response = fgets($socket);
    echo "DATA: $response";
    
    // Send email headers and content
    $email = "Subject: $subject\r\n";
    $email .= "To: $to\r\n";
    $email .= "From: $from\r\n";
    $email .= "Content-Type: text/html; charset=UTF-8\r\n";
    $email .= "\r\n";
    $email .= $message;
    $email .= "\r\n.\r\n";
    
    fputs($socket, $email);
    $response = fgets($socket);
    echo "Message: $response";
    
    // Send QUIT command
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    return true;
}

try {
    // Test sending email
    $result = send_local_email(
        'agowilt.nhule@gmail.com',
        'agowilt.nhule@gmail.com',
        'Test Local SMTP',
        '<h1>Test Email</h1><p>This is a test email sent using local SMTP server.</p>'
    );
    echo "\nEmail sent successfully!\n";
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}

// Additional debug information
echo "\nDebug Information:\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Operating System: " . PHP_OS . "\n";
?> 