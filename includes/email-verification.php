<?php
/**
 * Email Verification Handler for Stone Slab Calculator
 * 
 * This file handles email verification functionality including:
 * - Sending verification emails
 * - Verifying email tokens
 * - Resending verification emails
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send verification email to user after registration
 */
// VERIFICATION EMAIL TEMPORARILY DISABLED
/*
function stone_slab_send_verification_email($user_id, $email, $username) {
    // Generate verification token
    $token = wp_generate_password(32, false);
    
    // Store token in user meta with expiration (24 hours)
    $expiration = time() + (24 * 60 * 60);
    update_user_meta($user_id, 'email_verification_token', $token);
    update_user_meta($user_id, 'email_verification_expires', $expiration);
    update_user_meta($user_id, 'email_verified', false);
    
    // Create verification link
    $verification_url = add_query_arg(array(
        'action' => 'verify_email',
        'token' => $token,
        'user_id' => $user_id
    ), home_url());
    
    // Email subject and content
    $subject = 'Verify Your Email - Stone Slab Calculator';
    $message = "
    <html>
    <body>
        <h2>Welcome to Stone Slab Calculator!</h2>
        <p>Hi {$username},</p>
        <p>Thank you for registering with Stone Slab Calculator. To complete your registration and access the calculator, please verify your email address by clicking the link below:</p>
        <p><a href='{$verification_url}' style='background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: disabled-block;'>Verify Email Address</a></p>
        <p>Or copy and paste this link into your browser:</p>
        <p>{$verification_url}</p>
        <p>This link will expire in 24 hours.</p>
        <p>If you didn't create an account with us, please ignore this email.</p>
        <p>Best regards,<br>Stone Slab Calculator Team</p>
    </body>
    </html>
    ";
    
    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Stone Slab Calculator <noreply@' . $_SERVER['HTTP_HOST'] . '>'
    );
    
    // Debug logging before sending
    error_log('Stone Slab Calculator: Attempting to send verification email to ' . $email);
    error_log('Stone Slab Calculator: Verification URL: ' . $verification_url);
    
    // Send email
    $sent = wp_mail($email, $subject, $message, $headers);
    
    // Debug logging after sending
    if (!$sent) {
        error_log('Stone Slab Calculator: Failed to send verification email to ' . $email);
        if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
            error_log('Stone Slab Calculator: wp_mail error details - ' . print_r($GLOBALS['phpmailer']->ErrorInfo, true));
        }
    } else {
        error_log('Stone Slab Calculator: Verification email sent successfully to ' . $email);
    }
    
    if ($sent) {
        return array('success' => true, 'message' => 'Verification email sent successfully');
    } else {
        return array('success' => false, 'message' => 'Failed to send verification email. Please check your email settings.');
    }
}
*/

/**
 * Verify email token from verification link
 */
