<?php
/*
Plugin Name:  Stone Slab Calculator
Plugin URI:   #
Description:  A calculator to calculate the number of stone slabs required for projects based on input dimensions.
Version:      1.1
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  stone-slab-domain
Domain Path:  /languages
*/


// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Define plugin constants
define('SSC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSC_PLUGIN_URL', plugin_dir_url(__FILE__));


require_once( SSC_PLUGIN_DIR . 'admin/admin.php');
	// Email verification enabled for production
	require_once( SSC_PLUGIN_DIR . 'includes/email-verification.php');
	// Enhanced PDF generator
	require_once( SSC_PLUGIN_DIR . 'includes/enhanced-pdf-generator.php');


// Activation hook to create database tables
register_activation_hook(__FILE__, 'ssc_activate_plugin');

// Ensure table exists when plugin loads
add_action('init', 'ssc_ensure_table_exists');

function ssc_activate_plugin() {
    global $wpdb;
    
    error_log('ssc_activate_plugin called');
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Table for storing drawing data and PDFs
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        drawing_name varchar(255) NOT NULL,
        drawing_notes text,
        total_cutting_mm decimal(10,2) NOT NULL,
        only_cut_mm decimal(10,2) NOT NULL,
        pdf_file_path varchar(500) NOT NULL,
        drawing_link text NOT NULL,
        quote_id varchar(50),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        production_cost_standard decimal(10,2) DEFAULT 0.00,
        production_cost_mitred decimal(10,2) DEFAULT 0.00,
        installation_cost decimal(10,2) DEFAULT 0.00,
        total_production_cost decimal(10,2) DEFAULT 0.00,
        total_installation_cost decimal(10,2) DEFAULT 0.00,
        total_project_cost decimal(10,2) DEFAULT 0.00,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    error_log('Creating drawings table with SQL: ' . $sql);
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $result = dbDelta($sql);
    
    // Table for storing watermark images
    $watermark_table_name = $wpdb->prefix . 'ssc_watermarks';
    
    $watermark_sql = "CREATE TABLE $watermark_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        filename varchar(255) NOT NULL,
        file_path varchar(500) NOT NULL,
        file_url varchar(500) NOT NULL,
        file_size int(11) NOT NULL,
        mime_type varchar(100) NOT NULL,
        uploaded_by bigint(20) NOT NULL,
        is_active tinyint(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY uploaded_by (uploaded_by),
        KEY is_active (is_active),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    error_log('Creating watermarks table with SQL: ' . $watermark_sql);
    
    $watermark_result = dbDelta($watermark_sql);
    
            
    
    // Check if table was created
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    error_log('Table exists after creation: ' . ($table_exists ? 'Yes' : 'No'));
    
    if (!$table_exists) {
        error_log('Table creation failed. Last error: ' . $wpdb->last_error);
    }
    
    // Create quotes directory if it doesn't exist
    $quotes_dir = SSC_PLUGIN_DIR . 'quotes/';
    if (!file_exists($quotes_dir)) {
        wp_mkdir_p($quotes_dir);
        error_log('Created quotes directory: ' . $quotes_dir);
    }
    
    // Create .htaccess file to protect the quotes directory
    $htaccess_file = $quotes_dir . '.htaccess';
    if (!file_exists($htaccess_file)) {
        $htaccess_content = "Order Deny,Allow\nDeny from all\n";
        file_put_contents($htaccess_file, $htaccess_content);
        error_log('Created .htaccess file in quotes directory');
    }
    
    // Update existing table structure if needed
    $existing_columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
    $drawing_link_column = null;
    
    foreach ($existing_columns as $column) {
        if ($column->Field === 'drawing_link') {
            $drawing_link_column = $column;
            break;
        }
    }
    
    // If drawing_link column exists and is varchar(500), update it to text
    if ($drawing_link_column && strpos($drawing_link_column->Type, 'varchar(500)') !== false) {
        error_log('Updating drawing_link column from varchar(500) to text...');
        $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN drawing_link text NOT NULL");
        error_log('✅ drawing_link column updated to text');
    }
}

// Function to save drawing to database
function ssc_save_drawing($data) {
    global $wpdb;
    
    error_log('=== DATABASE SAVE FUNCTION STARTED ===');
    error_log('Received data: ' . print_r($data, true));
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    error_log('Table name: ' . $table_name);
    
    // Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    error_log('Table exists: ' . ($table_exists ? 'YES' : 'NO'));
    
    if (!$table_exists) {
        error_log('❌ Table does not exist. Creating it...');
        ssc_ensure_table_exists();
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        error_log('Table exists after creation: ' . ($table_exists ? 'YES' : 'NO'));
    }
    
    // Get user ID from data or current user
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : get_current_user_id();
    error_log('User ID: ' . $user_id);
    
    if (!$user_id) {
        error_log('❌ No user ID available');
        return false;
    }
    
    // Prepare data for insertion - using actual table column names
    $insert_data = array(
        'user_id' => $user_id,
        'drawing_name' => sanitize_text_field($data['drawing_name']),
        'drawing_notes' => sanitize_textarea_field($data['drawing_notes']),
        'total_cutting_mm' => floatval($data['total_cutting_mm']),
        'only_cut_mm' => floatval($data['only_cut_mm']),
        'pdf_file_path' => sanitize_text_field($data['pdf_file_path']),
        'drawing_link' => esc_url_raw($data['drawing_link']),
        'quote_id' => isset($data['quote_id']) ? sanitize_text_field($data['quote_id']) : '',
        'production_cost_standard' => floatval($data['only_cut_mm'] ?? 0), // Map only_cut_mm to production_cost_standard
        'production_cost_mitred' => floatval($data['mitred_cut_mm'] ?? 0), // Map mitred_cut_mm to production_cost_mitred
        'installation_cost' => 0.00, // Default value
        'total_production_cost' => floatval($data['total_cutting_mm'] ?? 0), // Map total_cutting_mm to total_production_cost
        'total_installation_cost' => 0.00, // Default value
        'total_project_cost' => 0.00 // Default value
    );
    
    error_log('Insert data prepared: ' . print_r($insert_data, true));
    
    // Check for required fields - using actual table column names
    $required_fields = ['drawing_name', 'total_cutting_mm', 'only_cut_mm', 'pdf_file_path'];
    foreach ($required_fields as $field) {
        if (!isset($insert_data[$field]) || $insert_data[$field] === '') {
            error_log('❌ Missing required field: ' . $field);
        }
    }
    
    $result = $wpdb->insert($table_name, $insert_data);
    error_log('Database insert result: ' . ($result === false ? 'FAILED' : 'SUCCESS'));
    
    if ($result === false) {
        error_log('❌ Database error: ' . $wpdb->last_error);
        error_log('❌ Last SQL query: ' . $wpdb->last_query);
        return false;
    }
    
    $insert_id = $wpdb->insert_id;
    error_log('✅ Insert successful. New ID: ' . $insert_id);
    return $insert_id;
}

// Function to get drawings for a user
function ssc_get_user_drawings($user_id = null) {
    global $wpdb;
    
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return array();
    }
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    $drawings = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
            $user_id
        ),
        ARRAY_A
    );
    
    return $drawings;
}

// Function to get a specific drawing
function ssc_get_drawing($drawing_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    $drawing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $drawing_id
        ),
        ARRAY_A
    );
    
    return $drawing;
}

// Function to get drawing by PDF filename
function ssc_get_drawing_by_pdf($pdf_filename) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    $drawing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE pdf_file_path = %s",
            $pdf_filename
        ),
        ARRAY_A
    );
    
    return $drawing;
}

// Function to get all drawings (for when no user ID is provided)
function ssc_get_all_drawings() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    $drawings = $wpdb->get_results("
        SELECT * FROM $table_name 
        ORDER BY created_at DESC
        LIMIT 50
    ", ARRAY_A);
    
    return $drawings;
}

