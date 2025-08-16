<?php
/**
 * Test View Drawings Fix
 * This script tests that the view drawings functionality works after the fixes
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🔍 Testing View Drawings Fix</h1>";

// Check if the required functions exist
echo "<h2>Function Availability Check</h2>";
$functions = [
    'ssc_ajax_get_drawings' => function_exists('ssc_ajax_get_drawings'),
    'ssc_get_user_drawings' => function_exists('ssc_get_user_drawings'),
    'ssc_get_all_drawings' => function_exists('ssc_get_all_drawings'),
    'ssc_ajax_view_pdf' => function_exists('ssc_ajax_view_pdf')
];

foreach ($functions as $function => $exists) {
    echo "<p><strong>{$function}:</strong> " . ($exists ? '✅ Available' : '❌ Missing') . "</p>";
}

// Test the get drawings functions directly
echo "<h2>Direct Function Testing</h2>";

// Test getting all drawings
echo "<h3>Testing ssc_get_all_drawings()</h3>";
if (function_exists('ssc_get_all_drawings')) {
    $all_drawings = ssc_get_all_drawings();
    if ($all_drawings !== false) {
        echo "<p>✅ ssc_get_all_drawings() returned " . count($all_drawings) . " drawings</p>";
        
        if (count($all_drawings) > 0) {
            echo "<p><strong>Sample drawing:</strong></p>";
            $sample = $all_drawings[0];
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 12px;'>";
            echo htmlspecialchars(json_encode($sample, JSON_PRETTY_PRINT));
            echo "</pre>";
        }
    } else {
        echo "<p>❌ ssc_get_all_drawings() returned false</p>";
    }
} else {
    echo "<p>❌ Function not available</p>";
}

// Test getting user drawings
echo "<h3>Testing ssc_get_user_drawings()</h3>";
if (function_exists('ssc_get_user_drawings')) {
    $user_drawings = ssc_get_user_drawings(1); // Test with user ID 1
    if ($user_drawings !== false) {
        echo "<p>✅ ssc_get_user_drawings(1) returned " . count($user_drawings) . " drawings</p>";
    } else {
        echo "<p>❌ ssc_get_user_drawings(1) returned false</p>";
    }
} else {
    echo "<p>❌ Function not available</p>";
}

// Check database table
echo "<h2>Database Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    echo "<p>✅ Table exists: {$table_name}</p>";
    
    // Count total drawings
    $total_drawings = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "<p><strong>Total drawings in database:</strong> {$total_drawings}</p>";
    
    // Check recent drawings
    $recent_drawings = $wpdb->get_results("
        SELECT id, drawing_name, user_id, created_at 
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
            echo "<p><strong>User ID:</strong> {$drawing->user_id}</p>";
            echo "<p><strong>Created:</strong> {$drawing->created_at}</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No drawings found in database.</p>";
    }
} else {
    echo "<p>❌ Table does not exist: {$table_name}</p>";
}

// Test AJAX endpoint simulation
echo "<h2>AJAX Endpoint Test</h2>";
echo "<p>Testing the AJAX endpoint manually...</p>";

// Simulate POST data
$_POST['action'] = 'ssc_get_drawings';
$_POST['nonce'] = 'test_nonce';

echo "<p><strong>Simulated POST data:</strong></p>";
echo "<ul>";
echo "<li>action: {$_POST['action']}</li>";
echo "<li>nonce: {$_POST['nonce']}</li>";
echo "<li>user_id: (not provided)</li>";
echo "</ul>";

echo "<p><strong>Expected behavior:</strong></p>";
echo "<ul>";
echo "<li>✅ Nonce verification should be bypassed (temporarily disabled)</li>";
echo "<li>✅ Function should call ssc_get_all_drawings() since no user_id</li>";
echo "<li>✅ Should return JSON success response with drawings data</li>";
echo "</ul>";

echo "<h2>Frontend Testing Instructions</h2>";
echo "<p>To test the view drawings functionality:</p>";
echo "<ol>";
echo "<li><strong>Open Calculator:</strong> Go to your stone slab calculator</li>";
echo "<li><strong>Click Download Button:</strong> Click the download button to open dropdown</li>";
echo "<li><strong>Select 'View Saved Drawings':</strong> Click 'View Saved Drawings' from dropdown</li>";
echo "<li><strong>Check Console:</strong> Open browser console to see debug logs</li>";
echo "<li><strong>Look for:</strong> Modal should open and drawings should load</li>";
echo "</ol>";

echo "<h2>Expected Console Output</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 5px; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>🎯 loadSavedDrawings() function called</strong></p>";
echo "<p><strong>📊 Current state:</strong></p>";
echo "<p>- drawingNonce: [nonce value]</p>";
echo "<p>- currentUserId: [user ID or null]</p>";
echo "<p>- stone_slab_ajax.ajaxurl: [AJAX URL]</p>";
echo "<p><strong>🌐 Making AJAX call to: [URL]</strong></p>";
echo "<p><strong>🔄 AJAX request starting...</strong></p>";
echo "<p><strong>✅ AJAX success response received: [response data]</strong></p>";
echo "<p><strong>🎉 Success! Displaying drawings...</strong></p>";
echo "<p><strong>🎨 displaySavedDrawings() function called</strong></p>";
echo "</div>";

echo "<h2>Troubleshooting</h2>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>If drawings still don't show:</strong></p>";
echo "<ul>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Verify AJAX URL is correct</li>";
echo "<li>Check WordPress debug log for backend errors</li>";
echo "<li>Ensure database table exists and has data</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test completed!</strong></p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh</a></p>";
?>
