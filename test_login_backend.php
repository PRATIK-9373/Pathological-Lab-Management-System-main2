<?php
// test_login_backend.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock POST data - REPLACE THESE with the credentials you just registered
$_POST['email'] = 'test_1@example.com'; // Try 'test_1@example.com' or whatever you used
$_POST['password'] = 'password123';

echo "<h1>Testing Login Backend</h1>";

// Include constraints similar to real execution
require_once('classes/Login.php');

$auth = new Login();

echo "<p>Calling client_login()...</p>";
// Buffering to catch unexpected output
ob_start();
$response = $auth->client_login();
$extra_output = ob_get_clean();

echo "<h2>Raw Output (including any extra whitespace):</h2>";
echo "<pre style='background:#eee; padding:10px; border:1px solid #ccc'>[" . $extra_output . $response . "]</pre>";

echo "<h2>Decoded JSON:</h2>";
$json = json_decode($response, true);
if ($json) {
    echo "Status: " . ($json['status'] ?? 'N/A') . "<br>";
    echo "Msg: " . ($json['msg'] ?? 'N/A') . "<br>";
} else {
    echo "<p style='color:red'>Invalid JSON returned.</p>";
    echo "Last JSON Error: " . json_last_error_msg();
}
?>
