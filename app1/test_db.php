<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config.php";

echo "<h1>Database Connection Test</h1>";
echo "<pre>";

echo "Trying to connect with these settings:\n";
echo "HOST: " . HOST . "\n";
echo "USER: " . USER . "\n";
echo "DB: " . DB . "\n\n";

$link = mysqli_connect(HOST, USER, PASSWORD, DB);

if (!$link) {
    echo "Connection failed!\n";
    echo "Error: " . mysqli_connect_error() . "\n";
    echo "Error number: " . mysqli_connect_errno() . "\n";
} else {
    echo "Connection successful!\n";
    
    echo "\nTesting vocab_lists table:\n";
    $result = mysqli_query($link, "SHOW CREATE TABLE vocab_lists");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['Create Table'] . "\n";
    } else {
        echo "Error checking vocab_lists table: " . mysqli_error($link) . "\n";
    }
    
    echo "\nTesting words table:\n";
    $result = mysqli_query($link, "SHOW CREATE TABLE words");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['Create Table'] . "\n";
    } else {
        echo "Error checking words table: " . mysqli_error($link) . "\n";
    }
    
    mysqli_close($link);
}

echo "</pre>";
?> 