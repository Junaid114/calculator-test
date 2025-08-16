<?php
/**
 * Test Save Drawing Functionality
 * Simple test to verify drawing save works
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to test drawing save');
}

echo "<h1>🧪 Test Save Drawing Functionality</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Test 1: Check if functions exist
echo "<h2>📋 Function Check</h2>";
if (function_exists('ssc_save_drawing')) {
    echo "✅ <code>ssc_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_save_drawing()</code> function does not exist<br>";
}

if (function_exists('ssc_ajax_save_drawing')) {
    echo "✅ <code>ssc_ajax_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_ajax_save_drawing()</code> function does not exist<br>";
}

// Test 2: Check database table
echo "<h2>🗄️ Database Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
if ($table_exists) {
    echo "✅ Database table exists<br>";
    
    // Count existing drawings
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total drawings: $total_count<br>";
    
    // Show recent drawings
    if ($total_count > 0) {
        $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 3", ARRAY_A);
        echo "<h3>Recent Drawings:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>User ID</th><th>PDF File</th><th>Created</th></tr>";
        foreach ($recent_drawings as $drawing) {
            echo "<tr>";
            echo "<td>{$drawing['id']}</td>";
            echo "<td>{$drawing['drawing_name']}</td>";
            echo "<td>{$drawing['user_id']}</td>";
            echo "<td>{$drawing['pdf_file_path']}</td>";
            echo "<td>{$drawing['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "❌ Database table does not exist<br>";
}

// Test 3: Check file permissions
echo "<h2>📁 File Permissions Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

// Test 4: Check AJAX actions
echo "<h2>🔧 AJAX Actions Check</h2>";
$ajax_actions = array(
    'ssc_save_drawing',
    'ssc_generate_enhanced_pdf',
    'ssc_get_drawings',
    'ssc_get_drawing',
    'ssc_view_pdf',
    'ssc_download_pdf',
    'ssc_delete_drawing'
);

foreach ($ajax_actions as $action) {
    if (has_action("wp_ajax_$action")) {
        echo "✅ <code>$action</code> is registered<br>";
    } else {
        echo "❌ <code>$action</code> is NOT registered<br>";
    }
}

// Test 5: Check nonces
echo "<h2>🔐 Nonce Check</h2>";
$drawing_nonce = wp_create_nonce('ssc_save_drawing_nonce');
$auth_nonce = wp_create_nonce('ssc_auth_nonce');
echo "Save drawing nonce: $drawing_nonce<br>";
echo "Auth nonce: $auth_nonce<br>";

// Test 6: Manual test instructions
echo "<h2>🧪 Manual Testing Instructions</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>Steps to Test Drawing Save:</h3>";
echo "<ol>";
echo "<li><strong>Go to Calculator:</strong> Navigate to a product page with the stone slab calculator</li>";
echo "<li><strong>Create a Drawing:</strong> Use the drawing tools to create a design</li>";
echo "<li><strong>Save Drawing:</strong> Click save and give it a name</li>";
echo "<li><strong>Check Console:</strong> Open browser console (F12) to see debug logs</li>";
echo "<li><strong>Verify Save:</strong> Check if drawing appears in saved drawings list</li>";
echo "<li><strong>Check Database:</strong> Refresh this page to see new records</li>";
echo "</ol>";

echo "<h3>Expected Results:</h3>";
echo "<ul>";
echo "<li>✅ Console shows debug logs with drawing data</li>";
echo "<li>✅ Drawing saves without errors</li>";
echo "<li>✅ New record appears in database</li>";
echo "<li>✅ PDF file is created in quotes directory</li>";
echo "<li>✅ Filename follows QuoteID-UserID-Product-Date.pdf format</li>";
echo "</ul>";

echo "<h3>If Issues Occur:</h3>";
echo "<ul>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Check WordPress debug log for PHP errors</li>";
echo "<li>Verify all AJAX actions are registered</li>";
echo "<li>Check file permissions on quotes directory</li>";
echo "</ul>";
echo "</div>";

// Test 7: Current status summary
echo "<h2>📊 Current Status Summary</h2>";
$status_ok = $table_exists && is_writable($quotes_dir);
echo "<div style='background: " . ($status_ok ? '#d4edda' : '#f8d7da') . "; border: 1px solid " . ($status_ok ? '#c3e6cb' : '#f5c6cb') . "; padding: 15px; border-radius: 5px;'>";
echo "<h3 style='color: " . ($status_ok ? '#155724' : '#721c24') . ";'>";
echo ($status_ok ? "✅ System Ready for Testing" : "⚠️ System Issues Found");
echo "</h3>";

if ($status_ok) {
    echo "<p>Your stone slab calculator is ready for testing. Follow the manual testing steps above.</p>";
} else {
    echo "<p>There are system issues that need to be resolved before testing:</p>";
    if (!$table_exists) echo "<li>Database table is missing</li>";
    if (!is_writable($quotes_dir)) echo "<li>Quotes directory is not writable</li>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


