<?php
require_once('./config.php');

echo "<h2>Adding Pathology Tests...</h2>";

$tests = [
    // Hematology Tests
    ['Hemoglobin (Hb)', 'Measures the amount of hemoglobin in blood to detect anemia or polycythemia.', 150],
    ['Platelet Count', 'Measures the number of platelets in blood for clotting disorders.', 200],
    ['Prothrombin Time (PT)', 'Measures blood clotting time, used to monitor warfarin therapy.', 450],
    ['APTT (Activated Partial Thromboplastin Time)', 'Evaluates blood clotting function and heparin therapy.', 500],
    ['Reticulocyte Count', 'Measures immature red blood cells to assess bone marrow function.', 350],
    ['Peripheral Blood Smear', 'Microscopic examination of blood cells for abnormalities.', 300],
    
    // Biochemistry Tests
    ['Blood Urea Nitrogen (BUN)', 'Measures urea nitrogen levels to assess kidney function.', 250],
    ['Serum Electrolytes (Na, K, Cl)', 'Measures sodium, potassium, and chloride levels.', 600],
    ['Serum Calcium', 'Measures calcium levels for bone and parathyroid disorders.', 350],
    ['Serum Phosphorus', 'Measures phosphorus levels related to kidney and bone health.', 300],
    ['Serum Magnesium', 'Measures magnesium levels important for muscle and nerve function.', 400],
    ['Total Protein', 'Measures total protein in blood for liver and kidney assessment.', 250],
    ['Serum Albumin', 'Measures albumin levels for liver function and nutrition status.', 300],
    ['Serum Globulin', 'Measures globulin proteins for immune system assessment.', 350],
    ['A/G Ratio (Albumin/Globulin)', 'Ratio of albumin to globulin for liver disease detection.', 200],
    ['Bilirubin (Total, Direct, Indirect)', 'Measures bilirubin levels for liver and bile duct assessment.', 450],
    ['SGOT/AST', 'Liver enzyme test for liver damage detection.', 300],
    ['SGPT/ALT', 'Liver enzyme test, more specific for liver damage.', 300],
    ['Alkaline Phosphatase (ALP)', 'Enzyme test for liver and bone disorders.', 350],
    ['GGT (Gamma-Glutamyl Transferase)', 'Liver enzyme test, sensitive for alcohol-related liver disease.', 400],
    ['LDH (Lactate Dehydrogenase)', 'Enzyme test for tissue damage in heart, liver, and muscles.', 450],
    ['Amylase', 'Enzyme test for pancreatic disorders.', 500],
    ['Lipase', 'Enzyme test for pancreatic function, more specific than amylase.', 550],
    
    // Cardiac Markers
    ['Troponin I', 'Cardiac marker for heart attack diagnosis.', 1200],
    ['Troponin T', 'Cardiac marker for myocardial injury.', 1200],
    ['CK-MB (Creatine Kinase-MB)', 'Cardiac enzyme for heart muscle damage.', 800],
    ['BNP (B-type Natriuretic Peptide)', 'Marker for heart failure diagnosis.', 1500],
    ['Homocysteine', 'Risk marker for cardiovascular disease.', 1200],
    
    // Diabetes Tests
    ['Fasting Blood Sugar (FBS)', 'Blood glucose after overnight fasting.', 100],
    ['Post Prandial Blood Sugar (PPBS)', 'Blood glucose 2 hours after meal.', 100],
    ['Random Blood Sugar (RBS)', 'Blood glucose at any time of day.', 100],
    ['Oral Glucose Tolerance Test (OGTT)', 'Comprehensive test for diabetes diagnosis.', 400],
    ['Fructosamine', 'Short-term blood sugar control indicator.', 600],
    ['Insulin Fasting', 'Measures fasting insulin levels for insulin resistance.', 800],
    ['C-Peptide', 'Measures insulin production by pancreas.', 1000],
    
    // Thyroid Tests
    ['T3 (Triiodothyronine)', 'Active thyroid hormone measurement.', 400],
    ['T4 (Thyroxine)', 'Main thyroid hormone measurement.', 400],
    ['TSH (Thyroid Stimulating Hormone)', 'Primary test for thyroid function.', 450],
    ['Free T3', 'Measures unbound active thyroid hormone.', 500],
    ['Free T4', 'Measures unbound thyroxine hormone.', 500],
    ['Anti-TPO Antibodies', 'Autoimmune thyroid disease marker.', 900],
    ['Anti-Thyroglobulin Antibodies', 'Thyroid autoantibody test.', 900],
    
    // Hormone Tests
    ['Cortisol (Morning)', 'Stress hormone measurement.', 600],
    ['Testosterone (Total)', 'Male hormone level test.', 700],
    ['Estradiol (E2)', 'Female hormone level test.', 800],
    ['FSH (Follicle Stimulating Hormone)', 'Reproductive hormone test.', 600],
    ['LH (Luteinizing Hormone)', 'Reproductive hormone test.', 600],
    ['Prolactin', 'Pituitary hormone test.', 700],
    ['Progesterone', 'Female reproductive hormone.', 650],
    ['DHEA-S', 'Adrenal hormone test.', 800],
    ['Beta HCG (Pregnancy Test)', 'Hormone test for pregnancy detection.', 500],
    
    // Infectious Disease Tests
    ['HIV 1 & 2 Antibodies', 'Screening test for HIV infection.', 500],
    ['HBsAg (Hepatitis B Surface Antigen)', 'Hepatitis B infection marker.', 400],
    ['Anti-HCV (Hepatitis C Antibodies)', 'Hepatitis C screening test.', 600],
    ['VDRL/RPR (Syphilis Test)', 'Screening test for syphilis.', 300],
    ['Dengue NS1 Antigen', 'Early dengue infection detection.', 800],
    ['Dengue IgG/IgM Antibodies', 'Dengue antibody detection.', 900],
    ['Malaria Antigen Test', 'Rapid test for malaria parasites.', 500],
    ['Typhoid (Widal Test)', 'Antibody test for typhoid fever.', 400],
    ['COVID-19 RT-PCR', 'Molecular test for COVID-19 detection.', 1500],
    ['COVID-19 Antibody Test', 'Antibody detection for past COVID-19 infection.', 800],
    
    // Urine Tests
    ['Urine Culture & Sensitivity', 'Identifies bacteria and antibiotic sensitivity.', 800],
    ['24-Hour Urine Protein', 'Measures protein excretion over 24 hours.', 500],
    ['Urine Microalbumin', 'Early kidney damage detection in diabetics.', 600],
    ['Urine Creatinine', 'Kidney function assessment from urine.', 300],
    
    // Stool Tests
    ['Stool Routine & Microscopy', 'Examines stool for parasites and blood.', 300],
    ['Stool Culture', 'Identifies bacterial infections in stool.', 700],
    ['Stool Occult Blood Test', 'Detects hidden blood in stool.', 250],
    ['H. Pylori Stool Antigen', 'Detects H. pylori infection.', 900],
    
    // Tumor Markers
    ['PSA (Prostate Specific Antigen)', 'Prostate cancer screening marker.', 800],
    ['CA-125', 'Ovarian cancer marker.', 1200],
    ['CA 19-9', 'Pancreatic and GI cancer marker.', 1200],
    ['CEA (Carcinoembryonic Antigen)', 'Colorectal and other cancer marker.', 1000],
    ['AFP (Alpha Fetoprotein)', 'Liver cancer and pregnancy marker.', 900],
    
    // Vitamin & Mineral Tests
    ['Iron Studies (Serum Iron, TIBC, Ferritin)', 'Complete iron status assessment.', 1200],
    ['Serum Ferritin', 'Iron storage test.', 600],
    ['Folate (Folic Acid)', 'B-vitamin level test.', 800],
    ['Zinc', 'Essential mineral level test.', 700],
    ['Copper', 'Essential mineral level test.', 700],
];

$successCount = 0;

foreach($tests as $test) {
    $name = $conn->real_escape_string($test[0]);
    $description = $conn->real_escape_string($test[1]);
    $cost = $test[2];
    
    // Check if test already exists
    $check = $conn->query("SELECT id FROM test_list WHERE name = '$name'");
    if($check->num_rows > 0) {
        echo "→ Test already exists: $name<br>";
        continue;
    }
    
    $sql = "INSERT INTO test_list (name, description, cost, status, delete_flag, date_created) 
            VALUES ('$name', '$description', $cost, 1, 0, NOW())";
    
    if($conn->query($sql)) {
        echo "✓ Added: $name (₹$cost)<br>";
        $successCount++;
    } else {
        echo "✗ Failed: $name - " . $conn->error . "<br>";
    }
}

echo "<hr>";
echo "<p style='color: green; font-size: 18px;'>✓ Successfully added: $successCount tests</p>";
echo "<p><a href='?page=tests' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Test List</a></p>";
?>