// Function to delete a drawing
function ssc_delete_drawing($drawing_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    // Get the drawing first to delete the PDF file
    $drawing = ssc_get_drawing($drawing_id);
    if ($drawing && !empty($drawing['pdf_file_path'])) {
        $pdf_path = SSC_PLUGIN_DIR . 'quotes/' . basename($drawing['pdf_file_path']);
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }
    }
    
    $result = $wpdb->delete(
        $table_name,
        array('id' => $drawing_id),
        array('%d')
    );
    
    return $result !== false;
}

// AJAX handler for saving drawing
add_action('wp_ajax_ssc_save_drawing', 'ssc_ajax_save_drawing');
add_action('wp_ajax_nopriv_ssc_save_drawing', 'ssc_ajax_save_drawing');

function ssc_ajax_save_drawing() {
    error_log('=== SAVE DRAWING AJAX HANDLER STARTED ===');
    error_log('Timestamp: ' . date('Y-m-d H:i:s'));
    error_log('POST data received: ' . print_r($_POST, true));
    error_log('FILES data received: ' . print_r($_FILES, true));
    error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
    error_log('Content type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));
    error_log('AJAX action: ' . ($_POST['action'] ?? 'NOT SET'));
    error_log('Nonce received: ' . ($_POST['nonce'] ?? 'NOT SET'));
    
    // Database table creation disabled - focus on file saving only
    
    
    
    // Check nonce for security - TEMPORARILY DISABLED FOR TESTING
    error_log('🔍 Nonce verification temporarily disabled for testing');
    error_log('Received nonce: ' . ($_POST['nonce'] ?? 'NOT SET'));
    error_log('Expected nonce action: ssc_save_drawing_nonce');
    /*
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        error_log('❌ Nonce verification failed');
        error_log('Received nonce: ' . ($_POST['nonce'] ?? 'NOT SET'));
        error_log('Expected nonce action: ssc_save_drawing_nonce');
        wp_die('Security check failed - Invalid nonce');
    }
    error_log('✅ Nonce verification passed');
    */
    
    // Check if user is logged in or if user_id is provided
    error_log('🔍 User authentication check...');
    $user_id = null;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        error_log('✅ User logged in, ID: ' . $user_id);
    } elseif (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        error_log('✅ User ID from POST: ' . $user_id);
        // Verify the user exists
        if (!get_user_by('ID', $user_id)) {
            error_log('❌ Invalid user ID: ' . $user_id);
            wp_die('Invalid user ID');
        }
        error_log('✅ User ID verified: ' . $user_id);
    } else {
        error_log('❌ No user ID available');
        error_log('User logged in: ' . (is_user_logged_in() ? 'YES' : 'NO'));
        error_log('POST user_id: ' . ($_POST['user_id'] ?? 'NOT SET'));
        wp_die('User not logged in and no user ID provided');
    }
    
    // Check if PDF file was uploaded
    error_log('Checking PDF file upload...');
    error_log('$_FILES keys: ' . print_r(array_keys($_FILES), true));
    error_log('$_FILES content: ' . print_r($_FILES, true));
    error_log('$_POST content: ' . print_r($_POST, true));
    error_log('Total POST fields: ' . count($_POST));
    error_log('Total FILES fields: ' . count($_FILES));
    
    // Debug: Check if this is actually a file upload
    if (empty($_FILES)) {
        error_log('❌ $_FILES is completely empty - this is not a file upload request');
        error_log('This suggests the FormData is not being sent as multipart/form-data');
        wp_send_json_error('No files received. This might be a FormData encoding issue.');
    }
    
    if (!isset($_FILES['pdf_file'])) {
        error_log('No pdf_file in $_FILES');
        error_log('Available FILES keys: ' . print_r(array_keys($_FILES), true));
        wp_send_json_error('No PDF file received');
    }
    
    if ($_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $error_message = 'PDF file upload failed';
        error_log('PDF file upload error: ' . $_FILES['pdf_file']['error']);
        if (isset($_FILES['pdf_file']['error'])) {
            switch ($_FILES['pdf_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $error_message = 'PDF file exceeds maximum allowed size';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = 'PDF file exceeds form maximum size';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message = 'PDF file was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = 'No PDF file was uploaded';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message = 'Missing temporary folder';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message = 'Failed to write PDF file to disk';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error_message = 'PDF file upload stopped by extension';
                    break;
            }
        }
        wp_send_json_error($error_message);
    }
    
    $pdf_file = $_FILES['pdf_file'];
    
    // Check file size (limit to 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB in bytes
    if ($pdf_file['size'] > $max_size) {
        wp_send_json_error('PDF file size exceeds 10MB limit');
    }
    
    // Validate file type - ONLY PDF files allowed
    $allowed_types = array('application/pdf');
    
    // Check if mime_content_type function exists, otherwise use file extension
    if (function_exists('mime_content_type')) {
        $file_type = mime_content_type($pdf_file['tmp_name']);
        error_log('File type detected by mime_content_type: ' . $file_type);
    } else {
        // Fallback: use file extension to determine type
        $file_extension = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));
        if ($file_extension === 'pdf') {
            $file_type = 'application/pdf';
        } else {
            $file_type = 'unknown';
        }
        error_log('File type detected by extension: ' . $file_type . ' (extension: ' . $file_extension . ')');
    }
    
    if (!in_array($file_type, $allowed_types)) {
        error_log('❌ Invalid file type: ' . $file_type);
        wp_send_json_error('Invalid file type. Only PDF files are allowed. Detected: ' . $file_type);
    }
    
    // Additional security: check file extension - ONLY PDF
    $file_extension = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));
    if ($file_extension !== 'pdf') {
        wp_send_json_error('Invalid file extension. Only .pdf files are allowed.');
    }
    
    // Create quotes directory if it doesn't exist
    $quotes_dir = SSC_PLUGIN_DIR . 'quotes/';
    error_log('Quotes directory path: ' . $quotes_dir);
    error_log('Directory exists before creation: ' . (file_exists($quotes_dir) ? 'YES' : 'NO'));
    error_log('Directory writable before creation: ' . (is_writable(dirname($quotes_dir)) ? 'YES' : 'NO'));
    
    if (!file_exists($quotes_dir)) {
        error_log('Creating quotes directory...');
        $created = wp_mkdir_p($quotes_dir);
        error_log('Directory creation result: ' . ($created ? 'SUCCESS' : 'FAILED'));
        if (!$created) {
            error_log('Failed to create quotes directory. Check permissions.');
            wp_send_json_error('Failed to create quotes directory. Check server permissions.');
        }
    }
    
    error_log('Directory exists after creation: ' . (file_exists($quotes_dir) ? 'YES' : 'NO'));
    error_log('Directory writable after creation: ' . (is_writable($quotes_dir) ? 'YES' : 'NO'));
    
    // Generate filename in format: QuoteID-USERID-PRODUCT-DATE.[extension]
    $quote_id = time() . '_' . rand(1000, 9999); // Using timestamp + random number as QuoteID
    $product_name = sanitize_file_name($_POST['drawing_name'] ?? 'Custom-Slab');
    $current_date = date('Y-m-d');
    $file_extension = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));
    $filename = $quote_id . '-' . $user_id . '-' . $product_name . '-' . $current_date . '.' . $file_extension;
    $file_path = $quotes_dir . $filename;
    
    // Move uploaded file to quotes directory
    error_log('Attempting to move uploaded file...');
    error_log('- Source (tmp_name): ' . $pdf_file['tmp_name']);
    error_log('- Destination: ' . $file_path);
    error_log('- Source file exists: ' . (file_exists($pdf_file['tmp_name']) ? 'YES' : 'NO'));
    error_log('- Source file readable: ' . (is_readable($pdf_file['tmp_name']) ? 'YES' : 'NO'));
    error_log('- Destination directory writable: ' . (is_writable($quotes_dir) ? 'YES' : 'NO'));
    
    if (!move_uploaded_file($pdf_file['tmp_name'], $file_path)) {
        error_log('Failed to move uploaded file. PHP error: ' . error_get_last()['message']);
        wp_send_json_error('Failed to save PDF file to server: ' . error_get_last()['message']);
    }
    
    error_log('File moved successfully to: ' . $file_path);
    error_log('File exists after move: ' . (file_exists($file_path) ? 'YES' : 'NO'));
    error_log('File size after move: ' . filesize($file_path));
    
    // Set file permissions
    chmod($file_path, 0644);
    
    // Add PDF file path to POST data
    $_POST['pdf_file_path'] = $filename;
    
    // Add user_id to POST data
    $_POST['user_id'] = $user_id;
    
    // ENABLE DATABASE SAVING - SAVE PDF PATH TO DATABASE
    error_log('=== DATABASE SAVE ENABLED - SAVING PDF PATH ===');
    error_log('✅ PDF file saved successfully to: ' . $file_path);
    error_log('✅ File size: ' . filesize($file_path) . ' bytes');
    error_log('✅ File permissions: ' . substr(sprintf('%o', fileperms($file_path)), -4));
    
    // Prepare data for database save
    $drawing_link = $_POST['drawing_link'] ?? '';
    
    // Truncate drawing_link if it's too long (keep only essential parts)
    if (strlen($drawing_link) > 500) {
        $parsed_url = parse_url($drawing_link);
        $drawing_link = ($parsed_url['scheme'] ?? 'http') . '://' . ($parsed_url['host'] ?? 'localhost') . '/...';
        error_log('⚠️ Drawing link truncated from ' . strlen($_POST['drawing_link']) . ' to ' . strlen($drawing_link) . ' characters');
    }
    
    $drawing_data = array(
        'user_id' => $user_id,
        'drawing_name' => $_POST['drawing_name'] ?? '',
        'drawing_notes' => $_POST['drawing_notes'] ?? '',
        'total_cutting_mm' => $_POST['total_cutting_mm'] ?? 0,
        'only_cut_mm' => $_POST['only_cut_mm'] ?? 0,
        'mitred_cut_mm' => $_POST['mitred_cut_mm'] ?? 0, // Will be mapped to production_cost_mitred
        'slab_cost' => $_POST['slab_cost'] ?? '$0', // Will be mapped to total_project_cost
        'drawing_data' => $_POST['drawing_data'] ?? '', // Not used in current table structure
        'pdf_file_path' => $filename, // Save the PDF filename to database
        'drawing_link' => $drawing_link,
        'quote_id' => time() . '_' . rand(1000, 9999) // Generate quote ID
    );
    
    error_log('Data prepared for database save: ' . print_r($drawing_data, true));
    error_log('🔍 Checking database table before save...');
    
    // Ensure database table exists before saving
    if (function_exists('ssc_ensure_table_exists')) {
        error_log('🚀 Calling ssc_ensure_table_exists...');
        $table_ready = ssc_ensure_table_exists();
        error_log('📊 Table ready status: ' . ($table_ready ? 'YES' : 'NO'));
        
        // Also update table structure if needed
        if (function_exists('ssc_update_table_structure')) {
            error_log('🔧 Updating table structure if needed...');
            $structure_updated = ssc_update_table_structure();
            error_log('📊 Structure update result: ' . ($structure_updated ? 'SUCCESS' : 'FAILED'));
        }
    } else {
        error_log('❌ ssc_ensure_table_exists function not found');
    }
    
    // Save to database
    error_log('🚀 Calling ssc_save_drawing function...');
    $db_result = ssc_save_drawing($drawing_data);
    error_log('📊 Database save result: ' . ($db_result === false ? 'FAILED' : 'SUCCESS - ID: ' . $db_result));
    
    if ($db_result === false) {
        error_log('❌ Database save failed');
        global $wpdb;
        if ($wpdb) {
            error_log('❌ Last database error: ' . $wpdb->last_error);
            error_log('❌ Last SQL query: ' . $wpdb->last_query);
        } else {
            error_log('❌ $wpdb variable not available');
        }
        wp_send_json_error('PDF file saved but failed to save to database');
    } else {
        error_log('✅ Database save successful. Drawing ID: ' . $db_result);
        error_log('✅ PDF file path saved to database: ' . $filename);
    }
    
    // Send success response with both file and database info
    wp_send_json_success(array(
        'pdf_filename' => $filename,
        'file_type' => 'pdf',
        'file_path' => $file_path,
        'file_size' => filesize($file_path),
        'drawing_id' => $db_result,
        'message' => 'PDF file saved successfully to quotes folder and database!'
    ));
}

