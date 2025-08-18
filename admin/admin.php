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
}

// Add submenu for saved drawings
function ssc_add_saved_drawings_menu() {
	add_submenu_page(
		'slab-calculator-settings',
		'Saved Drawings',
		'Saved Drawings',
		'manage_options',
		'ssc_saved_drawings',
		'ssc_saved_drawings_page'
	);
}

// Register the main menu
add_action('admin_menu', 'slab_calculator_settings_page');

// Register the submenu
add_action('admin_menu', 'ssc_add_saved_drawings_menu');

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
        register_setting('slab_calculator_settings_group', 'slab_calculator_internal_cc_email');
        register_setting('slab_calculator_settings_group', 'ssc_public_quote_access');
        
        // New PDF Template Settings
        register_setting('slab_calculator_settings_group', 'ssc_pdf_template_cover');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_template_body');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_template_footer');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_logo');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_name');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_address');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_phone');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_email');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_company_website');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_export_quality');
        register_setting('slab_calculator_settings_group', 'ssc_pdf_page_size');
        
        // Production and Installation Cost Settings
        register_setting('slab_calculator_settings_group', 'ssc_production_cost_standard');
        register_setting('slab_calculator_settings_group', 'ssc_production_cost_mitred');
        register_setting('slab_calculator_settings_group', 'ssc_installation_cost');
        
        // Canvas Lock Settings
        register_setting('slab_calculator_settings_group', 'ssc_disable_drawing_after_submission');

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
        
        // PDF Template Settings Section
        add_settings_field(
            'ssc_pdf_template_cover',
            'PDF Cover Template',
            'ssc_pdf_template_cover_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'ssc_pdf_template_body',
            'PDF Body Template',
            'ssc_pdf_template_body_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'ssc_pdf_template_footer',
            'PDF Footer Template',
            'ssc_pdf_template_footer_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        // Company Information Settings
        add_settings_field(
            'ssc_pdf_company_info',
            'Company Information',
            'ssc_pdf_company_info_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        // PDF Export Settings
        add_settings_field(
            'ssc_pdf_export_settings',
            'PDF Export Settings',
            'ssc_pdf_export_settings_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        // Production and Installation Cost Settings
        add_settings_field(
            'ssc_production_cost_standard',
            'Production Cost - Standard Cut',
            'ssc_production_cost_standard_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'ssc_production_cost_mitred',
            'Production Cost - Mitred Cut',
            'ssc_production_cost_mitred_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'ssc_installation_cost',
            'Installation Cost',
            'ssc_installation_cost_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'slab_calculator_internal_cc_email',
            'Internal CC Email',
            'slab_calculator_internal_cc_email_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        add_settings_field(
            'ssc_public_quote_access',
            'Public Quote Access',
            'ssc_public_quote_access_callback',
            'slab_calculator_settings',
            'slab_calculator_settings_section'
        );
        
        // Canvas Lock After Submission Setting
        add_settings_field(
            'ssc_disable_drawing_after_submission',
            'Disable Drawing After Submission',
            'ssc_disable_drawing_after_submission_callback',
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

// PDF Template Callback Functions
if (!function_exists('ssc_pdf_template_cover_callback')) {
    function ssc_pdf_template_cover_callback() {
        $cover_template = get_option('ssc_pdf_template_cover', '');
        if (empty($cover_template)) {
            $cover_template = '{{company_logo}}
{{company_name}}
{{company_address}}
{{company_phone}}
{{company_email}}
{{company_website}}

PROJECT QUOTE
{{drawing_name}}

Date: {{current_date}}
Quote ID: {{quote_id}}';
        }
        
        echo '<div style="margin-bottom: 15px;">';
        echo '<p><strong>Available Dynamic Fields for Cover:</strong></p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><code>{{company_logo}}</code> - Company logo image</li>';
        echo '<li><code>{{company_name}}</code> - Company name</li>';
        echo '<li><code>{{company_address}}</code> - Company address</li>';
        echo '<li><code>{{company_phone}}</code> - Company phone</li>';
        echo '<li><code>{{company_email}}</code> - Company email</li>';
        echo '<li><code>{{company_website}}</code> - Company website</li>';
        echo '<li><code>{{drawing_name}}</code> - Drawing/project name</li>';
        echo '<li><code>{{current_date}}</code> - Current date</li>';
        echo '<li><code>{{quote_id}}</code> - Unique quote ID</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<textarea name="ssc_pdf_template_cover" rows="10" style="width: 100%; font-family: monospace; font-size: 12px;">' . esc_textarea($cover_template) . '</textarea>';
        echo '<p class="description">Template for the PDF cover page. Use the dynamic fields above to customize the content.</p>';
    }
}

if (!function_exists('ssc_pdf_template_body_callback')) {
    function ssc_pdf_template_body_callback() {
        $body_template = get_option('ssc_pdf_template_body', '');
        if (empty($body_template)) {
            $body_template = 'PROJECT DETAILS

Drawing Name: {{drawing_name}}
Total Cutting Area: {{total_cutting_mm}} mm
Standard Cutting Area: {{only_cut_mm}} mm
Mitred Cutting Area: {{mitred_cut_mm}} mm
Slab Cost: {{slab_cost}}

{{drawing_image}}

NOTES
{{drawing_notes}}

CALCULATIONS
Total Cutting Required: {{total_cutting_mm}} mm
Standard Cuts: {{only_cut_mm}} mm
Mitred Cuts: {{mitred_cut_mm}} mm
Total Cost: {{slab_cost}}';
        }
        
        echo '<div style="margin-bottom: 15px;">';
        echo '<p><strong>Available Dynamic Fields for Body:</strong></p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><code>{{drawing_name}}</code> - Drawing/project name</li>';
        echo '<li><code>{{total_cutting_mm}}</code> - Total cutting area in mm</li>';
        echo '<li><code>{{only_cut_mm}}</code> - Standard cutting area in mm</li>';
        echo '<li><code>{{mitred_cut_mm}}</code> - Mitred cutting area in mm</li>';
        echo '<li><code>{{slab_cost}}</code> - Cost of the slab</li>';
        echo '<li><code>{{drawing_image}}</code> - The actual drawing image</li>';
        echo '<li><code>{{drawing_notes}}</code> - User notes about the drawing</li>';
        echo '<li><code>{{current_date}}</code> - Current date</li>';
        echo '<li><code>{{quote_id}}</code> - Unique quote ID</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<textarea name="ssc_pdf_template_body" rows="15" style="width: 100%; font-family: monospace; font-size: 12px;">' . esc_textarea($body_template) . '</textarea>';
        echo '<p class="description">Template for the main body of the PDF. The drawing image will be automatically inserted where {{drawing_image}} is placed.</p>';
    }
}

if (!function_exists('ssc_pdf_template_footer_callback')) {
    function ssc_pdf_template_footer_callback() {
        $footer_template = get_option('ssc_pdf_template_footer', '');
        if (empty($footer_template)) {
            $footer_template = 'Thank you for choosing {{company_name}}!

For questions or to proceed with this quote, please contact us:
Phone: {{company_phone}}
Email: {{company_email}}
Website: {{company_website}}

{{company_address}}

Quote ID: {{quote_id}} | Generated on: {{current_date}}';
        }
        
        echo '<div style="margin-bottom: 15px;">';
        echo '<p><strong>Available Dynamic Fields for Footer:</strong></p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><code>{{company_name}}</code> - Company name</li>';
        echo '<li><code>{{company_phone}}</code> - Company phone</li>';
        echo '<li><code>{{company_email}}</code> - Company email</li>';
        echo '<li><code>{{company_website}}</code> - Company website</li>';
        echo '<li><code>{{company_address}}</code> - Company address</li>';
        echo '<li><code>{{quote_id}}</code> - Unique quote ID</li>';
        echo '<li><code>{{current_date}}</code> - Current date</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<textarea name="ssc_pdf_template_footer" rows="5" style="width: 100%; font-family: monospace; font-size: 12px;">' . esc_textarea($footer_template) . '</textarea>';
        echo '<p class="description">Template for the PDF footer. This will appear at the bottom of each page.</p>';
    }
}

if (!function_exists('ssc_pdf_company_info_callback')) {
    function ssc_pdf_company_info_callback() {
        $company_logo = get_option('ssc_pdf_company_logo', '');
        $company_name = get_option('ssc_pdf_company_name', 'Bamby Stone');
        $company_address = get_option('ssc_pdf_company_address', 'Unit 6, 8 Technology Drive, Arundel QLD, Australia 4214');
        $company_phone = get_option('ssc_pdf_company_phone', '1300 536 120');
        $company_email = get_option('ssc_pdf_company_email', 'welcome@bambystone.com.au');
        $company_website = get_option('ssc_pdf_company_website', 'https://www.bambystone.com.au');

        echo '<div style="margin-bottom: 15px;">';
        echo '<h4>Company Information for PDF Templates</h4>';
        echo '<p class="description">This information will be used in the PDF templates above. Leave empty to use default values.</p>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_logo" style="margin-right:10px;font-weight:500;min-width:150px;">Company Logo (URL):</label>';
        echo '<input type="url" id="ssc_pdf_company_logo" name="ssc_pdf_company_logo" value="' . esc_attr($company_logo) . '" style="width: 300px;" placeholder="https://example.com/logo.png" />';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_name" style="margin-right:10px;font-weight:500;min-width:150px;">Company Name:</label>';
        echo '<input type="text" id="ssc_pdf_company_name" name="ssc_pdf_company_name" value="' . esc_attr($company_name) . '" style="width: 300px;" />';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_address" style="margin-right:10px;font-weight:500;min-width:150px;">Company Address:</label>';
        echo '<textarea id="ssc_pdf_company_address" name="ssc_pdf_company_address" rows="2" style="width: 300px;">' . esc_textarea($company_address) . '</textarea>';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_phone" style="margin-right:10px;font-weight:500;min-width:150px;">Company Phone:</label>';
        echo '<input type="text" id="ssc_pdf_company_phone" name="ssc_pdf_company_phone" value="' . esc_attr($company_phone) . '" style="width: 300px;" />';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_email" style="margin-right:10px;font-weight:500;min-width:150px;">Company Email:</label>';
        echo '<input type="email" id="ssc_pdf_company_email" name="ssc_pdf_company_email" value="' . esc_attr($company_email) . '" style="width: 300px;" />';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_company_website" style="margin-right:10px;font-weight:500;min-width:150px;">Company Website:</label>';
        echo '<input type="url" id="ssc_pdf_company_website" name="ssc_pdf_company_website" value="' . esc_attr($company_website) . '" style="width: 300px;" />';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('ssc_pdf_export_settings_callback')) {
    function ssc_pdf_export_settings_callback() {
        $export_quality = get_option('ssc_pdf_export_quality', 'high');
        $page_size = get_option('ssc_pdf_page_size', 'A3');

        echo '<div style="margin-bottom: 15px;">';
        echo '<h4>PDF Export Settings</h4>';
        echo '<p class="description">Configure the quality and size of exported PDFs.</p>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_export_quality" style="margin-right:10px;font-weight:500;min-width:150px;">Export Quality:</label>';
        echo '<select id="ssc_pdf_export_quality" name="ssc_pdf_export_quality">';
        echo '<option value="low" ' . selected($export_quality, 'low', false) . '>Low (Smaller file, faster generation)</option>';
        echo '<option value="medium" ' . selected($export_quality, 'medium', false) . '>Medium (Balanced quality and size)</option>';
        echo '<option value="high" ' . selected($export_quality, 'high', false) . '>High (Best quality, larger file)</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div style="display:flex;align-items:center;margin-bottom:10px;">';
        echo '<label for="ssc_pdf_page_size" style="margin-right:10px;font-weight:500;min-width:150px;">Page Size:</label>';
        echo '<select id="ssc_pdf_page_size" name="ssc_pdf_page_size">';
        echo '<option value="A4" ' . selected($page_size, 'A4', false) . '>A4 (210×297mm) - Standard</option>';
        echo '<option value="A3" ' . selected($page_size, 'A3', false) . '>A3 (297×420mm) - Large format</option>';
        echo '<option value="Letter" ' . selected($page_size, 'Letter', false) . '>Letter (216×279mm) - US Standard</option>';
        echo '<option value="Legal" ' . selected($page_size, 'Legal', false) . '>Legal (216×356mm) - US Legal</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div style="background: #e7f3fa; border-left: 4px solid #00a0d2; padding: 10px; margin-top: 15px;">';
        echo '<strong>💡 Tip:</strong> A3 size is recommended for detailed drawings and professional presentations.';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('ssc_production_cost_standard_callback')) {
    function ssc_production_cost_standard_callback() {
        $standard_cost = get_option('ssc_production_cost_standard', '0.00');
        ?>
        <input type="number" name="ssc_production_cost_standard" value="<?php echo esc_attr($standard_cost); ?>" step="0.01" min="0" />
        <p class="description">Cost per square meter for standard cutting.</p>
        <?php
    }
}

if (!function_exists('ssc_production_cost_mitred_callback')) {
    function ssc_production_cost_mitred_callback() {
        $mitred_cost = get_option('ssc_production_cost_mitred', '0.00');
        ?>
        <input type="number" name="ssc_production_cost_mitred" value="<?php echo esc_attr($mitred_cost); ?>" step="0.01" min="0" />
        <p class="description">Cost per square meter for mitred cutting.</p>
        <?php
    }
}

if (!function_exists('ssc_installation_cost_callback')) {
    function ssc_installation_cost_callback() {
        $installation_cost = get_option('ssc_installation_cost', '0.00');
        ?>
        <input type="number" name="ssc_installation_cost" value="<?php echo esc_attr($installation_cost); ?>" step="0.01" min="0" />
        <p class="description">Cost of installation per square meter.</p>
        <?php
    }
}

if (!function_exists('slab_calculator_internal_cc_email_callback')) {
	function slab_calculator_internal_cc_email_callback() {
		$cc_email = get_option('slab_calculator_internal_cc_email', '');
		?>
		<input type="email" 
			   name="slab_calculator_internal_cc_email" 
			   value="<?php echo esc_attr($cc_email); ?>" 
			   class="regular-text" 
			   placeholder="admin@example.com" />
		<p class="description">
			Enter an email address to receive copies of all quote emails sent to customers. 
			Leave empty to disable internal CC notifications.
		</p>
		<?php
	}
}

if (!function_exists('ssc_public_quote_access_callback')) {
	function ssc_public_quote_access_callback() {
		$public_access = get_option('ssc_public_quote_access', 'no');
		?>
		<select name="ssc_public_quote_access">
			<option value="no" <?php selected($public_access, 'no'); ?>>No - Only logged-in users and admins</option>
			<option value="yes" <?php selected($public_access, 'yes'); ?>>Yes - Allow public access to shared quotes</option>
		</select>
		<p class="description">
			When enabled, anyone with a direct quote link can view the PDF without logging in. 
			This is useful for sharing quotes with clients, but may expose quote information publicly.
		</p>
		<?php
	}
}

// Canvas Lock After Submission Callback
if (!function_exists('ssc_disable_drawing_after_submission_callback')) {
	function ssc_disable_drawing_after_submission_callback() {
		$disable_drawing = get_option('ssc_disable_drawing_after_submission', 'no');
		?>
		<select name="ssc_disable_drawing_after_submission">
			<option value="no" <?php selected($disable_drawing, 'no'); ?>>No - Allow continued drawing after submission</option>
			<option value="yes" <?php selected($disable_drawing, 'yes'); ?>>Yes - Lock canvas after submission</option>
		</select>
		<p class="description">
			When enabled, the drawing canvas will be locked after a quote is submitted, 
			showing a success message with option to start a new drawing. This prevents 
			accidental modifications to submitted quotes.
		</p>
		<?php
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
			// require_once(plugin_dir_path(__FILE__) . '../stone-slab-calculator.php'); // REMOVED - Circular dependency
			if (ssc_delete_drawing($drawing_id)) {
				echo '<div class="notice notice-success"><p>Drawing deleted successfully!</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>Failed to delete drawing.</p></div>';
			}
		}
	}
	
	echo '<div class="wrap">';
	echo '<h1>Saved Drawings</h1>';
	echo '<p>View and manage all saved drawings and PDFs from the calculator.</p>';
	
	// Add filter controls
	echo '<div class="ssc-admin-filters">';
	echo '<h3>Filter Drawings</h3>';
	echo '<div class="filter-grid">';
	
	// Quote ID filter
	echo '<div class="filter-item">';
	echo '<label for="filter_quote_id">Quote ID:</label>';
	echo '<input type="text" id="filter_quote_id" placeholder="Enter Quote ID">';
	echo '</div>';
	
	// User ID filter
	echo '<div class="filter-item">';
	echo '<label for="filter_user_id">User ID:</label>';
	echo '<input type="text" id="filter_user_id" placeholder="Enter User ID">';
	echo '</div>';
	
	// Full Name filter
	echo '<div class="filter-item">';
	echo '<label for="filter_full_name">Full Name:</label>';
	echo '<input type="text" id="filter_full_name" placeholder="Enter Full Name">';
	echo '</div>';
	
	// Email filter
	echo '<div class="filter-item">';
	echo '<label for="filter_email">Email:</label>';
	echo '<input type="email" id="filter_email" placeholder="Enter Email">';
	echo '</div>';
	
	// Product filter
	echo '<div class="filter-item">';
	echo '<label for="filter_product">Product/Slab Name:</label>';
	echo '<input type="text" id="filter_product" placeholder="Enter Product Name">';
	echo '</div>';
	
	// Date range filters
	echo '<div class="filter-item">';
	echo '<label for="filter_date_from">Date From:</label>';
	echo '<input type="date" id="filter_date_from">';
	echo '</div>';
	
	echo '<div class="filter-item">';
	echo '<label for="filter_date_to">Date To:</label>';
	echo '<input type="date" id="filter_date_to">';
	echo '</div>';
	
	echo '</div>';
	
	// Filter buttons
	echo '<div class="filter-buttons">';
	echo '<button type="button" id="apply_filters" class="button button-primary">Apply Filters</button>';
	echo '<button type="button" id="clear_filters" class="button">Clear Filters</button>';
	echo '</div>';
	
			// Add helpful hints
		echo '<div class="filter-hints">';
		echo '<strong>Tips:</strong> Use any combination of filters. Press Enter in any field to apply filters. Use Ctrl+F to focus on filters.';
		echo '</div>';
		
		// Add CC email status
		$cc_email = get_option('slab_calculator_internal_cc_email', '');
		if (!empty($cc_email)) {
			echo '<div class="filter-hints" style="background: #d4edda; border-left-color: #28a745; margin-top: 10px;">';
			echo '<strong>📧 Internal CC Active:</strong> All quote emails will be copied to ' . esc_html($cc_email);
			echo '</div>';
		}
	echo '</div>';
	
	// Results table container
	echo '<div id="ssc-results-container">';
	echo '<div id="ssc-loading">';
	echo '<p>Loading...</p>';
	echo '</div>';
	echo '<div id="ssc-results-table"></div>';
	echo '</div>';
	
	echo '</div>';
	
	// Add JavaScript for filtering
	echo '<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Load initial data
		loadFilteredDrawings();
		
		// Apply filters button
		$("#apply_filters").on("click", function() {
			loadFilteredDrawings();
		});
		
		// Clear filters button
		$("#clear_filters").on("click", function() {
			$("#filter_quote_id").val("");
			$("#filter_user_id").val("");
			$("#filter_full_name").val("");
			$("#filter_email").val("");
			$("#filter_product").val("");
			$("#filter_date_from").val("");
			$("#filter_date_to").val("");
			loadFilteredDrawings();
		});
		
		// Apply filters when Enter is pressed in any filter field
		$(".filter-item input").on("keypress", function(e) {
			if (e.which === 13) { // Enter key
				loadFilteredDrawings();
			}
		});
		
		// Auto-apply filters after a short delay when typing (for better UX)
		var filterTimeout;
		$(".filter-item input").on("input", function() {
			clearTimeout(filterTimeout);
			filterTimeout = setTimeout(function() {
				loadFilteredDrawings();
			}, 500); // 500ms delay
		});
		
		// Add keyboard shortcuts
		$(document).on("keydown", function(e) {
			// Ctrl/Cmd + F to focus on first filter
			if ((e.ctrlKey || e.metaKey) && e.key === "f") {
				e.preventDefault();
				$("#filter_quote_id").focus();
			}
		});
		
		function loadFilteredDrawings() {
			$("#ssc-loading").addClass("show");
			$("#ssc-results-table").hide();
			
			var filterData = {
				action: "ssc_admin_filter_drawings",
				_wpnonce: "' . wp_create_nonce('ssc_admin_filter_nonce') . '",
				quote_id: $("#filter_quote_id").val(),
				user_id: $("#filter_user_id").val(),
				full_name: $("#filter_full_name").val(),
				email: $("#filter_email").val(),
				product: $("#filter_product").val(),
				date_from: $("#filter_date_from").val(),
				date_to: $("#filter_date_to").val()
			};
			
			$.post(ajaxurl, filterData, function(response) {
				$("#ssc-loading").removeClass("show");
				if (response.success) {
					displayFilteredResults(response.data);
				} else {
					$("#ssc-results-table").html("<div class=\"notice notice-error\"><p>Error loading drawings: " + response.data + "</p></div>");
					$("#ssc-results-table").show();
				}
			}).fail(function() {
				$("#ssc-loading").removeClass("show");
				$("#ssc-results-table").html("<div class=\"notice notice-error\"><p>Failed to load drawings. Please try again.</p></div>");
				$("#ssc-results-table").show();
			});
		}
		
		function displayFilteredResults(drawings) {
			if (drawings.length === 0) {
				$("#ssc-results-table").html("<div class=\"notice notice-info\"><p>No drawings found matching the selected filters.</p></div>");
				$("#ssc-results-table").show();
				return;
			}
			
			// Calculate summary statistics
			var totalCutting = 0;
			var totalCost = 0;
			var validDrawings = 0;
			
			drawings.forEach(function(drawing) {
				if (drawing.total_cutting_mm && !isNaN(parseFloat(drawing.total_cutting_mm))) {
					totalCutting += parseFloat(drawing.total_cutting_mm);
					validDrawings++;
				}
				if (drawing.slab_cost && drawing.slab_cost !== "N/A") {
					var cost = parseFloat(drawing.slab_cost.replace(/[^0-9.-]+/g, ""));
					if (!isNaN(cost)) {
						totalCost += cost;
					}
				}
			});
			
			var avgCutting = validDrawings > 0 ? (totalCutting / validDrawings).toFixed(2) : 0;
			
			// Display summary statistics
			var summaryHtml = "<div class=\"ssc-summary-stats\">";
			summaryHtml += "<h4>Summary Statistics</h4>";
			summaryHtml += "<div class=\"stats-grid\">";
			summaryHtml += "<div class=\"stat-item\"><strong>Total Drawings</strong><div class=\"stat-value\">" + drawings.length + "</div></div>";
			summaryHtml += "<div class=\"stat-item\"><strong>Total Cutting</strong><div class=\"stat-value\">" + totalCutting.toFixed(2) + " mm</div></div>";
			summaryHtml += "<div class=\"stat-item\"><strong>Average Cutting</strong><div class=\"stat-value\">" + avgCutting + " mm</div></div>";
			summaryHtml += "<div class=\"stat-item\"><strong>Total Cost</strong><div class=\"stat-value\">$" + totalCost.toFixed(2) + "</div></div>";
			summaryHtml += "</div></div>";
			
			var tableHtml = summaryHtml + "<table class=\"wp-list-table widefat fixed striped\">";
			tableHtml += "<thead><tr>";
			tableHtml += "<th>Quote ID</th>";
			tableHtml += "<th>Customer</th>";
			tableHtml += "<th>Email</th>";
			tableHtml += "<th>Slab Name</th>";
			tableHtml += "<th>Total Cutting (mm)</th>";
			tableHtml += "<th>Standard Cut (mm)</th>";
			tableHtml += "<th>Mitred Cut (mm)</th>";
			tableHtml += "<th>Slab Cost</th>";
			tableHtml += "<th>Created</th>";
			tableHtml += "<th>Actions</th>";
			tableHtml += "</tr></thead><tbody>";
			
			drawings.forEach(function(drawing) {
				var customerName = drawing.display_name || "Unknown User";
				var email = drawing.user_email || "N/A";
				
				tableHtml += "<tr>";
				tableHtml += "<td>" + drawing.id + "</td>";
				tableHtml += "<td>" + customerName + "</td>";
				tableHtml += "<td>" + email + "</td>";
				tableHtml += "<td>" + (drawing.drawing_name || drawing.slab_name || "N/A") + "</td>";
				tableHtml += "<td>" + (drawing.total_cutting_mm || "N/A") + "</td>";
				tableHtml += "<td>" + (drawing.only_cut_mm || "N/A") + "</td>";
				tableHtml += "<td>" + (drawing.mitred_cut_mm || "N/A") + "</td>";
				tableHtml += "<td>" + (drawing.slab_cost || "N/A") + "</td>";
				tableHtml += "<td>" + formatDate(drawing.created_at) + "</td>";
				tableHtml += "<td>";
				
				// Generate direct quote link
				var directQuoteUrl = "' . home_url('/slab-calculator/quote/') . '" + drawing.id + "-" + drawing.user_id + "-" + (drawing.drawing_name || drawing.slab_name || "product").replace(/[^a-zA-Z0-9]/g, "-") + "-" + new Date(drawing.created_at).toISOString().split(\"T\")[0] + ".pdf";
				
				// View PDF link
				var pdfUrl = "<?php echo home_url('/ssc-pdf/'); ?>" + drawing.pdf_file_path;
				tableHtml += "<a href=\"" + pdfUrl + "\" target=\"_blank\" class=\"button button-small\">View PDF</a> ";
				
				// Download PDF link
				tableHtml += "<a href=\"" + pdfUrl + "\" download class=\"button button-small\">Download</a> ";
				
				// Direct Quote Link
				tableHtml += "<a href=\"" + directQuoteUrl + "\" target=\"_blank\" class=\"button button-small\" style=\"background: #007cba; color: white;\">Direct Link</a> ";
				
				// Delete button
				tableHtml += "<form method=\"post\" style=\"display:inline;\">";
				tableHtml += "' . wp_nonce_field('delete_drawing_', '_wpnonce', true, false) . '";
				tableHtml += "<input type=\"hidden\" name=\"drawing_id\" value=\"" + drawing.id + "\">";
				tableHtml += "<input type=\"submit\" name=\"delete_drawing\" value=\"Delete\" class=\"button button-small button-link-delete\" onclick=\"return confirm(\\\"Are you sure you want to delete this drawing?\\\")\">";
				tableHtml += "</form>";
				
				tableHtml += "</td>";
				tableHtml += "</tr>";
			});
			
			tableHtml += "</tbody></table>";
			tableHtml += "<p><strong>Total Results: " + drawings.length + "</strong></p>";
			
			$("#ssc-results-table").html(tableHtml);
			$("#ssc-results-table").show();
		}
		
		function formatDate(dateString) {
			if (!dateString) return "N/A";
			var date = new Date(dateString);
			return date.toLocaleDateString("en-US", {
				year: "numeric",
				month: "short",
				day: "numeric",
				hour: "2-digit",
				minute: "2-digit"
			});
		}
		
	});
	</script>';
}

// AJAX handler for admin filtering
add_action('wp_ajax_ssc_admin_filter_drawings', 'ssc_admin_filter_drawings');

// Enqueue admin styles
add_action('admin_enqueue_scripts', 'ssc_admin_enqueue_styles');

function ssc_admin_enqueue_styles($hook) {
    if (strpos($hook, 'ssc_saved_drawings') !== false) {
        wp_enqueue_style('ssc-admin-styles', plugin_dir_url(__FILE__) . 'css/admin-styles.css', array(), '1.0.0');
    }
}

function ssc_admin_filter_drawings() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['_wpnonce'], 'ssc_admin_filter_nonce')) {
        wp_die(__('Security check failed.'));
    }
    
    // Get filter parameters
    $quote_id = isset($_POST['quote_id']) ? sanitize_text_field($_POST['quote_id']) : '';
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : '';
    $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $product = isset($_POST['product']) ? sanitize_text_field($_POST['product']) : '';
    $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
    $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'ssc_drawings';
    $users_table = $wpdb->prefix . 'users';
    
    // Build the query with filters
    $where_conditions = array();
    $query_params = array();
    
    if (!empty($quote_id)) {
        $where_conditions[] = "d.id = %d";
        $query_params[] = intval($quote_id);
    }
    
    if (!empty($user_id)) {
        $where_conditions[] = "d.user_id = %d";
        $query_params[] = $user_id;
    }
    
    if (!empty($full_name)) {
        $where_conditions[] = "u.display_name LIKE %s";
        $query_params[] = '%' . $wpdb->esc_like($full_name) . '%';
    }
    
    if (!empty($email)) {
        $where_conditions[] = "u.user_email LIKE %s";
        $query_params[] = '%' . $wpdb->esc_like($email) . '%';
    }
    
    if (!empty($product)) {
        $where_conditions[] = "d.drawing_name LIKE %s";
        $query_params[] = '%' . $wpdb->esc_like($product) . '%';
    }
    
    if (!empty($date_from)) {
        $where_conditions[] = "DATE(d.created_at) >= %s";
        $query_params[] = $date_from;
    }
    
    if (!empty($date_to)) {
        $where_conditions[] = "DATE(d.created_at) <= %s";
        $query_params[] = $date_to;
    }
    
    // Build the WHERE clause
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Prepare the query
    $query = "SELECT d.*, u.display_name, u.user_email 
              FROM $table_name d 
              LEFT JOIN $users_table u ON d.user_id = u.ID 
              $where_clause 
              ORDER BY d.created_at DESC";
    
    if (!empty($query_params)) {
        $query = $wpdb->prepare($query, $query_params);
    }
    
    $drawings = $wpdb->get_results($query, ARRAY_A);
    
    if ($drawings !== false) {
        wp_send_json_success($drawings);
    } else {
        wp_send_json_error('Failed to get filtered drawings: ' . $wpdb->last_error);
    }
}
