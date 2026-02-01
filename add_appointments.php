<?php
require_once('./config.php');

echo "<h2>Adding Appointments...</h2>";

// Get all clients
$clientsResult = $conn->query("SELECT id FROM client_list WHERE delete_flag = 0");
$clients = [];
while($row = $clientsResult->fetch_assoc()) {
    $clients[] = $row['id'];
}

// Get all tests
$testsResult = $conn->query("SELECT id FROM test_list WHERE delete_flag = 0 AND status = 1");
$tests = [];
while($row = $testsResult->fetch_assoc()) {
    $tests[] = $row['id'];
}

if(empty($clients)) {
    echo "No clients found. Please add clients first.";
    exit;
}

if(empty($tests)) {
    echo "No tests found. Please add tests first.";
    exit;
}

// Status mapping
$statuses = [
    0 => 'Pending',
    1 => 'Approved',
    2 => 'Sample Collected',
    3 => 'Delivered to Lab',
    4 => 'Processing',
    5 => 'For Release',
    6 => 'Released'
];

$successCount = 0;

// Add 20 appointments
for($i = 0; $i < 20; $i++) {
    $year = date('Y');
    $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $code = $year . $month . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    
    // Random date in past 30 days to next 30 days
    $daysOffset = rand(-30, 30);
    $hoursOffset = rand(9, 17);
    $schedule = date('Y-m-d H:i:s', strtotime("$daysOffset days $hoursOffset:00:00"));
    
    $clientId = $clients[array_rand($clients)];
    $status = array_rand($statuses);
    
    // Check if code already exists
    $check = $conn->query("SELECT id FROM appointment_list WHERE code = '$code'");
    if($check->num_rows > 0) {
        $code = $year . $month . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    $sql = "INSERT INTO appointment_list (code, schedule, client_id, prescription_path, status, date_created) 
            VALUES ('$code', '$schedule', $clientId, '', $status, NOW())";
    
    if($conn->query($sql)) {
        $appointmentId = $conn->insert_id;
        
        // Add 1-4 random tests to this appointment
        $numTests = rand(1, 4);
        $selectedTestIndexes = array_rand($tests, min($numTests, count($tests)));
        if(!is_array($selectedTestIndexes)) {
            $selectedTestIndexes = [$selectedTestIndexes];
        }
        
        $testNames = [];
        foreach($selectedTestIndexes as $idx) {
            $testId = $tests[$idx];
            $conn->query("INSERT INTO appointment_test_list (appointment_id, test_id, date_created) VALUES ($appointmentId, $testId, NOW())");
            
            // Get test name for display
            $testResult = $conn->query("SELECT name FROM test_list WHERE id = $testId");
            if($testResult->num_rows > 0) {
                $testRow = $testResult->fetch_assoc();
                $testNames[] = $testRow['name'];
            }
        }
        
        // Add history entry
        $remarks = "Appointment booked.";
        $conn->query("INSERT INTO history_list (appointment_id, status, remarks, date_created) VALUES ($appointmentId, $status, '$remarks', NOW())");
        
        echo "✓ Added: $code - Status: {$statuses[$status]}<br>";
        echo "&nbsp;&nbsp;&nbsp;Tests: " . implode(', ', array_slice($testNames, 0, 2)) . (count($testNames) > 2 ? '...' : '') . "<br>";
        $successCount++;
    } else {
        echo "✗ Failed to add appointment: " . $conn->error . "<br>";
    }
}

echo "<hr>";
echo "<p style='color: green; font-size: 18px;'>✓ Successfully added: $successCount appointments</p>";
echo "<p><a href='?page=appointments' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Appointments</a></p>";
?>
