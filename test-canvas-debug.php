<?php
/**
 * Canvas Debug Script
 * Tests the canvas functionality and identifies issues
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to debug canvas functionality');
}

echo "<h1>🎨 Canvas Debug Script</h1>";
echo "<p><strong>Current User:</strong> " . wp_get_current_user()->display_name . " (ID: " . wp_get_current_user()->ID . ")</p>";

// Check URL parameters
echo "<h2>🔗 URL Parameters Check</h2>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

$required_params = array('slab_width', 'slab_height', 'pad_height');
echo "<h3>Required Parameters:</h3>";
foreach ($required_params as $param) {
    if (isset($_GET[$param])) {
        echo "✅ <code>$param</code>: " . htmlspecialchars($_GET[$param]) . "<br>";
    } else {
        echo "❌ <code>$param</code>: NOT SET<br>";
    }
}

// Check if we're on the right page
echo "<h2>📄 Page Check</h2>";
$current_page = get_permalink();
echo "Current page: $current_page<br>";

// Check if calculator shortcode is present
echo "<h2>🔧 Calculator Shortcode Check</h2>";
if (has_shortcode(get_the_content(), 'stone_slab_calculator')) {
    echo "✅ Stone slab calculator shortcode is present<br>";
} else {
    echo "❌ Stone slab calculator shortcode is NOT present<br>";
}

// Check JavaScript dependencies
echo "<h2>📜 JavaScript Dependencies Check</h2>";
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
    }
}

// Check canvas element
echo "<h2>🎯 Canvas Element Check</h2>";
echo "<p>Looking for canvas element with id='canvas'...</p>";

// Check if we're in the right context
echo "<h2>🔍 Context Check</h2>";
if (is_page() || is_single()) {
    echo "✅ We are on a page/post<br>";
    echo "Page/Post ID: " . get_the_ID() . "<br>";
    echo "Page/Post Title: " . get_the_title() . "<br>";
} else {
    echo "❌ We are not on a page/post<br>";
}

// Test canvas creation with sample parameters
echo "<h2>🧪 Test Canvas Creation</h2>";
echo "<p>Testing canvas creation with sample parameters...</p>";

// Create test parameters
$test_width = 1000; // 1000mm
$test_height = 800;  // 800mm
$test_pad_height = 600; // 600mm

echo "<p><strong>Test Parameters:</strong></p>";
echo "<ul>";
echo "<li>slab_width: {$test_width}mm</li>";
echo "<li>slab_height: {$test_height}mm</li>";
echo "<li>pad_height: {$test_pad_height}mm</li>";
echo "</ul>";

// Calculate expected canvas dimensions
$canvas_width_px = $test_width / 8; // convertMmToPx function
$canvas_height_px = $test_pad_height / 8;

echo "<p><strong>Expected Canvas Dimensions:</strong></p>";
echo "<ul>";
echo "<li>Width: {$canvas_width_px}px</li>";
echo "<li>Height: {$canvas_height_px}px</li>";
echo "</ul>";

// Create test canvas
echo "<h3>Test Canvas:</h3>";
echo "<canvas id='test-canvas' width='{$canvas_width_px}' height='{$canvas_height_px}' style='border: 2px solid #333; background: #f0f0f0;'></canvas>";

// Add some test content to canvas
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    const testCanvas = document.getElementById('test-canvas');
    if (testCanvas) {
        const ctx = testCanvas.getContext('2d');
        
        // Draw a test rectangle
        ctx.fillStyle = '#ff0000';
        ctx.fillRect(50, 50, 100, 100);
        
        // Draw some text
        ctx.fillStyle = '#000000';
        ctx.font = '16px Arial';
        ctx.fillText('Test Canvas Working!', 50, 200);
        
        console.log('Test canvas created successfully');
    } else {
        console.error('Test canvas not found');
    }
});
</script>";

// Check for JavaScript errors
echo "<h2>🐛 JavaScript Error Check</h2>";
echo "<p>Open browser console (F12) and check for any JavaScript errors.</p>";
echo "<p>Common issues:</p>";
echo "<ul>";
echo "<li>Canvas element not found</li>";
echo "<li>Fabric.js not loaded</li>";
echo "<li>Invalid canvas dimensions (NaN or 0)</li>";
echo "<li>URL parameters missing</li>";
echo "</ul>";

// Instructions for testing
echo "<h2>🧪 Testing Instructions</h2>";
echo "<div style='background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px;'>";
echo "<h3>Steps to Fix Canvas:</h3>";
echo "<ol>";
echo "<li><strong>Check URL:</strong> Ensure you're on a page with ?slab_width=X&slab_height=Y&pad_height=Z</li>";
echo "<li><strong>Check Console:</strong> Open F12 and look for JavaScript errors</li>";
echo "<li><strong>Check Parameters:</strong> Verify all required parameters are set</li>";
echo "<li><strong>Test Drawing:</strong> Try to draw on the canvas</li>";
echo "<li><strong>Check Elements:</strong> Verify canvas element exists in DOM</li>";
echo "</ol>";

echo "<h3>Expected Results:</h3>";
echo "<ul>";
echo "<li>✅ Canvas element visible on page</li>";
echo "<li>✅ No JavaScript errors in console</li>";
echo "<li>✅ Drawing tools respond to clicks</li>";
echo "<li>✅ Shapes can be added to canvas</li>";
echo "<li>✅ Canvas dimensions are valid numbers</li>";
echo "</ul>";
echo "</div>";

// Current status
echo "<h2>📊 Current Status</h2>";
$has_params = isset($_GET['slab_width']) && isset($_GET['slab_height']) && isset($_GET['pad_height']);
$status_ok = $has_params;

echo "<div style='background: " . ($status_ok ? '#d4edda' : '#f8d7da') . "; border: 1px solid " . ($status_ok ? '#c3e6cb' : '#f5c6cb') . "; padding: 15px; border-radius: 5px;'>";
echo "<h3 style='color: " . ($status_ok ? '#155724' : '#721c24') . ";'>";
echo ($status_ok ? "✅ Canvas Ready for Testing" : "⚠️ Canvas Issues Found");
echo "</h3>";

if ($status_ok) {
    echo "<p>Your canvas should be working. Check the browser console for any JavaScript errors.</p>";
} else {
    echo "<p>Canvas issues detected:</p>";
    if (!isset($_GET['slab_width'])) echo "<li>Missing slab_width parameter</li>";
    if (!isset($_GET['slab_height'])) echo "<li>Missing slab_height parameter</li>";
    if (!isset($_GET['pad_height'])) echo "<li>Missing pad_height parameter</li>";
    echo "<p><strong>Solution:</strong> Navigate to a page with the correct URL parameters.</p>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Note:</strong> This debug script should be removed in production.</p>";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>";
?>

