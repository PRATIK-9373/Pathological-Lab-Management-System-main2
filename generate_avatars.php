<?php
require_once('./config.php');

echo "<h2>Generating Avatars for All Users...</h2>";

// Colors for avatars (background colors)
$colors = ['3498db', 'e74c3c', '2ecc71', '9b59b6', 'f39c12', '1abc9c', '34495e', 'e91e63', '00bcd4', 'ff5722'];

function downloadAvatar($name, $filename, $bgColor) {
    $initials = '';
    $parts = explode(' ', trim($name));
    foreach($parts as $part) {
        if(!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    $initials = substr($initials, 0, 2);
    
    // Use UI Avatars service
    $url = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&size=200&background=" . $bgColor . "&color=ffffff&bold=true&format=png";
    
    // Download the image
    $imageData = @file_get_contents($url);
    
    if($imageData !== false) {
        return file_put_contents($filename, $imageData);
    }
    return false;
}

$successCount = 0;
$colorIndex = 0;

// Generate for system users
echo "<h3>System Users (users table):</h3>";
$users = $conn->query("SELECT id, firstname, lastname, avatar FROM users WHERE id != 1");
while($user = $users->fetch_assoc()) {
    $name = $user['firstname'] . ' ' . $user['lastname'];
    $filename = __DIR__ . '/uploads/avatar-' . $user['id'] . '.png';
    $dbPath = 'uploads/avatar-' . $user['id'] . '.png';
    
    $color = $colors[$colorIndex % count($colors)];
    $colorIndex++;
    
    if(downloadAvatar($name, $filename, $color)) {
        // Update database
        $avatarPath = $dbPath . '?v=' . time();
        $conn->query("UPDATE users SET avatar = '$avatarPath' WHERE id = " . $user['id']);
        echo "✓ Generated avatar for: $name<br>";
        $successCount++;
    } else {
        echo "✗ Failed for: $name<br>";
    }
}

// Generate for clients (only those without avatars)
echo "<h3>Clients (client_list table):</h3>";
$clients = $conn->query("SELECT id, firstname, lastname, avatar FROM client_list WHERE delete_flag = 0");
while($client = $clients->fetch_assoc()) {
    $name = $client['firstname'] . ' ' . $client['lastname'];
    $filename = __DIR__ . '/uploads/client-' . $client['id'] . '.png';
    $dbPath = 'uploads/client-' . $client['id'] . '.png';
    
    // Check if avatar file already exists
    $existingPath = __DIR__ . '/' . explode('?', $client['avatar'] ?? '')[0];
    if(!empty($client['avatar']) && file_exists($existingPath)) {
        echo "→ Avatar exists for: $name<br>";
        continue;
    }
    
    $color = $colors[$colorIndex % count($colors)];
    $colorIndex++;
    
    if(downloadAvatar($name, $filename, $color)) {
        // Update database
        $avatarPath = $dbPath . '?v=' . time();
        $conn->query("UPDATE client_list SET avatar = '$avatarPath' WHERE id = " . $client['id']);
        echo "✓ Generated avatar for: $name<br>";
        $successCount++;
    } else {
        echo "✗ Failed for: $name<br>";
    }
}

echo "<hr>";
echo "<p style='color: green; font-size: 18px;'>✓ Successfully generated: $successCount avatars</p>";
echo "<p><a href='?page=users' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View User List</a> ";
echo "<a href='?page=clients' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>View Clients</a></p>";
?>