function stone_slab_verify_email() {
    if (isset($_GET['action']) && $_GET['action'] === 'verify_email') {
        $token = sanitize_text_field($_GET['token']);
        $user_id = intval($_GET['user_id']);
        
        if (empty($token) || empty($user_id)) {
            wp_die('Invalid verification link');
        }
        
        // Get stored token and expiration
        $stored_token = get_user_meta($user_id, 'email_verification_token', true);
        $expiration = get_user_meta($user_id, 'email_verification_expires', true);
        
        if (empty($stored_token) || empty($expiration)) {
            wp_die('Verification link has expired or is invalid');
        }
        
        // Check if token matches and hasn't expired
        if ($token === $stored_token && time() < $expiration) {
            // Mark email as verified
            update_user_meta($user_id, 'email_verified', true);
            delete_user_meta($user_id, 'email_verification_token');
            delete_user_meta($user_id, 'email_verification_expires');
            
            // Show success message
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Email Verified - Stone Slab Calculator</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f8f9fa; }
                    .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .success-icon { color: #28a745; font-size: 48px; margin-bottom: 20px; }
                    h1 { color: #28a745; margin-bottom: 20px; }
                    p { color: #6c757d; line-height: 1.6; margin-bottom: 20px; }
                    .btn { background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px; }
                    .btn:hover { background: #5a6fd8; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='success-icon'>✓</div>
                    <h1>Email Verified Successfully!</h1>
                    <p>Your email address has been verified. You can now login to your account and access the Stone Slab Calculator.</p>
                    <a href='" . home_url() . "' class='btn'>Go to Calculator</a>
                </div>
            </body>
            </html>
            ";
            exit;
        } else {
            wp_die('Verification link has expired or is invalid');
        }
    }
}
add_action('init', 'stone_slab_verify_email');

/**
 * AJAX handler for resending verification email
 */
function stone_slab_resend_verification_ajax() {
    // Verify nonce - TEMPORARILY DISABLED FOR TESTING
    // if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_nonce')) {
    //     wp_die(json_encode(array('success' => false, 'message' => 'Security check failed')));
    // }
    
    $email = sanitize_email($_POST['email']);
    
    if (empty($email)) {
        wp_die(json_encode(array('success' => false, 'message' => 'Email is required')));
    }
    
    // Find user by email
    $user = get_user_by('email', $email);
    
    if (!$user) {
        wp_die(json_encode(array('success' => false, 'message' => 'User not found')));
    }
    
    // Check if email is already verified
    if (get_user_meta($user->ID, 'email_verified', true)) {
        wp_die(json_encode(array('success' => false, 'message' => 'Email is already verified')));
    }
    
    // Send verification email
    $result = stone_slab_send_verification_email($user->ID, $email, $user->user_login);
    
    // Debug logging
    error_log('Stone Slab Calculator: Resend verification attempt for user ' . $user->ID . ' with email ' . $email);
    error_log('Stone Slab Calculator: Resend result - ' . print_r($result, true));
    
    wp_die(json_encode($result));
}
// VERIFICATION EMAIL TEMPORARILY DISABLED
// add_action('wp_ajax_stone_slab_resend_verification', 'stone_slab_resend_verification_ajax');
// add_action('wp_ajax_nopriv_stone_slab_resend_verification', 'stone_slab_resend_verification_ajax');

/**
 * Modify login to check email verification
 */
// VERIFICATION EMAIL TEMPORARILY DISABLED
/*
function stone_slab_check_email_verification($user, $username, $password) {
    // Only check for users logging in with email
    if (is_email($username)) {
        $user = get_user_by('email', $user_id);
        if ($user) {
            $email_verified = get_user_meta($user->ID, 'email_verified', true);
            if (!$email_verified) {
                return new WP_Error('email_not_verified', 'Please verify your email address before logging in. Check your inbox for the verification link.');
            }
        }
    }
    
    return $user;
}
*/
// VERIFICATION EMAIL TEMPORARILY DISABLED
// add_filter('authenticate', 'stone_slab_check_email_verification', 30, 3);

/**
 * Modify registration to send verification email instead of auto-login
 */
// VERIFICATION EMAIL TEMPORARILY DISABLED
/*
function stone_slab_modify_registration($user_id) {
    $user = get_user_by('id', $user_id);
    if ($user) {
        // Send verification email
        stone_slab_send_verification_email($user_id, $user->user_email, $user->user_login);
        
        // Don't auto-login the user
        wp_set_current_user(0);
        wp_set_auth_cookie(0);
    }
}
*/
// VERIFICATION EMAIL TEMPORARILY DISABLED
// add_action('user_register', 'stone_slab_modify_registration');

/**
 * Check if user's email is verified
 */
// VERIFICATION EMAIL TEMPORARILY DISABLED
/*
function stone_slab_is_email_verified($user_id) {
    return get_user_meta($user_id, 'email_verified', true) ? true : false;
}
*/

/**
 * Test email functionality with detailed debugging
 */
function stone_slab_test_email_detailed() {
    if (current_user_can('manage_options')) {
        $test_email = get_option('admin_email');
        $subject = 'Test Email - Stone Slab Calculator - ' . date('Y-m-d H:i:s');
        $message = '<p>This is a test email to verify that the email system is working properly.</p>';
        $message .= '<p>Sent at: ' . date('Y-m-d H:i:s') . '</p>';
        $message .= '<p>Site URL: ' . get_option('siteurl') . '</p>';
        $message .= '<p>Admin Email: ' . $test_email . '</p>';
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Stone Slab Calculator <noreply@' . $_SERVER['HTTP_HOST'] . '>'
        );
        
        // Check WordPress email configuration
        $wp_mail_errors = array();
        
        // Test 1: Basic wp_mail
        $sent = wp_mail($test_email, $subject, $message, $headers);
        
        // Test 2: Check for PHPMailer errors
        if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
            $phpmailer = $GLOBALS['phpmailer'];
            if (isset($phpmailer->ErrorInfo)) {
                $wp_mail_errors[] = 'PHPMailer Error: ' . $phpmailer->ErrorInfo;
            }
        }
        
        // Test 3: Check WordPress email settings
        $wp_mail_errors[] = 'WordPress Admin Email: ' . get_option('admin_email');
        $wp_mail_errors[] = 'Site URL: ' . get_option('siteurl');
        $wp_mail_errors[] = 'WordPress Version: ' . get_bloginfo('version');
        
        // Test 4: Check if SMTP is configured
        if (defined('SMTP_HOST')) {
            $wp_mail_errors[] = 'SMTP Host: ' . SMTP_HOST;
            $wp_mail_errors[] = 'SMTP Port: ' . (defined('SMTP_PORT') ? SMTP_PORT : 'Not set');
            $wp_mail_errors[] = 'SMTP Username: ' . (defined('SMTP_USER') ? 'Set' : 'Not set');
            $wp_mail_errors[] = 'SMTP Password: ' . (defined('SMTP_PASS') ? 'Set' : 'Not set');
        } else {
            $wp_mail_errors[] = 'SMTP: Not configured (using default WordPress mail)';
        }
        
        // Test 5: Check server mail configuration
        $wp_mail_errors[] = 'PHP mail() function: ' . (function_exists('mail') ? 'Available' : 'Not available');
        $wp_mail_errors[] = 'Server Software: ' . $_SERVER['SERVER_SOFTWARE'];
        
        if ($sent) {
            echo '<div class="notice notice-success"><p><strong>✅ Test email sent successfully to:</strong> ' . $test_email . '</p></div>';
            echo '<div class="notice notice-info"><p><strong>📧 Email Details:</strong></p><ul>';
            foreach ($wp_mail_errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        } else {
            echo '<div class="notice notice-error"><p><strong>❌ Failed to send test email to:</strong> ' . $test_email . '</p></div>';
            echo '<div class="notice notice-error"><p><strong>🔍 Debug Information:</strong></p><ul>';
            foreach ($wp_mail_errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
            
            // Additional debugging
            echo '<div class="notice notice-warning"><p><strong>💡 Troubleshooting Tips:</strong></p>';
            echo '<ul>';
            echo '<li>Check if your hosting provider allows sending emails</li>';
            echo '<li>Verify SMTP settings if using SMTP</li>';
            echo '<li>Check WordPress email plugins (like WP Mail SMTP)</li>';
            echo '<li>Contact your hosting provider about email restrictions</li>';
            echo '</ul></div>';
        }
    }
}

/**
 * Add admin menu for email testing
 */
function stone_slab_add_admin_menu() {
    add_submenu_page(
        'tools.php',
        'Email Verification Test',
        'Email Test',
        'manage_options',
        'stone-slab-email-test',
        'stone_slab_email_test_page'
    );
}
add_action('admin_menu', 'stone_slab_add_admin_menu');

/**
 * Email test page content
 */
function stone_slab_email_test_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    echo '<div class="wrap">';
    echo '<h1>Stone Slab Calculator - Email Verification Test</h1>';
    
    // Show email configuration
    stone_slab_check_email_config();
    
    // Test email functionality
    if (isset($_POST['test_email'])) {
        stone_slab_test_email_detailed();
    }
    
    // Manual verification email
    if (isset($_POST['send_verification']) && isset($_POST['user_email'])) {
        $user_email = sanitize_email($_POST['user_email']);
        $user = get_user_by('email', $user_email);
        
        if ($user) {
            $result = stone_slab_manual_verification_email($user->ID);
            if ($result['success']) {
                echo '<div class="notice notice-success"><p>Verification email sent successfully to: ' . $user_email . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Failed to send verification email: ' . $result['message'] . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>User with email ' . $user_email . ' not found.</p></div>';
        }
    }
    
    echo '<form method="post" style="margin: 20px 0;">';
    echo '<h3>Test Email System</h3>';
    echo '<p>Click the button below to send a test email to the admin email address:</p>';
    echo '<input type="submit" name="test_email" value="Send Test Email" class="button button-primary">';
    echo '</form>';
    
    echo '<form method="post" style="margin: 20px 0;">';
    echo '<h3>Send Manual Verification Email</h3>';
    echo '<p>Enter a user email address to manually send a verification email:</p>';
    echo '<input type="email" name="user_email" placeholder="user@example.com" required style="width: 300px; margin-right: 10px;">';
    echo '<input type="submit" name="send_verification" value="Send Verification Email" class="button button-secondary">';
    echo '</form>';
    
    echo '</div>';
}

/**
 * Add email configuration check
 */
function stone_slab_check_email_config() {
    if (current_user_can('manage_options')) {
        $admin_email = get_option('admin_email');
        $site_url = get_option('siteurl');
        
        echo '<div class="notice notice-info"><p><strong>Email Configuration:</strong></p>';
        echo '<p>Admin Email: ' . $admin_email . '</p>';
        echo '<p>Site URL: ' . $site_url . '</p>';
        echo '<p>SMTP Status: ' . (defined('SMTP_HOST') ? 'Configured' : 'Not configured') . '</p>';
        echo '</div>';
    }
}

/**
 * Manually send verification email for existing user
 */
function stone_slab_manual_verification_email($user_id) {
    if (current_user_can('manage_options')) {
        $user = get_user_by('id', $user_id);
        if ($user) {
            $result = stone_slab_send_verification_email($user_id, $user->user_email, $user->user_login);
            return $result;
        }
        return array('success' => false, 'message' => 'User not found');
    }
    return array('success' => false, 'message' => 'Insufficient permissions');
}

/**
 * Add admin notice for email verification status
 */
function stone_slab_admin_notices() {
    if (current_user_can('manage_options')) {
        $unverified_users = get_users(array(
            'meta_key' => 'email_verified',
            'meta_value' => false,
            'count_total' => true
        ));
        
        if ($unverified_users > 0) {
            echo '<div class="notice notice-warning"><p><strong>Stone Slab Calculator:</strong> There are ' . $unverified_users . ' users with unverified email addresses.</p></div>';
        }
    }
}
add_action('admin_notices', 'stone_slab_admin_notices');
