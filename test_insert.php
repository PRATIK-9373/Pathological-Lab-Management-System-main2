<?php
// test_insert.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock POST data
$_POST = array(
    'firstname' => 'Test',
    'middlename' => 'T',
    'lastname' => 'User',
    'gender' => 'Male',
    'dob' => '1990-01-01',
    'contact' => '1234567890',
    'address' => 'Test Address',
    'email' => 'test_' . time() . '@example.com', // Unique email
    'password' => 'password123',
    'cpass' => 'password123',
    'id' => ''
);

echo "<h1>Testing save_client directly</h1>";

// Need to match the environment expected by Users.php
// Users.php requires ../config.php. 
// Since test_insert.php is in root, we need to handle the path in Users.php or move test_insert to classes/.
// Let's rely on Users.php require_once('../config.php'). 
// IF we include classes/Users.php from root, '../config.php' will look involved.
// calculated path: ROOT/classes/../config.php -> ROOT/config.php. This functions correctly.

require_once('classes/Users.php');

$users = new Users();

echo "<p>Calling save_client()...</p>";
$response = $users->save_client();

echo "<h2>Response:</h2>";
echo "<pre>";
var_dump($response);
echo "</pre>";

echo "<h2>Decoded JSON:</h2>";
$json = json_decode($response, true);
if ($json) {
    echo "Status: " . ($json['status'] ?? 'N/A') . "<br>";
    echo "Msg: " . ($json['msg'] ?? 'N/A') . "<br>";
    if(isset($json['err'])) echo "Err: " . $json['err'] . "<br>";
} else {
    echo "Invalid JSON returned.";
}

?>
