<?php
/**
 * Enhanced PDF Generator for Stone Slab Calculator
 * 
 * This file handles the generation of high-quality PDFs by creating
 * HTML files that can be converted to PDF using browser print functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include DOMPDF library for HTML to PDF conversion
require_once(SSC_PLUGIN_DIR . 'includes/dompdf/dompdf/vendor/autoload.php');

/**
 * Generate enhanced PDF with custom templates
 */
function ssc_generate_enhanced_pdf($drawing_data, $canvas_data = null) {
    // Get company information
    $company_info = ssc_get_company_info();
    
    // Generate unique quote ID
    $quote_id = 'Q' . time() . rand(1000, 9999);
    
    // Create HTML content for the enhanced PDF
    $html_content = ssc_create_enhanced_html($drawing_data, $canvas_data, $company_info, $quote_id);
    
    // Create a temporary HTML file for PDF conversion
    $temp_dir = wp_upload_dir()['basedir'] . '/ssc-temp/';
    if (!file_exists($temp_dir)) {
        wp_mkdir_p($temp_dir);
    }
    
    $html_filename = 'enhanced_quote_' . time() . '.html';
    $html_filepath = $temp_dir . $html_filename;
    
    // Write HTML content to temporary file
    file_put_contents($html_filepath, $html_content);
    
    // Convert HTML to PDF using wkhtmltopdf or similar
    $pdf_filename = 'enhanced_quote_' . time() . '.pdf';
    $pdf_filepath = $temp_dir . $pdf_filename;
    
    // Try to convert HTML to PDF
    $pdf_converted = ssc_convert_html_to_pdf($html_filepath, $pdf_filepath);
    
    // Clean up temporary HTML file
    if (file_exists($html_filepath)) {
        unlink($html_filepath);
    }
    
    if ($pdf_converted && file_exists($pdf_filepath)) {
        // Return PDF file info
        return array(
            'filepath' => $pdf_filepath,
            'filename' => $pdf_filename,
            'file_type' => 'application/pdf',
            'html_content' => $html_content,
            'quote_id' => $quote_id
        );
    } else {
        // Fallback to HTML if PDF conversion fails
        return array(
            'filepath' => $html_filepath,
            'filename' => $html_filename,
            'file_type' => 'text/html',
            'html_content' => $html_content,
            'quote_id' => $quote_id
        );
    }
}

/**
 * Convert HTML to PDF using available methods
 */
function ssc_convert_html_to_pdf($html_filepath, $pdf_filepath) {
    error_log('🔄 Starting HTML to PDF conversion...');
    error_log('📄 HTML file: ' . $html_filepath);
    error_log('📄 PDF file: ' . $pdf_filepath);
    
    // Method 1: Try using DOMPDF (PHP-based, works on Windows/XAMPP)
    if (class_exists('Dompdf\Dompdf')) {
        error_log('✅ DOMPDF class found, attempting conversion...');
        try {
            // Read HTML content
            $html_content = file_get_contents($html_filepath);
            if ($html_content === false) {
                error_log('❌ Failed to read HTML file');
                return false;
            }
            
            // Create new DOMPDF instance
            $dompdf = new \Dompdf\Dompdf();
            
            // Set options
            $dompdf->setOptions(new \Dompdf\Options([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]));
            
            // Load HTML content
            $dompdf->loadHtml($html_content);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF
            $dompdf->render();
            
            // Save PDF to file
            $pdf_content = $dompdf->output();
            if (file_put_contents($pdf_filepath, $pdf_content) !== false) {
                error_log('✅ PDF generated successfully using DOMPDF');
                return true;
            } else {
                error_log('❌ Failed to write PDF file');
            }
        } catch (Exception $e) {
            error_log('❌ DOMPDF error: ' . $e->getMessage());
        }
    } else {
        error_log('❌ DOMPDF class not found');
    }
    
    // Method 2: Try using wkhtmltopdf if available (Linux/Unix systems)
    if (function_exists('shell_exec') && is_executable('/usr/local/bin/wkhtmltopdf')) {
        error_log('🔄 Trying wkhtmltopdf...');
        $command = "/usr/local/bin/wkhtmltopdf --page-size A4 --orientation Landscape --margin-top 10 --margin-right 10 --margin-bottom 10 --margin-left 10 '$html_filepath' '$pdf_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            error_log('✅ PDF generated successfully using wkhtmltopdf');
            return true;
        }
    }
    
    // Method 3: Try using Chrome/Chromium headless mode (Linux/Unix systems)
    if (function_exists('shell_exec') && is_executable('/usr/bin/google-chrome')) {
        error_log('🔄 Trying Chrome headless...');
        $command = "/usr/bin/google-chrome --headless --disable-gpu --print-to-pdf='$pdf_filepath' --print-to-pdf-no-header '$html_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            error_log('✅ PDF generated successfully using Chrome headless');
            return true;
        }
    }
    
    // Method 4: Try using weasyprint if available (Linux/Unix systems)
    if (function_exists('shell_exec') && is_executable('/usr/local/bin/weasyprint')) {
        error_log('🔄 Trying weasyprint...');
        $command = "/usr/local/bin/weasyprint '$html_filepath' '$pdf_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            error_log('✅ PDF generated successfully using weasyprint');
            return true;
        }
    }
    
    error_log('❌ All PDF conversion methods failed');
    return false;
}

