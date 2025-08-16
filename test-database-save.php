<?php
/**
 * Test Database Save Functionality
 * This script tests if the database table exists and can save data
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🧪 Database Save Test</h1>";
echo "<p>Testing database functionality for Stone Slab Calculator...</p>";

// Test 1: Check if table exists
echo "<h2>Test 1: Database Table Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
echo "<p>Table '$table_name' exists: <strong>" . ($table_exists ? '✅ YES' : '❌ NO') . "</strong></p>";

if (!$table_exists) {
    echo "<p>Creating table...</p>";
    if (function_exists('ssc_activate_plugin')) {
        ssc_activate_plugin();
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        echo "<p>Table created: <strong>" . ($table_exists ? '✅ YES' : '❌ NO') . "</strong></p>";
    } else {
        echo "<p>❌ ssc_activate_plugin function not found</p>";
    }
}

// Test 2: Check table structure
if ($table_exists) {
    echo "<h2>Test 2: Table Structure</h2>";
    $columns = $wpdb->get_results("DESCRIBE $table_name");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column->Field}</td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Key}</td>";
        echo "<td>{$column->Default}</td>";
        echo "<td>{$column->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 3: Test database save function
if ($table_exists && function_exists('ssc_save_drawing')) {
    echo "<h2>Test 3: Database Save Function</h2>";
    
    $test_data = array(
        'user_id' => 1,
        'drawing_name' => 'TEST_DRAWING_' . time(),
        'drawing_notes' => 'This is a test drawing for database verification',
        'total_cutting_mm' => 100.50,
        'only_cut_mm' => 50.25,
        'mitred_cut_mm' => 25.75,
        'slab_cost' => '$150.00',
        'drawing_data' => '{"test": "data"}',
        'pdf_file_path' => 'test_quote_' . time() . '.pdf',
        'drawing_link' => 'http://localhost/test'
    );
    
    echo "<p>Testing with data:</p>";
    echo "<pre>" . print_r($test_data, true) . "</pre>";
    
    $result = ssc_save_drawing($test_data);
    
    if ($result !== false) {
        echo "<p>✅ Database save successful! New ID: <strong>$result</strong></p>";
        
        // Test 4: Retrieve the saved data
        echo "<h2>Test 4: Data Retrieval</h2>";
        $saved_drawing = ssc_get_drawing($result);
        if ($saved_drawing) {
            echo "<p>✅ Data retrieved successfully:</p>";
            echo "<pre>" . print_r($saved_drawing, true) . "</pre>";
        } else {
            echo "<p>❌ Failed to retrieve saved data</p>";
        }
    } else {
        echo "<p>❌ Database save failed</p>";
        echo "<p>Last error: " . $wpdb->last_error . "</p>";
        echo "<p>Last query: " . $wpdb->last_query . "</p>";
    }
} else {
    echo "<p>❌ Cannot test database save - function not available</p>";
}

// Test 5: Check if quotes directory exists
echo "<h2>Test 5: Quotes Directory</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "<p>Quotes directory: <strong>$quotes_dir</strong></p>";
echo "<p>Directory exists: <strong>" . (file_exists($quotes_dir) ? '✅ YES' : '❌ NO') . "</strong></p>";
echo "<p>Directory writable: <strong>" . (is_writable($quotes_dir) ? '✅ YES' : '❌ NO') . "</strong></p>";

if (file_exists($quotes_dir)) {
    $files = scandir($quotes_dir);
    $pdf_files = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
    });
    echo "<p>PDF files in quotes directory: <strong>" . count($pdf_files) . "</strong></p>";
    if (!empty($pdf_files)) {
        echo "<ul>";
        foreach ($pdf_files as $file) {
            $file_path = $quotes_dir . $file;
            $file_size = filesize($file_path);
            echo "<li>$file ($file_size bytes)</li>";
        }
        echo "</ul>";
    }
}

echo "<hr>";
echo "<p><strong>Test completed!</strong></p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh Test</a></p>";
?>
