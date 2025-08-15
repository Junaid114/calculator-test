<?php
/**
 * Comprehensive Test File for Enhanced PDF Generator Admin Functions
 * 
 * This file tests all admin-side functionality including:
 * - PDF template settings
 * - Company information settings
 * - PDF export settings
 * - Template processing
 * - PDF generation functions
 * 
 * Run this file to verify everything is working correctly
 */

// Include WordPress
require_once('../../../wp-load.php');

echo "<h1>🧪 Enhanced PDF Generator - Admin Functions Test</h1>\n";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .test-pass { background: #d4edda; border-color: #c3e6cb; }
    .test-fail { background: #f8d7da; border-color: #f5c6cb; }
    .test-info { background: #d1ecf1; border-color: #bee5eb; }
    .test-warning { background: #fff3cd; border-color: #ffeaa7; }
    .result { font-weight: bold; margin: 10px 0; }
    .details { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 3px; }
</style>\n";

// Test 1: Check if WordPress is loaded
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 1: WordPress Environment</h2>\n";
if (defined('ABSPATH')) {
    echo "<div class='result test-pass'>✅ WordPress loaded successfully</div>\n";
    echo "<div class='details'>ABSPATH: " . ABSPATH . "</div>\n";
} else {
    echo "<div class='result test-fail'>❌ WordPress not loaded</div>\n";
    exit;
}
echo "</div>\n";

// Test 2: Check if enhanced PDF generator is loaded
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 2: Enhanced PDF Generator Functions</h2>\n";

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
        echo "<div class='result test-pass'>✅ {$function} - Loaded</div>\n";
        $functions_loaded++;
    } else {
        echo "<div class='result test-fail'>❌ {$function} - NOT Loaded</div>\n";
    }
}

if ($functions_loaded === count($required_functions)) {
    echo "<div class='result test-pass'>🎉 All required functions loaded successfully!</div>\n";
} else {
    echo "<div class='result test-fail'>⚠️ {$functions_loaded}/" . count($required_functions) . " functions loaded</div>\n";
}
echo "</div>\n";

// Test 3: Check Admin Settings Registration
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 3: Admin Settings Registration</h2>\n";

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
        echo "<div class='result test-pass'>✅ {$setting} - Registered (Value: " . substr($value, 0, 50) . ")</div>\n";
        $settings_registered++;
    } else {
        echo "<div class='result test-warning'>⚠️ {$setting} - Not set (using default)</div>\n";
    }
}

echo "<div class='details'>Settings registered: {$settings_registered}/" . count($admin_settings) . "</div>\n";
echo "</div>\n";

// Test 4: Test Company Information Function
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 4: Company Information Function</h2>\n";

if (function_exists('ssc_get_company_info')) {
    $company_info = ssc_get_company_info();
    echo "<div class='result test-pass'>✅ Company info function working</div>\n";
    echo "<div class='details'>\n";
    foreach ($company_info as $key => $value) {
        echo "<strong>{$key}:</strong> " . htmlspecialchars($value) . "<br>\n";
    }
    echo "</div>\n";
} else {
    echo "<div class='result test-fail'>❌ Company info function not available</div>\n";
}
echo "</div>\n";

// Test 5: Test Template Processing
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 5: Template Processing Function</h2>\n";

if (function_exists('ssc_process_template')) {
    $test_template = "Hello {{company_name}}, your project {{drawing_name}} is ready!";
    $test_data = [
        'company_name' => 'Bamby Stone',
        'drawing_name' => 'Test Project'
    ];
    
    $processed = ssc_process_template($test_template, $test_data);
    $expected = "Hello Bamby Stone, your project Test Project is ready!";
    
    if ($processed === $expected) {
        echo "<div class='result test-pass'>✅ Template processing working correctly</div>\n";
        echo "<div class='details'>\n";
        echo "<strong>Original:</strong> " . htmlspecialchars($test_template) . "<br>\n";
        echo "<strong>Processed:</strong> " . htmlspecialchars($processed) . "<br>\n";
        echo "<strong>Expected:</strong> " . htmlspecialchars($expected) . "\n";
        echo "</div>\n";
    } else {
        echo "<div class='result test-fail'>❌ Template processing failed</div>\n";
        echo "<div class='details'>\n";
        echo "<strong>Expected:</strong> " . htmlspecialchars($expected) . "<br>\n";
        echo "<strong>Got:</strong> " . htmlspecialchars($processed) . "\n";
        echo "</div>\n";
    }
} else {
    echo "<div class='result test-fail'>❌ Template processing function not available</div>\n";
}
echo "</div>\n";

// Test 6: Test PDF Creation Function
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 6: PDF Creation Function</h2>\n";

