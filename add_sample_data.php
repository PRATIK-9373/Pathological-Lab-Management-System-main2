<?php
require_once('./config.php');

echo "<h2>Adding Sample Data to Database...</h2>";

// Sample data arrays
$firstNames = ['Amit', 'Priya', 'Rahul', 'Sneha', 'Vikram', 'Anita', 'Suresh', 'Kavita', 'Rajesh', 'Meera', 
               'Arun', 'Pooja', 'Deepak', 'Neha', 'Sanjay', 'Ritu', 'Manoj', 'Swati', 'Vijay', 'Anjali'];
$lastNames = ['Sharma', 'Patel', 'Singh', 'Kumar', 'Gupta', 'Verma', 'Joshi', 'Rao', 'Desai', 'Mehta',
              'Shah', 'Reddy', 'Nair', 'Pillai', 'Das', 'Roy', 'Bose', 'Sen', 'Iyer', 'Menon'];
$middleNames = ['A', 'B', 'C', 'D', 'K', 'M', 'N', 'P', 'R', 'S'];

$addresses = [
    'MG Road, Bangalore, Karnataka',
    'Andheri West, Mumbai, Maharashtra', 
    'Connaught Place, New Delhi',
    'Salt Lake, Kolkata, West Bengal',
    'Banjara Hills, Hyderabad, Telangana',
    'Anna Nagar, Chennai, Tamil Nadu',
    'Aundh, Pune, Maharashtra',
    'Vastrapur, Ahmedabad, Gujarat',
    'Gomti Nagar, Lucknow, UP',
    'Koregaon Park, Pune, Maharashtra'
];

// Test names
$testNames = [
    'Complete Blood Count (CBC)' => 800,
    'Lipid Profile' => 1200,
    'Liver Function Test (LFT)' => 1500,
    'Kidney Function Test (KFT)' => 1400,
    'Thyroid Profile (T3, T4, TSH)' => 1800,
    'HbA1c (Glycated Hemoglobin)' => 900,
    'Vitamin D Test' => 1600,
    'Vitamin B12 Test' => 1100,
    'Urine Routine & Microscopy' => 400,
    'Blood Glucose Fasting' => 300,
    'Blood Glucose PP' => 350,
    'Serum Creatinine' => 500,
    'Serum Uric Acid' => 450,
    'ESR (Erythrocyte Sedimentation Rate)' => 250,
    'C-Reactive Protein (CRP)' => 700
];

$testDescriptions = [
    'Complete Blood Count (CBC)' => 'A complete blood count test measures several components and features of blood including red blood cells, white blood cells, hemoglobin, hematocrit, and platelets.',
    'Lipid Profile' => 'A lipid profile measures the amount of cholesterol and triglycerides in the blood. It helps assess the risk of cardiovascular disease.',
    'Liver Function Test (LFT)' => 'Liver function tests check how well the liver is working by measuring levels of proteins, liver enzymes, and bilirubin.',
    'Kidney Function Test (KFT)' => 'Kidney function tests measure how well kidneys are filtering blood and removing waste products.',
    'Thyroid Profile (T3, T4, TSH)' => 'Thyroid function tests check how well the thyroid gland is working by measuring hormone levels.',
    'HbA1c (Glycated Hemoglobin)' => 'HbA1c test measures average blood sugar levels over the past 2-3 months.',
    'Vitamin D Test' => 'Measures the level of Vitamin D in the blood, important for bone health and immune function.',
    'Vitamin B12 Test' => 'Measures the level of Vitamin B12 in the blood, essential for nerve function and blood cell formation.',
    'Urine Routine & Microscopy' => 'Examines physical, chemical, and microscopic properties of urine.',
    'Blood Glucose Fasting' => 'Measures blood sugar levels after an overnight fast.',
    'Blood Glucose PP' => 'Measures blood sugar levels 2 hours after eating.',
    'Serum Creatinine' => 'Measures creatinine levels to assess kidney function.',
    'Serum Uric Acid' => 'Measures uric acid levels in the blood.',
    'ESR (Erythrocyte Sedimentation Rate)' => 'Measures how quickly red blood cells settle at the bottom of a test tube.',
    'C-Reactive Protein (CRP)' => 'Measures the level of CRP in blood, a marker of inflammation.'
];

$successCount = 0;
$errorCount = 0;

