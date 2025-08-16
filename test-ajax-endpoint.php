<?php
/*
 * Test AJAX Endpoint
 * This file tests if the AJAX endpoint is working correctly
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    echo "<h1>❌ User not logged in</h1>";
    echo "<p>Please log in to test the AJAX functionality.</p>";
    exit;
}

$user = wp_get_current_user();
echo "<h1>🧪 Test AJAX Endpoint</h1>";
echo "<p><strong>Current User:</strong> " . $user->display_name . " (ID: " . $user->ID . ")</p>";

// Check AJAX URL
$ajax_url = admin_url('admin-ajax.php');
echo "<h2>AJAX URL Check</h2>";
echo "<p><strong>AJAX URL:</strong> <code>$ajax_url</code></p>";

// Test if we can access the AJAX endpoint
echo "<h2>Test AJAX Endpoint Access</h2>";
echo "<p>Testing if we can access the AJAX endpoint...</p>";

// Create a simple test request
$test_data = array(
    'action' => 'ssc_save_drawing',
    'user_id' => $user->ID,
    'drawing_name' => 'TEST_AJAX_' . time(),
    'drawing_notes' => 'Testing AJAX endpoint',
    'total_cutting_mm' => 100.50,
    'only_cut_mm' => 50.25,
    'mitred_cut_mm' => 50.25,
    'slab_cost' => '1000',
    'drawing_data' => json_encode(array('test' => 'ajax')),
    'drawing_link' => site_url()
);

echo "<h3>Test Data:</h3>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

// Test the AJAX endpoint directly
echo "<h3>Testing AJAX Endpoint Directly...</h3>";

// Simulate POST request
$_POST = $test_data;
$_FILES = array(); // No files for this test

// Capture output
ob_start();

// Call the AJAX handler directly
try {
    ssc_ajax_save_drawing();
    $output = ob_get_clean();
    echo "<h4>✅ AJAX Handler Output:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "<h4>❌ AJAX Handler Error:</h4>";
    echo "<p><strong>Exception:</strong> " . $e->getMessage() . "</p>";
    echo "<h4>Output:</h4>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
}

// Test enhanced PDF generation
echo "<h2>Test Enhanced PDF Generation</h2>";
echo "<p>Testing if the enhanced PDF generation endpoint is working...</p>";

$pdf_test_data = array(
    'action' => 'ssc_generate_enhanced_pdf',
    'user_id' => $user->ID,
    'drawing_name' => 'TEST_PDF_' . time(),
    'drawing_notes' => 'Testing PDF generation',
    'total_cutting_mm' => 100.50,
    'only_cut_mm' => 50.25,
    'mitred_cut_mm' => 50.25,
    'slab_cost' => '$1000',
    'canvas_data' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCdABmX/9k='
);

echo "<h3>PDF Test Data:</h3>";
echo "<pre>" . print_r($pdf_test_data, true) . "</pre>";

// Simulate POST request for PDF generation
$_POST = $pdf_test_data;

// Capture output
ob_start();

// Call the PDF generation handler directly
try {
    ssc_ajax_generate_enhanced_pdf();
    $pdf_output = ob_get_clean();
    echo "<h4>✅ PDF Generation Output:</h4>";
    echo "<pre>" . htmlspecialchars($pdf_output) . "</pre>";
} catch (Exception $e) {
    $pdf_output = ob_get_clean();
    echo "<h4>❌ PDF Generation Error:</h4>";
    echo "<p><strong>Exception:</strong> " . $e->getMessage() . "</p>";
    echo "<h4>Output:</h4>";
    echo "<pre>" . htmlspecialchars($pdf_output) . "</pre>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If both tests pass, the backend is working correctly</li>";
echo "<li>If tests fail, check the error messages above</li>";
echo "<li>Try the calculator again with the debugging enabled</li>";
echo "<li>Check the browser console for any JavaScript errors</li>";
echo "</ol>";

echo "<h2>Common Issues</h2>";
echo "<ul>";
echo "<li><strong>Database connection:</strong> Check if WordPress can connect to the database</li>";
echo "<li><strong>Function availability:</strong> Make sure all required functions exist</li>";
echo "<li><strong>File permissions:</strong> Check if the plugin files are readable</li>";
echo "<li><strong>WordPress hooks:</strong> Verify that AJAX actions are properly registered</li>";
echo "</ul>";
?>
