<?php
require_once('./config.php');

echo "<h2>Adding System Users...</h2>";

$users = [
    ['Rahul', 'Sharma', 'rsharma', 2],
    ['Priya', 'Patel', 'ppatel', 2],
    ['Amit', 'Kumar', 'akumar', 2],
    ['Neha', 'Singh', 'nsingh', 2],
    ['Vikram', 'Gupta', 'vgupta', 1],
    ['Anita', 'Verma', 'averma', 2],
    ['Deepak', 'Joshi', 'djoshi', 2],
    ['Kavita', 'Rao', 'krao', 2],
    ['Sanjay', 'Mehta', 'smehta', 1],
    ['Pooja', 'Reddy', 'preddy', 2]
];

$successCount = 0;

foreach($users as $u) {
    $firstname = $u[0];
    $lastname = $u[1];
    $username = $u[2];
    $type = $u[3];
    $password = 'admin123';
    $typeName = $type == 1 ? 'Administrator' : 'Staff';
    
    // Check if username already exists
    $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if($check->num_rows > 0) {
        echo "→ User already exists: $username<br>";
        continue;
    }
    
    $sql = "INSERT INTO users (firstname, lastname, username, password, type) VALUES ('$firstname', '$lastname', '$username', '$password', $type)";
    if($conn->query($sql)) {
        echo "✓ Added: $firstname $lastname ($username) - $typeName<br>";
        $successCount++;
    } else {
        echo "✗ Failed: $firstname $lastname - " . $conn->error . "<br>";
    }
}

echo "<hr>";
echo "<p style='color: green;'>✓ Successfully added: $successCount users</p>";
echo "<p><a href='?page=users' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View User List</a></p>";
?>
