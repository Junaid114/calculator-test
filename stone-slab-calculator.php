<?php
/*
Plugin Name:  Stone Slab Calculator
Plugin URI:   #
Description:  A calculator to calculate the number of stone slabs required for projects based on input dimensions.
Version:      1.0
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
	// EMAIL VERIFICATION TEMPORARILY DISABLED
	// require_once( SSC_PLUGIN_DIR . 'includes/email-verification.php');


// Activation hook to create database tables
register_activation_hook(__FILE__, 'ssc_activate_plugin');

function ssc_activate_plugin() {
    global $wpdb;
    
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
        mitred_cut_mm decimal(10,2) NOT NULL,
        slab_cost varchar(50) NOT NULL,
        drawing_data longtext NOT NULL,
        pdf_file_path varchar(500) NOT NULL,
        drawing_link varchar(500) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Function to save drawing to database
function ssc_save_drawing($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    // Get user ID from data or current user
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    // Prepare data for insertion
    $insert_data = array(
        'user_id' => $user_id,
        'drawing_name' => sanitize_text_field($data['drawing_name']),
        'drawing_notes' => sanitize_textarea_field($data['drawing_notes']),
        'total_cutting_mm' => floatval($data['total_cutting_mm']),
        'only_cut_mm' => floatval($data['only_cut_mm']),
        'mitred_cut_mm' => floatval($data['mitred_cut_mm']),
        'slab_cost' => sanitize_text_field($data['slab_cost']),
        'drawing_data' => wp_kses_post($data['drawing_data']),
        'pdf_file_path' => sanitize_text_field($data['pdf_file_path']),
        'drawing_link' => esc_url_raw($data['drawing_link']),
        'created_at' => current_time('mysql')
    );
    
    $result = $wpdb->insert($table_name, $insert_data);
    
    if ($result === false) {
        return false;
    }
    
    return $wpdb->insert_id;
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

// Function to delete a drawing
function ssc_delete_drawing($drawing_id) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ssc_drawings';
    
    // Get the drawing first to delete the PDF file
    $drawing = ssc_get_drawing($drawing_id);
    if ($drawing && !empty($drawing['pdf_file_path'])) {
        $pdf_path = SSC_PLUGIN_DIR . 'uploads/' . basename($drawing['pdf_file_path']);
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
    // Check nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    
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
    
    // Check if PDF file was uploaded
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $error_message = 'PDF file upload failed';
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
    
    // Validate file type
    $allowed_types = array('application/pdf');
    $file_type = mime_content_type($pdf_file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        wp_send_json_error('Invalid file type. Only PDF files are allowed.');
    }
    
    // Additional security: check file extension
    $file_extension = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));
    if ($file_extension !== 'pdf') {
        wp_send_json_error('Invalid file extension. Only .pdf files are allowed.');
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = SSC_PLUGIN_DIR . 'uploads/';
    if (!file_exists($upload_dir)) {
        wp_mkdir_p($upload_dir);
    }
    
    // Generate unique filename
    $filename = 'drawing_' . time() . '_' . wp_generate_password(8, false) . '.pdf';
    $file_path = $upload_dir . $filename;
    
    // Move uploaded file to uploads directory
    if (!move_uploaded_file($pdf_file['tmp_name'], $file_path)) {
        wp_send_json_error('Failed to save PDF file to server');
    }
    
    // Set file permissions
    chmod($file_path, 0644);
    
    // Add PDF file path to POST data
    $_POST['pdf_file_path'] = $filename;
    
    // Add user_id to POST data
    $_POST['user_id'] = $user_id;
    
    // Save drawing to database
    $drawing_id = ssc_save_drawing($_POST);
    
    if ($drawing_id) {
        wp_send_json_success(array(
            'drawing_id' => $drawing_id,
            'pdf_filename' => $filename,
            'message' => 'Drawing and PDF saved successfully'
        ));
    } else {
        // If database save failed, delete the uploaded file
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        wp_send_json_error('Failed to save drawing to database');
    }
}

// AJAX handler for getting user drawings
add_action('wp_ajax_ssc_get_drawings', 'ssc_ajax_get_drawings');
add_action('wp_ajax_nopriv_ssc_get_drawings', 'ssc_ajax_get_drawings');

function ssc_ajax_get_drawings() {
    // Check nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    
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
    
    $drawings = ssc_get_user_drawings($user_id);
    
    if ($drawings !== false) {
        wp_send_json_success($drawings);
    } else {
        wp_send_json_error('Failed to get drawings');
    }
}

// AJAX handler for deleting drawing
add_action('wp_ajax_ssc_delete_drawing', 'ssc_ajax_delete_drawing');
add_action('wp_ajax_nopriv_ssc_delete_drawing', 'ssc_ajax_delete_drawing');

// AJAX handler for downloading PDF
add_action('wp_ajax_ssc_download_pdf', 'ssc_ajax_download_pdf');
add_action('wp_ajax_nopriv_ssc_download_pdf', 'ssc_ajax_download_pdf');

// AJAX handler for viewing PDF
add_action('wp_ajax_ssc_view_pdf', 'ssc_ajax_view_pdf');
add_action('wp_ajax_nopriv_ssc_view_pdf', 'ssc_ajax_view_pdf');

function ssc_ajax_delete_drawing() {
    // Check nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    
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
    // Check nonce for security
    if (!wp_verify_nonce($_GET['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    
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
    
    // Get drawing to verify user access
    $drawing = ssc_get_drawing_by_pdf($pdf_filename);
    if (!$drawing || $drawing['user_id'] != $user_id) {
        wp_die('Access denied');
    }
    
    $file_path = SSC_PLUGIN_DIR . 'uploads/' . $pdf_filename;
    
    if (!file_exists($file_path)) {
        wp_die('PDF file not found');
    }
    
    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $pdf_filename . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    // Output file content
    readfile($file_path);
    exit;
}

// Function to handle PDF viewing
function ssc_ajax_view_pdf() {
    // Check nonce for security
    if (!wp_verify_nonce($_GET['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die('Security check failed');
    }
    
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
    
    // Get drawing to verify user access
    $drawing = ssc_get_drawing_by_pdf($pdf_filename);
    if (!$drawing || $drawing['user_id'] != $user_id) {
        wp_die('Access denied');
    }
    
    $file_path = SSC_PLUGIN_DIR . 'uploads/' . $pdf_filename;
    
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
			iframePath += "?name=<?=$product->get_name()?>&slab_width=<?=$dimensions['width']?>&slab_height=<?=$dimensions['height']?>&pad_width=<?=$drawing_pad_width?>&pad_height=<?=$drawing_pad_height?>&edges="+edgeProfiles+"&site_url=<?=urlencode(site_url())?>&nonce=<?=wp_create_nonce('ssc_save_drawing_nonce')?>";
			
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
		
		// Create uploads directory if it doesn't exist
		$uploads_dir = SSC_PLUGIN_DIR . 'uploads/';
		if (!file_exists($uploads_dir)) {
			wp_mkdir_p($uploads_dir);
		}
		
		// Generate unique filename for PDF
		$pdf_filename = 'drawing_' . time() . '_' . sanitize_file_name($pdf_file['name']);
		$target_path = $uploads_dir . $pdf_filename;

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
		
		$replacements = array(
			'{{customer_name}}' => $customer_name,
			'{{slab_name}}' => $slab_name,
			'{{total_cutting_mm}}' => number_format($total_cutting_mm),
			'{{only_cut_mm}}' => number_format($only_cut_mm),
			'{{mitred_cut_mm}}' => number_format($mitred_cut_mm),
			'{{slab_cost}}' => $slab_cost,
			'{{drawing_link}}' => $drawing_link
		);
		
		$html_content = str_replace(array_keys($replacements), array_values($replacements), $html_content);

		// Email headers
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Bamby Stone <welcome@bambystone.com.au>');

		// Send the email
		$sent = wp_mail($email, 'Project Proposal and Quote', $html_content, $headers, [$target_path]);

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
	
	// Add rewrite rule for serving PDF files
	add_action('init', 'ssc_add_rewrite_rules');
	add_action('template_redirect', 'ssc_serve_pdf');
	
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
			$file_path = SSC_PLUGIN_DIR . 'uploads/' . $filename;
			
			if (file_exists($file_path) && is_user_logged_in()) {
				// Check if user has access to this file
				$drawing = ssc_get_drawing_by_pdf($filename);
				if ($drawing && $drawing['user_id'] == get_current_user_id()) {
					header('Content-Type: application/pdf');
					header('Content-Disposition: inline; filename="' . $filename . '"');
					header('Content-Length: ' . filesize($file_path));
					readfile($file_path);
					exit;
				}
			}
			
			wp_die('File not found or access denied');
		}
	}
	
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
}

// Authentication System Functions

// Handle user login
if (!function_exists('stone_slab_login_handler')) {
	function stone_slab_login_handler() {
		// Verify nonce for security - TEMPORARILY DISABLED FOR TESTING
		// if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce')) {
		// 	wp_send_json_error(['message' => 'Security check failed']);
		// }

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

		// EMAIL VERIFICATION TEMPORARILY DISABLED
		// Check if email is verified
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
		// Verify nonce for security - TEMPORARILY DISABLED FOR TESTING
		// if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce')) {
		// 	wp_send_json_error(['message' => 'Security check failed']);
		// }

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
		$user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($user_id)) {
			wp_send_json_error(['message' => 'Failed to create user account']);
		}

		// Update user meta
		wp_update_user([
			'ID' => $user_id,
			'display_name' => $username
		]);

		// EMAIL VERIFICATION TEMPORARILY DISABLED
		// Don't log in the user automatically - they need to verify email first
		// The email verification will be handled by the email-verification.php file

		// Return success response
		wp_send_json_success([
			'message' => 'Account created successfully! You can now login to your account.',
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
		// Verify nonce for security - TEMPORARILY DISABLED FOR TESTING
		// if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce')) {
		// 	wp_send_json_error(['message' => 'Security check failed']);
		// }

		// Check if user is logged in
		if (!is_user_logged_in()) {
			wp_send_json_error(['message' => 'No user is currently logged in']);
		}

		// Log out the user
		wp_logout();

		// Return success response
		wp_send_json_success(['message' => 'Logged out successfully']);
	}
	add_action('wp_ajax_stone_slab_logout', 'stone_slab_logout_handler');
}

// Check authentication status
if (!function_exists('stone_slab_check_auth_handler')) {
	function stone_slab_check_auth_handler() {
		// Verify nonce for security - TEMPORARILY DISABLED FOR TESTING
		// if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce')) {
		// 	wp_send_json_error(['message' => 'Security check failed']);
		// }

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