<?php
/**
 * Test Save Functionality
 * Verifies that the drawing save functionality is working correctly
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to test save functionality');
}

echo "<h1>🧪 Test Save Functionality</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Check database
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
        $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5", ARRAY_A);
        echo "<h3>Recent Drawings:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>User ID</th><th>PDF File</th><th>Created</th><th>Canvas Data</th></tr>";
        foreach ($recent_drawings as $drawing) {
            echo "<tr>";
            echo "<td>{$drawing['id']}</td>";
            echo "<td>{$drawing['drawing_name']}</td>";
            echo "<td>{$drawing['user_id']}</td>";
            echo "<td>{$drawing['pdf_file_path']}</td>";
            echo "<td>{$drawing['created_at']}</td>";
            
            // Check canvas data
            if (!empty($drawing['drawing_data'])) {
                try {
                    $drawing_data = json_decode($drawing['drawing_data'], true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($drawing_data['canvas_data'])) {
                        echo "<td>✅ Available (" . strlen($drawing_data['canvas_data']) . " chars)</td>";
                    } else {
                        echo "<td>⚠️ No canvas data</td>";
                    }
                } catch (Exception $e) {
                    echo "<td>❌ Error: " . $e->getMessage() . "</td>";
                }
            } else {
                echo "<td>❌ Empty</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "❌ Database table does not exist<br>";
}

// Check file permissions
echo "<h2>📁 File Permissions Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

// Check PDF files
if (file_exists($quotes_dir)) {
    $pdf_files = glob($quotes_dir . '*.pdf');
    echo "PDF files found: " . count($pdf_files) . "<br>";
    
    if (count($pdf_files) > 0) {
        echo "<h3>PDF Files:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Filename</th><th>Size</th><th>Modified</th></tr>";
        foreach ($pdf_files as $pdf_file) {
            $filename = basename($pdf_file);
            $size = filesize($pdf_file);
            $modified = date('Y-m-d H:i:s', filemtime($pdf_file));
            echo "<tr>";
            echo "<td>$filename</td>";
            echo "<td>" . number_format($size) . " bytes</td>";
            echo "<td>$modified</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Check AJAX actions
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

// Check nonces
echo "<h2>🔐 Nonce Check</h2>";
$drawing_nonce = wp_create_nonce('ssc_save_drawing_nonce');
$auth_nonce = wp_create_nonce('stone_slab_auth_nonce');
echo "Save drawing nonce: $drawing_nonce<br>";
echo "Auth nonce: $auth_nonce<br>";

// Test instructions
echo "<h2>🧪 Testing Instructions</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>Steps to Test Drawing Save:</h3>";
echo "<ol>";
echo "<li><strong>Go to Calculator:</strong> Navigate to the stone slab calculator</li>";
echo "<li><strong>Create a Drawing:</strong> Use the drawing tools to create a design</li>";
echo "<li><strong>Open Console:</strong> Press F12 to open browser console</li>";
echo "<li><strong>Save Drawing:</strong> Click save and give it a name</li>";
echo "<li><strong>Check Console:</strong> Look for debug messages</li>";
echo "<li><strong>Verify Save:</strong> Check if drawing appears in saved drawings</li>";
echo "<li><strong>Check Database:</strong> Refresh this page to see new records</li>";
echo "</ol>";

echo "<h3>Expected Console Output:</h3>";
echo "<ul>";
echo "<li>✅ 'saveDrawing function called'</li>";
echo "<li>✅ 'Save parameters: {...}'</li>";
echo "<li>✅ 'Canvas data generated, length: X'</li>";
echo "<li>✅ 'Generating enhanced/basic PDF...'</li>";
echo "<li>✅ 'generateEnhancedPDF/generateBasicPDF called with: {...}'</li>";
echo "<li>✅ 'Calculated values: {...}'</li>";
echo "<li>✅ 'Saving drawing with data: {...}'</li>";
echo "</ul>";

echo "<h3>If Save Still Doesn't Work:</h3>";
echo "<ul>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Verify all AJAX actions are registered</li>";
echo "<li>Check if canvas data is being generated</li>";
echo "<li>Look for AJAX request failures</li>";
echo "<li>Check WordPress debug log for PHP errors</li>";
echo "</ul>";
echo "</div>";

// Current status
echo "<h2>📊 Current Status</h2>";
$status_ok = $table_exists && is_writable($quotes_dir);
echo "<div style='background: " . ($status_ok ? '#d4edda' : '#f8d7da') . "; border: 1px solid " . ($status_ok ? '#c3e6cb' : '#f5c6cb') . "; padding: 15px; border-radius: 5px;'>";
echo "<h3 style='color: " . ($status_ok ? '#155724' : '#721c24') . ";'>";
echo ($status_ok ? "✅ System Ready for Testing" : "⚠️ System Issues Found");
echo "</h3>";

if ($status_ok) {
    echo "<p>Your save functionality should now be working. Follow the testing steps above.</p>";
    echo "<p><strong>Key Fixes Applied:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Fixed undefined variables in PDF generation functions</li>";
    echo "<li>✅ Added proper canvas calculation calls</li>";
    echo "<li>✅ Added comprehensive debug logging</li>";
    echo "<li>✅ Fixed variable scope issues</li>";
    echo "</ul>";
} else {
    echo "<p>There are system issues that need to be resolved:</p>";
    if (!$table_exists) echo "<li>Database table is missing</li>";
    if (!is_writable($quotes_dir)) echo "<li>Quotes directory is not writable</li>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


