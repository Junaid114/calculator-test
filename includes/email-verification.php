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
    
    
    
    // Send email
    $sent = wp_mail($email, $subject, $message, $headers);
    
    
    
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
    // Verify nonce - accept both auth nonce and drawing nonce temporarily
    if (!wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce') && 
        !wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_die(json_encode(array('success' => false, 'message' => 'Security check failed')));
    }
    
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
    
    
    
    wp_die(json_encode($result));
}
// Enable resend verification for production
add_action('wp_ajax_stone_slab_resend_verification', 'stone_slab_resend_verification_ajax');
add_action('wp_ajax_nopriv_stone_slab_resend_verification', 'stone_slab_resend_verification_ajax');

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
// Debug test function removed for production

// Admin menu for email testing removed for production

// Email test page function removed for production

// Email configuration check function removed for production

// Manual verification function removed for production

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