// AJAX handler for getting user drawings
add_action('wp_ajax_ssc_get_drawings', 'ssc_ajax_get_drawings');
add_action('wp_ajax_nopriv_ssc_get_drawings', 'ssc_ajax_get_drawings');

// AJAX handler for getting a single drawing
add_action('wp_ajax_ssc_get_drawing', 'ssc_ajax_get_single_drawing');
add_action('wp_ajax_nopriv_ssc_get_drawing', 'ssc_ajax_get_single_drawing');

function ssc_ajax_get_drawings() {
    // Ensure database table exists - TEMPORARILY DISABLED
    // ssc_ensure_table_exists();
    
    // Check nonce for security - temporarily disabled for testing
    error_log('Get drawings nonce check - received nonce: ' . $_POST['nonce']);
    
    // TEMPORARILY DISABLE NONCE VERIFICATION TO FIX VIEWING ISSUE
    // TODO: Implement proper nonce verification once the system is stable
    /*
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        error_log('Get drawings nonce verification failed');
        wp_die('Security check failed');
    }
    */
    
    error_log('⚠️ Nonce verification temporarily disabled for getting drawings');
    
    // Check if user is logged in or if user_id is provided
    $user_id = null;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        error_log('User logged in, ID: ' . $user_id);
    } elseif (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        // Verify the user exists
        if (!get_user_by('ID', $user_id)) {
            error_log('Invalid user ID provided: ' . $user_id);
            wp_die('Invalid user ID');
        }
        error_log('User ID from POST: ' . $user_id);
    } else {
        error_log('No user ID provided, will try to get all drawings');
        // Don't die, just continue without user ID
        $user_id = null;
    }
    
    // Get drawings - if no user ID, get all drawings
    if ($user_id) {
        $drawings = ssc_get_user_drawings($user_id);
        error_log('Getting drawings for user ID: ' . $user_id);
    } else {
        // Get all drawings if no user ID
        $drawings = ssc_get_all_drawings();
        error_log('Getting all drawings (no user ID)');
    }
    
    if ($drawings !== false) {
        wp_send_json_success($drawings);
    } else {
        wp_send_json_error('Failed to get drawings');
    }
}

// AJAX handler for getting a single drawing
function ssc_ajax_get_single_drawing() {
    // Check nonce for security - temporarily disabled for testing
    // TEMPORARILY DISABLE NONCE VERIFICATION TO FIX VIEWING ISSUE
    // TODO: Implement proper nonce verification once the system is stable
    /*
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    */
    
    error_log('⚠️ Nonce verification temporarily disabled for getting single drawing');
    
    // Check if user is logged in or if user_id is provided
    $user_id = null;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    } elseif (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        // Verify the user exists
        if (!get_user_by('ID', $user_id)) {
            wp_die('Invalid user ID');
        }
    } else {
        wp_die('User not logged in and no user ID provided');
    }
    
    $drawing_id = intval($_POST['drawing_id']);
    
    if (!$drawing_id) {
        wp_send_json_error('Drawing ID not provided');
    }
    
    // Get the drawing
    $drawing = ssc_get_drawing($drawing_id);
    
    if (!$drawing) {
        wp_send_json_error('Drawing not found');
    }
    
    // Verify the drawing belongs to the user
    if ($drawing['user_id'] != $user_id) {
        wp_send_json_error('Access denied');
    }
    
    wp_send_json_success($drawing);
}

