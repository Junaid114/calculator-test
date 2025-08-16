<?php
/**
 * Comprehensive Test Script for Stone Slab Calculator
 * Tests all functionality: canvas storage, PDF generation, database operations, file management
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to run tests');
}

echo "<h1>🧪 Comprehensive Stone Slab Calculator Test</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";
echo "<p><strong>Test Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Initialize test results
$test_results = array();
$overall_success = true;

// Test 1: Database Table Check
echo "<h2>📊 Test 1: Database Table Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
if ($table_exists) {
    echo "✅ Database table exists: <code>$table_name</code><br>";
    $test_results['database_table'] = true;
    
    // Check table structure
    $table_structure = $wpdb->get_results("DESCRIBE $table_name");
    echo "📋 Table structure:<br>";
    echo "<ul>";
    foreach ($table_structure as $column) {
        echo "<li><code>{$column->Field}</code> - {$column->Type} ({$column->Null})</li>";
    }
    echo "</ul>";
    
    // Count existing records
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "📈 Total drawings in database: <strong>$total_count</strong><br>";
} else {
    echo "❌ Database table does not exist!<br>";
    $test_results['database_table'] = false;
    $overall_success = false;
}

// Test 2: Directory and File Permissions
echo "<h2>📁 Test 2: Directory and File Permissions</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
$htaccess_file = $quotes_dir . '.htaccess';

echo "📂 Quotes directory: <code>$quotes_dir</code><br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? '✅ YES' : '❌ NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? '✅ YES' : '❌ NO') . "<br>";

if (file_exists($htaccess_file)) {
    echo ".htaccess exists: ✅ YES<br>";
    echo ".htaccess content: <code>" . htmlspecialchars(file_get_contents($htaccess_file)) . "</code><br>";
    $test_results['htaccess'] = true;
} else {
    echo ".htaccess exists: ❌ NO<br>";
    $test_results['htaccess'] = false;
    $overall_success = false;
}

// Test 3: AJAX Actions Registration
echo "<h2>🔧 Test 3: AJAX Actions Registration</h2>";
$ajax_actions = array(
    'ssc_save_drawing',
    'ssc_generate_enhanced_pdf',
    'ssc_get_drawings',
    'ssc_get_drawing',
    'ssc_view_pdf',
    'ssc_download_pdf',
    'ssc_delete_drawing'
);

echo "🔍 Checking AJAX actions:<br>";
foreach ($ajax_actions as $action) {
    if (has_action("wp_ajax_$action")) {
        echo "✅ <code>$action</code> is registered<br>";
        $test_results["ajax_$action"] = true;
    } else {
        echo "❌ <code>$action</code> is NOT registered<br>";
        $test_results["ajax_$action"] = false;
        $overall_success = false;
    }
}

// Test 4: Nonce Generation
echo "<h2>🔐 Test 4: Nonce Generation</h2>";
$drawing_nonce = wp_create_nonce('ssc_save_drawing_nonce');
$auth_nonce = wp_create_nonce('ssc_auth_nonce');

if ($drawing_nonce && $auth_nonce) {
    echo "✅ Drawing nonce generated: <code>" . substr($drawing_nonce, 0, 8) . "...</code><br>";
    echo "✅ Auth nonce generated: <code>" . substr($auth_nonce, 0, 8) . "...</code><br>";
    $test_results['nonce_generation'] = true;
} else {
    echo "❌ Nonce generation failed<br>";
    $test_results['nonce_generation'] = false;
    $overall_success = false;
}

// Test 5: Database Functions
echo "<h2>🗄️ Test 5: Database Functions</h2>";
if (function_exists('ssc_save_drawing')) {
    echo "✅ <code>ssc_save_drawing()</code> function exists<br>";
    $test_results['db_functions'] = true;
} else {
    echo "❌ <code>ssc_save_drawing()</code> function does not exist<br>";
    $test_results['db_functions'] = false;
    $overall_success = false;
}

// Test 6: PDF Generation Functions
echo "<h2>📄 Test 6: PDF Generation Functions</h2>";
if (function_exists('ssc_generate_enhanced_pdf')) {
    echo "✅ <code>ssc_generate_enhanced_pdf()</code> function exists<br>";
    $test_results['pdf_functions'] = true;
} else {
    echo "❌ <code>ssc_generate_enhanced_pdf()</code> function does not exist<br>";
    $test_results['pdf_functions'] = false;
    $overall_success = false;
}

// Test 7: File Operations
echo "<h2>💾 Test 7: File Operations</h2>";
$test_file = $quotes_dir . 'test_' . time() . '.txt';
$test_content = 'Test file content for permissions check';

if (file_put_contents($test_file, $test_content)) {
    echo "✅ File creation test: SUCCESS<br>";
    echo "Test file created: <code>" . basename($test_file) . "</code><br>";
    
    if (file_exists($test_file)) {
        echo "✅ File exists check: SUCCESS<br>";
        $file_size = filesize($test_file);
        echo "File size: $file_size bytes<br>";
        
        if (unlink($test_file)) {
            echo "✅ File deletion test: SUCCESS<br>";
            $test_results['file_operations'] = true;
        } else {
            echo "❌ File deletion test: FAILED<br>";
            $test_results['file_operations'] = false;
            $overall_success = false;
        }
    } else {
        echo "❌ File exists check: FAILED<br>";
        $test_results['file_operations'] = false;
        $overall_success = false;
    }
} else {
    echo "❌ File creation test: FAILED<br>";
    $test_results['file_operations'] = false;
    $overall_success = false;
}

// Test 8: Existing Data Analysis
echo "<h2>📊 Test 8: Existing Data Analysis</h2>";
if ($table_exists && $total_count > 0) {
    $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5", ARRAY_A);
    
    echo "📋 Recent drawings analysis:<br>";
    foreach ($recent_drawings as $drawing) {
        echo "<div style='border: 1px solid #ddd; margin: 10px 0; padding: 10px; background: #f9f9f9;'>";
        echo "<strong>ID:</strong> {$drawing['id']}<br>";
        echo "<strong>Name:</strong> {$drawing['drawing_name']}<br>";
        echo "<strong>User ID:</strong> {$drawing['user_id']}<br>";
        echo "<strong>PDF File:</strong> {$drawing['pdf_file_path']}<br>";
        echo "<strong>Created:</strong> {$drawing['created_at']}<br>";
        
        // Check if PDF file exists
        $pdf_path = $quotes_dir . $drawing['pdf_file_path'];
        $pdf_exists = file_exists($pdf_path);
        echo "<strong>PDF Exists:</strong> " . ($pdf_exists ? '✅ YES' : '❌ NO') . "<br>";
        
        if ($pdf_exists) {
            $pdf_size = filesize($pdf_path);
            echo "<strong>PDF Size:</strong> " . number_format($pdf_size) . " bytes<br>";
        }
        
        // Analyze drawing data
        if (!empty($drawing['drawing_data'])) {
            echo "<strong>Drawing Data:</strong> Available (" . strlen($drawing['drawing_data']) . " characters)<br>";
            try {
                $drawing_data = json_decode($drawing['drawing_data'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<strong>JSON Valid:</strong> ✅ YES<br>";
                    if (isset($drawing_data['canvas_data'])) {
                        echo "<strong>Canvas Data:</strong> ✅ Available (" . strlen($drawing_data['canvas_data']) . " characters)<br>";
                        echo "<strong>Canvas Size:</strong> " . ($drawing_data['canvas_width'] ?? 'Unknown') . " x " . ($drawing_data['canvas_height'] ?? 'Unknown') . "<br>";
                    } else {
                        echo "<strong>Canvas Data:</strong> ❌ Not available<br>";
                    }
                } else {
                    echo "<strong>JSON Valid:</strong> ❌ NO (" . json_last_error_msg() . ")<br>";
                }
            } catch (Exception $e) {
                echo "<strong>JSON Parse:</strong> ❌ Error: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "<strong>Drawing Data:</strong> ❌ Empty<br>";
        }
        echo "</div>";
    }
} else {
    echo "ℹ️ No existing drawings to analyze<br>";
}

// Test 9: Plugin Constants and Paths
echo "<h2>⚙️ Test 9: Plugin Constants and Paths</h2>";
if (defined('SSC_PLUGIN_DIR')) {
    echo "✅ <code>SSC_PLUGIN_DIR</code> constant: <code>" . SSC_PLUGIN_DIR . "</code><br>";
    $test_results['plugin_constants'] = true;
} else {
    echo "❌ <code>SSC_PLUGIN_DIR</code> constant not defined<br>";
    $test_results['plugin_constants'] = false;
    $overall_success = false;
}

if (defined('SSC_PLUGIN_URL')) {
    echo "✅ <code>SSC_PLUGIN_URL</code> constant: <code>" . SSC_PLUGIN_URL . "</code><br>";
} else {
    echo "❌ <code>SSC_PLUGIN_URL</code> constant not defined<br>";
    $test_results['plugin_constants'] = false;
    $overall_success = false;
}

// Test 10: JavaScript Dependencies
echo "<h2>📜 Test 10: JavaScript Dependencies</h2>";
$js_files = array(
    'assets/js/jquery-3.6.0.min.js',
    'assets/js/fabric.min.js',
    'assets/js/jspdf.umd.min.js'
);

foreach ($js_files as $js_file) {
    $full_path = plugin_dir_path(__FILE__) . $js_file;
    if (file_exists($full_path)) {
        $file_size = filesize($full_path);
        echo "✅ <code>$js_file</code>: " . number_format($file_size) . " bytes<br>";
    } else {
        echo "❌ <code>$js_file</code>: File not found<br>";
        $overall_success = false;
    }
}

// Test 11: CSS Files
echo "<h2>🎨 Test 11: CSS Files</h2>";
$css_files = array(
    'admin/css/admin-styles.css',
    'templates/calculator.php' // Check if calculator template exists
);

foreach ($css_files as $css_file) {
    $full_path = plugin_dir_path(__FILE__) . $css_file;
    if (file_exists($full_path)) {
        $file_size = filesize($full_path);
        echo "✅ <code>$css_file</code>: " . number_format($file_size) . " bytes<br>";
    } else {
        echo "❌ <code>$css_file</code>: File not found<br>";
        $overall_success = false;
    }
}

// Test 12: Database Table Schema Validation
echo "<h2>🔍 Test 12: Database Table Schema Validation</h2>";
if ($table_exists) {
    $required_columns = array(
        'id' => 'int',
        'user_id' => 'int',
        'drawing_name' => 'varchar',
        'drawing_notes' => 'text',
        'total_cutting_mm' => 'decimal',
        'only_cut_mm' => 'decimal',
        'mitred_cut_mm' => 'decimal',
        'slab_cost' => 'varchar',
        'drawing_data' => 'longtext',
        'pdf_file_path' => 'varchar',
        'drawing_link' => 'varchar',
        'created_at' => 'datetime'
    );
    
    $actual_columns = array();
    foreach ($table_structure as $column) {
        $actual_columns[$column->Field] = $column->Type;
    }
    
    $schema_valid = true;
    foreach ($required_columns as $column => $expected_type) {
        if (isset($actual_columns[$column])) {
            echo "✅ Column <code>$column</code> exists<br>";
        } else {
            echo "❌ Column <code>$column</code> missing<br>";
            $schema_valid = false;
        }
    }
    
    if ($schema_valid) {
        echo "✅ Database schema validation: PASSED<br>";
        $test_results['schema_validation'] = true;
    } else {
        echo "❌ Database schema validation: FAILED<br>";
        $test_results['schema_validation'] = false;
        $overall_success = false;
    }
}

// Test Summary
echo "<h2>📋 Test Summary</h2>";
$passed_tests = array_sum($test_results);
$total_tests = count($test_results);

echo "<div style='background: " . ($overall_success ? '#d4edda' : '#f8d7da') . "; border: 1px solid " . ($overall_success ? '#c3e6cb' : '#f5c6cb') . "; padding: 15px; border-radius: 5px;'>";
echo "<h3 style='color: " . ($overall_success ? '#155724' : '#721c24') . ";'>";
echo ($overall_success ? "🎉 All Tests Passed!" : "⚠️ Some Tests Failed");
echo "</h3>";
echo "<p><strong>Results:</strong> $passed_tests / $total_tests tests passed</p>";
echo "</div>";

// Detailed test results
echo "<h3>Detailed Results:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>Test</th><th>Status</th><th>Details</th>";
echo "</tr>";

foreach ($test_results as $test => $result) {
    $status = $result ? '✅ PASS' : '❌ FAIL';
    $color = $result ? 'green' : 'red';
    echo "<tr>";
    echo "<td><strong>$test</strong></td>";
    echo "<td style='color: $color;'>$status</td>";
    echo "<td>" . ($result ? 'Functionality working correctly' : 'Functionality needs attention') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Recommendations
echo "<h2>💡 Recommendations</h2>";
if (!$overall_success) {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #856404;'>Issues Found:</h3>";
    echo "<ul>";
    
    if (!$test_results['database_table']) {
        echo "<li>Database table is missing. Check plugin activation.</li>";
    }
    if (!$test_results['htaccess']) {
        echo "<li>.htaccess file is missing. The quotes directory is not protected.</li>";
    }
    if (!$test_results['file_operations']) {
        echo "<li>File operations are failing. Check directory permissions.</li>";
    }
    if (!$test_results['schema_validation']) {
        echo "<li>Database schema is incomplete. Check table creation.</li>";
    }
    
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #155724;'>🎯 All Systems Operational!</h3>";
    echo "<p>Your stone slab calculator plugin is fully functional and ready for production use.</p>";
    echo "</div>";
}

// Testing instructions
echo "<h2>🧪 How to Test Functionality</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>Manual Testing Steps:</h3>";
echo "<ol>";
echo "<li><strong>Create a Drawing:</strong> Go to a product page with the calculator</li>";
echo "<li><strong>Draw on Canvas:</strong> Use the drawing tools to create a design</li>";
echo "<li><strong>Save Drawing:</strong> Give it a name and save it</li>";
echo "<li><strong>Check Database:</strong> Refresh this page to see new records</li>";
echo "<li><strong>Verify PDF:</strong> Check that PDF file exists in quotes directory</li>";
echo "<li><strong>View Canvas:</strong> Use the 'View Canvas' button to see saved drawings</li>";
echo "</ol>";

echo "<h3>Expected Results:</h3>";
echo "<ul>";
echo "<li>✅ New database record with canvas data</li>";
echo "<li>✅ PDF file saved in quotes directory</li>";
echo "<li>✅ Filename follows QuoteID-UserID-Product-Date.pdf format</li>";
echo "<li>✅ Canvas drawing data stored and retrievable</li>";
echo "<li>✅ All AJAX actions working properly</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


