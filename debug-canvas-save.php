<?php
/*
 * Debug Canvas Save Functionality
 * This file helps debug why the canvas save function isn't working
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    echo "<h1>❌ User not logged in</h1>";
    echo "<p>Please log in to test the save functionality.</p>";
    exit;
}

$user = wp_get_current_user();
echo "<h1>🔍 Debug Canvas Save Functionality</h1>";
echo "<p><strong>Current User:</strong> " . $user->display_name . " (ID: " . $user->ID . ")</p>";

// Check if functions exist
echo "<h2>Function Availability Check</h2>";
if (function_exists('ssc_save_drawing')) {
    echo "✅ <code>ssc_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_save_drawing()</code> function does NOT exist<br>";
}

if (function_exists('ssc_ajax_save_drawing')) {
    echo "✅ <code>ssc_ajax_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_ajax_save_drawing()</code> function does NOT exist<br>";
}

if (function_exists('ssc_generate_enhanced_pdf')) {
    echo "✅ <code>ssc_generate_enhanced_pdf()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_generate_enhanced_pdf()</code> function does NOT exist<br>";
}

// Check database table
echo "<h2>Database Table Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

if ($table_exists) {
    echo "✅ Database table <code>$table_name</code> exists<br>";
    
    // Check table structure
    $columns = $wpdb->get_results("DESCRIBE $table_name");
    echo "<p><strong>Table columns:</strong></p><ul>";
    foreach ($columns as $column) {
        echo "<li><code>{$column->Field}</code> - {$column->Type}</li>";
    }
    echo "</ul>";
    
    // Check if table has data
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "<p><strong>Total drawings in database:</strong> $count</p>";
    
    // Check user's drawings
    $user_drawings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 5",
        $user->ID
    ));
    
    if ($user_drawings) {
        echo "<p><strong>Your recent drawings:</strong></p><ul>";
        foreach ($user_drawings as $drawing) {
            echo "<li><strong>{$drawing->drawing_name}</strong> - Created: {$drawing->created_at}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No drawings found for your user account.</p>";
    }
    
} else {
    echo "❌ Database table <code>$table_name</code> does NOT exist<br>";
}

// Check nonces
echo "<h2>Nonce Check</h2>";
$drawing_nonce = wp_create_nonce('ssc_save_drawing_nonce');
$auth_nonce = wp_create_nonce('stone_slab_auth_nonce');

echo "✅ Drawing nonce: <code>$drawing_nonce</code><br>";
echo "✅ Auth nonce: <code>$auth_nonce</code><br>";

// Check AJAX URL
echo "<h2>AJAX URL Check</h2>";
$ajax_url = admin_url('admin-ajax.php');
echo "✅ AJAX URL: <code>$ajax_url</code><br>";

// Test basic save functionality
echo "<h2>Test Basic Save Functionality</h2>";
echo "<form method='post' action=''>";
echo "<input type='hidden' name='test_save' value='1'>";
echo "<input type='hidden' name='nonce' value='$drawing_nonce'>";
echo "<button type='submit'>Test Basic Save Function</button>";
echo "</form>";

if (isset($_POST['test_save']) && wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
    echo "<h3>Testing Basic Save Function...</h3>";
    
    $test_data = array(
        'user_id' => $user->ID,
        'drawing_name' => 'TEST_DRAWING_' . time(),
        'drawing_notes' => 'This is a test drawing for debugging',
        'total_cutting_mm' => 100.50,
        'only_cut_mm' => 50.25,
        'mitred_cut_mm' => 50.25,
        'slab_cost' => '1000',
        'drawing_data' => json_encode(array('test' => 'data')),
        'pdf_file_path' => 'test_drawing.pdf',
        'drawing_link' => 'test_link'
    );
    
    $result = ssc_save_drawing($test_data);
    
    if ($result) {
        echo "✅ Basic save successful! Drawing ID: $result<br>";
        
        // Try to retrieve the saved drawing
        $saved_drawing = ssc_get_drawing($result);
        if ($saved_drawing) {
            echo "<h4>Saved Drawing Details:</h4>";
            echo "<pre>" . print_r($saved_drawing, true) . "</pre>";
        }
    } else {
        echo "❌ Basic save failed<br>";
        echo "<p><strong>Last database error:</strong> " . $wpdb->last_error . "</p>";
    }
}

// Test AJAX endpoint
echo "<h2>Test AJAX Endpoint</h2>";
echo "<p>To test the AJAX endpoint, you can use the browser's developer tools or a tool like Postman.</p>";
echo "<p><strong>AJAX POST data for testing:</strong></p>";
echo "<pre>";
echo "URL: $ajax_url\n";
echo "Action: ssc_save_drawing\n";
echo "Nonce: $drawing_nonce\n";
echo "User ID: {$user->ID}\n";
echo "Drawing Name: Test Drawing\n";
echo "Drawing Notes: Test notes\n";
echo "Total Cutting MM: 100.50\n";
echo "Only Cut MM: 50.25\n";
echo "Mitred Cut MM: 50.25\n";
echo "Slab Cost: $1000\n";
echo "Drawing Data: {\"test\": \"data\"}\n";
echo "Drawing Link: " . site_url() . "\n";
echo "</pre>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Open the calculator in your browser</li>";
echo "<li>Open Developer Tools (F12) and go to Console tab</li>";
echo "<li>Try to save a drawing and check for any error messages</li>";
echo "<li>Check the Network tab to see if AJAX requests are being sent</li>";
echo "<li>Look for any JavaScript errors in the Console</li>";
echo "</ol>";

echo "<h2>Common Issues to Check</h2>";
echo "<ul>";
echo "<li><strong>Canvas undefined:</strong> Check if the canvas variable is properly defined</li>";
echo "<li><strong>AJAX URL:</strong> Verify the ajaxurl variable in JavaScript</li>";
echo "<li><strong>Nonce:</strong> Check if the nonce is being passed correctly</li>";
echo "<li><strong>Form submission:</strong> Verify the form submit event is working</li>";
echo "<li><strong>Modal display:</strong> Check if the save modal is showing properly</li>";
echo "</ul>";
?>