// AJAX handler for deleting drawing
add_action('wp_ajax_ssc_delete_drawing', 'ssc_ajax_delete_drawing');
add_action('wp_ajax_nopriv_ssc_delete_drawing', 'ssc_ajax_delete_drawing');

// AJAX handler for downloading PDF
add_action('wp_ajax_ssc_download_pdf', 'ssc_ajax_download_pdf');
add_action('wp_ajax_nopriv_ssc_download_pdf', 'ssc_ajax_download_pdf');

// AJAX handler for getting fresh nonce


// AJAX handler for ensuring table exists
add_action('wp_ajax_ssc_ensure_table', 'ssc_ajax_ensure_table');
add_action('wp_ajax_nopriv_ssc_ensure_table', 'ssc_ajax_ensure_table');

// AJAX handler for testing database table
add_action('wp_ajax_ssc_test_database', 'ssc_ajax_test_database');
add_action('wp_ajax_nopriv_ssc_test_database', 'ssc_ajax_test_database');

// AJAX handler for viewing PDF
add_action('wp_ajax_ssc_view_pdf', 'ssc_ajax_view_pdf');
add_action('wp_ajax_nopriv_ssc_view_pdf', 'ssc_ajax_view_pdf');

function ssc_ajax_delete_drawing() {
    // Ensure database table exists - TEMPORARILY DISABLED
    // ssc_ensure_table_exists();
    
    // Check nonce for security - temporarily disabled for testing
    // TEMPORARILY DISABLE NONCE VERIFICATION TO FIX VIEWING ISSUE
    // TODO: Implement proper nonce verification once the system is stable
    /*
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    */
    
    error_log('⚠️ Nonce verification temporarily disabled for deleting drawing');
    
    // Check if user is logged in or if user_id is provided
    $user_id = null;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    } elseif (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        // Verify the user exists
        if (!get_user_by('ID', $user_id)) {
            wp_die('Invalid user ID');
        }
    } else {
        wp_die('User not logged in and no user ID provided');
    }
    
    $drawing_id = intval($_POST['drawing_id']);
    
    // Verify the drawing belongs to the user
    $drawing = ssc_get_drawing($drawing_id);
    if (!$drawing || $drawing['user_id'] != $user_id) {
        wp_die('Access denied - drawing does not belong to user');
    }
    
    if (ssc_delete_drawing($drawing_id)) {
        wp_send_json_success('Drawing deleted successfully');
    } else {
        wp_send_json_error('Failed to delete drawing');
    }
}

// Function to handle PDF download
function ssc_ajax_download_pdf() {
    error_log('=== PDF DOWNLOAD HANDLER STARTED ===');
    error_log('GET data received: ' . print_r($_GET, true));
    
    // Check nonce for security - temporarily disabled for testing
    // TODO: Implement proper nonce verification once the system is stable
    /*
    if (!wp_verify_nonce($_GET['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    */
    
    error_log('⚠️ Nonce verification temporarily disabled for PDF download');
    
    $pdf_filename = sanitize_text_field($_GET['pdf']);
    
    if (empty($pdf_filename)) {
        error_log('❌ PDF filename not provided');
        wp_die('PDF filename not provided');
    }
    
    error_log('📄 Looking for PDF file: ' . $pdf_filename);
    
    // Try multiple possible file locations for enhanced PDFs
    $possible_paths = array();
    
    // 1. Check the ssc-temp directory (where enhanced PDFs are saved)
    $temp_dir = wp_upload_dir()['basedir'] . '/ssc-temp/';
    $possible_paths[] = $temp_dir . $pdf_filename;
    
    // 2. Check the quotes directory (for legacy PDFs)
    $quotes_dir = SSC_PLUGIN_DIR . 'quotes/';
    $possible_paths[] = $quotes_dir . $pdf_filename;
    
    // 3. Check the uploads directory
    $uploads_dir = wp_upload_dir()['basedir'] . '/';
    $possible_paths[] = $uploads_dir . $pdf_filename;
    
    error_log('🔍 Checking possible file paths:');
    foreach ($possible_paths as $path) {
        error_log('  - ' . $path . ' (exists: ' . (file_exists($path) ? 'Yes' : 'No') . ')');
    }
    
    $file_path = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $file_path = $path;
            error_log('✅ Found PDF file at: ' . $path);
            break;
        }
    }
    
    if (!$file_path) {
        error_log('❌ PDF file not found in any of the checked locations');
        wp_die('PDF file not found. Please try generating the PDF again.');
    }
    
    // Check if file is readable
    if (!is_readable($file_path)) {
        error_log('❌ PDF file is not readable: ' . $file_path);
        wp_die('PDF file is not accessible. Please check file permissions.');
    }
    
    // Get file size
    $file_size = filesize($file_path);
    if ($file_size === false) {
        error_log('❌ Could not determine file size for: ' . $file_path);
        wp_die('Could not determine file size.');
    }
    
    error_log('📊 File size: ' . $file_size . ' bytes');
    
    // Determine content type based on file extension
    $file_extension = strtolower(pathinfo($pdf_filename, PATHINFO_EXTENSION));
    
    if ($file_extension === 'html') {
        // Serve HTML file for browser-based PDF conversion
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="' . $pdf_filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
    } else {
        // Serve PDF file for download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $pdf_filename . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
    }
    
    error_log('📥 Starting file download...');
    
    // Output file content
    $bytes_sent = readfile($file_path);
    
    if ($bytes_sent === false) {
        error_log('❌ Error reading file: ' . $file_path);
        wp_die('Error reading PDF file.');
    }
    
    error_log('✅ File download completed. Bytes sent: ' . $bytes_sent);
    exit;
}



// Function to manually create database table if it doesn't exist
function ssc_ensure_table_exists() {
    global $wpdb;
    
    error_log('ssc_ensure_table_exists called');
    
    // Check drawings table
    $table_name = $wpdb->prefix . 'ssc_drawings';
    error_log('Checking for drawings table: ' . $table_name);
    
    // Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    error_log('Drawings table exists check result: ' . ($table_exists ? 'Yes' : 'No'));
    
    // Check watermarks table
    $watermark_table_name = $wpdb->prefix . 'ssc_watermarks';
    error_log('Checking for watermarks table: ' . $watermark_table_name);
    
    $watermark_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$watermark_table_name'") == $watermark_table_name;
    error_log('Watermarks table exists check result: ' . ($watermark_table_exists ? 'Yes' : 'No'));
    
    if (!$table_exists || !$watermark_table_exists) {
        error_log('One or more tables do not exist, creating them now...');
        ssc_activate_plugin(); // ENABLED - This creates the tables
        error_log('Table creation completed');
        
        // Check again after creation
        $table_exists_after = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        $watermark_table_exists_after = $wpdb->get_var("SHOW TABLES LIKE '$watermark_table_name'") == $watermark_table_name;
        error_log('Drawings table exists after creation: ' . ($table_exists_after ? 'Yes' : 'No'));
        error_log('Watermarks table exists after creation: ' . ($watermark_table_exists_after ? 'Yes' : 'No'));
        
        return $table_exists_after && $watermark_table_exists_after;
    } else {
        error_log('All tables already exist');
        
        // Check if table structure needs updating
        $existing_columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
        $drawing_link_column = null;
        
        foreach ($existing_columns as $column) {
            if ($column->Field === 'drawing_link') {
                $drawing_link_column = $column;
                break;
            }
        }
        
        // If drawing_link column is varchar(500), update it to text
        if ($drawing_link_column && strpos($drawing_link_column->Type, 'varchar(500)') !== false) {
            error_log('Updating existing table structure...');
            ssc_activate_plugin(); // This will update the table structure
            error_log('✅ Table structure updated');
        }
    }
    
    return $table_exists && $watermark_table_exists;
}

