<?php
/**
 * Database and PDF Storage Check
 * This script verifies that PDFs are being saved and recorded in the database
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Please log in to check database records');
}

echo "<h1>Database and PDF Storage Check</h1>";

// Get current user info
$current_user = wp_get_current_user();
echo "<h2>Current User</h2>";
echo "User ID: " . $current_user->ID . "<br>";
echo "Username: " . $current_user->user_login . "<br>";
echo "Display Name: " . $current_user->display_name . "<br>";

// Check database table
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

echo "<h2>Database Table Check</h2>";
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
echo "Table exists: " . ($table_exists ? 'YES' : 'NO') . "<br>";

if ($table_exists) {
    // Get total count
    $total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Total drawings in database: $total_count<br>";
    
    // Get user's drawings
    $user_drawings = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
        $current_user->ID
    ), ARRAY_A);
    
    echo "<h3>Your Drawings (User ID: {$current_user->ID})</h3>";
    if (empty($user_drawings)) {
        echo "<p>No drawings found for your user account.</p>";
        echo "<p><strong>To test:</strong> Use the calculator to create and save a drawing.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Drawing Name</th><th>PDF File Path</th><th>Created</th><th>File Exists</th>";
        echo "</tr>";
        
        foreach ($user_drawings as $drawing) {
            $pdf_path = plugin_dir_path(__FILE__) . 'quotes/' . $drawing['pdf_file_path'];
            $file_exists = file_exists($pdf_path) ? 'YES' : 'NO';
            $file_size = file_exists($pdf_path) ? number_format(filesize($pdf_path)) . ' bytes' : 'N/A';
            
            echo "<tr>";
            echo "<td>{$drawing['id']}</td>";
            echo "<td>{$drawing['drawing_name']}</td>";
            echo "<td>{$drawing['pdf_file_path']}</td>";
            echo "<td>{$drawing['created_at']}</td>";
            echo "<td style='color: " . ($file_exists ? 'green' : 'red') . ";'>$file_exists ($file_size)</td>";
            echo "</tr>";
            
            // Show canvas data if available
            if (!empty($drawing['drawing_data'])) {
                try {
                    $drawing_data = json_decode($drawing['drawing_data'], true);
                    if (isset($drawing_data['canvas_data'])) {
                        echo "<tr><td colspan='5' style='background: #f9f9f9; padding: 10px;'>";
                        echo "<strong>Canvas Data:</strong> Available (" . strlen($drawing_data['canvas_data']) . " characters)<br>";
                        echo "<strong>Canvas Size:</strong> " . ($drawing_data['canvas_width'] ?? 'Unknown') . " x " . ($drawing_data['canvas_height'] ?? 'Unknown') . "<br>";
                        echo "<strong>Notes:</strong> " . ($drawing_data['notes'] ?? 'None') . "<br>";
                        echo "<strong>Total Cutting:</strong> " . ($drawing_data['total_cutting_mm'] ?? '0') . " mm<br>";
                        echo "<strong>Standard Cut:</strong> " . ($drawing_data['only_cut_mm'] ?? '0') . " mm<br>";
                        echo "<strong>Mitred Cut:</strong> " . ($drawing_data['mitred_cut_mm'] ?? '0') . " mm<br>";
                        echo "<strong>Slab Cost:</strong> " . ($drawing_data['slab_cost'] ?? '$0') . "<br>";
                        echo "</td></tr>";
                    } else {
                        echo "<tr><td colspan='5' style='background: #f9f9f9; padding: 10px;'>";
                        echo "<strong>Canvas Data:</strong> Not available<br>";
                        echo "<strong>Drawing Data:</strong> " . substr($drawing['drawing_data'], 0, 100) . "...<br>";
                        echo "</td></tr>";
                    }
                } catch (Exception $e) {
                    echo "<tr><td colspan='5' style='background: #f9f9f9; padding: 10px;'>";
                    echo "<strong>Canvas Data:</strong> Error parsing: " . $e->getMessage() . "<br>";
                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='background: #f9f9f9; padding: 10px;'>";
                echo "<strong>Canvas Data:</strong> No drawing data stored<br>";
                echo "</td></tr>";
            }
        }
        echo "</table>";
    }
    
    // Show all drawings (admin view)
    if (current_user_can('manage_options')) {
        echo "<h3>All Drawings (Admin View)</h3>";
        $all_drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 20", ARRAY_A);
        
        if ($all_drawings) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>ID</th><th>User ID</th><th>Drawing Name</th><th>PDF File Path</th><th>Created</th><th>File Exists</th>";
            echo "</tr>";
            
            foreach ($all_drawings as $drawing) {
                $pdf_path = plugin_dir_path(__FILE__) . 'quotes/' . $drawing['pdf_file_path'];
                $file_exists = file_exists($pdf_path) ? 'YES' : 'NO';
                $file_size = file_exists($pdf_path) ? number_format(filesize($pdf_path)) . ' bytes' : 'N/A';
                
                echo "<tr>";
                echo "<td>{$drawing['id']}</td>";
                echo "<td>{$drawing['user_id']}</td>";
                echo "<td>{$drawing['drawing_name']}</td>";
                echo "<td>{$drawing['pdf_file_path']}</td>";
                echo "<td>{$drawing['created_at']}</td>";
                echo "<td style='color: " . ($file_exists ? 'green' : 'red') . ";'>$file_exists ($file_size)</td>";
                echo "</tr>";
                
                // Show canvas data if available
                if (!empty($drawing['drawing_data'])) {
                    try {
                        $drawing_data = json_decode($drawing['drawing_data'], true);
                        if (isset($drawing_data['canvas_data'])) {
                            echo "<tr><td colspan='6' style='background: #f9f9f9; padding: 10px;'>";
                            echo "<strong>Canvas Data:</strong> Available (" . strlen($drawing_data['canvas_data']) . " characters)<br>";
                            echo "<strong>Canvas Size:</strong> " . ($drawing_data['canvas_width'] ?? 'Unknown') . " x " . ($drawing_data['canvas_height'] ?? 'Unknown') . "<br>";
                            echo "<strong>Notes:</strong> " . ($drawing_data['notes'] ?? 'None') . "<br>";
                            echo "<strong>Total Cutting:</strong> " . ($drawing_data['total_cutting_mm'] ?? '0') . " mm<br>";
                            echo "<strong>Standard Cut:</strong> " . ($drawing_data['only_cut_mm'] ?? '0') . " mm<br>";
                            echo "<strong>Mitred Cut:</strong> " . ($drawing_data['mitred_cut_mm'] ?? '0') . " mm<br>";
                            echo "<strong>Slab Cost:</strong> " . ($drawing_data['slab_cost'] ?? '$0') . "<br>";
                            echo "</td></tr>";
                        } else {
                            echo "<tr><td colspan='6' style='background: #f9f9f9; padding: 10px;'>";
                            echo "<strong>Canvas Data:</strong> Not available<br>";
                            echo "<strong>Drawing Data:</strong> " . substr($drawing['drawing_data'], 0, 100) . "...<br>";
                            echo "</td></tr>";
                        }
                    } catch (Exception $e) {
                        echo "<tr><td colspan='6' style='background: #f9f9f9; padding: 10px;'>";
                        echo "<strong>Canvas Data:</strong> Error parsing: " . $e->getMessage() . "<br>";
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='background: #f9f9f9; padding: 10px;'>";
                    echo "<strong>Canvas Data:</strong> No drawing data stored<br>";
                    echo "</td></tr>";
                }
            }
            echo "</table>";
        }
    }
} else {
    echo "<p style='color: red;'>Database table does not exist!</p>";
}

// Check quotes directory
echo "<h2>Quotes Directory Check</h2>";
$quotes_dir = plugin_dir_path(__FILE__) . 'quotes/';
echo "Quotes directory: $quotes_dir<br>";
echo "Directory exists: " . (file_exists($quotes_dir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($quotes_dir) ? 'YES' : 'NO') . "<br>";

// List PDF files
$pdf_files = glob($quotes_dir . '*.pdf');
echo "<h3>PDF Files in Quotes Directory</h3>";
if (empty($pdf_files)) {
    echo "<p>No PDF files found in quotes directory.</p>";
} else {
    echo "<p>Found " . count($pdf_files) . " PDF file(s):</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Filename</th><th>Size</th><th>Modified</th><th>Format Check</th>";
    echo "</tr>";
    
    foreach ($pdf_files as $pdf) {
        $filename = basename($pdf);
        $filesize = filesize($pdf);
        $filetime = date('Y-m-d H:i:s', filemtime($pdf));
        
        // Check filename format - allow hyphens in product name
        if (preg_match('/^(\d+)-(\d+)-(.+)-(\d{4}-\d{2}-\d{2})\.pdf$/', $filename, $matches)) {
            $format_check = "✓ QuoteID($matches[1])-UserID($matches[2])-Product($matches[3])-Date($matches[4])";
            $format_color = "green";
        } else {
            $format_check = "✗ Does not match expected format";
            $format_color = "red";
        }
        
        echo "<tr>";
        echo "<td>$filename</td>";
        echo "<td>" . number_format($filesize) . " bytes</td>";
        echo "<td>$filetime</td>";
        echo "<td style='color: $format_color;'>$format_check</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h2>Testing Instructions</h2>";
echo "<ol>";
echo "<li><strong>Create a Drawing:</strong> Go to a product page and use the calculator</li>";
echo "<li><strong>Save the Drawing:</strong> Give it a name and save it</li>";
echo "<li><strong>Check Database:</strong> Refresh this page to see new records</li>";
echo "<li><strong>Verify PDF:</strong> Check that PDF file exists in quotes directory</li>";
echo "<li><strong>Check Format:</strong> Ensure filename follows QuoteID-UserID-Product-Date.pdf</li>";
echo "</ol>";

echo "<p><strong>Expected Result:</strong> After saving a drawing, you should see:</p>";
echo "<ul>";
echo "<li>New record in database with PDF file path</li>";
echo "<li>PDF file in quotes directory</li>";
echo "<li>Filename matching the required format</li>";
echo "</ul>";

echo "<p><strong>Note:</strong> This script should be removed in production.</p>";
?>