// 1. Add 10 new clients
echo "<h3>Adding Clients...</h3>";
$clientIds = [];
for($i = 0; $i < 10; $i++) {
    $firstname = $firstNames[array_rand($firstNames)];
    $middlename = $middleNames[array_rand($middleNames)];
    $lastname = $lastNames[array_rand($lastNames)];
    $gender = rand(0, 1) ? 'Male' : 'Female';
    $contact = '98' . rand(10000000, 99999999);
    $email = strtolower($firstname) . '.' . strtolower($lastname) . rand(1, 99) . '@email.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $dob = date('Y-m-d', strtotime('-' . rand(20, 50) . ' years -' . rand(1, 365) . ' days'));
    $address = $addresses[array_rand($addresses)];
    
    $sql = "INSERT INTO `client_list` (firstname, middlename, lastname, gender, contact, email, password, dob, address, delete_flag, date_created) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $firstname, $middlename, $lastname, $gender, $contact, $email, $password, $dob, $address);
    
    if($stmt->execute()) {
        $clientIds[] = $conn->insert_id;
        echo "✓ Added client: $firstname $lastname ($email)<br>";
        $successCount++;
    } else {
        echo "✗ Failed to add client: $firstname $lastname - " . $conn->error . "<br>";
        $errorCount++;
    }
}

// 2. Add new tests (if not already present)
echo "<h3>Adding Tests...</h3>";
$testIds = [];
foreach($testNames as $name => $cost) {
    // Check if test already exists
    $check = $conn->query("SELECT id FROM test_list WHERE name = '$name'");
    if($check->num_rows == 0) {
        $description = $testDescriptions[$name];
        $sql = "INSERT INTO `test_list` (name, description, cost, status, delete_flag, date_created) 
                VALUES (?, ?, ?, 1, 0, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $name, $description, $cost);
        
        if($stmt->execute()) {
            $testIds[] = $conn->insert_id;
            echo "✓ Added test: $name (₹$cost)<br>";
            $successCount++;
        } else {
            echo "✗ Failed to add test: $name - " . $conn->error . "<br>";
            $errorCount++;
        }
    } else {
        $row = $check->fetch_assoc();
        $testIds[] = $row['id'];
        echo "→ Test already exists: $name<br>";
    }
}

// Get all client IDs
$allClients = $conn->query("SELECT id FROM client_list WHERE delete_flag = 0");
$allClientIds = [];
while($row = $allClients->fetch_assoc()) {
    $allClientIds[] = $row['id'];
}

// Get all test IDs
$allTests = $conn->query("SELECT id FROM test_list WHERE delete_flag = 0 AND status = 1");
$allTestIds = [];
while($row = $allTests->fetch_assoc()) {
    $allTestIds[] = $row['id'];
}

// 3. Add 10 appointments with various statuses
echo "<h3>Adding Appointments...</h3>";
$statuses = [0, 1, 2, 3, 4, 5, 6]; // 0=Pending, 1=Approved, 2=Sample Collected, 3=Testing, 4=Done, 5=For Release, 6=Released
$statusNames = [0 => 'Pending', 1 => 'Approved', 2 => 'Sample Collected', 3 => 'Testing', 4 => 'Done', 5 => 'For Release', 6 => 'Released'];

for($i = 0; $i < 10; $i++) {
    $year = date('Y');
    $month = date('m');
    $code = $year . $month . '-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT);
    
    // Schedule in next 30 days
    $schedule = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days +' . rand(9, 17) . ' hours'));
    $clientId = $allClientIds[array_rand($allClientIds)];
    $status = $statuses[array_rand($statuses)];
    
    $sql = "INSERT INTO `appointment_list` (code, schedule, client_id, prescription_path, status, date_created) 
            VALUES (?, ?, ?, '', ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $code, $schedule, $clientId, $status);
    
    if($stmt->execute()) {
        $appointmentId = $conn->insert_id;
        echo "✓ Added appointment: $code (Status: {$statusNames[$status]})<br>";
        $successCount++;
        
        // Add 1-3 tests to this appointment
        $numTests = rand(1, 3);
        $selectedTests = array_rand($allTestIds, min($numTests, count($allTestIds)));
        if(!is_array($selectedTests)) $selectedTests = [$selectedTests];
        
        foreach($selectedTests as $testIndex) {
            $testId = $allTestIds[$testIndex];
            $conn->query("INSERT INTO appointment_test_list (appointment_id, test_id, date_created) VALUES ($appointmentId, $testId, NOW())");
        }
        
        // Add history entry
        $remarks = "Appointment created via sample data script.";
        $conn->query("INSERT INTO history_list (appointment_id, status, remarks, date_created) VALUES ($appointmentId, $status, '$remarks', NOW())");
        
    } else {
        echo "✗ Failed to add appointment: $code - " . $conn->error . "<br>";
        $errorCount++;
    }
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "<p style='color: green;'>✓ Successfully added: $successCount entries</p>";
if($errorCount > 0) {
    echo "<p style='color: red;'>✗ Failed: $errorCount entries</p>";
}
echo "<p><a href='./' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>";
?>