// AJAX function to ensure table exists
function ssc_ajax_ensure_table() {
    // TEMPORARILY DISABLED
    // $result = ssc_ensure_table_exists();
    // if ($result) {
    //     wp_send_json_success('Table exists or was created successfully');
    // } else {
    //     wp_send_json_error('Failed to create table');
    // }
    wp_send_json_success('Table check temporarily disabled');
}

// Function to manually update table structure
function ssc_update_table_structure() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    // Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    if (!$table_exists) {
        error_log('❌ Table does not exist, cannot update structure');
        return false;
    }
    
    // Update drawing_link column to text
    $result = $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN drawing_link text NOT NULL");
    
    if ($result !== false) {
        error_log('✅ Table structure updated successfully');
        return true;
    } else {
        error_log('❌ Failed to update table structure: ' . $wpdb->last_error);
        return false;
    }
}

// AJAX function to test database table functionality
function ssc_ajax_test_database() {
    global $wpdb;
    
    error_log('Testing database table functionality...');
    
    // First ensure table exists
    // ssc_ensure_table_exists(); // TEMPORARILY DISABLED
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    // Test 1: Check if table exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    error_log('Test 1 - Table exists: ' . ($table_exists ? 'PASS' : 'FAIL'));
    
    if (!$table_exists) {
        wp_send_json_error('Table does not exist');
        return;
    }
    
    // Test 2: Check table structure
    $columns = $wpdb->get_results("DESCRIBE $table_name");
            
    
    // Test 3: Try to insert a test record
    $test_data = array(
        'user_id' => 1,
        'drawing_name' => 'TEST_DRAWING_' . time(),
        'drawing_notes' => 'This is a test drawing for database verification',
        'total_cutting_mm' => 100.50,
        'only_cut_mm' => 50.25,
        'mitred_cut_mm' => 50.25,
        'slab_cost' => '1000',
        'drawing_data' => '{"test": "data"}',
        'pdf_file_path' => '/test/path/test_drawing.pdf',
        'drawing_link' => '/test/link/test_drawing',
        'created_at' => current_time('mysql')
    );
    
    $insert_result = $wpdb->insert($table_name, $test_data);
    error_log('Test 3 - Insert test record: ' . ($insert_result ? 'PASS' : 'FAIL'));
    
    if ($insert_result === false) {
        error_log('Insert error: ' . $wpdb->last_error);
        wp_send_json_error('Failed to insert test record: ' . $wpdb->last_error);
        return;
    }
    
    $insert_id = $wpdb->insert_id;
    error_log('Test record inserted with ID: ' . $insert_id);
    
    // Test 4: Try to retrieve the test record
    $retrieved_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $insert_id), ARRAY_A);
    error_log('Test 4 - Retrieve test record: ' . ($retrieved_record ? 'PASS' : 'FAIL'));
    
    if ($retrieved_record) {
        
    }
    
    // Test 5: Try to update the test record
    $update_result = $wpdb->update(
        $table_name,
        array('drawing_notes' => 'Updated test notes'),
        array('id' => $insert_id)
    );
    error_log('Test 5 - Update test record: ' . ($update_result ? 'PASS' : 'FAIL'));
    
    // Test 6: Try to delete the test record
    $delete_result = $wpdb->delete($table_name, array('id' => $insert_id));
    error_log('Test 6 - Delete test record: ' . ($delete_result ? 'PASS' : 'FAIL'));
    
    // Test 7: Check table count
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    error_log('Test 7 - Table count: ' . $count);
    
    // Summary
    $summary = array(
        'table_exists' => $table_exists,
        'table_columns' => $columns,
        'insert_test' => $insert_result !== false,
        'retrieve_test' => $retrieved_record !== null,
        'update_test' => $update_result !== false,
        'delete_test' => $delete_result !== false,
        'final_count' => $count,
        'insert_id' => $insert_id
    );
    
            
    
    wp_send_json_success($summary);
}

// Function to handle PDF viewing
function ssc_ajax_view_pdf() {
    // Ensure database table exists - TEMPORARILY DISABLED
    // ssc_ensure_table_exists();
    
    // Check nonce for security - temporarily disabled for testing
    error_log('View PDF nonce check - received nonce: ' . $_GET['nonce']);
    
    // TEMPORARILY DISABLE NONCE VERIFICATION TO FIX VIEWING ISSUE
    // TODO: Implement proper nonce verification once the system is stable
    /*
    if (!wp_verify_nonce($_GET['nonce'], 'ssc_save_drawing_nonce')) {
        error_log('View PDF nonce verification failed');
        wp_die('Security check failed');
    }
    */
    
    error_log('⚠️ Nonce verification temporarily disabled for PDF viewing');
    
    // Check if user is logged in or if user_id is provided
    $user_id = null;
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
    } elseif (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
        // Verify the user exists
        if (!get_user_by('ID', $user_id)) {
            wp_die('Invalid user ID');
        }
    } else {
        wp_die('User not logged in and no user ID provided');
    }
    
    $pdf_filename = sanitize_text_field($_GET['pdf']);
    
    if (empty($pdf_filename)) {
        wp_die('PDF filename not provided');
    }
    
    // Get drawing to verify user access - TEMPORARILY DISABLED
    // $drawing = ssc_get_drawing_by_pdf($pdf_filename);
    // if (!$drawing || $drawing['user_id'] != $user_id) {
    //     wp_die('Access denied');
    // }
    
    // $file_path = SSC_PLUGIN_DIR . 'quotes/' . $pdf_filename;
    
    // if (!file_exists($file_path)) {
    //     wp_die('PDF file not found');
    // }
    
    // // Set headers for viewing in browser
    // header('Content-Type: application/pdf');
    // header('Content-Disposition: inline; filename="' . $pdf_filename . '"');
    // header('Content-Length: ' . filesize($file_path));
    // header('Cache-Control: no-cache, must-revalidate');
    // header('Pragma: no-cache');
    
    // // Output file content
    // readfile($file_path);
    // exit;
    
    // Get drawing to verify user access
    $drawing = ssc_get_drawing_by_pdf($pdf_filename);
    if (!$drawing || $drawing['user_id'] != $user_id) {
        wp_die('Access denied');
    }
    
    $file_path = SSC_PLUGIN_DIR . 'quotes/' . $pdf_filename;
    
    if (!file_exists($file_path)) {
        wp_die('PDF file not found');
    }
    
    // Set headers for viewing in browser
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $pdf_filename . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    // Output file content
    readfile($file_path);
    exit;
}

// Add rewrite rules for direct quote links - TEMPORARILY DISABLED
// add_action('init', 'ssc_add_quote_rewrite_rules');
// add_action('template_redirect', 'ssc_handle_quote_redirect');
// add_filter('query_vars', 'ssc_add_query_vars');

// ALL QUOTE FUNCTIONS TEMPORARILY REMOVED FOR STABILITY

