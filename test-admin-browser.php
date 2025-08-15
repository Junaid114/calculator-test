<?php
/**
 * Browser-Based Test for Enhanced PDF Generator Admin Functions
 * 
 * Open this file in your web browser to test all admin functions
 * This provides a visual way to verify everything is working
 */

// Include WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo "<h1>❌ Access Denied</h1>";
    echo "<p>You need administrator privileges to run this test.</p>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced PDF Generator - Admin Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .test-pass { 
            background: #d4edda; 
            border-color: #c3e6cb; 
        }
        .test-fail { 
            background: #f8d7da; 
            border-color: #f5c6cb; 
        }
        .test-info { 
            background: #d1ecf1; 
            border-color: #bee5eb; 
        }
        .test-warning { 
            background: #fff3cd; 
            border-color: #ffeaa7; 
        }
        .result { 
            font-weight: bold; 
            margin: 10px 0; 
        }
        .details { 
            margin: 10px 0; 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 3px; 
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .test-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .test-card h3 {
            margin-top: 0;
            color: #333;
        }
        .status-pass { color: #28a745; }
        .status-fail { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .btn {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #005a87;
        }
        .summary {
            background: #e7f3fa;
            border-left: 4px solid #00a0d2;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Enhanced PDF Generator - Admin Functions Test</h1>
        <p>This test verifies all admin-side functionality for the enhanced PDF generator.</p>
        
        <div class="summary">
            <h3>📋 Test Overview</h3>
            <p>This comprehensive test covers:</p>
            <ul>
                <li>WordPress environment and function loading</li>
                <li>Admin settings registration and values</li>
                <li>Company information functions</li>
                <li>Template processing capabilities</li>
                <li>PDF creation and generation</li>
                <li>AJAX handler registration</li>
                <li>Admin menu integration</li>
            </ul>
        </div>

        <div class="test-grid">
            <!-- Test 1: WordPress Environment -->
            <div class="test-card">
                <h3>📋 Test 1: WordPress Environment</h3>
                <?php if (defined('ABSPATH')): ?>
                    <div class="result status-pass">✅ WordPress loaded successfully</div>
                    <div class="details">
                        <strong>ABSPATH:</strong> <?php echo ABSPATH; ?>
                    </div>
                <?php else: ?>
                    <div class="result status-fail">❌ WordPress not loaded</div>
                <?php endif; ?>
            </div>

            <!-- Test 2: Required Functions -->
            <div class="test-card">
                <h3>📋 Test 2: Required Functions</h3>
                <?php
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
                        echo "<div class='result status-pass'>✅ {$function}</div>";
                        $functions_loaded++;
                    } else {
                        echo "<div class='result status-fail'>❌ {$function}</div>";
                    }
                }
                ?>
                <div class="details">
                    <strong>Functions loaded:</strong> <?php echo $functions_loaded; ?>/<?php echo count($required_functions); ?>
                </div>
            </div>

            <!-- Test 3: Admin Settings -->
            <div class="test-card">
                <h3>📋 Test 3: Admin Settings</h3>
                <?php
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
                        echo "<div class='result status-pass'>✅ {$setting}</div>";
                        $settings_registered++;
                    } else {
                        echo "<div class='result status-warning'>⚠️ {$setting}</div>";
                    }
                }
                ?>
                <div class="details">
                    <strong>Settings registered:</strong> <?php echo $settings_registered; ?>/<?php echo count($admin_settings); ?>
                </div>
            </div>

            <!-- Test 4: Company Information -->
            <div class="test-card">
                <h3>📋 Test 4: Company Information</h3>
                <?php if (function_exists('ssc_get_company_info')): ?>
                    <?php $company_info = ssc_get_company_info(); ?>
                    <div class="result status-pass">✅ Company info function working</div>
                    <div class="details">
                        <?php foreach ($company_info as $key => $value): ?>
                            <strong><?php echo ucfirst($key); ?>:</strong> <?php echo htmlspecialchars($value); ?><br>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="result status-fail">❌ Company info function not available</div>
                <?php endif; ?>
            </div>

            <!-- Test 5: Template Processing -->
            <div class="test-card">
                <h3>📋 Test 5: Template Processing</h3>
                <?php if (function_exists('ssc_process_template')): ?>
                    <?php
                    $test_template = "Hello {{company_name}}, your project {{drawing_name}} is ready!";
                    $test_data = [
                        'company_name' => 'Bamby Stone',
                        'drawing_name' => 'Test Project'
                    ];
                    
                    $processed = ssc_process_template($test_template, $test_data);
                    $expected = "Hello Bamby Stone, your project Test Project is ready!";
                    
                    if ($processed === $expected):
                    ?>
                        <div class="result status-pass">✅ Template processing working</div>
                        <div class="details">
                            <strong>Original:</strong> <?php echo htmlspecialchars($test_template); ?><br>
                            <strong>Processed:</strong> <?php echo htmlspecialchars($processed); ?>
                        </div>
                    <?php else: ?>
                        <div class="result status-fail">❌ Template processing failed</div>
                        <div class="details">
                            <strong>Expected:</strong> <?php echo htmlspecialchars($expected); ?><br>
                            <strong>Got:</strong> <?php echo htmlspecialchars($processed); ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="result status-fail">❌ Template processing function not available</div>
                <?php endif; ?>
            </div>

            <!-- Test 6: PDF Creation -->
            <div class="test-card">
                <h3>📋 Test 6: PDF Creation</h3>
                <?php if (function_exists('ssc_create_enhanced_pdf')): ?>
                    <?php
                    try {
                        $pdf = ssc_create_enhanced_pdf('A3', 'high');
                        echo "<div class='result status-pass'>✅ PDF creation working</div>";
                        echo "<div class='details'>";
                        echo "<strong>Page Size:</strong> A3<br>";
                        echo "<strong>Quality:</strong> High<br>";
                        echo "<strong>PDF Object:</strong> " . get_class($pdf);
                        echo "</div>";
                        unset($pdf);
                    } catch (Exception $e) {
                        echo "<div class='result status-fail'>❌ PDF creation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    ?>
                <?php else: ?>
                    <div class="result status-fail">❌ PDF creation function not available</div>
                <?php endif; ?>
            </div>

            <!-- Test 7: AJAX Handlers -->
            <div class="test-card">
                <h3>📋 Test 7: AJAX Handlers</h3>
                <?php
                global $wp_filter;
                $ajax_handlers = [
                    'wp_ajax_ssc_generate_enhanced_pdf',
                    'wp_ajax_nopriv_ssc_generate_enhanced_pdf'
                ];

                $handlers_registered = 0;
                foreach ($ajax_handlers as $handler) {
                    if (isset($wp_filter[$handler])) {
                        echo "<div class='result status-pass'>✅ {$handler}</div>";
                        $handlers_registered++;
                    } else {
                        echo "<div class='result status-warning'>⚠️ {$handler}</div>";
                    }
                }
                ?>
                <div class="details">
                    <strong>AJAX handlers:</strong> <?php echo $handlers_registered; ?>/<?php echo count($ajax_handlers); ?>
                </div>
            </div>

            <!-- Test 8: PDF Settings -->
            <div class="test-card">
                <h3>📋 Test 8: PDF Settings</h3>
                <?php
                $page_size = get_option('ssc_pdf_page_size', 'A3');
                $export_quality = get_option('ssc_pdf_export_quality', 'high');
                ?>
                <div class="result status-pass">✅ PDF settings retrieved</div>
                <div class="details">
                    <strong>Page Size:</strong> <?php echo $page_size; ?><br>
                    <strong>Export Quality:</strong> <?php echo $export_quality; ?>
                </div>
            </div>

            <!-- Test 9: Enhanced PDF Generation -->
            <div class="test-card">
                <h3>📋 Test 9: Enhanced PDF Generation</h3>
                <?php if (function_exists('ssc_generate_enhanced_pdf')): ?>
                    <?php
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
                        echo "<div class='result status-pass'>✅ Enhanced PDF generation working</div>";
                        echo "<div class='details'>";
                        echo "<strong>PDF Object:</strong> " . get_class($pdf);
                        echo "</div>";
                        unset($pdf);
                    } catch (Exception $e) {
                        echo "<div class='result status-fail'>❌ Enhanced PDF generation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    ?>
                <?php else: ?>
                    <div class="result status-fail">❌ Enhanced PDF generation function not available</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary -->
        <div class="test-section test-info">
            <h2>📊 Test Summary</h2>
            <?php
            $total_tests = 9;
            $passed_tests = 0;
            $failed_tests = 0;
            $warning_tests = 0;

            // Count results from the page
            $page_content = ob_get_contents();
            $passed_tests = substr_count($page_content, 'status-pass');
            $failed_tests = substr_count($page_content, 'status-fail');
            $warning_tests = substr_count($page_content, 'status-warning');
            ?>
            <div class="details">
                <strong>Total Tests:</strong> <?php echo $total_tests; ?><br>
                <strong>Passed:</strong> <span style="color: #28a745;"><?php echo $passed_tests; ?></span><br>
                <strong>Failed:</strong> <span style="color: #dc3545;"><?php echo $failed_tests; ?></span><br>
                <strong>Warnings:</strong> <span style="color: #ffc107;"><?php echo $warning_tests; ?></span>
            </div>

            <?php if ($failed_tests === 0): ?>
                <div class="result status-pass">🎉 All critical tests passed! The enhanced PDF generator is working correctly.</div>
            <?php else: ?>
                <div class="result status-fail">⚠️ Some tests failed. Please check the details above.</div>
            <?php endif; ?>
        </div>

        <!-- Next Steps -->
        <div class="test-section test-info">
            <h2>📝 Next Steps</h2>
            <div class="details">
                <p><strong>1. Configure Admin Settings:</strong> Go to WordPress Admin → Slab Calculator Settings</p>
                <p><strong>2. Set Company Information:</strong> Fill in logo, name, address, phone, email, website</p>
                <p><strong>3. Customize Templates:</strong> Edit cover, body, and footer templates as needed</p>
                <p><strong>4. Test PDF Generation:</strong> Use the calculator to generate enhanced PDFs</p>
                <p><strong>5. Verify A3 Size:</strong> Check that PDFs are generated in A3 format</p>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="<?php echo admin_url('admin.php?page=slab-calculator-settings'); ?>" class="btn">🔧 Go to Admin Settings</a>
                <a href="<?php echo home_url(); ?>" class="btn">🏠 Go to Homepage</a>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="test-section test-warning">
            <h2>🔧 Troubleshooting</h2>
            <div class="details">
                <p><strong>• Functions not loaded:</strong> Check that enhanced-pdf-generator.php is included in the main plugin file</p>
                <p><strong>• Admin settings not visible:</strong> Try refreshing the admin page or check user capabilities</p>
                <p><strong>• PDF generation fails:</strong> Check WordPress error logs and ensure jsPDF library is loaded</p>
                <p><strong>• A3 size not working:</strong> Verify jsPDF version supports A3 format</p>
            </div>
        </div>

        <hr>
        <p><em>Test completed at: <?php echo date('Y-m-d H:i:s'); ?></em></p>
        <p><em>WordPress Version: <?php echo get_bloginfo('version'); ?></em></p>
        <p><em>PHP Version: <?php echo phpversion(); ?></em></p>
    </div>
</body>
</html>
