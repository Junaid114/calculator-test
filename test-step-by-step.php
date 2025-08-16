<?php
/**
 * Step by Step Save Test
 * Tests each part of the save process individually
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to test save functionality');
}

echo "<h1>🧪 Step by Step Save Test</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Step 1: Check basic functions
echo "<h2>🔧 Step 1: Function Check</h2>";
$functions_exist = true;

if (function_exists('ssc_ajax_save_drawing')) {
    echo "✅ <code>ssc_ajax_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_ajax_save_drawing()</code> function does NOT exist<br>";
    $functions_exist = false;
}

if (function_exists('ssc_save_drawing')) {
    echo "✅ <code>ssc_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_save_drawing()</code> function does NOT exist<br>";
    $functions_exist = false;
}

// Step 2: Check database
echo "<h2>🗄️ Step 2: Database Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
if ($table_exists) {
    echo "✅ Database table exists<br>";
    
    // Count existing drawings
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total drawings: $total_count<br>";
    
    // Check table structure
    $table_structure = $wpdb->get_results("DESCRIBE $table_name", ARRAY_A);
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($table_structure as $field) {
        echo "<tr>";
        echo "<td>{$field['Field']}</td>";
        echo "<td>{$field['Type']}</td>";
        echo "<td>{$field['Null']}</td>";
        echo "<td>{$field['Key']}</td>";
        echo "<td>{$field['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Database table does not exist<br>";
    $functions_exist = false;
}

// Step 3: Check directory
echo "<h2>📁 Step 3: Directory Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

if (!file_exists($quotes_dir)) {
    echo "⚠️ Creating quotes directory...<br>";
    if (wp_mkdir_p($quotes_dir)) {
        echo "✅ Quotes directory created<br>";
    } else {
        echo "❌ Failed to create quotes directory<br>";
        $functions_exist = false;
    }
}

// Step 4: Test basic save
echo "<h2>🧪 Step 4: Test Basic Save</h2>";
if ($functions_exist && $table_exists) {
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='test_basic_save'>";
    echo "<button type='submit'>Test Basic Save Function</button>";
    echo "</form>";
    
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'test_basic_save') {
        echo "<h3>Basic Save Test Results:</h3>";
        
        // Test data
        $test_data = [
            'user_id' => get_current_user_id(),
            'drawing_name' => 'Test Drawing ' . date('Y-m-d H:i:s'),
            'drawing_notes' => 'Test notes from basic save',
            'total_cutting_mm' => 1000,
            'only_cut_mm' => 800,
            'mitred_cut_mm' => 200,
            'slab_cost' => '$1500',
            'drawing_data' => json_encode(['test' => 'data']),
            'pdf_file_path' => 'test_' . time() . '.pdf',
            'drawing_link' => site_url()
        ];
        
        echo "<h4>Test Data:</h4>";
        echo "<pre>" . print_r($test_data, true) . "</pre>";
        
        // Test the save function
        try {
            $result = ssc_save_drawing($test_data);
            if ($result) {
                echo "✅ Basic save successful! Drawing ID: $result<br>";
                
                // Verify it was saved
                $saved_drawing = ssc_get_drawing($result);
                if ($saved_drawing) {
                    echo "✅ Drawing retrieved from database<br>";
                    echo "<h4>Saved Drawing:</h4>";
                    echo "<pre>" . print_r($saved_drawing, true) . "</pre>";
                } else {
                    echo "❌ Drawing not found in database after save<br>";
                }
            } else {
                echo "❌ Basic save failed<br>";
                
                // Check for database errors
                global $wpdb;
                if ($wpdb->last_error) {
                    echo "Database error: " . $wpdb->last_error . "<br>";
                }
            }
        } catch (Exception $e) {
            echo "❌ Exception during save: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "⚠️ Cannot test basic save - prerequisites not met<br>";
}

// Step 5: Test AJAX handler
echo "<h2>🔧 Step 5: Test AJAX Handler</h2>";
if ($functions_exist) {
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='test_ajax_handler'>";
    echo "<input type='hidden' name='nonce' value='" . wp_create_nonce('ssc_save_drawing_nonce') . "'>";
    echo "<button type='submit'>Test AJAX Handler</button>";
    echo "</form>";
    
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'test_ajax_handler') {
        echo "<h3>AJAX Handler Test Results:</h3>";
        
        // Simulate AJAX request
        $_POST['action'] = 'ssc_save_drawing';
        $_POST['drawing_name'] = 'AJAX Test ' . date('Y-m-d H:i:s');
        $_POST['drawing_notes'] = 'Test from AJAX handler';
        $_POST['total_cutting_mm'] = 1200;
        $_POST['only_cut_mm'] = 900;
        $_POST['mitred_cut_mm'] = 300;
        $_POST['slab_cost'] = '$1800';
        $_POST['drawing_data'] = json_encode(['ajax_test' => 'data']);
        $_POST['drawing_link'] = site_url();
        
        echo "<h4>Simulated AJAX Data:</h4>";
        echo "<pre>" . print_r($_POST, true) . "</pre>";
        
        // Test nonce verification
        if (wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
            echo "✅ Nonce verification passed<br>";
            
            // Test the AJAX handler
            try {
                ob_start();
                ssc_ajax_save_drawing();
                $output = ob_get_clean();
                
                echo "✅ AJAX handler executed<br>";
                echo "<h4>Output:</h4>";
                echo "<pre>" . htmlspecialchars($output) . "</pre>";
                
            } catch (Exception $e) {
                echo "❌ Exception in AJAX handler: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "❌ Nonce verification failed<br>";
        }
    }
} else {
    echo "⚠️ Cannot test AJAX handler - prerequisites not met<br>";
}

// Step 6: Check current drawings
echo "<h2>📊 Step 6: Current Drawings</h2>";
if ($table_exists) {
    $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10", ARRAY_A);
    
    if (count($recent_drawings) > 0) {
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
                    if (json_last_error() === JSON_ERROR_NONE) {
                        echo "<td>✅ Valid JSON (" . strlen($drawing['drawing_data']) . " chars)</td>";
                    } else {
                        echo "<td>⚠️ Invalid JSON</td>";
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
    } else {
        echo "<p>No drawings found in database.</p>";
    }
} else {
    echo "⚠️ Cannot check drawings - table does not exist<br>";
}

// Summary
echo "<h2>📋 Summary</h2>";
$overall_status = $functions_exist && $table_exists && is_writable($quotes_dir);

echo "<div style='background: " . ($overall_status ? '#d4edda' : '#f8d7da') . "; border: 1px solid " . ($overall_status ? '#c3e6cb' : '#f5c6cb') . "; padding: 15px; border-radius: 5px;'>";
echo "<h3 style='color: " . ($overall_status ? '#155724' : '#721c24') . ";'>";
echo ($overall_status ? "✅ System Ready" : "⚠️ System Issues Found");
echo "</h3>";

if ($overall_status) {
    echo "<p>Your save functionality should be working. The issue might be in the JavaScript or canvas initialization.</p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to the calculator page</li>";
    echo "<li>Open browser console (F12)</li>";
    echo "<li>Try to save a drawing</li>";
    echo "<li>Check console for error messages</li>";
    echo "</ol>";
} else {
    echo "<p>There are system issues that need to be resolved:</p>";
    if (!$functions_exist) echo "<li>Required functions are missing</li>";
    if (!$table_exists) echo "<li>Database table is missing</li>";
    if (!is_writable($quotes_dir)) echo "<li>Quotes directory is not writable</li>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