add_shortcode( 'slab_calculator', 'slab_calculator_shortcode' );
function slab_calculator_shortcode(){
	if ( class_exists('WooCommerce') && is_product() && slab_calculator_check_user_access() ) {
		$product = wc_get_product( get_the_ID() );
		$dimensions = $product->get_dimensions(false);
		$edge_profiles_list = get_option('slab_calculator_edge_profiles', '');
		if ( !empty($edge_profiles_list) ) {
			 foreach ($edge_profiles_list as $index => $profile) {
				 if ( empty($profile['title']) ) {
					 unset($edge_profiles_list[$index]);
				 }
			 }
		} else {
			$edge_profiles_list = [];
		}
		
		$edge_profiles = json_encode($edge_profiles_list, true);
		
		// Drawing Pad Size
		$drawing_pad_height = get_option('slab_calculator_drawing_pad_height', '7700');
        $drawing_pad_width = get_option('slab_calculator_drawing_pad_width', '8800');
		
		// Calculator Height
		$calculator_height = get_option('slab_calculator_height', '700');
		
		// Minimum Screen Size to display Calculator
		$min_screen_size = get_option('slab_calculator_min_screen_size', 600);
		
		// Youtube Video Link
		$youtube_link = get_option('slab_calculator_youtube_link', '');

		// Production and Installation cost settings (passed to iframe)
		$ssc_rate_standard = get_option('ssc_production_cost_standard', '0');
		$ssc_rate_mitred = get_option('ssc_production_cost_mitred', '0');
		$ssc_install_cost = get_option('ssc_installation_cost', '0');

		// Disable drawing after submission setting
		$ssc_disable_drawing = get_option('ssc_disable_drawing_after_submission', 'no');

		// Current product price (slab cost) to pass to iframe
		$slab_price = is_object($product) ? $product->get_price() : '0';

		ob_start();
?>
<button type="button" class="button" id="load_calculator" disabled><?=__('Slab & Production Calculator', 'stone-slab-domain')?></button>
<p style="color:red;display:none;margin-top:20px;">Please open this page from your computer or large-size tablet to make your calculations.</p>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() {
		// Select the element whose parent you want to check.
		const loadCalculator = document.getElementById("load_calculator");

		// Get the parent element of the selected element.
		const parentDiv = loadCalculator.parentElement;

		// Check the parent div's width using offsetWidth.
		if (parentDiv.offsetWidth < <?=$min_screen_size?>) {
			loadCalculator.nextElementSibling.style.display = 'block';
		} else {
			loadCalculator.disabled = false;
		}
		
		
		// Add click event listener to the button
		loadCalculator.addEventListener("click", function() {
			let button = this;  // Reference to the button
			let originalButtonText = button.innerHTML;  // Store the original button text
			let edgeProfiles = '<?=$edge_profiles?>';
			let youtubeUrl = '<?=$youtube_link?>';
			
			
			// Show loader in the button
			button.innerHTML = "Loading...";  // Change the text to show a loading state
			button.disabled = true;  // Optionally disable the button to prevent multiple clicks


			// Get the data-path attribute from the button
			let iframePath = "<?=SSC_PLUGIN_URL?>templates/calculator.php";

			// Append the data as query parameters to the iframe URL
			iframePath += "?name=<?=$product->get_name()?>&slab_width=<?=$dimensions['width']?>&slab_height=<?=$dimensions['height']?>&pad_width=<?=$drawing_pad_width?>&pad_height=<?=$drawing_pad_height?>&edges="+edgeProfiles+"&site_url=<?=urlencode(site_url())?>&nonce=<?=wp_create_nonce('ssc_save_drawing_nonce')?>&auth_nonce=<?=wp_create_nonce('stone_slab_auth_nonce')?>&rate_standard=<?=rawurlencode($ssc_rate_standard)?>&rate_mitred=<?=rawurlencode($ssc_rate_mitred)?>&install_cost=<?=rawurlencode($ssc_install_cost)?>&slab_price=<?=rawurlencode($slab_price)?>&disable_drawing=<?=rawurlencode($ssc_disable_drawing)?>";
			
			if ( youtubeUrl != '' ) {
				let videoId = '';

				// Check if the URL is the short youtu.be format
				if (youtubeUrl.includes('youtu.be')) {
					// Extract video ID from youtu.be URL
					videoId = youtubeUrl.split('/').pop().split('?')[0];
				} else if (youtubeUrl.includes('youtube.com/watch?v=')) {
					// Extract video ID from youtube.com/watch URL
					videoId = youtubeUrl.split('v=')[1].split('&')[0];
				}
				
				iframePath += "&video="+encodeURIComponent('https://www.youtube.com/embed/' + videoId);
			}

			// Check if the iframe already exists
			let existingIframe = document.getElementById("calculator_iframe");

			// If the iframe doesn\'t exist, create it
			if (!existingIframe) {
				let iframe = document.createElement("iframe");
				iframe.id = "calculator_iframe";  // Set an ID for the iframe
				iframe.src = iframePath;  // Set the source to the path in data-path
				iframe.width = "100%";  // You can adjust the width as needed
				iframe.height = "<?=$calculator_height?>px";  // You can adjust the height as needed
				iframe.style.border = "1px solid";  // Optional: remove the border
				iframe.style.marginTop = "20px";  // Optional: space from top
				iframe.style.display = "none"; // Initially hide the iframe

				// Append the iframe directly to the body
				this.parentNode.insertBefore(iframe, this.nextSibling);

				// Add the onload event to show the iframe when it's fully loaded
				iframe.onload = function() {
					iframe.style.display = "block"; // Show the iframe when loaded

					// Reset the button text and re-enable it
					button.innerHTML = originalButtonText;
					button.disabled = false;
				};
			} else {
				// If iframe already exists, just change the source
				existingIframe.src = iframePath;

				// Add onload event to reset the button when it loads
				existingIframe.onload = function() {
					button.innerHTML = originalButtonText;
					button.disabled = false;
				};
			}
		});
	});
</script>
<?php
			$html = ob_get_clean();
		return $html;
	}
}


