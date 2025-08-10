<?php
/**
 * Test Script to Verify Email Verification is Disabled
 * 
 * This script tests the login functionality to ensure email verification
 * is completely disabled.
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>Email Verification Test</h1>";

// Check if a user exists
$users = get_users(['number' => 1]);
if (empty($users)) {
    echo "<p>No users found in the system.</p>";
    exit;
}

$user = $users[0];
echo "<p>Testing with user: {$user->user_login} ({$user->user_email})</p>";

// Check user meta
$email_verified = get_user_meta($user->ID, 'email_verified', true);
echo "<p>Email verification status: " . ($email_verified ? 'Verified' : 'Not Verified') . "</p>";

// Check if the authenticate filter is active
global $wp_filter;
if (isset($wp_filter['authenticate'])) {
    echo "<p>Active authenticate filters:</p>";
    foreach ($wp_filter['authenticate']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            if (is_array($callback['function']) && is_object($callback['function'][0])) {
                $class_name = get_class($callback['function'][0]);
                $method_name = $callback['function'][1];
                echo "<p>- Priority {$priority}: {$class_name}::{$method_name}</p>";
            } elseif (is_string($callback['function'])) {
                echo "<p>- Priority {$priority}: {$callback['function']}</p>";
            }
        }
    }
} else {
    echo "<p>No authenticate filters found.</p>";
}

// Test if we can authenticate the user without verification
$authenticated_user = wp_authenticate($user->user_login, 'test_password');
if (is_wp_error($authenticated_user)) {
    echo "<p>Authentication error: " . $authenticated_user->get_error_message() . "</p>";
} else {
    echo "<p>Authentication successful for user: " . $authenticated_user->user_login . "</p>";
}

// Check if the stone_slab_check_email_verification function exists and is callable
if (function_exists('stone_slab_check_email_verification')) {
    echo "<p>WARNING: stone_slab_check_email_verification function is still callable!</p>";
} else {
    echo "<p>stone_slab_check_email_verification function is not callable (good).</p>";
}

// Check if the email-verification.php file is being included
$included_files = get_included_files();
$email_verification_included = false;
foreach ($included_files as $file) {
    if (strpos($file, 'email-verification.php') !== false) {
        $email_verification_included = true;
        break;
    }
}

if ($email_verification_included) {
    echo "<p>WARNING: email-verification.php is still being included!</p>";
} else {
    echo "<p>email-verification.php is not included (good).</p>";
}

echo "<h2>Summary</h2>";
echo "<p>If you see 'WARNING' messages above, email verification is still active.</p>";
echo "<p>If all checks pass, email verification should be completely disabled.</p>";
?>
