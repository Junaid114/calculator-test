<?php
/**
 * Simple Test for Enhanced PDF Generator
 * Run this from command line: php simple-test.php
 */

echo "=== Enhanced PDF Generator Test ===\n\n";

// Check if we can include WordPress
$wp_load_path = '../../../wp-load.php';
if (file_exists($wp_load_path)) {
    require_once($wp_load_path);
    echo "✅ WordPress loaded successfully\n";
} else {
    echo "❌ WordPress not found at: $wp_load_path\n";
    echo "Current directory: " . getcwd() . "\n";
    exit;
}

// Test 1: Check if enhanced PDF generator is loaded
echo "\n=== Test 1: Required Functions ===\n";
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
        echo "✅ $function\n";
        $functions_loaded++;
    } else {
        echo "❌ $function\n";
    }
}

echo "\nFunctions loaded: $functions_loaded/" . count($required_functions) . "\n";

// Test 2: Check admin settings
echo "\n=== Test 2: Admin Settings ===\n";
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
        echo "✅ $setting\n";
        $settings_registered++;
    } else {
        echo "⚠️ $setting (not set)\n";
    }
}

echo "\nSettings registered: $settings_registered/" . count($admin_settings) . "\n";

// Test 3: Test company info function
echo "\n=== Test 3: Company Information ===\n";
if (function_exists('ssc_get_company_info')) {
    try {
        $company_info = ssc_get_company_info();
        echo "✅ Company info function working\n";
        foreach ($company_info as $key => $value) {
            echo "   $key: $value\n";
        }
    } catch (Exception $e) {
        echo "❌ Company info function error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Company info function not available\n";
}

// Test 4: Test template processing
echo "\n=== Test 4: Template Processing ===\n";
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
        echo "   Original: $test_template\n";
        echo "   Processed: $processed\n";
    } else {
        echo "❌ Template processing failed\n";
        echo "   Expected: $expected\n";
        echo "   Got: $processed\n";
    }
} else {
    echo "❌ Template processing function not available\n";
}

// Test 5: Test PDF creation
echo "\n=== Test 5: PDF Creation ===\n";
if (function_exists('ssc_create_enhanced_pdf')) {
    try {
        $pdf = ssc_create_enhanced_pdf('A3', 'high');
        echo "✅ PDF creation working\n";
        echo "   PDF Object: " . get_class($pdf) . "\n";
        unset($pdf);
    } catch (Exception $e) {
        echo "❌ PDF creation failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ PDF creation function not available\n";
}

// Summary
echo "\n=== Test Summary ===\n";
$total_tests = 5;
$passed_tests = 0;
$failed_tests = 0;

// Count results
$output = ob_get_contents();
$passed_tests = substr_count($output, '✅');
$failed_tests = substr_count($output, '❌');

echo "Total Tests: $total_tests\n";
echo "Passed: $passed_tests\n";
echo "Failed: $failed_tests\n\n";

if ($failed_tests === 0) {
    echo "🎉 All tests passed! Enhanced PDF generator is working correctly.\n";
} else {
    echo "⚠️ Some tests failed. Check the details above.\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Go to WordPress Admin → Slab Calculator Settings\n";
echo "2. Configure PDF templates and company information\n";
echo "3. Test PDF generation in the calculator\n";
echo "4. Verify A3 size and high resolution output\n";

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "PHP Version: " . phpversion() . "\n";
?>