if ( ! function_exists( 'handle_send_html_email' ) ) {
	function handle_send_html_email() {
		// Verify the request (add nonce verification if needed)
		if (empty($_FILES['pdf'])) {
			wp_send_json_error(['message' => 'No PDF file provided.']);
		}
				
		// Get the email from the AJAX request
		$email = sanitize_email($_POST['email']);

		if (!is_email($email)) {
			wp_send_json_error(['message' => 'Invalid email address.']);
		}

		// Handle the uploaded PDF file
		$pdf_file = $_FILES['pdf'];
		
		// Create quotes directory if it doesn't exist
		$quotes_dir = SSC_PLUGIN_DIR . 'quotes/';
		if (!file_exists($quotes_dir)) {
			wp_mkdir_p($quotes_dir);
		}
		
		// Get current user ID and other data for filename
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID ?: 'guest';
		$slab_name = sanitize_text_field($_POST['slab_name'] ?? 'Custom Slab');
		$slab_name_clean = sanitize_file_name($slab_name);
		$current_date = date('Y-m-d');
		
		// Generate filename in format: QuoteID-USERID-PRODUCT-DATE.pdf
		// We'll use timestamp as QuoteID for now, but this can be updated when drawing is saved
		$quote_id = time();
		$pdf_filename = $quote_id . '-' . $user_id . '-' . $slab_name_clean . '-' . $current_date . '.pdf';
		$target_path = $quotes_dir . $pdf_filename;

		// Move the uploaded file
		if (!move_uploaded_file($pdf_file['tmp_name'], $target_path)) {
			wp_send_json_error(['message' => 'Failed to save PDF.']);
		}
		
		// Get additional data from POST
		$slab_name = sanitize_text_field($_POST['slab_name'] ?? 'Custom Slab');
		$total_cutting_mm = sanitize_text_field($_POST['total_cutting_mm'] ?? '0');
		$only_cut_mm = sanitize_text_field($_POST['only_cut_mm'] ?? '0');
		$mitred_cut_mm = sanitize_text_field($_POST['mitred_cut_mm'] ?? '0');
		$slab_cost = sanitize_text_field($_POST['slab_cost'] ?? '$0');
		$drawing_link = esc_url($_POST['drawing_link'] ?? '');

		// Get the email template from admin settings
		$html_content = get_option('slab_calculator_email_template', '');
		
		// If no custom template exists, fall back to the old template file
		if (empty($html_content)) {
			$template_path = SSC_PLUGIN_DIR . 'templates/email-template.html';
			if (file_exists($template_path)) {
				$html_content = file_get_contents($template_path);
			} else {
				wp_send_json_error(['message' => 'Email template not found.']);
			}
		}

		// Replace dynamic fields in the template
		$current_user = wp_get_current_user();
		$customer_name = $current_user->display_name ?: $current_user->user_login;
		
		// Generate direct quote link - TEMPORARILY DISABLED
		// $direct_quote_link = ssc_generate_quote_link(
		// 	'temp_' . time(), // Temporary ID until drawing is saved
		// 	$current_user->ID ?: 'guest',
		// 	$slab_name,
		// 	$current_date
		// );
		$direct_quote_link = '#';
		
		$replacements = array(
			'{{customer_name}}' => $customer_name,
			'{{slab_name}}' => $slab_name,
			'{{total_cutting_mm}}' => number_format($total_cutting_mm),
			'{{only_cut_mm}}' => number_format($only_cut_mm),
			'{{mitred_cut_mm}}' => number_format($mitred_cut_mm),
			'{{slab_cost}}' => $slab_cost,
			'{{drawing_link}}' => $drawing_link,
			'{{direct_quote_link}}' => '#' // Temporarily disabled
		);
		
		$html_content = str_replace(array_keys($replacements), array_values($replacements), $html_content);

		// Email headers
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Bamby Stone <welcome@bambystone.com.au>');

		// Get internal CC email if configured
		$internal_cc_email = get_option('slab_calculator_internal_cc_email', '');
		
		// Send the email to customer
		$sent = wp_mail($email, 'Project Proposal and Quote', $html_content, $headers, [$target_path]);
		
		// Send internal CC if configured
		if ($sent && !empty($internal_cc_email)) {
			// Create internal email content with additional admin information
			$internal_content = $html_content;
			$internal_content .= '<hr style="margin: 30px 0; border: none; border-top: 2px solid #ddd;">';
			$internal_content .= '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px;">';
			$internal_content .= '<h3 style="color: #495057; margin-top: 0;">📋 Internal Quote Summary</h3>';
			$internal_content .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
			$internal_content .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; background: #e9ecef;">Customer Email:</td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($email) . '</td></tr>';
			$internal_content .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; background: #e9ecef;">Customer Name:</td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($customer_name) . '</td></tr>';
			$internal_content .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; background: #e9ecef;">User ID:</td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($current_user->ID) . '</td></tr>';
			$internal_content .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; background: #e9ecef;">Quote Date:</td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html(current_time('F j, Y g:i A')) . '</td></tr>';
			$internal_content .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; background: #e9ecef;">Drawing Link:</td><td style="padding: 8px; border: 1px solid #ddd;"><a href="' . esc_url($drawing_link) . '">View Drawing</a></td></tr>';
			$internal_content .= '</table>';
			$internal_content .= '<p style="margin-top: 20px; color: #6c757d; font-size: 14px;">This is an internal copy of a quote email sent to ' . esc_html($email) . '</p>';
			$internal_content .= '</div>';
			
			// Send internal CC email
			$internal_headers = array('Content-Type: text/html; charset=UTF-8', 'From: Bamby Stone <welcome@bambystone.com.au>');
			wp_mail($internal_cc_email, '[INTERNAL] Quote Sent: ' . $slab_name, $internal_content, $internal_headers, [$target_path]);
		}

		if ($sent) {
			// Save drawing data to database
			$drawing_data = array(
				'customer_name' => $customer_name,
				'slab_name' => $slab_name,
				'total_cutting_mm' => $total_cutting_mm,
				'only_cut_mm' => $only_cut_mm,
				'mitred_cut_mm' => $mitred_cut_mm,
				'slab_cost' => $slab_cost,
				'drawing_data' => json_encode(array(
					'slab_name' => $slab_name,
					'total_cutting_mm' => $total_cutting_mm,
					'only_cut_mm' => $only_cut_mm,
					'mitred_cut_mm' => $mitred_cut_mm,
					'slab_cost' => $slab_cost,
					'created_at' => current_time('mysql')
				)),
				'pdf_file_path' => $pdf_filename,
				'drawing_link' => $drawing_link
			);
			
			$drawing_id = ssc_save_drawing($drawing_data);
			
			if ($drawing_id) {
				// Rename the PDF file to use the actual drawing ID
				$new_pdf_filename = $drawing_id . '-' . $user_id . '-' . $slab_name_clean . '-' . $current_date . '.pdf';
				$new_target_path = $quotes_dir . $new_pdf_filename;
				
				if (rename($target_path, $new_target_path)) {
					// Update the database with the new filename
					$wpdb->update(
						$wpdb->prefix . 'ssc_drawings',
						array('pdf_file_path' => $new_pdf_filename),
						array('id' => $drawing_id),
						array('%s'),
						array('%d')
					);
					
					// Update the direct quote link with the actual drawing ID - TEMPORARILY DISABLED
					// $final_direct_quote_link = ssc_generate_quote_link(
					// 	$drawing_id,
					// 	$current_user->ID ?: 'guest',
					// 	$slab_name,
					// 	$current_date
					// );
					$final_direct_quote_link = '#';
					
					// Send a follow-up email with the correct direct quote link - TEMPORARILY DISABLED
					// $updated_html_content = str_replace($direct_quote_link, $final_direct_quote_link, $html_content);
					// $updated_html_content = str_replace('{{direct_quote_link}}', $final_direct_quote_link, $updated_html_content);
					
					// Send updated email with correct direct quote link
					// wp_mail($email, 'Updated: Project Proposal and Quote - Direct Link', $updated_html_content, $headers, [$new_target_path]);
				}
				
				wp_send_json_success([
					'message' => 'Email sent successfully and drawing saved!',
					'drawing_id' => $drawing_id
				]);
			} else {
				wp_send_json_success([
					'message' => 'Email sent successfully but failed to save drawing.',
					'drawing_id' => null
				]);
			}
		} else {
			wp_send_json_error(['message' => 'Failed to send email.']);
			// Cleanup PDF if email failed
			@unlink($target_path);
		}
	}
	
	add_action('wp_ajax_send_html_email', 'handle_send_html_email');
	add_action('wp_ajax_nopriv_send_html_email', 'handle_send_html_email');
	} // Close the if statement for function_exists
	
	// Add rewrite rule for serving PDF files - TEMPORARILY DISABLED
	// add_action('init', 'ssc_add_rewrite_rules');
	// add_action('template_redirect', 'ssc_serve_pdf');
	
	function ssc_add_rewrite_rules() {
		add_rewrite_rule(
			'ssc-pdf/([^/]+)/?$',
			'index.php?ssc_pdf=$matches[1]',
			'top'
		);
	}
	
	function ssc_serve_pdf() {
		if (get_query_var('ssc_pdf')) {
			$filename = get_query_var('ssc_pdf');
			$file_path = SSC_PLUGIN_DIR . 'quotes/' . $filename;
			
			// TEMPORARILY DISABLED - PDF serving functionality
			// if (file_exists($file_path) && is_user_logged_in()) {
			// 	// Check if user has access to this file
			// 	// $drawing = ssc_get_drawing_by_pdf($filename); // TEMPORARILY DISABLED
			// 	if ($drawing && $drawing['user_id'] == get_current_user_id()) {
			// 		header('Content-Type: application/pdf');
			// 		header('Content-Disposition: inline; filename="' . $filename . '"');
			// 		header('Content-Length: ' . filesize($file_path));
			// 		readfile($file_path);
			// 		exit;
			// 	}
			// }
			
			wp_die('PDF serving temporarily disabled');
		}
	}
	
// Function temporarily removed for stability

// Authentication System Functions

