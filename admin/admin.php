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
    
    <p>Thank you for choosing us! We\'re thrilled to be part of your project. To receive a detailed quote or invoice, please email us at <a href="mailto:welcome@bambystone.com.au" style="color: #007bff;">welcome@bambystone.com.au</a>, including this email with the attached drawings.</p>
    
    <h3 style="margin-top: 20px; color: #333;">Project Details</h3>
    <ul style="list-style-type: disc; margin-left: 20px;">
        <li><strong>Slab Name:</strong> {{slab_name}}</li>
        <li><strong>Total Cutting Area:</strong> {{total_cutting_mm}} mm²</li>
        <li><strong>Drawing Link:</strong> <a href="{{drawing_link}}" style="color: #007bff;">View Drawing</a></li>
    </ul>

    <h3 style="margin-top: 20px; color: #333;">Payment and Next Steps</h3>
    <ul style="list-style-type: disc; margin-left: 20px;">
        <li><strong>Holds:</strong> Unfortunately, due to high demand and frequent changes, we are unable to place holds on stock.</li>
        <li><strong>Payment:</strong> Full payment is required before we can proceed.</li>
        <li><strong>Prices:</strong> All our prices are available online and are subject to change.</li>
        <li><strong>Drawings:</strong> Your drawings, based on the entered sizes, are available in the attached PDF.</li>
    </ul>

    <p>We look forward to hearing your feedback!</p>

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
        echo '<ul style="margin-left: 20px; columns: 2;">';
        echo '<li><code>{{customer_name}}</code> - Customer name (from user account)</li>';
        echo '<li><code>{{slab_name}}</code> - Slab name (from drawing)</li>';
        echo '<li><code>{{total_cutting_mm}}</code> - Total cutting area in mm²</li>';
        echo '<li><code>{{drawing_link}}</code> - Link to the drawing (if applicable)</li>';
        echo '</ul>';
        echo '<p style="margin-top: 10px;"><em>Note: PDF attachment is automatically included with emails.</em></p>';
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