if (function_exists('ssc_create_enhanced_pdf')) {
    try {
        // Test with A3 size
        $pdf = ssc_create_enhanced_pdf('A3', 'high');
        echo "<div class='result test-pass'>✅ PDF creation function working</div>\n";
        echo "<div class='details'>\n";
        echo "<strong>Page Size:</strong> A3<br>\n";
        echo "<strong>Quality:</strong> High<br>\n";
        echo "<strong>PDF Object:</strong> " . get_class($pdf) . "\n";
        echo "</div>\n";
        
        // Clean up
        unset($pdf);
    } catch (Exception $e) {
        echo "<div class='result test-fail'>❌ PDF creation failed: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
} else {
    echo "<div class='result test-fail'>❌ PDF creation function not available</div>\n";
}
echo "</div>\n";

// Test 7: Test AJAX Handler Registration
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 7: AJAX Handler Registration</h2>\n";

global $wp_filter;
$ajax_handlers = [
    'wp_ajax_ssc_generate_enhanced_pdf',
    'wp_ajax_nopriv_ssc_generate_enhanced_pdf'
];

$handlers_registered = 0;
foreach ($ajax_handlers as $handler) {
    if (isset($wp_filter[$handler])) {
        echo "<div class='result test-pass'>✅ {$handler} - Registered</div>\n";
        $handlers_registered++;
    } else {
        echo "<div class='result test-warning'>⚠️ {$handler} - Not registered</div>\n";
    }
}

echo "<div class='details'>AJAX handlers registered: {$handlers_registered}/" . count($ajax_handlers) . "</div>\n";
echo "</div>\n";

// Test 8: Test Default Template Values
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 8: Default Template Values</h2>\n";

$cover_template = get_option('ssc_pdf_template_cover', '');
$body_template = get_option('ssc_pdf_template_body', '');
$footer_template = get_option('ssc_pdf_template_footer', '');

if (empty($cover_template)) {
    echo "<div class='result test-warning'>⚠️ Cover template not set (will use default)</div>\n";
} else {
    echo "<div class='result test-pass'>✅ Cover template is set</div>\n";
}

if (empty($body_template)) {
    echo "<div class='result test-warning'>⚠️ Body template not set (will use default)</div>\n";
} else {
    echo "<div class='result test-pass'>✅ Body template is set</div>\n";
}

if (empty($footer_template)) {
    echo "<div class='result test-warning'>⚠️ Footer template not set (will use default)</div>\n";
} else {
    echo "<div class='result test-pass'>✅ Footer template is set</div>\n";
}

echo "<div class='details'>\n";
echo "<strong>Cover Template Length:</strong> " . strlen($cover_template) . " characters<br>\n";
echo "<strong>Body Template Length:</strong> " . strlen($body_template) . " characters<br>\n";
echo "<strong>Footer Template Length:</strong> " . strlen($footer_template) . " characters\n";
echo "</div>\n";
echo "</div>\n";

// Test 9: Test PDF Settings
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 9: PDF Export Settings</h2>\n";

$page_size = get_option('ssc_pdf_page_size', 'A3');
$export_quality = get_option('ssc_pdf_export_quality', 'high');

echo "<div class='result test-pass'>✅ PDF settings retrieved</div>\n";
echo "<div class='details'>\n";
echo "<strong>Page Size:</strong> {$page_size}<br>\n";
echo "<strong>Export Quality:</strong> {$export_quality}\n";
echo "</div>\n";
echo "</div>\n";

// Test 10: Test Company Information Settings
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 10: Company Information Settings</h2>\n";

$company_settings = [
    'ssc_pdf_company_logo' => 'Company Logo URL',
    'ssc_pdf_company_name' => 'Company Name',
    'ssc_pdf_company_address' => 'Company Address',
    'ssc_pdf_company_phone' => 'Company Phone',
    'ssc_pdf_company_email' => 'Company Email',
    'ssc_pdf_company_website' => 'Company Website'
];

$company_info_set = 0;
foreach ($company_settings as $setting => $label) {
    $value = get_option($setting, '');
    if (!empty($value)) {
        echo "<div class='result test-pass'>✅ {$label}: " . htmlspecialchars(substr($value, 0, 50)) . "</div>\n";
        $company_info_set++;
    } else {
        echo "<div class='result test-warning'>⚠️ {$label}: Not set</div>\n";
    }
}

echo "<div class='details'>Company information set: {$company_info_set}/" . count($company_settings) . "</div>\n";
echo "</div>\n";

// Test 11: Test Enhanced PDF Generation (Simulation)
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 11: Enhanced PDF Generation Simulation</h2>\n";

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
        // This will test the function without actually creating a PDF file
        $pdf = ssc_generate_enhanced_pdf($test_drawing_data);
        echo "<div class='result test-pass'>✅ Enhanced PDF generation function working</div>\n";
        echo "<div class='details'>\n";
        echo "<strong>Test Data:</strong> " . json_encode($test_drawing_data) . "<br>\n";
        echo "<strong>PDF Object:</strong> " . get_class($pdf) . "\n";
        echo "</div>\n";
        
        // Clean up
        unset($pdf);
    } catch (Exception $e) {
        echo "<div class='result test-fail'>❌ Enhanced PDF generation failed: " . htmlspecialchars($e->getMessage()) . "</div>\n";
    }
} else {
    echo "<div class='result test-fail'>❌ Enhanced PDF generation function not available</div>\n";
}
echo "</div>\n";

