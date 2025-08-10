<?php
/**
 * Authentication System Test File
 * 
 * This file tests the authentication system implementation
 * Place this in your WordPress root directory and access via browser
 */

// Include WordPress
require_once('wp-config.php');

// Test the authentication functions
echo "<h1>Stone Slab Calculator Authentication System Test</h1>";

// Test 1: Check if functions exist
echo "<h2>Test 1: Function Existence</h2>";
$functions = [
    'stone_slab_login_handler',
    'stone_slab_register_handler', 
    'stone_slab_logout_handler',
    'stone_slab_check_auth_handler',
    'stone_slab_enqueue_auth_scripts'
];

foreach ($functions as $function) {
    if (function_exists($function)) {
        echo "✅ {$function} - EXISTS<br>";
    } else {
        echo "❌ {$function} - MISSING<br>";
    }
}

// Test 2: Check if AJAX actions are registered
echo "<h2>Test 2: AJAX Actions Registration</h2>";
global $wp_filter;

$ajax_actions = [
    'wp_ajax_stone_slab_login',
    'wp_ajax_nopriv_stone_slab_login',
    'wp_ajax_stone_slab_register',
    'wp_ajax_nopriv_stone_slab_register',
    'wp_ajax_stone_slab_logout',
    'wp_ajax_stone_slab_check_auth',
    'wp_ajax_nopriv_stone_slab_check_auth'
];

foreach ($ajax_actions as $action) {
    if (isset($wp_filter[$action])) {
        echo "✅ {$action} - REGISTERED<br>";
    } else {
        echo "❌ {$action} - NOT REGISTERED<br>";
    }
}

// Test 3: Check WordPress user functions
echo "<h2>Test 3: WordPress User Functions</h2>";
if (function_exists('wp_create_user')) {
    echo "✅ wp_create_user - AVAILABLE<br>";
} else {
    echo "❌ wp_create_user - NOT AVAILABLE<br>";
}

if (function_exists('wp_authenticate')) {
    echo "✅ wp_authenticate - AVAILABLE<br>";
} else {
    echo "❌ wp_authenticate - NOT AVAILABLE<br>";
}

if (function_exists('wp_logout')) {
    echo "✅ wp_logout - AVAILABLE<br>";
} else {
    echo "❌ wp_logout - NOT AVAILABLE<br>";
}

// Test 4: Check nonce creation
echo "<h2>Test 4: Nonce Creation</h2>";
$nonce = wp_create_nonce('stone_slab_auth_nonce');
if ($nonce) {
    echo "✅ Nonce created successfully: " . substr($nonce, 0, 10) . "...<br>";
} else {
    echo "❌ Failed to create nonce<br>";
}

// Test 5: Check AJAX URL
echo "<h2>Test 5: AJAX URL</h2>";
$ajax_url = admin_url('admin-ajax.php');
if ($ajax_url) {
    echo "✅ AJAX URL: {$ajax_url}<br>";
} else {
    echo "❌ Failed to get AJAX URL<br>";
}

echo "<hr>";
echo "<p><strong>Test completed!</strong></p>";
echo "<p>If all tests pass, your authentication system should be working properly.</p>";
echo "<p>You can now test the login/register forms in your calculator interface.</p>";
?>
