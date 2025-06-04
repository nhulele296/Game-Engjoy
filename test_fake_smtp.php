<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

class FakeSMTPServer {
    private $emails = array();
    
    public function send($from, $to, $subject, $message) {
        $email = array(
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        $this->emails[] = $email;
        
        // Save to file for persistence
        $logFile = 'email_log.txt';
        $logEntry = json_encode($email) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        return true;
    }
    
    public function getEmails() {
        return $this->emails;
    }
    
    public function displayEmails() {
        echo "\nSent Emails:\n";
        echo "------------\n";
        
        foreach ($this->emails as $email) {
            echo "From: " . $email['from'] . "\n";
            echo "To: " . $email['to'] . "\n";
            echo "Subject: " . $email['subject'] . "\n";
            echo "Message: " . $email['message'] . "\n";
            echo "Sent at: " . $email['timestamp'] . "\n";
            echo "------------\n";
        }
    }
}

// Test the fake SMTP server
$smtp = new FakeSMTPServer();

try {
    // Send test email
    $result = $smtp->send(
        'agowilt.nhule@gmail.com',
        'agowilt.nhule@gmail.com',
        'Test Email',
        '<h1>Test Email</h1><p>This is a test email sent using fake SMTP server.</p>'
    );
    
    echo "Email queued successfully!\n";
    
    // Display all sent emails
    $smtp->displayEmails();
    
    echo "\nEmail log has been saved to email_log.txt\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Additional debug information
echo "\nDebug Information:\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Operating System: " . PHP_OS . "\n";
?> 