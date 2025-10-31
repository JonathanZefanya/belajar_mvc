<?php
/**
 * Test script untuk university search
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load core classes
require_once ROOT_PATH . '/app/core/Database.php';
require_once ROOT_PATH . '/app/models/University.php';

echo "=== UNIVERSITY SEARCH TEST ===\n\n";

// Test 1: Database connection
echo "1. Testing database connection...\n";
try {
    $db = new Database();
    $conn = $db->connect();
    echo "   ✓ Database connected successfully\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 2: University model
echo "2. Testing University model...\n";
$universityModel = new University();
echo "   ✓ University model instantiated\n\n";

// Test 3: Get all universities without search
echo "3. Testing getAll() without search...\n";
$universities = $universityModel->getAll(10, 0);
echo "   ✓ Found " . count($universities) . " universities\n";
if (count($universities) > 0) {
    echo "   - First university: " . $universities[0]['name'] . "\n";
}
echo "\n";

// Test 4: Get total count without search
echo "4. Testing getTotalCount() without search...\n";
$total = $universityModel->getTotalCount();
echo "   ✓ Total universities: " . $total . "\n\n";

// Test 5: Get all universities with search
echo "5. Testing getAll() with search keyword 'Indonesia'...\n";
$universities = $universityModel->getAll(10, 0, 'Indonesia');
echo "   ✓ Found " . count($universities) . " universities\n";
foreach ($universities as $univ) {
    echo "   - " . $univ['name'] . "\n";
}
echo "\n";

// Test 6: Get total count with search
echo "6. Testing getTotalCount() with search keyword 'Indonesia'...\n";
$total = $universityModel->getTotalCount('Indonesia');
echo "   ✓ Total universities matching 'Indonesia': " . $total . "\n\n";

// Test 7: Search method
echo "7. Testing search() method with keyword 'Bandung'...\n";
$universities = $universityModel->search('Bandung');
echo "   ✓ Found " . count($universities) . " universities\n";
foreach ($universities as $univ) {
    echo "   - " . $univ['name'] . "\n";
}
echo "\n";

echo "=== TEST COMPLETE ===\n";
