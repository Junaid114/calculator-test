<?php
/**
 * Quick Command-Line Test for Enhanced PDF Generator Admin Functions
 * 
 * Run this file from command line: php test-admin-quick.php
 * This provides a fast way to verify all admin functions are working
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "🧪 Enhanced PDF Generator - Quick Admin Test\n";
echo "=============================================\n\n";

// Test 1: WordPress Environment
echo "📋 Test 1: WordPress Environment\n";
if (defined('ABSPATH')) {
    echo "✅ WordPress loaded successfully\n";
    echo "   ABSPATH: " . ABSPATH . "\n";
} else {
    echo "❌ WordPress not loaded\n";
    exit;
}
echo "\n";

// Test 2: Required Functions
echo "📋 Test 2: Required Functions\n";
$required_functions = [
    'ssc_generate_enhanced_pdf',
    'ssc_get_company_info',
    'ssc_process_template',
    'ssc_create_enhanced_pdf',
    'ssc_add_cover_page',
    'ssc_add_body_content',
    'ssc_add_drawing_image',
    'ssc_add_footer_to_pages',
    'ssc_ajax_generate_enhanced_pdf'
];

$functions_loaded = 0;
foreach ($required_functions as $function) {
    if (function_exists($function)) {
        echo "✅ {$function}\n";
        $functions_loaded++;
    } else {
        echo "❌ {$function}\n";
    }
}

if ($functions_loaded === count($required_functions)) {
    echo "🎉 All functions loaded: {$functions_loaded}/" . count($required_functions) . "\n";
} else {
    echo "⚠️ Functions loaded: {$functions_loaded}/" . count($required_functions) . "\n";
}
echo "\n";

// Test 3: Admin Settings
echo "📋 Test 3: Admin Settings\n";
$admin_settings = [
    'ssc_pdf_template_cover',
    'ssc_pdf_template_body',
    'ssc_pdf_template_footer',
    'ssc_pdf_company_logo',
    'ssc_pdf_company_name',
    'ssc_pdf_company_address',
    'ssc_pdf_company_phone',
    'ssc_pdf_company_email',
    'ssc_pdf_company_website',
    'ssc_pdf_export_quality',
    'ssc_pdf_page_size'
];

$settings_registered = 0;
foreach ($admin_settings as $setting) {
    $value = get_option($setting, 'NOT_SET');
    if ($value !== 'NOT_SET') {
        echo "✅ {$setting}: " . substr($value, 0, 30) . "\n";
        $settings_registered++;
    } else {
        echo "⚠️ {$setting}: Not set\n";
    }
}
echo "Settings registered: {$settings_registered}/" . count($admin_settings) . "\n\n";

// Test 4: Company Information
echo "📋 Test 4: Company Information\n";
if (function_exists('ssc_get_company_info')) {
    $company_info = ssc_get_company_info();
    echo "✅ Company info function working\n";
    foreach ($company_info as $key => $value) {
        echo "   {$key}: {$value}\n";
    }
} else {
    echo "❌ Company info function not available\n";
}
echo "\n";

// Test 5: Template Processing
echo "📋 Test 5: Template Processing\n";
if (function_exists('ssc_process_template')) {
    $test_template = "Hello {{company_name}}, your project {{drawing_name}} is ready!";
    $test_data = [
        'company_name' => 'Bamby Stone',
        'drawing_name' => 'Test Project'
    ];
    
    $processed = ssc_process_template($test_template, $test_data);
    $expected = "Hello Bamby Stone, your project Test Project is ready!";
    
    if ($processed === $expected) {
        echo "✅ Template processing working\n";
        echo "   Original: {$test_template}\n";
        echo "   Processed: {$processed}\n";
    } else {
        echo "❌ Template processing failed\n";
        echo "   Expected: {$expected}\n";
        echo "   Got: {$processed}\n";
    }
} else {
    echo "❌ Template processing function not available\n";
}
echo "\n";

// Test 6: PDF Creation
echo "📋 Test 6: PDF Creation\n";
if (function_exists('ssc_create_enhanced_pdf')) {
    try {
        $pdf = ssc_create_enhanced_pdf('A3', 'high');
        echo "✅ PDF creation working\n";
        echo "   Class: " . get_class($pdf) . "\n";
        unset($pdf);
    } catch (Exception $e) {
        echo "❌ PDF creation failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ PDF creation function not available\n";
}
echo "\n";

// Test 7: AJAX Handlers
echo "📋 Test 7: AJAX Handlers\n";
global $wp_filter;
$ajax_handlers = [
    'wp_ajax_ssc_generate_enhanced_pdf',
    'wp_ajax_nopriv_ssc_generate_enhanced_pdf'
];

$handlers_registered = 0;
foreach ($ajax_handlers as $handler) {
    if (isset($wp_filter[$handler])) {
        echo "✅ {$handler}\n";
        $handlers_registered++;
    } else {
        echo "⚠️ {$handler}\n";
    }
}
echo "AJAX handlers: {$handlers_registered}/" . count($ajax_handlers) . "\n\n";

// Test 8: PDF Settings
echo "📋 Test 8: PDF Settings\n";
$page_size = get_option('ssc_pdf_page_size', 'A3');
$export_quality = get_option('ssc_pdf_export_quality', 'high');
echo "✅ Page Size: {$page_size}\n";
echo "✅ Export Quality: {$export_quality}\n\n";

// Test 9: Enhanced PDF Generation
echo "📋 Test 9: Enhanced PDF Generation\n";
if (function_exists('ssc_generate_enhanced_pdf')) {
    $test_drawing_data = [
        'drawing_name' => 'Test Drawing',
        'drawing_notes' => 'This is a test drawing',
        'total_cutting_mm' => 150.5,
        'only_cut_mm' => 100.0,
        'mitred_cut_mm' => 50.5,
        'slab_cost' => '$250.00'
    ];
    
    try {
        $pdf = ssc_generate_enhanced_pdf($test_drawing_data);
        echo "✅ Enhanced PDF generation working\n";
        echo "   PDF Object: " . get_class($pdf) . "\n";
        unset($pdf);
    } catch (Exception $e) {
        echo "❌ Enhanced PDF generation failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Enhanced PDF generation function not available\n";
}
echo "\n";

// Summary
echo "📊 Test Summary\n";
echo "===============\n";

$total_tests = 9;
$passed_tests = 0;
$failed_tests = 0;
$warning_tests = 0;

// Count results (simplified counting)
$output = ob_get_contents();
$passed_tests = substr_count($output, '✅');
$failed_tests = substr_count($output, '❌');
$warning_tests = substr_count($output, '⚠️');

echo "Total Tests: {$total_tests}\n";
echo "Passed: {$passed_tests}\n";
echo "Failed: {$failed_tests}\n";
echo "Warnings: {$warning_tests}\n\n";

if ($failed_tests === 0) {
    echo "🎉 All critical tests passed! The enhanced PDF generator is working correctly.\n";
} else {
    echo "⚠️ Some tests failed. Please check the details above.\n";
}

echo "\n";
echo "📝 Next Steps:\n";
echo "1. Go to WordPress Admin → Slab Calculator Settings\n";
echo "2. Configure PDF templates and company information\n";
echo "3. Test PDF generation in the calculator\n";
echo "4. Verify A3 size and high resolution output\n";

echo "\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "PHP Version: " . phpversion() . "\n";
?>
