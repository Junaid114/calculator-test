<?php
/**
 * Fix Database Table Structure
 * This script manually updates the drawing_link column to text type
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Fixing Database Table Structure</h1>";
echo "<p>Updating the drawing_link column to handle longer URLs...</p>";

// Check if the function exists
if (function_exists('ssc_update_table_structure')) {
    echo "<p>✅ Function found, updating table structure...</p>";
    
    $result = ssc_update_table_structure();
    
    if ($result) {
        echo "<p>✅ Table structure updated successfully!</p>";
    } else {
        echo "<p>❌ Failed to update table structure</p>";
    }
} else {
    echo "<p>❌ Function not found</p>";
}

// Also try to manually update the table
echo "<h2>Manual Table Update</h2>";
global $wpdb;
$table_name = $wpdb->prefix . 'ssc_drawings';

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
echo "<p>Table exists: <strong>" . ($table_exists ? '✅ YES' : '❌ NO') . "</strong></p>";

if ($table_exists) {
    // Check current column structure
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
    echo "<p>Current table structure:</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        $highlight = '';
        if ($column->Field === 'drawing_link' && strpos($column->Type, 'varchar') !== false) {
            $highlight = ' style="background-color: #ffeb3b;"';
        }
        echo "<tr{$highlight}>";
        echo "<td>{$column->Field}</td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Key}</td>";
        echo "<td>{$column->Default}</td>";
        echo "<td>{$column->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Try to update the drawing_link column
    echo "<p>Attempting to update drawing_link column...</p>";
    $result = $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN drawing_link text NOT NULL");
    
    if ($result !== false) {
        echo "<p>✅ Column updated successfully!</p>";
        
        // Check the new structure
        $columns_after = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
        echo "<p>Updated table structure:</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns_after as $column) {
            $highlight = '';
            if ($column->Field === 'drawing_link') {
                $highlight = ' style="background-color: #4caf50;"';
            }
            echo "<tr{$highlight}>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>{$column->Key}</td>";
            echo "<td>{$column->Default}</td>";
            echo "<td>{$column->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Failed to update column: " . $wpdb->last_error . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Fix completed!</strong></p>";
echo "<p><a href='javascript:location.reload()'>🔄 Refresh</a></p>";
?>
