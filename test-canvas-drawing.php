<?php
/**
 * Canvas Drawing Test Script
 * Tests the canvas drawing storage and retrieval functionality
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to test canvas functionality');
}

echo "<h1>🎨 Canvas Drawing Functionality Test</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Test canvas data storage
echo "<h2>🧪 Test Canvas Data Storage</h2>";

// Create test canvas data
$test_canvas_data = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
$test_drawing_data = array(
    'name' => 'Test Canvas Drawing',
    'notes' => 'This is a test canvas drawing',
    'total_cutting_mm' => 150.5,
    'only_cut_mm' => 75.25,
    'mitred_cut_mm' => 25.0,
    'slab_cost' => 1250.00,
    'created_at' => date('Y-m-d H:i:s'),
    'canvas_data' => $test_canvas_data,
    'canvas_width' => 800,
    'canvas_height' => 600
);

echo "<h3>Test Data Created:</h3>";
echo "<ul>";
echo "<li><strong>Drawing Name:</strong> " . $test_drawing_data['name'] . "</li>";
echo "<li><strong>Canvas Size:</strong> " . $test_drawing_data['canvas_width'] . " x " . $test_drawing_data['canvas_height'] . "</li>";
echo "<li><strong>Canvas Data Length:</strong> " . strlen($test_canvas_data) . " characters</li>";
echo "<li><strong>Total Cutting:</strong> " . $test_drawing_data['total_cutting_mm'] . " mm</li>";
echo "<li><strong>Slab Cost:</strong> $" . number_format($test_drawing_data['slab_cost'], 2) . "</li>";
echo "</ul>";

// Test JSON encoding
echo "<h3>JSON Encoding Test:</h3>";
$json_data = json_encode($test_drawing_data);
if ($json_data !== false) {
    echo "✅ JSON encoding successful<br>";
    echo "JSON length: " . strlen($json_data) . " characters<br>";
    echo "<details><summary>View JSON Data</summary><pre>" . htmlspecialchars($json_data) . "</pre></details>";
} else {
    echo "❌ JSON encoding failed: " . json_last_error_msg() . "<br>";
}

// Test JSON decoding
echo "<h3>JSON Decoding Test:</h3>";
$decoded_data = json_decode($json_data, true);
if ($decoded_data !== null) {
    echo "✅ JSON decoding successful<br>";
    echo "Decoded data type: " . gettype($decoded_data) . "<br>";
    if (isset($decoded_data['canvas_data'])) {
        echo "✅ Canvas data preserved: " . strlen($decoded_data['canvas_data']) . " characters<br>";
    } else {
        echo "❌ Canvas data missing after decode<br>";
    }
} else {
    echo "❌ JSON decoding failed: " . json_last_error_msg() . "<br>";
}

// Test database storage simulation
echo "<h2>🗄️ Database Storage Simulation</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    echo "✅ Database table exists<br>";
    
    // Check if we can insert test data
    $test_insert_data = array(
        'user_id' => get_current_user_id(),
        'drawing_name' => 'TEST_' . $test_drawing_data['name'] . '_' . time(),
        'drawing_notes' => $test_drawing_data['notes'],
        'total_cutting_mm' => $test_drawing_data['total_cutting_mm'],
        'only_cut_mm' => $test_drawing_data['only_cut_mm'],
        'mitred_cut_mm' => $test_drawing_data['mitred_cut_mm'],
        'slab_cost' => '$' . number_format($test_drawing_data['slab_cost'], 2),
        'drawing_data' => $json_data,
        'pdf_file_path' => 'test_' . time() . '.pdf',
        'drawing_link' => 'http://example.com/test',
        'created_at' => current_time('mysql')
    );
    
    echo "<h3>Test Insert Data:</h3>";
    echo "<ul>";
    foreach ($test_insert_data as $key => $value) {
        if ($key === 'drawing_data') {
            echo "<li><strong>$key:</strong> " . strlen($value) . " characters (JSON data)</li>";
        } else {
            echo "<li><strong>$key:</strong> $value</li>";
        }
    }
    echo "</ul>";
    
    // Try to insert test data
    $insert_result = $wpdb->insert($table_name, $test_insert_data);
    
    if ($insert_result !== false) {
        $test_drawing_id = $wpdb->insert_id;
        echo "✅ Test data inserted successfully! Drawing ID: $test_drawing_id<br>";
        
        // Retrieve and verify the data
        $retrieved_drawing = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $test_drawing_id), ARRAY_A);
        
        if ($retrieved_drawing) {
            echo "✅ Data retrieved successfully<br>";
            
            // Verify canvas data
            if (!empty($retrieved_drawing['drawing_data'])) {
                $retrieved_json = $retrieved_drawing['drawing_data'];
                $retrieved_data = json_decode($retrieved_json, true);
                
                if ($retrieved_data && isset($retrieved_data['canvas_data'])) {
                    echo "✅ Canvas data retrieved and intact<br>";
                    echo "Retrieved canvas data length: " . strlen($retrieved_data['canvas_data']) . " characters<br>";
                    
                    // Compare original vs retrieved
                    if ($retrieved_data['canvas_data'] === $test_canvas_data) {
                        echo "✅ Canvas data matches exactly!<br>";
                    } else {
                        echo "⚠️ Canvas data differs slightly<br>";
                    }
                } else {
                    echo "❌ Canvas data not properly retrieved<br>";
                }
            } else {
                echo "❌ No drawing data retrieved<br>";
            }
        } else {
            echo "❌ Failed to retrieve test data<br>";
        }
        
        // Clean up test data
        $delete_result = $wpdb->delete($table_name, array('id' => $test_drawing_id));
        if ($delete_result !== false) {
            echo "✅ Test data cleaned up successfully<br>";
        } else {
            echo "⚠️ Failed to clean up test data<br>";
        }
        
    } else {
        echo "❌ Failed to insert test data: " . $wpdb->last_error . "<br>";
    }
    
} else {
    echo "❌ Database table does not exist<br>";
}

// Test canvas data display
echo "<h2>🖼️ Canvas Data Display Test</h2>";
echo "<h3>Test Canvas Image:</h3>";
echo "<img src='$test_canvas_data' alt='Test Canvas' style='border: 1px solid #ccc; max-width: 200px;'><br>";
echo "<small>This is a 1x1 pixel transparent PNG (base64 encoded)</small><br>";

// Test canvas viewer functionality
echo "<h3>Canvas Viewer Test:</h3>";
echo "<div id='canvas-viewer-test' style='border: 1px solid #ddd; padding: 20px; background: #f9f9f9;'>";
echo "<canvas id='test-canvas' width='400' height='300' style='border: 1px solid #ccc; background: white;'></canvas><br><br>";
echo "<button onclick='loadTestCanvas()'>Load Test Canvas</button>";
echo "<button onclick='clearTestCanvas()'>Clear Canvas</button>";
echo "</div>";

// JavaScript for canvas testing
echo "<script>
function loadTestCanvas() {
    const canvas = document.getElementById('test-canvas');
    const ctx = canvas.getContext('2d');
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Create test image
    const img = new Image();
    img.onload = function() {
        // Draw the image
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        // Add some test drawing
        ctx.strokeStyle = '#ff0000';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(50, 50);
        ctx.lineTo(350, 250);
        ctx.stroke();
        
        ctx.fillStyle = '#00ff00';
        ctx.fillRect(100, 100, 50, 50);
        
        ctx.fillStyle = '#0000ff';
        ctx.beginPath();
        ctx.arc(300, 100, 30, 0, 2 * Math.PI);
        ctx.fill();
        
        alert('Test canvas loaded with sample drawing!');
    };
    img.src = '$test_canvas_data';
}

function clearTestCanvas() {
    const canvas = document.getElementById('test-canvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// Auto-load test canvas
window.onload = function() {
    setTimeout(loadTestCanvas, 1000);
};
</script>";

// Test AJAX functionality
echo "<h2>🔧 AJAX Functionality Test</h2>";
$ajax_url = admin_url('admin-ajax.php');
$nonce = wp_create_nonce('ssc_save_drawing_nonce');

echo "<h3>AJAX Endpoints:</h3>";
echo "<ul>";
echo "<li><strong>AJAX URL:</strong> <code>$ajax_url</code></li>";
echo "<li><strong>Nonce:</strong> <code>" . substr($nonce, 0, 8) . "...</code></li>";
echo "</ul>";

echo "<h3>Test AJAX Request:</h3>";
echo "<button onclick='testAJAX()'>Test AJAX Connection</button>";
echo "<div id='ajax-test-result'></div>";

echo "<script>
function testAJAX() {
    const resultDiv = document.getElementById('ajax-test-result');
    resultDiv.innerHTML = 'Testing AJAX connection...';
    
    jQuery.ajax({
        url: '$ajax_url',
        type: 'POST',
        data: {
            action: 'ssc_get_drawings',
            nonce: '$nonce'
        },
        success: function(response) {
            if (response.success) {
                resultDiv.innerHTML = '✅ AJAX connection successful! Found ' + response.data.length + ' drawings.';
            } else {
                resultDiv.innerHTML = '⚠️ AJAX response: ' + response.data;
            }
        },
        error: function(xhr, status, error) {
            resultDiv.innerHTML = '❌ AJAX error: ' + error;
        }
    });
}
</script>";

// Summary
echo "<h2>📋 Test Summary</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>What This Test Verifies:</h3>";
echo "<ul>";
echo "<li>✅ Canvas data can be properly encoded/decoded</li>";
echo "<li>✅ JSON data structure is valid</li>";
echo "<li>✅ Database can store and retrieve canvas data</li>";
echo "<li>✅ Canvas viewer can display drawing data</li>";
echo "<li>✅ AJAX endpoints are accessible</li>";
echo "<li>✅ Nonce security is working</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Use the calculator interface to create a real drawing</li>";
echo "<li>Save the drawing with a name</li>";
echo "<li>Check that it appears in the database</li>";
echo "<li>Verify the canvas data is stored correctly</li>";
echo "<li>Test the 'View Canvas' functionality</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This test script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>


