<?php
/**
 * Simple Email Test Script for Stone Slab Calculator
 * Place this file in your WordPress root directory and access it via browser
 * to test if emails are working
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required.');
}

echo '<h1>Stone Slab Calculator - Email Test</h1>';

// Test 1: Check WordPress email configuration
echo '<h2>WordPress Email Configuration</h2>';
echo '<ul>';
echo '<li>Admin Email: ' . get_option('admin_email') . '</li>';
echo '<li>Site URL: ' . get_option('siteurl') . '</li>';
echo '<li>WordPress Version: ' . get_bloginfo('version') . '</li>';
echo '</ul>';

// Test 2: Check server configuration
echo '<h2>Server Configuration</h2>';
echo '<ul>';
echo '<li>PHP Version: ' . phpversion() . '</li>';
echo '<li>Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
echo '<li>PHP mail() function: ' . (function_exists('mail') ? 'Available' : 'Not available') . '</li>';
echo '</ul>';

// Test 3: Check SMTP configuration
echo '<h2>SMTP Configuration</h2>';
echo '<ul>';
if (defined('SMTP_HOST')) {
    echo '<li>SMTP Host: ' . SMTP_HOST . '</li>';
    echo '<li>SMTP Port: ' . (defined('SMTP_PORT') ? SMTP_PORT : 'Not set') . '</li>';
    echo '<li>SMTP Username: ' . (defined('SMTP_USER') ? 'Set' : 'Not set') . '</li>';
    echo '<li>SMTP Password: ' . (defined('SMTP_PASS') ? 'Set' : 'Not set') . '</li>';
} else {
    echo '<li>SMTP: Not configured (using default WordPress mail)</li>';
}
echo '</ul>';

// Test 4: Send test email
if (isset($_POST['send_test'])) {
    echo '<h2>Test Email Results</h2>';
    
    $test_email = get_option('admin_email');
    $subject = 'Test Email - Stone Slab Calculator - ' . date('Y-m-d H:i:s');
    $message = '<p>This is a test email to verify that the email system is working properly.</p>';
    $message .= '<p>Sent at: ' . date('Y-m-d H:i:s') . '</p>';
    $message .= '<p>Site URL: ' . get_option('siteurl') . '</p>';
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Stone Slab Calculator <noreply@' . $_SERVER['HTTP_HOST'] . '>'
    );
    
    $sent = wp_mail($test_email, $subject, $message, $headers);
    
    if ($sent) {
        echo '<div style="color: green; padding: 10px; border: 1px solid green; margin: 10px 0;">';
        echo '<strong>✅ Test email sent successfully to:</strong> ' . $test_email;
        echo '</div>';
    } else {
        echo '<div style="color: red; padding: 10px; border: 1px solid red; margin: 10px 0;">';
        echo '<strong>❌ Failed to send test email to:</strong> ' . $test_email;
        echo '</div>';
        
        // Check for PHPMailer errors
        if (isset($GLOBALS['phpmailer']) && is_object($GLOBALS['phpmailer'])) {
            $phpmailer = $GLOBALS['phpmailer'];
            if (isset($phpmailer->ErrorInfo)) {
                echo '<div style="color: red; padding: 10px; border: 1px solid red; margin: 10px 0;">';
                echo '<strong>PHPMailer Error:</strong> ' . $phpmailer->ErrorInfo;
                echo '</div>';
            }
        }
        
        echo '<div style="color: orange; padding: 10px; border: 1px solid orange; margin: 10px 0;">';
        echo '<strong>💡 Troubleshooting Tips:</strong>';
        echo '<ul>';
        echo '<li>Check if your hosting provider allows sending emails</li>';
        echo '<li>Verify SMTP settings if using SMTP</li>';
        echo '<li>Check WordPress email plugins (like WP Mail SMTP)</li>';
        echo '<li>Contact your hosting provider about email restrictions</li>';
        echo '</ul>';
        echo '</div>';
    }
}

// Test 5: Check error logs
echo '<h2>Error Logs</h2>';
$log_file = WP_CONTENT_DIR . '/debug.log';
if (file_exists($log_file)) {
    echo '<p>Debug log file exists at: ' . $log_file . '</p>';
    echo '<p>Last 10 lines:</p>';
    $lines = file($log_file);
    $last_lines = array_slice($lines, -10);
    echo '<pre style="background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;">';
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line);
    }
    echo '</pre>';
} else {
    echo '<p>Debug log file not found. Enable debugging in wp-config.php:</p>';
    echo '<pre>define(\'WP_DEBUG\', true);</pre>';
    echo '<pre>define(\'WP_DEBUG_LOG\', true);</pre>';
}

// Test form
echo '<h2>Send Test Email</h2>';
echo '<form method="post">';
echo '<input type="submit" name="send_test" value="Send Test Email" style="padding: 10px 20px; background: #0073aa; color: white; border: none; cursor: pointer;">';
echo '</form>';

echo '<hr>';
echo '<p><em>This test script helps identify email configuration issues. Delete this file after testing.</em></p>';
?>
