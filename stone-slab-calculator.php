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
			iframePath += "?name=<?=$product->get_name()?>&slab_width=<?=$dimensions['width']?>&slab_height=<?=$dimensions['height']?>&pad_width=<?=$drawing_pad_width?>&pad_height=<?=$drawing_pad_height?>&edges="+edgeProfiles+"&site_url=<?=urlencode(site_url())?>";
			
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
		$upload_dir = wp_upload_dir();
		$target_path = $upload_dir['path'] . '/' . $pdf_file['name'];

		// Move the uploaded file
		if (!move_uploaded_file($pdf_file['tmp_name'], $target_path)) {
			wp_send_json_error(['message' => 'Failed to save PDF.']);
		}
		
		// Get additional data from POST
		$slab_name = sanitize_text_field($_POST['slab_name'] ?? 'Custom Slab');
		$total_cutting_mm = sanitize_text_field($_POST['total_cutting_mm'] ?? '0');
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
			'{{drawing_link}}' => $drawing_link
		);
		
		$html_content = str_replace(array_keys($replacements), array_values($replacements), $html_content);

		// Email headers
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: Bamby Stone <welcome@bambystone.com.au>');

		// Send the email
		$sent = wp_mail($email, 'Project Proposal and Quote', $html_content, $headers, [$target_path]);

		if ($sent) {
			wp_send_json_success(['message' => 'Email sent successfully!']);
		} else {
			wp_send_json_error(['message' => 'Failed to send email.']);
		}
		
		// Cleanup
		@unlink($target_path);
	}
	
	add_action('wp_ajax_send_html_email', 'handle_send_html_email');
	add_action('wp_ajax_nopriv_send_html_email', 'handle_send_html_email');
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