<?php

if (!function_exists('slab_calculator_settings_page')) {
    function slab_calculator_settings_page() {
        add_menu_page(
            'Slab Calculator Settings',
            'Slab Calculator Settings',
            'manage_options',
            'slab-calculator-settings',
            'slab_calculator_settings_page_html',
            'dashicons-admin-generic',
            25
        );
    }
    add_action('admin_menu', 'slab_calculator_settings_page');

// Add submenu for saved drawings
add_action('admin_menu', 'ssc_add_saved_drawings_menu');

function ssc_add_saved_drawings_menu() {
	add_submenu_page(
		'slab_calculator_settings',
		'Saved Drawings',
		'Saved Drawings',
		'manage_options',
		'ssc_saved_drawings',
		'ssc_saved_drawings_page'
	);
}
}

if (!function_exists('slab_calculator_settings_page_html')) {
    function slab_calculator_settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap">';
        echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
		if ( ! class_exists('WooCommerce') ) {
			echo '<h3 style="color:red">The Slab Calculator Settings Plugin requires the WooCommerce plugin to be installed and activated. Please install and activate WooCommerce to use this plugin properly.</h3>';
		}
        echo '<form method="post" action="options.php">';
        settings_fields('slab_calculator_settings_group');
        do_settings_sections('slab_calculator_settings');
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

if (!function_exists('slab_calculator_register_settings')) {
    function slab_calculator_register_settings() {
        register_setting('slab_calculator_settings_group', 'slab_calculator_access_type');
        register_setting('slab_calculator_settings_group', 'slab_calculator_visible_roles');
        register_setting('slab_calculator_settings_group', 'slab_calculator_edge_profiles');
		register_setting('slab_calculator_settings_group', 'slab_calculator_youtube_link');
        register_setting('slab_calculator_settings_group', 'slab_calculator_height');
        register_setting('slab_calculator_settings_group', 'slab_calculator_drawing_pad_height');
        register_setting('slab_calculator_settings_group', 'slab_calculator_drawing_pad_width');
		register_setting('slab_calculator_settings_group', 'slab_calculator_min_screen_size');
		register_setting('slab_calculator_settings_group', 'slab_calculator_email_template');

        add_settings_section(
            'slab_calculator_settings_section',
            '',
            '',
            'slab_calculator_settings'
        );

        add_settings_field(
            'slab_calculator_access_type',
            'Access Permissions',
            'slab_calculator_access_type_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
		
        add_settings_field(
            'slab_calculator_visible_roles',
            'Visible Roles',
            'slab_calculator_visible_roles_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );

        add_settings_field(
            'slab_calculator_edge_profiles',
            'Edge Profiles',
            'slab_calculator_edge_profiles_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
		
        add_settings_field(
            'slab_calculator_youtube_link',
            'YouTube Video Link',
            'slab_calculator_youtube_link_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );

        add_settings_field(
            'slab_calculator_height',
            'Calculator Height',
            'slab_calculator_height_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );

        add_settings_field(
            'slab_calculator_drawing_pad_size',
            'Drawing Pad Size',
            'slab_calculator_drawing_pad_size_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
		
		add_settings_field(
            'slab_calculator_min_screen_size',
            'Minimum Screen Size for Calculator (px)',
            'slab_calculator_min_screen_size_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );

        add_settings_field(
            		'slab_calculator_email_template',
		'Email Template',
		'slab_calculator_email_template_callback',
		'slab_calculator_settings',
		'slab_calculator_settings_section'
	);

    }
    add_action('admin_init', 'slab_calculator_register_settings');
}


if (!function_exists('slab_calculator_settings_section_callback')) {
    function slab_calculator_settings_section_callback() {
        echo '<p>Select which user roles can access the slab calculator settings.</p>';
    }
}


if (!function_exists('slab_calculator_access_type_callback')) {
    function slab_calculator_access_type_callback() {
        $access_type = get_option('slab_calculator_access_type', 'only_logged_in');
        ?>
		<label>
            <input type="radio" name="slab_calculator_access_type" value="everyone" <?php checked($access_type, 'everyone'); ?>>
            Everyone
        </label><br>
        <label>
            <input type="radio" name="slab_calculator_access_type" value="only_logged_in" <?php checked($access_type, 'only_logged_in'); ?>>
            All logged-in Users
        </label><br>
        <label>
            <input type="radio" name="slab_calculator_access_type" value="restricted_roles" <?php checked($access_type, 'restricted_roles'); ?>>
            Restricted to Admins and Authors
        </label>
        <?php
    }
}

 
if (!function_exists('slab_calculator_visible_roles_callback')) {
    function slab_calculator_visible_roles_callback() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $selected_roles = get_option('slab_calculator_visible_roles', '');
		
		if ( empty($selected_roles) ) {
			$selected_roles = array();
		}
		
        foreach ($roles as $role_slug => $role) {
            $checked = in_array($role_slug, $selected_roles) ? 'checked' : '';
            echo '<label><input type="checkbox" name="slab_calculator_visible_roles[]" value="' . esc_attr($role_slug) . '" ' . $checked . ' /> ' . esc_html($role['name']) . '</label><br />';
        }
    }
}


if (!function_exists('slab_calculator_edge_profiles_callback')) {
    function slab_calculator_edge_profiles_callback() {
        $edge_profiles = get_option('slab_calculator_edge_profiles', []);

        echo '<div id="edge-profiles-repeater">';
        if (!empty($edge_profiles)) {
			echo '<div><label style="display:inline-block;width:174px;margin-bottom:10px;font-weight:500">Title</label><label style="display:inline-block;width:174px;margin-bottom:10px;font-weight:500">Value(mm)</label></div>';
            foreach ($edge_profiles as $index => $profile) {
                echo '<div class="repeater-item" style="margin-bottom:10px;">';
                echo '<input type="text" name="slab_calculator_edge_profiles[' . $index . '][title]" placeholder="Title" value="' . esc_attr($profile['title']) . '" />';
                echo '<input type="text" name="slab_calculator_edge_profiles[' . $index . '][value]" placeholder="Value(mm)" value="' . esc_attr($profile['value']) . '" />';
                echo '<button type="button" class="button remove-repeater-item">Remove</button>';
                echo '</div>';
            }
        }

        echo '</div>';
        echo '<button type="button" class="button" id="add-edge-profile">Add Edge Profile</button>';

        ?>
		<style>
			.wp-core-ui .button.remove-repeater-item {
				background-color: #d63638;
				border-color: #d63638;
				color: #fff;
			}
		</style>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
				var slab_calculator_access_type = jQuery('input[name="slab_calculator_access_type"]:checked').val();
				if ( slab_calculator_access_type != 'restricted_roles' ) {
					jQuery('form[action="options.php"] table.form-table tbody > tr:nth-of-type(2)').hide();
				}
				
				$(document).on('change', 'input[name="slab_calculator_access_type"]', function() {
                    var slab_calculator_access_type = jQuery('input[name="slab_calculator_access_type"]:checked').val();
					if ( slab_calculator_access_type != 'restricted_roles' ) {
						jQuery('form[action="options.php"] table.form-table tbody > tr:nth-of-type(2)').hide();
					} else {
						jQuery('form[action="options.php"] table.form-table tbody > tr:nth-of-type(2)').show();
					}
                });
				
                $('#add-edge-profile').click(function() {
                    var index = $('#edge-profiles-repeater .repeater-item').length;
                    $('#edge-profiles-repeater').append(
                        '<div class="repeater-item" style="margin-bottom:10px;display:none;">' +
                        '<input type="text" name="slab_calculator_edge_profiles[' + index + '][title]" placeholder="Title" />' +
                        '<input type="text" name="slab_calculator_edge_profiles[' + index + '][value]" placeholder="Value(mm)" />' +
                        '<button type="button" class="button remove-repeater-item">Remove</button>' +
                        '</div>'
                    );
					
					$('#edge-profiles-repeater > .repeater-item:last-child').slideDown();
                });

                $(document).on('click', '.remove-repeater-item', function() {
                    $(this).closest('.repeater-item').slideUp(function(){
						$(this).remove();
					});
                });
				
				
            });
        </script>
        <?php
    }
}


if (!function_exists('slab_calculator_youtube_link_callback')) {
    function slab_calculator_youtube_link_callback() {
        $youtube_link = get_option('slab_calculator_youtube_link', '');
        echo '<input type="url" name="slab_calculator_youtube_link" value="' . esc_attr($youtube_link) . '" placeholder="Enter YouTube link" style="width: 348px" />';
    }
}


if (!function_exists('slab_calculator_height_callback')) {
    function slab_calculator_height_callback() {
        $height = get_option('slab_calculator_height', '');
        echo '<input type="number" name="slab_calculator_height" value="' . esc_attr($height) . '" placeholder="Enter height in mm" />';
    }
}


if (!function_exists('slab_calculator_drawing_pad_size_callback')) {
    function slab_calculator_drawing_pad_size_callback() {
        $drawing_pad_height = get_option('slab_calculator_drawing_pad_height', '');
        $drawing_pad_width = get_option('slab_calculator_drawing_pad_width', '');

        echo '<div style="display:inline-block"><label style="display:block;margin-bottom:10px;font-weight:500">Height in mm</label><input type="number" name="slab_calculator_drawing_pad_height" value="' . esc_attr($drawing_pad_height) . '" placeholder="Height in mm" /></div>';
        echo '<div style="display:inline-block"><label style="display:block;margin-bottom:10px;font-weight:500">Width in mm</label><input type="number" name="slab_calculator_drawing_pad_width" value="' . esc_attr($drawing_pad_width) . '" placeholder="Width in mm" /></div>';
    }
}


if (!function_exists('slab_calculator_min_screen_size_callback')) {
    function slab_calculator_min_screen_size_callback() {
        $min_screen_size = get_option('slab_calculator_min_screen_size', '');
        echo '<input type="number" name="slab_calculator_min_screen_size" value="' . esc_attr($min_screen_size) . '" placeholder="Enter size in pixels" />';
    }
}

if (!function_exists('slab_calculator_email_template_callback')) {
    function slab_calculator_email_template_callback() {
        // Default email template with dynamic fields
        $default_template = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Drawings & Calculations</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <p>Hi {{customer_name}},</p>
    
    <p>Thank you for choosing us! We\'re thrilled to be part of your project. Please find your project details and drawing calculations below.</p>
    
    <h3 style="margin-top: 20px; color: #333;">Project Details</h3>
    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Slab Name:</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{slab_name}}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Total Cutting MM:</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{total_cutting_mm}} mm</td>
        </tr>
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Standard Cutting Area:</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{only_cut_mm}} mm</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Mitred Cutting Area:</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{mitred_cut_mm}} mm</td>
        </tr>
    </table>

    <h3 style="margin-top: 20px; color: #333;">Cost Breakdown</h3>
    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Slab Cost:</td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{slab_cost}}</td>
        </tr>
    </table>

    <div style="margin: 20px 0; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">
        <h4 style="margin: 0 0 10px 0; color: #155724;">📋 Your Drawing</h4>
        <p style="margin: 0; color: #155724;">
            <a href="{{drawing_link}}" style="color: #155724; text-decoration: underline;">View Your Drawing Online</a><br>
            <em>A PDF copy of your drawing is also attached to this email.</em>
        </p>
    </div>

    <h3 style="margin-top: 20px; color: #333;">Next Steps</h3>
    <ul style="list-style-type: disc; margin-left: 20px;">
        <li><strong>Quote Request:</strong> To receive a detailed quote or invoice, please email us at <a href="mailto:welcome@bambystone.com.au" style="color: #007bff;">welcome@bambystone.com.au</a> with this email and attached drawings.</li>
        <li><strong>Payment:</strong> Full payment is required before we can proceed with your project.</li>
        <li><strong>Pricing:</strong> All prices are available online and subject to change.</li>
        <li><strong>Stock Holds:</strong> Due to high demand, we are unable to place holds on stock.</li>
    </ul>

    <p>We look forward to working with you on this project!</p>

    <p>Warm regards,</p>
    <p>
        <strong>Adele Anderson</strong> | Customer Relations Representative<br>
        Phone: <a href="tel:1300536120" style="color: #007bff;">1300 536 120</a><br>
        Email: <a href="mailto:welcome@bambystone.com.au" style="color: #007bff;">welcome@bambystone.com.au</a><br>
        Website: <a href="https://www.bambystone.com.au" style="color: #007bff;">www.bambystone.com.au</a><br>
        Address: Unit 6, 8 Technology Drive, Arundel QLD, Australia 4214
    </p>

    <hr style="border: none; border-top: 1px solid #ccc; margin: 20px 0;">
    <p style="font-size: 0.85em; color: #555;">DISCLAIMER: This email is intended for the recipient only. If received in error, please notify us and delete it immediately. Bamby Stone does not guarantee the integrity or error-free nature of this communication.</p>
</body>
</html>';

        $email_template = get_option('slab_calculator_email_template', $default_template);
        
        echo '<div style="margin-bottom: 15px;">';
        echo '<p><strong>Available Dynamic Fields:</strong></p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><code>{{customer_name}}</code> - Customer name (extracted from user account or input)</li>';
        echo '<li><code>{{slab_name}}</code> - Slab name (from the calculator drawing)</li>';
        echo '<li><code>{{total_cutting_mm}}</code> - Total cutting MM (sum of all cutting areas)</li>';
        echo '<li><code>{{only_cut_mm}}</code> - Standard cutting area in millimeters</li>';
        echo '<li><code>{{mitred_cut_mm}}</code> - Mitred cutting area in millimeters</li>';
        echo '<li><code>{{drawing_link}}</code> - Link to view the drawing online</li>';
        echo '<li><code>{{slab_cost}}</code> - Cost of the slab from WooCommerce product</li>';
        echo '</ul>';
        echo '<div style="margin-top: 15px; padding: 12px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">';
        echo '<h4 style="margin: 0 0 8px 0; color: #856404;">📎 PDF Attachment</h4>';
        echo '<p style="margin: 0; color: #856404;">PDF with the drawing is automatically generated and attached to every email sent through the calculator.</p>';
        echo '</div>';
        echo '</div>';
        
        wp_editor($email_template, 'slab_calculator_email_template', array(
            'textarea_name' => 'slab_calculator_email_template',
            'textarea_rows' => 20,
            'media_buttons' => true,
            'teeny' => false,
            'tinymce' => array(
                'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,forecolor,backcolor,removeformat,code,fullscreen',
                'toolbar2' => ''
            )
        ));
    }
}



