<?php
/**
 * Enhanced PDF Generator for Stone Slab Calculator
 * 
 * This file handles the generation of high-quality PDFs with:
 * - A3 size support
 * - Customizable templates (cover, body, footer)
 * - Company branding and contact information
 * - High-resolution output
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate enhanced PDF with custom templates
 */
function ssc_generate_enhanced_pdf($drawing_data, $canvas_data = null) {
    // Get PDF settings from admin
    $page_size = get_option('ssc_pdf_page_size', 'A3');
    $export_quality = get_option('ssc_pdf_export_quality', 'high');
    
    // Get company information
    $company_info = ssc_get_company_info();
    
    // Get templates
    $cover_template = get_option('ssc_pdf_template_cover', '');
    $body_template = get_option('ssc_pdf_template_body', '');
    $footer_template = get_option('ssc_pdf_template_footer', '');
    
    // Generate unique quote ID
    $quote_id = 'Q' . time() . rand(1000, 9999);
    
    // Prepare template data
    $template_data = array(
        'company_logo' => $company_info['logo'],
        'company_name' => $company_info['name'],
        'company_address' => $company_info['address'],
        'company_phone' => $company_info['phone'],
        'company_email' => $company_info['email'],
        'company_website' => $company_info['website'],
        'drawing_name' => $drawing_data['drawing_name'] ?? 'Custom Drawing',
        'total_cutting_mm' => $drawing_data['total_cutting_mm'] ?? '0',
        'only_cut_mm' => $drawing_data['only_cut_mm'] ?? '0',
        'mitred_cut_mm' => $drawing_data['mitred_cut_mm'] ?? '0',
        'slab_cost' => $drawing_data['slab_cost'] ?? '$0',
        'drawing_notes' => $drawing_data['drawing_notes'] ?? '',
        'current_date' => date('F j, Y'),
        'quote_id' => $quote_id
    );
    
    // Process templates with dynamic fields
    $cover_content = ssc_process_template($cover_template, $template_data);
    $body_content = ssc_process_template($body_template, $template_data);
    $footer_content = ssc_process_template($footer_template, $template_data);
    
    // Create PDF using jsPDF
    $pdf = ssc_create_enhanced_pdf($page_size, $export_quality);
    
    // Add cover page
    if (!empty($cover_content)) {
        ssc_add_cover_page($pdf, $cover_content, $company_info);
        $pdf->addPage();
    }
    
    // Add body content
    ssc_add_body_content($pdf, $body_content, $canvas_data, $drawing_data);
    
    // Add footer to all pages
    ssc_add_footer_to_pages($pdf, $footer_content, $company_info);
    
    return $pdf;
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
 * Process template with dynamic field replacements
 */
function ssc_process_template($template, $data) {
    if (empty($template)) {
        return '';
    }
    
    foreach ($data as $key => $value) {
        $template = str_replace('{{' . $key . '}}', $value, $template);
    }
    
    return $template;
}

/**
 * Create enhanced PDF with specified settings
 */
function ssc_create_enhanced_pdf($page_size, $quality) {
    // Map page sizes to jsPDF format
    $page_sizes = array(
        'A4' => 'a4',
        'A3' => 'a3',
        'Letter' => 'letter',
        'Legal' => 'legal'
    );
    
    $format = $page_sizes[$page_size] ?? 'a3';
    
    // Create PDF with high quality settings
    $pdf = new jsPDF(array(
        'orientation' => 'p', // Portrait for better A3 layout
        'unit' => 'mm',
        'format' => $format,
        'compress' => false // Disable compression for better quality
    ));
    
    // Set document properties
    $pdf->setProperties(array(
        'title' => 'Stone Slab Project Quote',
        'subject' => 'Project Drawing and Calculations',
        'author' => 'Bamby Stone Calculator',
        'creator' => 'Stone Slab Calculator Plugin'
    ));
    
    return $pdf;
}

/**
 * Add cover page to PDF
 */
function ssc_add_cover_page($pdf, $content, $company_info) {
    $pdf->setFont('helvetica', 'bold', 24);
    $pdf->setTextColor(0, 0, 0);
    
    // Add company logo if available
    if (!empty($company_info['logo'])) {
        try {
            // Try to add logo (this will fail gracefully if logo can't be loaded)
            $pdf->addImage($company_info['logo'], 'JPEG', 20, 20, 60, 30);
        } catch (Exception $e) {
            // Logo failed to load, continue without it
        }
    }
    
    // Add company name
    $pdf->setFont('helvetica', 'bold', 28);
    $pdf->text(20, 80, $company_info['name']);
    
    // Add company contact info
    $pdf->setFont('helvetica', 'normal', 12);
    $pdf->text(20, 100, $company_info['address']);
    $pdf->text(20, 110, 'Phone: ' . $company_info['phone']);
    $pdf->text(20, 120, 'Email: ' . $company_info['email']);
    $pdf->text(20, 130, 'Website: ' . $company_info['website']);
    
    // Add project title
    $pdf->setFont('helvetica', 'bold', 20);
    $pdf->text(20, 180, 'PROJECT QUOTE');
    
    // Add content lines
    $lines = explode("\n", $content);
    $y_position = 200;
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            if (strpos($line, '{{') === false) { // Only add non-template lines
                $pdf->setFont('helvetica', 'normal', 14);
                $pdf->text(20, $y_position, $line);
                $y_position += 8;
            }
        }
    }
}

