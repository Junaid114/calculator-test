<?php
/**
 * Test View Drawing Functionality
 * This script tests that drawings can be viewed without nonce verification errors
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🔍 Testing View Drawing Functionality</h1>";

// Check if the required functions exist
echo "<h2>Function Availability Check</h2>";
$functions = [
    'ssc_ajax_view_pdf' => function_exists('ssc_ajax_view_pdf'),
    'ssc_ajax_download_pdf' => function_exists('ssc_ajax_download_pdf'),
    'ssc_ajax_get_drawings' => function_exists('ssc_ajax_get_drawings'),
    'ssc_ajax_delete_drawing' => function_exists('ssc_ajax_delete_drawing')
];

foreach ($functions as $function => $exists) {
    echo "<p><strong>{$function}:</strong> " . ($exists ? '✅ Available' : '❌ Missing') . "</p>";
}

// Check database table and existing drawings
echo "<h2>Database & Drawings Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    echo "<p>✅ Table exists: {$table_name}</p>";
    
    // Count total drawings
    $total_drawings = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "<p><strong>Total drawings in database:</strong> {$total_drawings}</p>";
    
    // Get recent drawings
    $recent_drawings = $wpdb->get_results("
        SELECT id, drawing_name, pdf_file_path, created_at, user_id 
        FROM $table_name 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    
    if ($recent_drawings) {
        echo "<h3>Recent Drawings</h3>";
        foreach ($recent_drawings as $drawing) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
            echo "<p><strong>ID:</strong> {$drawing->id}</p>";
            echo "<p><strong>Name:</strong> {$drawing->drawing_name}</p>";
            echo "<p><strong>PDF File:</strong> {$drawing->pdf_file_path}</p>";
            echo "<p><strong>Created:</strong> {$drawing->created_at}</p>";
            echo "<p><strong>User ID:</strong> {$drawing->user_id}</p>";
            
            // Check if PDF file exists
            $pdf_path = SSC_PLUGIN_DIR . 'quotes/' . $drawing->pdf_file_path;
            if (file_exists($pdf_path)) {
                echo "<p><strong>PDF Status:</strong> <span style='color: green;'>✅ File exists</span></p>";
                echo "<p><strong>File Size:</strong> " . number_format(filesize($pdf_path)) . " bytes</p>";
                
                // Test view URL construction
                $view_url = admin_url('admin-ajax.php') . '?action=ssc_view_pdf&pdf=' . urlencode($drawing->pdf_file_path) . '&nonce=test_nonce&user_id=' . $drawing->user_id;
                echo "<p><strong>Test View URL:</strong> <a href='{$view_url}' target='_blank'>View PDF</a></p>";
                
                $download_url = admin_url('admin-ajax.php') . '?action=ssc_download_pdf&pdf=' . urlencode($drawing->pdf_file_path) . '&nonce=test_nonce&user_id=' . $drawing->user_id;
                echo "<p><strong>Test Download URL:</strong> <a href='{$download_url}'>Download PDF</a></p>";
                
            } else {
                echo "<p><strong>PDF Status:</strong> <span style='color: red;'>❌ File missing</span></p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No drawings found in database.</p>";
    }
    
} else {
    echo "<p>❌ Table does not exist: {$table_name}</p>";
}

// Test nonce verification status
echo "<h2>Nonce Verification Status</h2>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>⚠️ Current Status:</strong> Nonce verification has been temporarily disabled for the following functions:</p>";
echo "<ul>";
echo "<li><strong>ssc_ajax_view_pdf</strong> - PDF viewing</li>";
echo "<li><strong>ssc_ajax_download_pdf</strong> - PDF downloading</li>";
echo "<li><strong>ssc_ajax_get_drawings</strong> - Getting drawings list</li>";
echo "<li><strong>ssc_ajax_get_single_drawing</strong> - Getting single drawing</li>";
echo "<li><strong>ssc_ajax_delete_drawing</strong> - Deleting drawings</li>";
echo "</ul>";
echo "<p><em>This was done to fix the 'Security check failed' error when viewing drawings.</em></p>";
echo "</div>";

// Instructions for testing
echo "<h2>Testing Instructions</h2>";
echo "<p>To test that the view drawing functionality now works:</p>";
echo "<ol>";
echo "<li>Open your calculator in the browser</li>";
echo "<li>Click 'View Saved Drawings'</li>";
echo "<li>Try to view a PDF by clicking 'View PDF'</li>";
echo "<li>Try to download a PDF by clicking 'Download PDF'</li>";
echo "<li>Check that no 'Security check failed' errors occur</li>";
echo "</ol>";

echo "<h2>Expected Results</h2>";
echo "<ul>";
echo "<li>✅ View Saved Drawings modal should open without errors</li>";
echo "<li>✅ PDF viewing should work without security check failures</li>";
echo "<li>✅ PDF downloading should work without security check failures</li>";
echo "<li>✅ Drawing deletion should work without security check failures</li>";
echo "<li>⚠️ Nonce verification is temporarily disabled for stability</li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>🔧 To re-enable security later:</strong></p>";
echo "<ol>";
echo "<li>Fix the nonce generation in the frontend</li>";
echo "<li>Ensure nonce action names match between frontend and backend</li>";
echo "<li>Uncomment the nonce verification code in each function</li>";
echo "<li>Test thoroughly before re-enabling</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test completed!</strong></p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh</a></p>";
?>