if (!function_exists('slab_calculator_check_user_access')) {
	function slab_calculator_check_user_access() {
		$access_type = get_option('slab_calculator_access_type', 'everyone');
		
		if ( $access_type === 'everyone' ) {
			return true;
		} else if ( is_user_logged_in() ) {
			if ($access_type === 'only_logged_in') {
				return true;
			} else if ($access_type === 'restricted_roles') {
				$visible_roles = get_option('slab_calculator_visible_roles', '');
				if ( !empty($visible_roles) && is_array($visible_roles) && count($visible_roles) > 0 ) {
					$user = wp_get_current_user();
					foreach ($visible_roles as $role) {
						if (in_array($role, (array)$user->roles)) {
							return true;
						}
					}
				}
				return false;
			}
		} else {
			return false;
		}
	}
}

// Saved Drawings Page
function ssc_saved_drawings_page() {
	// Check if user has permission
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	
	// Handle drawing deletion
	if (isset($_POST['delete_drawing']) && isset($_POST['drawing_id'])) {
		$drawing_id = intval($_POST['drawing_id']);
		if (wp_verify_nonce($_POST['_wpnonce'], 'delete_drawing_' . $drawing_id)) {
			// Include the main plugin file to access the delete function
			require_once(plugin_dir_path(__FILE__) . '../stone-slab-calculator.php');
			if (ssc_delete_drawing($drawing_id)) {
				echo '<div class="notice notice-success"><p>Drawing deleted successfully!</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>Failed to delete drawing.</p></div>';
			}
		}
	}
	
	// Get all drawings
	global $wpdb;
	$table_name = $wpdb->prefix . 'ssc_drawings';
	$drawings = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);
	
	echo '<div class="wrap">';
	echo '<h1>Saved Drawings</h1>';
	echo '<p>View and manage all saved drawings and PDFs from the calculator.</p>';
	
	if (empty($drawings)) {
		echo '<div class="notice notice-info"><p>No drawings have been saved yet.</p></div>';
	} else {
		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>Customer</th>';
		echo '<th>Slab Name</th>';
		echo '<th>Total Cutting (mm)</th>';
		echo '<th>Standard Cut (mm)</th>';
		echo '<th>Mitred Cut (mm)</th>';
		echo '<th>Slab Cost</th>';
		echo '<th>Created</th>';
		echo '<th>Actions</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		
		foreach ($drawings as $drawing) {
			$user = get_user_by('id', $drawing['user_id']);
			$customer_name = $user ? $user->display_name : 'Unknown User';
			
			echo '<tr>';
			echo '<td>' . esc_html($customer_name) . '</td>';
			echo '<td>' . esc_html($drawing['slab_name']) . '</td>';
			echo '<td>' . esc_html($drawing['total_cutting_mm']) . '</td>';
			echo '<td>' . esc_html($drawing['only_cut_mm']) . '</td>';
			echo '<td>' . esc_html($drawing['mitred_cut_mm']) . '</td>';
			echo '<td>' . esc_html($drawing['slab_cost']) . '</td>';
			echo '<td>' . esc_html(date('M j, Y g:i A', strtotime($drawing['created_at']))) . '</td>';
			echo '<td>';
			
			// View PDF link
			$pdf_url = home_url('/ssc-pdf/' . $drawing['pdf_file_path']);
			echo '<a href="' . esc_url($pdf_url) . '" target="_blank" class="button button-small">View PDF</a> ';
			
			// Download PDF link
			echo '<a href="' . esc_url($pdf_url) . '" download class="button button-small">Download</a> ';
			
			// Delete button
			echo '<form method="post" style="display:inline;">';
			echo wp_nonce_field('delete_drawing_' . $drawing['id'], '_wpnonce', true, false);
			echo '<input type="hidden" name="drawing_id" value="' . $drawing['id'] . '">';
			echo '<input type="submit" name="delete_drawing" value="Delete" class="button button-small button-link-delete" onclick="return confirm(\'Are you sure you want to delete this drawing?\')">';
			echo '</form>';
			
			echo '</td>';
			echo '</tr>';
		}
		
		echo '</tbody>';
		echo '</table>';
	}
	
	echo '</div>';
}
