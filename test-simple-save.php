<?php
/**
 * Simple Save Test
 * Tests the basic save functionality without the calculator interface
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to test save functionality');
}

echo "<h1>🧪 Simple Save Test</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Check if the save function exists
echo "<h2>🔧 Function Check</h2>";
if (function_exists('ssc_ajax_save_drawing')) {
    echo "✅ <code>ssc_ajax_save_drawing()</code> function exists<br>";
} else {
    echo "❌ <code>ssc_ajax_save_drawing()</code> function does NOT exist<br>";
}

// Check database table
echo "<h2>🗄️ Database Check</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
if ($table_exists) {
    echo "✅ Database table exists<br>";
    
    // Count existing drawings
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total drawings: $total_count<br>";
} else {
    echo "❌ Database table does not exist<br>";
}

// Check quotes directory
echo "<h2>📁 Directory Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

// Create test form
echo "<h2>🧪 Test Save Form</h2>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='hidden' name='action' value='test_save'>";
echo "<input type='hidden' name='nonce' value='" . wp_create_nonce('ssc_save_drawing_nonce') . "'>";
echo "<input type='hidden' name='user_id' value='" . get_current_user_id() . "'>";
echo "<input type='hidden' name='drawing_name' value='Test Drawing " . date('Y-m-d H:i:s') . "'>";
echo "<input type='hidden' name='drawing_notes' value='Test notes'>";
echo "<input type='hidden' name='total_cutting_mm' value='1000'>";
echo "<input type='hidden' name='only_cut_mm' value='800'>";
echo "<input type='hidden' name='mitred_cut_mm' value='200'>";
echo "<input type='hidden' name='slab_cost' value='$1500'>";
echo "<input type='hidden' name='drawing_link' value='" . site_url() . "'>";

// Create a simple test PDF
$test_pdf_content = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Test PDF) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000204 00000 n \ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n297\n%%EOF\n";

$test_drawing_data = json_encode([
    'name' => 'Test Drawing ' . date('Y-m-d H:i:s'),
    'notes' => 'Test notes',
    'total_cutting_mm' => 1000,
    'only_cut_mm' => 800,
    'mitred_cut_mm' => 200,
    'slab_cost' => 1500,
    'created_at' => date('Y-m-d H:i:s'),
    'canvas_data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
    'canvas_width' => 800,
    'canvas_height' => 600
]);

echo "<input type='hidden' name='drawing_data' value='" . htmlspecialchars($test_drawing_data) . "'>";

echo "<button type='submit'>Test Save Drawing</button>";
echo "</form>";

// Handle test save
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'test_save') {
    echo "<h2>🧪 Test Results</h2>";
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        echo "❌ Nonce verification failed<br>";
    } else {
        echo "✅ Nonce verification passed<br>";
        
        // Create test PDF file
        $test_filename = 'test_' . time() . '.pdf';
        $test_file_path = $quotes_dir . $test_filename;
        
        if (file_put_contents($test_file_path, $test_pdf_content)) {
            echo "✅ Test PDF file created: $test_filename<br>";
            
            // Prepare data for save function
            $test_data = [
                'user_id' => $_POST['user_id'],
                'drawing_name' => $_POST['drawing_name'],
                'drawing_notes' => $_POST['drawing_notes'],
                'total_cutting_mm' => $_POST['total_cutting_mm'],
                'only_cut_mm' => $_POST['only_cut_mm'],
                'mitred_cut_mm' => $_POST['mitred_cut_mm'],
                'slab_cost' => $_POST['slab_cost'],
                'drawing_link' => $_POST['drawing_link'],
                'drawing_data' => $_POST['drawing_data'],
                'pdf_file_path' => $test_filename
            ];
            
            echo "<h3>Test Data:</h3>";
            echo "<pre>" . print_r($test_data, true) . "</pre>";
            
            // Try to save to database
            try {
                $result = ssc_save_drawing($test_data);
                if ($result) {
                    echo "✅ Drawing saved to database with ID: $result<br>";
                } else {
                    echo "❌ Failed to save drawing to database<br>";
                }
            } catch (Exception $e) {
                echo "❌ Error saving drawing: " . $e->getMessage() . "<br>";
            }
            
            // Check if file was created
            if (file_exists($test_file_path)) {
                echo "✅ PDF file exists on disk<br>";
                echo "File size: " . number_format(filesize($test_file_path)) . " bytes<br>";
            } else {
                echo "❌ PDF file was not created<br>";
            }
            
        } else {
            echo "❌ Failed to create test PDF file<br>";
        }
    }
}

// Show current drawings
if ($table_exists) {
    echo "<h2>📊 Current Drawings</h2>";
    $recent_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5", ARRAY_A);
    
    if (count($recent_drawings) > 0) {
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
    } else {
        echo "<p>No drawings found in database.</p>";
    }
}

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