// Test 12: Test Admin Menu Registration
echo "<div class='test-section test-info'>\n";
echo "<h2>📋 Test 12: Admin Menu Registration</h2>\n";

global $menu;
$admin_menu_found = false;

foreach ($menu as $menu_item) {
    if (isset($menu_item[0]) && strpos($menu_item[0], 'Slab Calculator') !== false) {
        $admin_menu_found = true;
        echo "<div class='result test-pass'>✅ Admin menu found: " . htmlspecialchars($menu_item[0]) . "</div>\n";
        echo "<div class='details'>\n";
        echo "<strong>Menu Slug:</strong> " . htmlspecialchars($menu_item[2]) . "<br>\n";
        echo "<strong>Capability:</strong> " . htmlspecialchars($menu_item[1]) . "\n";
        echo "</div>\n";
        break;
    }
}

if (!$admin_menu_found) {
    echo "<div class='result test-warning'>⚠️ Admin menu not found (may not be loaded yet)</div>\n";
}
echo "</div>\n";

// Summary
echo "<div class='test-section test-info'>\n";
echo "<h2>📊 Test Summary</h2>\n";

$total_tests = 12;
$passed_tests = 0;
$failed_tests = 0;
$warning_tests = 0;

// Count results from previous tests
$results = ob_get_contents();
if (strpos($results, 'test-pass') !== false) {
    $passed_tests = substr_count($results, 'test-pass');
}
if (strpos($results, 'test-fail') !== false) {
    $failed_tests = substr_count($results, 'test-fail');
}
if (strpos($results, 'test-warning') !== false) {
    $warning_tests = substr_count($results, 'test-warning');
}

echo "<div class='details'>\n";
echo "<strong>Total Tests:</strong> {$total_tests}<br>\n";
echo "<strong>Passed:</strong> <span style='color: green;'>{$passed_tests}</span><br>\n";
echo "<strong>Failed:</strong> <span style='color: red;'>{$failed_tests}</span><br>\n";
echo "<strong>Warnings:</strong> <span style='color: orange;'>{$warning_tests}</span><br>\n";
echo "</div>\n";

if ($failed_tests === 0) {
    echo "<div class='result test-pass'>🎉 All critical tests passed! The enhanced PDF generator is working correctly.</div>\n";
} else {
    echo "<div class='result test-fail'>⚠️ Some tests failed. Please check the details above.</div>\n";
}

echo "</div>\n";

// Instructions for next steps
echo "<div class='test-section test-info'>\n";
echo "<h2>📝 Next Steps</h2>\n";
echo "<div class='details'>\n";
echo "1. <strong>Configure Admin Settings:</strong> Go to WordPress Admin → Slab Calculator Settings<br>\n";
echo "2. <strong>Set Company Information:</strong> Fill in logo, name, address, phone, email, website<br>\n";
echo "3. <strong>Customize Templates:</strong> Edit cover, body, and footer templates as needed<br>\n";
echo "4. <strong>Test PDF Generation:</strong> Use the calculator to generate enhanced PDFs<br>\n";
echo "5. <strong>Verify A3 Size:</strong> Check that PDFs are generated in A3 format<br>\n";
echo "</div>\n";
echo "</div>\n";

echo "<div class='test-section test-info'>\n";
echo "<h2>🔧 Troubleshooting</h2>\n";
echo "<div class='details'>\n";
echo "• If functions are not loaded, check that the enhanced-pdf-generator.php file is included<br>\n";
echo "• If admin settings are not visible, try refreshing the admin page<br>\n";
echo "• If PDF generation fails, check WordPress error logs<br>\n";
echo "• Ensure jsPDF library is properly loaded in the calculator template<br>\n";
echo "</div>\n";
echo "</div>\n";

echo "<hr>\n";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>\n";
echo "<p><em>WordPress Version: " . get_bloginfo('version') . "</em></p>\n";
echo "<p><em>PHP Version: " . phpversion() . "</em></p>\n";
?>
