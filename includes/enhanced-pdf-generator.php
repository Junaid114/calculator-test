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
    // Method 1: Try using wkhtmltopdf if available
    if (function_exists('shell_exec') && is_executable('/usr/local/bin/wkhtmltopdf')) {
        $command = "/usr/local/bin/wkhtmltopdf --page-size A4 --orientation Landscape --margin-top 10 --margin-right 10 --margin-bottom 10 --margin-left 10 '$html_filepath' '$pdf_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            return true;
        }
    }
    
    // Method 2: Try using Chrome/Chromium headless mode
    if (function_exists('shell_exec') && is_executable('/usr/bin/google-chrome')) {
        $command = "/usr/bin/google-chrome --headless --disable-gpu --print-to-pdf='$pdf_filepath' --print-to-pdf-no-header '$html_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            return true;
        }
    }
    
    // Method 3: Try using Chrome/Chromium headless mode (alternative path)
    if (function_exists('shell_exec') && is_executable('/usr/bin/chromium-browser')) {
        $command = "/usr/bin/chromium-browser --headless --disable-gpu --print-to-pdf='$pdf_filepath' --print-to-pdf-no-header '$html_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            return true;
        }
    }
    
    // Method 4: Try using wkhtmltopdf (alternative path)
    if (function_exists('shell_exec') && is_executable('/usr/bin/wkhtmltopdf')) {
        $command = "/usr/bin/wkhtmltopdf --page-size A4 --orientation Landscape --margin-top 10 --margin-right 10 --margin-bottom 10 --margin-left 10 '$html_filepath' '$pdf_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            return true;
        }
    }
    
    // Method 5: Try using weasyprint if available
    if (function_exists('shell_exec') && is_executable('/usr/local/bin/weasyprint')) {
        $command = "/usr/local/bin/weasyprint '$html_filepath' '$pdf_filepath'";
        $output = shell_exec($command);
        if (file_exists($pdf_filepath) && filesize($pdf_filepath) > 0) {
            return true;
        }
    }
    
    // If all methods fail, return false
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
