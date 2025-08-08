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
