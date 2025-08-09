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
			iframePath += "?name=<?=$product->get_name()?>&slab_width=<?=$dimensions['width']?>&slab_heigth=<?=$dimensions['height']?>&pad_width=<?=$drawing_pad_width?>&pad_heigth=<?=$drawing_pad_height?>&edges="+edgeProfiles;
			
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
		$customer_name = $user->display_name ?: $user->user_login;
		
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