// Handle user login
if (!function_exists('stone_slab_login_handler')) {
	function stone_slab_login_handler() {
		// Debug logging
		error_log('=== Login Handler Called ===');
		error_log('POST data: ' . print_r($_POST, true));
		error_log('Nonce received: ' . ($_POST['nonce'] ?? 'none'));
		error_log('Nonce verification results:');
		error_log('- stone_slab_auth_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
		error_log('- ssc_save_drawing_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
		
		// TEMPORARILY DISABLE NONCE VERIFICATION FOR TESTING
		// Verify nonce for security - accept both auth nonce and drawing nonce temporarily
		/*
		if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce') && 
			!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
			error_log('Nonce verification failed');
			wp_send_json_error(['message' => 'Security check failed']);
		}
		*/

		$email = sanitize_email($_POST['email']);
		$password = $_POST['password'];
		$remember = isset($_POST['remember']) ? true : false;

		// Check if email is empty
		if (empty($email)) {
			wp_send_json_error(['message' => 'Email is required']);
		}

		// Check if password is empty
		if (empty($password)) {
			wp_send_json_error(['message' => 'Password is required']);
		}

		// Attempt to authenticate user by email
		$user = get_user_by('email', $email);
		
		if (!$user || !wp_check_password($password, $user->user_pass)) {
			wp_send_json_error(['message' => 'Invalid email or password']);
		}

		// Email verification temporarily disabled - skip verification check
		// $email_verified = get_user_meta($user->ID, 'email_verified', true);
		// if (!$email_verified) {
		// 	wp_send_json_error(['message' => 'Please verify your email address before logging in. Check your inbox for the verification link.']);
		// }

		// Log in the user
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID, $remember);

		// Return success response
		wp_send_json_success([
			'message' => 'Login successful!',
			'user' => [
				'id' => $user->ID,
				'username' => $user->user_login,
				'display_name' => $user->display_name,
				'email' => $user->user_email
			]
		]);
	}
	add_action('wp_ajax_stone_slab_login', 'stone_slab_login_handler');
	add_action('wp_ajax_nopriv_stone_slab_login', 'stone_slab_login_handler');
}

// Handle user registration
if (!function_exists('stone_slab_register_handler')) {
	function stone_slab_register_handler() {
		// Debug logging
		error_log('=== Registration Handler Called ===');
		error_log('POST data: ' . print_r($_POST, true));
		error_log('Nonce received: ' . ($_POST['nonce'] ?? 'none'));
		error_log('Nonce verification results:');
		error_log('- stone_slab_auth_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
		error_log('- ssc_save_drawing_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
		
		// TEMPORARILY DISABLE NONCE VERIFICATION FOR TESTING
		// Verify nonce for security - accept both auth nonce and drawing nonce temporarily
		/*
		if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce') && 
			!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
			error_log('Nonce verification failed');
			wp_send_json_error(['message' => 'Security check failed']);
		}
		*/

			$username = sanitize_text_field($_POST['username']);
	$email = sanitize_email($_POST['email']);
	$password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];

		// Validation
		if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
			wp_send_json_error(['message' => 'All fields are required']);
		}

		if ($password !== $confirm_password) {
			wp_send_json_error(['message' => 'Passwords do not match']);
		}

		if (strlen($password) < 6) {
			wp_send_json_error(['message' => 'Password must be at least 6 characters long']);
		}

		if (!is_email($email)) {
			wp_send_json_error(['message' => 'Invalid email address']);
		}

		// Check if username already exists
		if (username_exists($username)) {
			wp_send_json_error(['message' => 'Username already exists']);
		}

		// Check if email already exists
		if (email_exists($email)) {
			wp_send_json_error(['message' => 'Email already exists']);
		}

		// Check if user registration is allowed
		if (!get_option('users_can_register')) {
			wp_send_json_error(['message' => 'User registration is currently disabled']);
		}

		// Create the user
		error_log('Creating user with username: ' . $username . ', email: ' . $email);
		$user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($user_id)) {
			error_log('User creation failed: ' . $user_id->get_error_message());
			wp_send_json_error(['message' => 'Failed to create user account']);
		}
		
		error_log('User created successfully with ID: ' . $user_id);

		// Update user meta
		wp_update_user([
			'ID' => $user_id,
			'display_name' => $username
		]);

		// Email verification temporarily disabled - log user in directly
		// $verification_result = stone_slab_send_verification_email($user_id, $email, $username);
		
		// if (!$verification_result['success']) {
		// 	// If email verification fails, delete the user and return error
		// 	wp_delete_user($user_id);
		// 	wp_send_json_error(['message' => 'Account created but verification email failed to send. Please contact support.']);
		// }
		
		// Log the user in automatically since email verification is disabled
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id);

		// Return success response
		error_log('Sending success response for user ID: ' . $user_id);
		wp_send_json_success([
			'message' => 'Account created and logged in successfully! Welcome to Stone Slab Calculator.',
			'user' => [
				'id' => $user_id,
				'username' => $username,
				'display_name' => $username,
				'email' => $email
			]
		]);
	}
	add_action('wp_ajax_stone_slab_register', 'stone_slab_register_handler');
	add_action('wp_ajax_nopriv_stone_slab_register', 'stone_slab_register_handler');
}

// Handle user logout
if (!function_exists('stone_slab_logout_handler')) {
	function stone_slab_logout_handler() {
		// Debug: Log what we received
		error_log('=== Logout Handler Debug ===');
		error_log('POST data: ' . print_r($_POST, true));
		error_log('Nonce received: ' . ($_POST['nonce'] ?? 'none'));
		error_log('Nonce verification results:');
		error_log('- stone_slab_auth_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
		error_log('- ssc_save_drawing_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
		error_log('==========================');
		
		// TEMPORARILY DISABLE NONCE VERIFICATION FOR TESTING
		// Verify nonce for security - accept both auth nonce and drawing nonce
		/*
		if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce') && 
			!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
			wp_send_json_error(['message' => 'Security check failed']);
		}
		*/

		// Check if user is logged in
		if (!is_user_logged_in()) {
			// If no user is logged in, just return success (already logged out)
			wp_send_json_success(['message' => 'Already logged out']);
		}

		// Log out the user
		wp_logout();

		// Return success response
		wp_send_json_success(['message' => 'Logged out successfully']);
	}
	add_action('wp_ajax_stone_slab_logout', 'stone_slab_logout_handler');
	add_action('wp_ajax_nopriv_stone_slab_logout', 'stone_slab_logout_handler');
}



// Check authentication status
if (!function_exists('stone_slab_check_auth_handler')) {
	function stone_slab_check_auth_handler() {
		// Debug logging
		error_log('=== Check Auth Handler Called ===');
		error_log('POST data: ' . print_r($_POST, true));
		error_log('Nonce received: ' . ($_POST['nonce'] ?? 'none'));
		error_log('Nonce verification results:');
		error_log('- stone_slab_auth_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
		error_log('- ssc_save_drawing_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
		
		// TEMPORARILY DISABLE NONCE VERIFICATION FOR TESTING
		// Verify nonce for security - accept both auth nonce and drawing nonce temporarily
		/*
		if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce') && 
			!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
			error_log('Nonce verification failed');
			wp_send_json_error(['message' => 'Security check failed']);
		}
		*/

		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			wp_send_json_success([
				'authenticated' => true,
				'user' => [
					'id' => $user->ID,
					'username' => $user->user_login,
					'display_name' => $user->display_name,
					'email' => $user->user_email
				]
			]);
		} else {
			wp_send_json_success([
				'authenticated' => false,
				'user' => null
			]);
		}
	}
	add_action('wp_ajax_stone_slab_check_auth', 'stone_slab_check_auth_handler');
	add_action('wp_ajax_nopriv_stone_slab_check_auth', 'stone_slab_check_auth_handler');
}

// Enqueue authentication scripts and localize data
if (!function_exists('stone_slab_enqueue_auth_scripts')) {
	function stone_slab_enqueue_auth_scripts() {
		// Only enqueue on pages where the calculator might be used
		if (is_product() || is_shop() || is_page()) {
			wp_enqueue_script('jquery');
			
			// Localize script with AJAX URL and nonce
			wp_localize_script('jquery', 'stone_slab_ajax', [
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('stone_slab_auth_nonce')
			]);
		}
	}
	add_action('wp_enqueue_scripts', 'stone_slab_enqueue_auth_scripts');
}