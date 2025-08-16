<?php
/**
 * Debug Save Drawing Process
 * Tests the drawing save functionality step by step
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to debug drawing save');
}

echo "<h1>Debug Save Drawing Process</h1>";
echo "<h2>Current User</h2>";
echo "User ID: " . wp_get_current_user()->ID . "<br>";
echo "Username: " . wp_get_current_user()->user_login . "<br>";

// Check AJAX actions
echo "<h2>AJAX Actions Check</h2>";
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
        echo "✅ <code>$action</code> action is registered<br>";
    } else {
        echo "❌ <code>$action</code> action is NOT registered<br>";
    }
}

// Check database
echo "<h2>Database Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
echo "Table exists: " . ($table_exists ? 'YES' : 'NO') . "<br>";

if ($table_exists) {
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total drawings: $total_count<br>";
    
    // Show recent drawings
    $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5", ARRAY_A);
    if ($recent_drawings) {
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
}

// Check file permissions
echo "<h2>File Permissions Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

// Check nonces
echo "<h2>Nonce Check</h2>";
$drawing_nonce = wp_create_nonce('ssc_save_drawing_nonce');
$auth_nonce = wp_create_nonce('ssc_auth_nonce');
echo "Save drawing nonce: $drawing_nonce<br>";
echo "Auth nonce: $auth_nonce<br>";

// Test form submission
echo "<h2>Test Form Submission</h2>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='hidden' name='action' value='ssc_save_drawing'>";
echo "<input type='hidden' name='nonce' value='$drawing_nonce'>";
echo "<input type='hidden' name='user_id' value='" . wp_get_current_user()->ID . "'>";
echo "<input type='hidden' name='drawing_name' value='Test Drawing'>";
echo "<input type='hidden' name='drawing_notes' value='Test Notes'>";
echo "<input type='hidden' name='total_cutting_mm' value='100'>";
echo "<input type='hidden' name='only_cut_mm' value='50'>";
echo "<input type='hidden' name='mitred_cut_mm' value='50'>";
echo "<input type='hidden' name='slab_cost' value='$1000'>";
echo "<input type='hidden' name='drawing_data' value='{\"test\": \"data\"}'>";
echo "<input type='hidden' name='drawing_link' value='test'>";
echo "<input type='file' name='pdf_file' accept='.pdf' required><br><br>";
echo "<button type='submit'>Test Save Drawing</button>";
echo "</form>";

// Process form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'ssc_save_drawing') {
    echo "<h3>Test Submission Results</h3>";
    
    // Check nonce
    if (wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        echo "✅ Nonce verification passed<br>";
    } else {
        echo "❌ Nonce verification failed<br>";
        return;
    }
    
    // Check PDF file
    if (isset($_FILES['pdf_file'])) {
        echo "✅ PDF file received<br>";
        echo "File name: " . $_FILES['pdf_file']['name'] . "<br>";
        echo "File size: " . $_FILES['pdf_file']['size'] . " bytes<br>";
        echo "File error: " . $_FILES['pdf_file']['error'] . "<br>";
        
        if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            echo "<h4>Attempting to save drawing...</h4>";
            
            // Call the save function directly
            $result = ssc_ajax_save_drawing();
            
            if ($result) {
                echo "✅ Drawing saved successfully!<br>";
            } else {
                echo "❌ Failed to save drawing<br>";
            }
        } else {
            echo "❌ PDF file upload error<br>";
        }
    } else {
        echo "❌ No PDF file received<br>";
    }
}

// Check for errors in WordPress debug log
echo "<h2>WordPress Debug Log Check</h2>";
$debug_log = WP_CONTENT_DIR . '/debug.log';
if (file_exists($debug_log)) {
    echo "Debug log exists<br>";
    $recent_logs = file_get_contents($debug_log);
    if ($recent_logs) {
        $lines = explode("\n", $recent_logs);
        $recent_lines = array_slice($lines, -10); // Last 10 lines
        echo "<h3>Recent Debug Log Entries:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;'>";
        foreach ($recent_lines as $line) {
            if (trim($line)) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre>";
    }
} else {
    echo "Debug log not found<br>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> This debug script should be removed in production.</p>";
?>