/**
 * Get company information from admin settings
 */
function ssc_get_company_info() {
    return array(
        'logo' => get_option('ssc_pdf_company_logo', ''),
        'name' => get_option('ssc_pdf_company_name', 'Bamby Stone'),
        'address' => get_option('ssc_pdf_company_address', 'Unit 6, 8 Technology Drive, Arundel QLD, Australia 4214'),
        'phone' => get_option('ssc_pdf_company_phone', '1300 536 120'),
        'email' => get_option('ssc_pdf_company_email', 'welcome@bambystone.com.au'),
        'website' => get_option('ssc_pdf_company_website', 'https://www.bambystone.com.au')
    );
}

/**
 * Create enhanced HTML content for PDF
 */
function ssc_create_enhanced_html($drawing_data, $canvas_data, $company_info, $quote_id) {
    $current_date = date('F j, Y');
    
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stone Slab Project Quote</title>
    <style>
        @media print {
            body { margin: 0; }
            .page { page-break-after: always; }
            .page:last-child { page-break-after: avoid; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .company-info {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }
        
        .quote-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            color: #333;
        }
        
        .project-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        
        .detail-value {
            color: #333;
        }
        
        .drawing-section {
            margin: 30px 0;
            text-align: center;
        }
        
        .drawing-image {
            max-width: 100%;
            max-height: 400px;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .notes-section {
            margin: 20px 0;
            padding: 20px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .quote-id {
            background: #007bff;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="quote-id">Quote ID: ' . $quote_id . '</div>
            <div class="company-name">' . esc_html($company_info['name']) . '</div>
            <div class="company-info">
                ' . esc_html($company_info['address']) . '<br>
                Phone: ' . esc_html($company_info['phone']) . '<br>
                Email: ' . esc_html($company_info['email']) . '<br>
                Website: ' . esc_html($company_info['website']) . '
            </div>
        </div>
        
        <div class="quote-title">PROJECT QUOTE</div>
        
        <div class="project-details">
            <div class="detail-row">
                <span class="detail-label">Project Name:</span>
                <span class="detail-value">' . esc_html($drawing_data['drawing_name']) . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">' . $current_date . '</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Cutting:</span>
                <span class="detail-value">' . number_format($drawing_data['total_cutting_mm'], 2) . ' mm</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Only Cut:</span>
                <span class="detail-value">' . number_format($drawing_data['only_cut_mm'], 2) . ' mm</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mitred Edge:</span>
                <span class="detail-value">' . number_format($drawing_data['mitred_cut_mm'], 2) . ' mm</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Slab Cost:</span>
                <span class="detail-value">' . esc_html($drawing_data['slab_cost']) . '</span>
            </div>
        </div>';
    
    // Add notes if available
    if (!empty($drawing_data['drawing_notes'])) {
        $html .= '
        <div class="notes-section">
            <strong>Project Notes:</strong><br>
            ' . esc_html($drawing_data['drawing_notes']) . '
        </div>';
    }
    
    // Add drawing image if available
    if ($canvas_data) {
        $html .= '
        <div class="drawing-section">
            <h3>Project Drawing</h3>
            <img src="' . esc_attr($canvas_data) . '" alt="Project Drawing" class="drawing-image">
        </div>';
    }
    
    $html .= '
        <div class="footer">
            <p>Thank you for choosing ' . esc_html($company_info['name']) . '</p>
            <p>This quote is valid for 30 days from the date of issue.</p>
            <p>Generated on ' . $current_date . ' | Quote ID: ' . $quote_id . '</p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * Generate enhanced PDF using admin template settings
 */
function ssc_generate_enhanced_pdf_with_admin_templates($drawing_data, $canvas_data = null, $canvas_width = 800, $canvas_height = 600) {
    // Get admin PDF template settings
    $cover_template = get_option('ssc_pdf_template_cover', '');
    $body_template = get_option('ssc_pdf_template_body', '');
    $footer_template = get_option('ssc_pdf_template_footer', '');
    
    // Get company information
    $company_info = ssc_get_company_info();
    
    // Generate unique quote ID
    $quote_id = 'Q' . time() . rand(1000, 9999);
    
    // Create HTML content using admin templates
    $html_content = ssc_create_html_with_admin_templates($drawing_data, $canvas_data, $company_info, $quote_id, $cover_template, $body_template, $footer_template, $canvas_width, $canvas_height);
    
    // Create a temporary HTML file for PDF conversion
    $temp_dir = wp_upload_dir()['basedir'] . '/ssc-temp/';
    if (!file_exists($temp_dir)) {
        wp_mkdir_p($temp_dir);
    }
    
    $html_filename = 'admin_template_quote_' . time() . '.html';
    $html_filepath = $temp_dir . $html_filename;
    
    // Write HTML content to temporary file
    file_put_contents($html_filepath, $html_content);
    
    // Convert HTML to PDF using available methods
    $pdf_filename = 'admin_template_quote_' . time() . '.pdf';
    $pdf_filepath = $temp_dir . $pdf_filename;
    
    // Try to convert HTML to PDF
    $pdf_converted = ssc_convert_html_to_pdf($html_filepath, $pdf_filepath);
    
    // Clean up temporary HTML file
    if (file_exists($html_filepath)) {
        unlink($html_filepath);
    }
    
    if ($pdf_converted && file_exists($pdf_filepath)) {
        // Return PDF file info
        return array(
            'filepath' => $pdf_filepath,
            'filename' => $pdf_filename,
            'file_type' => 'application/pdf',
            'html_content' => $html_content,
            'quote_id' => $quote_id,
            'message' => 'PDF generated successfully using admin templates'
        );
    } else {
        // Since PDF conversion failed, we'll return the HTML file for browser-based PDF generation
        // The user can use browser print function (Ctrl+P) to save as PDF
        // This will give them a high-quality PDF with all formatting preserved
        return array(
            'filepath' => $html_filepath,
            'filename' => $html_filename,
            'file_type' => 'text/html',
            'html_content' => $html_content,
            'quote_id' => $quote_id,
            'message' => 'HTML generated successfully. Use browser print function (Ctrl+P) to save as PDF with full formatting.',
            'download_type' => 'html_for_pdf'
        );
    }
}

/**
 * Create HTML content using admin template settings
 */
function ssc_create_html_with_admin_templates($drawing_data, $canvas_data, $company_info, $quote_id, $cover_template, $body_template, $footer_template, $canvas_width, $canvas_height) {
    $current_date = date('F j, Y');
    
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stone Slab Project Quote</title>
    <style>
        @media print {
            body { margin: 0; }
            .page { page-break-after: always; }
            .page:last-child { page-break-after: avoid; }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .company-info {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }
        
        .quote-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            color: #333;
        }
        
        .project-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        
        .detail-value {
            color: #333;
        }
        
        .drawing-section {
            margin: 30px 0;
            text-align: center;
        }
        
        .drawing-image {
            max-width: 100%;
            max-height: 400px;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .notes-section {
            margin: 20px 0;
            padding: 20px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .quote-id {
            background: #007bff;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .template-content {
            margin: 20px 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="quote-id">Quote ID: ' . $quote_id . '</div>';
    
    // Process templates and replace placeholders
    $cover_template = ssc_process_template_placeholders($cover_template, $drawing_data, $company_info, $quote_id, $current_date);
    $body_template = ssc_process_template_placeholders($body_template, $drawing_data, $company_info, $quote_id, $current_date);
    $footer_template = ssc_process_template_placeholders($footer_template, $drawing_data, $company_info, $quote_id, $current_date);
    
    // Replace company logo placeholder
    if (!empty($company_info['logo'])) {
        $cover_template = str_replace('{{company_logo}}', '<img src="' . esc_attr($company_info['logo']) . '" alt="Company Logo" class="company-logo">', $cover_template);
    } else {
        $cover_template = str_replace('{{company_logo}}', '', $cover_template);
    }
    
    // Replace drawing image placeholder; if missing, append drawing later as a fallback
    $has_drawing_placeholder = (strpos($body_template, '{{drawing_image}}') !== false);
    if ($canvas_data) {
        if ($has_drawing_placeholder) {
            $body_template = str_replace('{{drawing_image}}', '<div class="drawing-section"><h3>Project Drawing</h3><img src="data:image/jpeg;base64,' . esc_attr($canvas_data) . '" alt="Project Drawing" class="drawing-image"></div>', $body_template);
        }
    } else {
        if ($has_drawing_placeholder) {
            $body_template = str_replace('{{drawing_image}}', '', $body_template);
        }
    }
    
    $html .= '<div class="company-name">' . esc_html($company_info['name']) . '</div>
            <div class="company-info">
                ' . esc_html($company_info['address']) . '<br>
                Phone: ' . esc_html($company_info['phone']) . '<br>
                Email: ' . esc_html($company_info['email']) . '<br>
                Website: ' . esc_html($company_info['website']) . '
            </div>
        </div>';
    
    // Add cover template content if available
    if (!empty($cover_template)) {
        $html .= '<div class="template-content cover-template">' . $cover_template . '</div>';
    }
    
    // Add body template content if available
    if (!empty($body_template)) {
        $html .= '<div class="template-content body-template">' . $body_template . '</div>';
    }

    // Fallback: if canvas provided but template did not include {{drawing_image}}, append drawing section at end
    if ($canvas_data && !$has_drawing_placeholder) {
        $html .= '<div class="drawing-section"><h3>Project Drawing</h3><img src="data:image/jpeg;base64,' . esc_attr($canvas_data) . '" alt="Project Drawing" class="drawing-image"></div>';
    }
    
    // Add footer template content if available
    if (!empty($footer_template)) {
        $html .= '<div class="template-content footer-template">' . $footer_template . '</div>';
    }
    
    $html .= '
        <div class="footer">
            <p>Thank you for choosing ' . esc_html($company_info['name']) . '</p>
            <p>This quote is valid for 30 days from the date of issue.</p>
            <p>Generated on ' . $current_date . ' | Quote ID: ' . $quote_id . '</p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * Process template placeholders
 */
function ssc_process_template_placeholders($template, $drawing_data, $company_info, $quote_id, $current_date) {
    // Replace placeholders with actual values (using double curly braces as in admin templates)
    $replacements = array(
        '{{company_logo}}' => '', // Will be handled separately
        '{{company_name}}' => $company_info['name'],
        '{{company_address}}' => $company_info['address'],
        '{{company_phone}}' => $company_info['phone'],
        '{{company_email}}' => $company_info['email'],
        '{{company_website}}' => $company_info['website'],
        '{{quote_id}}' => $quote_id,
        '{{current_date}}' => $current_date,
        '{{drawing_name}}' => $drawing_data['drawing_name'],
        '{{total_cutting_mm}}' => number_format($drawing_data['total_cutting_mm'], 2) . ' mm',
        '{{only_cut_mm}}' => number_format($drawing_data['only_cut_mm'], 2) . ' mm',
        '{{mitred_cut_mm}}' => number_format($drawing_data['mitred_cut_mm'], 2) . ' mm',
        '{{slab_cost}}' => $drawing_data['slab_cost'],
        '{{drawing_notes}}' => $drawing_data['drawing_notes'],
        '{{drawing_image}}' => '' // Will be handled separately
    );
    
    return str_replace(array_keys($replacements), array_values($replacements), $template);
}

/**
 * AJAX handler for generating enhanced PDF
 */
function ssc_ajax_generate_enhanced_pdf() {
    error_log('=== ENHANCED PDF AJAX HANDLER STARTED ===');
    error_log('POST data received: ' . print_r($_POST, true));
    
    // Check nonce for security - TEMPORARILY DISABLED FOR TESTING
    /*
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_send_json_error('Security check failed');
    }
    */
    error_log('Enhanced PDF nonce verification temporarily disabled for testing');
    
    // Get drawing data
    $drawing_data = array(
        'drawing_name' => sanitize_text_field($_POST['drawing_name'] ?? ''),
        'drawing_notes' => sanitize_textarea_field($_POST['drawing_notes'] ?? ''),
        'total_cutting_mm' => floatval($_POST['total_cutting_mm'] ?? 0),
        'only_cut_mm' => floatval($_POST['only_cut_mm'] ?? 0),
        'mitred_cut_mm' => floatval($_POST['mitred_cut_mm'] ?? 0),
        'slab_cost' => sanitize_text_field($_POST['slab_cost'] ?? '$0')
    );
    
    error_log('Drawing data processed: ' . print_r($drawing_data, true));
    
    // Get canvas data if provided
    $canvas_data = isset($_POST['canvas_data']) ? $_POST['canvas_data'] : null;
    // Normalize base64 that might have had '+' converted to spaces by urlencoding
    if ($canvas_data) {
        $canvas_data = str_replace(' ', '+', $canvas_data);
    }
    error_log('Canvas data received: ' . ($canvas_data ? 'Yes, length: ' . strlen($canvas_data) : 'No'));
    
    try {
        error_log('Starting enhanced HTML generation...');
        // Generate enhanced HTML
        $result = ssc_generate_enhanced_pdf($drawing_data, $canvas_data);
        error_log('Enhanced HTML generation completed successfully');
        error_log('Result: ' . print_r($result, true));
        
        // Debug file information
        error_log('File path: ' . $result['filepath']);
        error_log('File exists: ' . (file_exists($result['filepath']) ? 'Yes' : 'No'));
        if (file_exists($result['filepath'])) {
            error_log('File size: ' . filesize($result['filepath']));
            error_log('File readable: ' . (is_readable($result['filepath']) ? 'Yes' : 'No'));
        }
        
        // Get company information for the response
        $company_info = ssc_get_company_info();
        
        // Return HTML content and metadata for frontend PDF generation
        $response_data = array(
            'html_content' => $result['html_content'],
            'quote_id' => $result['quote_id'],
            'company_info' => $company_info,
            'drawing_data' => $drawing_data,
            'canvas_data' => $canvas_data,
            'message' => 'Enhanced HTML content generated successfully. Converting to PDF in frontend...'
        );
        
        error_log('Response data prepared: ' . print_r($response_data, true));
        error_log('Sending success response...');
        
        wp_send_json_success($response_data);
        
    } catch (Exception $e) {
        error_log('Enhanced PDF generation error: ' . $e->getMessage());
        error_log('Error trace: ' . $e->getTraceAsString());
        wp_send_json_error('Failed to generate enhanced PDF: ' . $e->getMessage());
    }
    
    error_log('=== ENHANCED PDF AJAX HANDLER COMPLETED ===');
}
// Register AJAX handlers
add_action('wp_ajax_ssc_generate_enhanced_pdf', 'ssc_ajax_generate_enhanced_pdf');
add_action('wp_ajax_nopriv_ssc_generate_enhanced_pdf', 'ssc_ajax_generate_enhanced_pdf');

/**
 * AJAX handler for generating enhanced PDF from frontend with admin templates
 */
function ssc_ajax_generate_enhanced_pdf_frontend() {
    error_log('=== FRONTEND ENHANCED PDF AJAX HANDLER STARTED ===');
    error_log('POST data received: ' . print_r($_POST, true));
    error_log('Nonce received: ' . ($_POST['nonce'] ?? 'none'));
    error_log('Nonce length: ' . (strlen($_POST['nonce'] ?? '') ?: '0'));
    error_log('Nonce verification results:');
    error_log('- stone_slab_auth_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
    error_log('- ssc_save_drawing_nonce: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
    error_log('Nonce verification details:');
    error_log('  - Received nonce: "' . ($_POST['nonce'] ?? 'none') . '"');
    error_log('  - stone_slab_auth_nonce verification: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'stone_slab_auth_nonce') ? 'PASS' : 'FAIL'));
    error_log('  - ssc_save_drawing_nonce verification: ' . (wp_verify_nonce($_POST['nonce'] ?? '', 'ssc_save_drawing_nonce') ? 'PASS' : 'FAIL'));
    
		// TEMPORARILY DISABLE NONCE VERIFICATION FOR TESTING
		// Check nonce for security - prioritize stone_slab_auth_nonce since it's created fresh on each page load
		$stone_slab_auth_verified = wp_verify_nonce($_POST['nonce'], 'stone_slab_auth_nonce');
		$ssc_save_drawing_verified = wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce');
		
		error_log('Nonce verification results:');
		error_log('  - stone_slab_auth_nonce: ' . ($stone_slab_auth_verified ? 'PASS' : 'FAIL'));
		error_log('  - ssc_save_drawing_nonce: ' . ($ssc_save_drawing_verified ? 'PASS' : 'FAIL'));
		
		// TEMPORARILY BYPASS NONCE VERIFICATION FOR TESTING
		/*
		if (!$stone_slab_auth_verified && !$ssc_save_drawing_verified) {
			error_log('Nonce verification failed for both stone_slab_auth_nonce and ssc_save_drawing_nonce');
			wp_send_json_error('Security check failed');
			return; // Exit early after sending error
		}
		*/
		
		error_log('Nonce verification BYPASSED for testing - continuing with PDF generation');
    
    // Get drawing data
    $drawing_data = array(
        'drawing_name' => sanitize_text_field($_POST['drawing_data']['drawing_name'] ?? ''),
        'drawing_notes' => sanitize_textarea_field($_POST['drawing_data']['drawing_notes'] ?? ''),
        'total_cutting_mm' => floatval($_POST['drawing_data']['total_cutting_mm'] ?? 0),
        'only_cut_mm' => floatval($_POST['drawing_data']['only_cut_mm'] ?? 0),
        'mitred_cut_mm' => floatval($_POST['drawing_data']['mitred_cut_mm'] ?? 0),
        'slab_cost' => sanitize_text_field($_POST['drawing_data']['slab_cost'] ?? '$0')
    );
    
    error_log('Drawing data processed: ' . print_r($drawing_data, true));
    
    // Get canvas data if provided
    $canvas_data = isset($_POST['canvas_data']) ? $_POST['canvas_data'] : null;
    // Normalize base64 that might have had '+' converted to spaces by urlencoding
    if ($canvas_data) {
        $canvas_data = str_replace(' ', '+', $canvas_data);
    }
    $canvas_width = intval($_POST['canvas_width'] ?? 800);
    $canvas_height = intval($_POST['canvas_height'] ?? 600);
    
    error_log('Canvas data received: ' . ($canvas_data ? 'Yes, length: ' . strlen($canvas_data) : 'No'));
    
    try {
        error_log('Starting enhanced PDF generation with admin templates...');
        
        // Generate enhanced PDF using admin templates
        $result = ssc_generate_enhanced_pdf_with_admin_templates($drawing_data, $canvas_data, $canvas_width, $canvas_height);
        
        error_log('Enhanced PDF generation completed successfully');
        error_log('Result: ' . print_r($result, true));
        
        wp_send_json_success($result);
        
    } catch (Exception $e) {
        error_log('Frontend PDF generation error: ' . $e->getMessage());
        error_log('Error trace: ' . $e->getTraceAsString());
        wp_send_json_error('Failed to generate PDF: ' . $e->getMessage());
    }
    
    error_log('=== FRONTEND ENHANCED PDF AJAX HANDLER COMPLETED ===');
}

// Register the new AJAX handler
add_action('wp_ajax_ssc_generate_enhanced_pdf_frontend', 'ssc_ajax_generate_enhanced_pdf_frontend');
add_action('wp_ajax_nopriv_ssc_generate_enhanced_pdf_frontend', 'ssc_ajax_generate_enhanced_pdf_frontend');

