<?php
/**
 * Simple Test for Canvas Recreation
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🎨 Canvas Recreation Test</h1>";

// Check if the required functions exist
echo "<h2>Function Check</h2>";
$functions = [
    'ssc_ajax_view_pdf' => function_exists('ssc_ajax_view_pdf'),
    'ssc_ajax_get_drawings' => function_exists('ssc_ajax_get_drawings'),
    'ssc_ajax_delete_drawing' => function_exists('ssc_ajax_delete_drawing')
];

foreach ($functions as $function => $exists) {
    echo "<p><strong>{$function}:</strong> " . ($exists ? '✅ Available' : '❌ Missing') . "</p>";
}

// Check database for drawings with canvas data
echo "<h2>Drawings with Canvas Data</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $drawings_with_canvas = $wpdb->get_results("
        SELECT id, drawing_name, drawing_data, created_at 
        FROM $table_name 
        WHERE drawing_data IS NOT NULL 
        AND drawing_data != '' 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    
    if ($drawings_with_canvas) {
        foreach ($drawings_with_canvas as $drawing) {
            echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 8px; background: #f9f9f9;'>";
            echo "<h3>🎨 {$drawing->drawing_name}</h3>";
            echo "<p><strong>ID:</strong> {$drawing->id}</p>";
            echo "<p><strong>Created:</strong> {$drawing->created_at}</p>";
            
            // Parse drawing data
            try {
                $data = json_decode($drawing->drawing_data, true);
                if ($data && isset($data['canvas_objects'])) {
                    echo "<p><strong>Canvas Objects:</strong> <span style='color: green; font-weight: bold;'>" . count($data['canvas_objects']) . " objects</span></p>";
                    echo "<p><strong>Canvas Size:</strong> {$data['canvas_width']} x {$data['canvas_height']}</p>";
                    
                    // Show object types
                    $object_types = array_count_values(array_column($data['canvas_objects'], 'type'));
                    echo "<p><strong>Object Types:</strong> ";
                    foreach ($object_types as $type => $count) {
                        echo "<span style='background: #e3f2fd; padding: 2px 6px; margin: 2px; border-radius: 3px;'>{$type} ({$count})</span>";
                    }
                    echo "</p>";
                    
                    // Show sample object data
                    if (count($data['canvas_objects']) > 0) {
                        $sample = $data['canvas_objects'][0];
                        echo "<p><strong>Sample Object:</strong></p>";
                        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 12px;'>";
                        echo htmlspecialchars(json_encode($sample, JSON_PRETTY_PRINT));
                        echo "</pre>";
                    }
                    
                } else {
                    echo "<p><strong>Canvas Objects:</strong> <span style='color: orange;'>No canvas objects found</span></p>";
                }
            } catch (Exception $e) {
                echo "<p><strong>Error:</strong> <span style='color: red;'>" . $e->getMessage() . "</span></p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No drawings with canvas data found.</p>";
        echo "<p><em>Hint: Create a new drawing with some shapes/text and save it to generate canvas data.</em></p>";
    }
} else {
    echo "<p>❌ Database table does not exist.</p>";
}

echo "<h2>How to Test Canvas Recreation</h2>";
echo "<ol>";
echo "<li><strong>Open Calculator:</strong> Go to your stone slab calculator</li>";
echo "<li><strong>Create Drawing:</strong> Add some shapes, text, or lines to the canvas</li>";
echo "<li><strong>Save Drawing:</strong> Use 'Save Drawing' with Enhanced PDF selected</li>";
echo "<li><strong>View Saved Drawings:</strong> Click 'View Saved Drawings' button</li>";
echo "<li><strong>Look for 🎨 Button:</strong> You should see '🎨 Recreate Canvas' button</li>";
echo "<li><strong>Click Recreate:</strong> Click the button to load the drawing on canvas</li>";
echo "</ol>";

echo "<h2>Expected Results</h2>";
echo "<ul>";
echo "<li>✅ Canvas should clear and show your saved drawing</li>";
echo "<li>✅ All shapes, text, and objects should appear in correct positions</li>";
echo "<li>✅ Canvas should be editable (you can move, resize, delete objects)</li>";
echo "<li>✅ Modal should close automatically after successful recreation</li>";
echo "</ul>";

echo "<h2>Troubleshooting</h2>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>If canvas recreation doesn't work:</strong></p>";
echo "<ul>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "<li>Ensure Fabric.js library is loaded</li>";
echo "<li>Verify canvas variable is accessible</li>";
echo "<li>Check that drawing data contains canvas_objects</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test completed!</strong></p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh</a></p>";
?>