/**
 * Add body content to PDF
 */
function ssc_add_body_content($pdf, $content, $canvas_data, $drawing_data) {
    $pdf->setFont('helvetica', 'normal', 12);
    $pdf->setTextColor(0, 0, 0);
    
    // Add content lines
    $lines = explode("\n", $content);
    $y_position = 30;
    $page_height = $pdf->getPageHeight();
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            // Check if this is a special field
            if ($line === '{{drawing_image}}' && $canvas_data) {
                // Add drawing image
                ssc_add_drawing_image($pdf, $canvas_data, $y_position);
                $y_position += 150; // Space for image
            } elseif (strpos($line, '{{') === false) {
                // Regular text line
                if ($y_position > $page_height - 50) {
                    $pdf->addPage();
                    $y_position = 30;
                }
                
                // Check if it's a heading
                if (strpos($line, 'PROJECT DETAILS') !== false || 
                    strpos($line, 'NOTES') !== false || 
                    strpos($line, 'CALCULATIONS') !== false) {
                    $pdf->setFont('helvetica', 'bold', 16);
                    $pdf->text(20, $y_position, $line);
                    $y_position += 12;
                    $pdf->setFont('helvetica', 'normal', 12);
                } else {
                    $pdf->text(20, $y_position, $line);
                    $y_position += 8;
                }
            }
        }
    }
}

/**
 * Add drawing image to PDF
 */
function ssc_add_drawing_image($pdf, $canvas_data, $y_position) {
    try {
        // Convert canvas data to image
        $image_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $canvas_data));
        
        // Create temporary file
        $temp_file = tempnam(sys_get_temp_dir(), 'ssc_pdf_');
        file_put_contents($temp_file, $image_data);
        
        // Add image to PDF
        $pdf->addImage($temp_file, 'JPEG', 20, $y_position, 170, 130);
        
        // Clean up temp file
        unlink($temp_file);
    } catch (Exception $e) {
        // If image fails, add placeholder text
        $pdf->setFont('helvetica', 'italic', 12);
        $pdf->setTextColor(128, 128, 128);
        $pdf->text(20, $y_position + 65, '[Drawing Image - See attached file]');
        $pdf->setTextColor(0, 0, 0);
    }
}

/**
 * Add footer to all pages
 */
function ssc_add_footer_to_pages($pdf, $footer_content, $company_info) {
    $page_count = $pdf->getNumberOfPages();
    
    for ($i = 1; $i <= $page_count; $i++) {
        $pdf->setPage($i);
        
        // Get page dimensions
        $page_height = $pdf->getPageHeight();
        
        // Add footer content
        $pdf->setFont('helvetica', 'normal', 10);
        $pdf->setTextColor(128, 128, 128);
        
        $lines = explode("\n", $footer_content);
        $y_position = $page_height - 20;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '{{') === false) {
                $pdf->text(20, $y_position, $line);
                $y_position += 5;
            }
        }
        
        // Add page number
        $pdf->text($pdf->getPageWidth() - 30, $page_height - 10, 'Page ' . $i . ' of ' . $page_count);
    }
}

/**
 * AJAX handler for generating enhanced PDF
 */
function ssc_ajax_generate_enhanced_pdf() {
    // Check nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'ssc_save_drawing_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    // Get drawing data
    $drawing_data = array(
        'drawing_name' => sanitize_text_field($_POST['drawing_name'] ?? ''),
        'drawing_notes' => sanitize_textarea_field($_POST['drawing_notes'] ?? ''),
        'total_cutting_mm' => floatval($_POST['total_cutting_mm'] ?? 0),
        'only_cut_mm' => floatval($_POST['only_cut_mm'] ?? 0),
        'mitred_cut_mm' => floatval($_POST['mitred_cut_mm'] ?? 0),
        'slab_cost' => sanitize_text_field($_POST['slab_cost'] ?? '$0')
    );
    
    // Get canvas data if provided
    $canvas_data = isset($_POST['canvas_data']) ? $_POST['canvas_data'] : null;
    
    try {
        // Generate PDF
        $pdf = ssc_generate_enhanced_pdf($drawing_data, $canvas_data);
        
        // Convert to blob
        $pdf_blob = $pdf->output('blob');
        
        // Create file
        $filename = 'enhanced_quote_' . time() . '.pdf';
        $pdf_file = new File([$pdf_blob], $filename, array('type' => 'application/pdf'));
        
        wp_send_json_success(array(
            'pdf_file' => $pdf_file,
            'filename' => $filename,
            'message' => 'Enhanced PDF generated successfully'
        ));
        
    } catch (Exception $e) {
        wp_send_json_error('Failed to generate PDF: ' . $e->getMessage());
    }
}

// Register AJAX handlers
add_action('wp_ajax_ssc_generate_enhanced_pdf', 'ssc_ajax_generate_enhanced_pdf');
add_action('wp_ajax_nopriv_ssc_generate_enhanced_pdf', 'ssc_ajax_generate_enhanced_pdf');
