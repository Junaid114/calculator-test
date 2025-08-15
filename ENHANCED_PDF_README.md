# Enhanced PDF Generator for Stone Slab Calculator

## Overview

The Stone Slab Calculator plugin has been enhanced with professional PDF generation capabilities, including A3 size support, customizable templates, and company branding.

## New Features

### 1. A3 Size Support
- **Page Sizes Available:**
  - A3 (297×420mm) - **Recommended for detailed drawings**
  - A4 (210×297mm) - Standard size
  - Letter (216×279mm) - US Standard
  - Legal (216×356mm) - US Legal

### 2. High-Resolution Export
- **Quality Options:**
  - Low: Smaller file, faster generation
  - Medium: Balanced quality and size
  - High: Best quality, larger file (**Default**)

### 3. Editable Templates in Admin Panel

#### Cover Page Template
- Company logo placement
- Company information display
- Project title and quote ID
- Date and contact details

#### Body Template
- Project details section
- Drawing image integration
- Calculations summary
- User notes section

#### Footer Template
- Company contact information
- Quote ID and generation date
- Professional closing message

### 4. Company Branding & Contact Information
- Company logo (URL input)
- Company name
- Address
- Phone number
- Email address
- Website URL

## Admin Configuration

### Accessing Settings
1. Go to **WordPress Admin** → **Slab Calculator Settings**
2. Navigate to the new PDF template sections

### Template Configuration

#### Cover Template
```text
{{company_logo}}
{{company_name}}
{{company_address}}
{{company_phone}}
{{company_email}}
{{company_website}}

PROJECT QUOTE
{{drawing_name}}

Date: {{current_date}}
Quote ID: {{quote_id}}
```

#### Body Template
```text
PROJECT DETAILS

Drawing Name: {{drawing_name}}
Total Cutting Area: {{total_cutting_mm}} mm
Standard Cutting Area: {{only_cut_mm}} mm
Mitred Cutting Area: {{mitred_cut_mm}} mm
Slab Cost: {{slab_cost}}

{{drawing_image}}

NOTES
{{drawing_notes}}

CALCULATIONS
Total Cutting Required: {{total_cutting_mm}} mm
Standard Cuts: {{only_cut_mm}} mm
Mitred Cuts: {{mitred_cut_mm}} mm
Total Cost: {{slab_cost}}
```

#### Footer Template
```text
Thank you for choosing {{company_name}}!

For questions or to proceed with this quote, please contact us:
Phone: {{company_phone}}
Email: {{company_email}}
Website: {{company_website}}

{{company_address}}

Quote ID: {{quote_id}} | Generated on: {{current_date}}
```

### Available Dynamic Fields

| Field | Description | Used In |
|-------|-------------|---------|
| `{{company_logo}}` | Company logo image | Cover |
| `{{company_name}}` | Company name | Cover, Footer |
| `{{company_address}}` | Company address | Cover, Footer |
| `{{company_phone}}` | Company phone | Cover, Footer |
| `{{company_email}}` | Company email | Cover, Footer |
| `{{company_website}}` | Company website | Cover, Footer |
| `{{drawing_name}}` | Drawing/project name | Cover, Body |
| `{{total_cutting_mm}}` | Total cutting area in mm | Body |
| `{{only_cut_mm}}` | Standard cutting area in mm | Body |
| `{{mitred_cut_mm}}` | Mitred cutting area in mm | Body |
| `{{slab_cost}}` | Cost of the slab | Body |
| `{{drawing_image}}` | The actual drawing image | Body |
| `{{drawing_notes}}` | User notes about the drawing | Body |
| `{{current_date}}` | Current date | Cover, Body, Footer |
| `{{quote_id}}` | Unique quote ID | Cover, Body, Footer |

## User Experience

### PDF Quality Selection
Users can now choose between:
- **Enhanced PDF**: A3 size, high quality, company branding, cover page
- **Basic PDF**: A4 size, standard quality, simple layout

### Enhanced PDF Features
1. **Cover Page**: Professional company branding and project overview
2. **Body Content**: Detailed project information with drawing image
3. **Footer**: Company contact information and quote details
4. **High Resolution**: A3 format for detailed drawings
5. **Professional Layout**: Consistent branding across all pages

### Fallback System
If enhanced PDF generation fails, the system automatically falls back to basic PDF generation, ensuring users always get a PDF.

## Technical Implementation

### Files Modified/Added
- `admin/admin.php` - Added PDF template settings
- `includes/enhanced-pdf-generator.php` - New enhanced PDF generation logic
- `templates/calculator.php` - Updated PDF generation interface
- `stone-slab-calculator.php` - Included enhanced PDF generator

### AJAX Endpoints
- `ssc_generate_enhanced_pdf` - Generates enhanced PDFs
- `ssc_save_drawing` - Saves drawings with generated PDFs

### Dependencies
- jsPDF library (already included)
- WordPress admin settings API
- AJAX handling for PDF generation

## Testing

### Test File
Run `test-enhanced-pdf.php` to verify:
- Enhanced PDF generator is loaded
- Admin settings are accessible
- Company information is configured
- Templates are available

### Manual Testing
1. Configure PDF templates in admin
2. Use the calculator to create a drawing
3. Select "Enhanced PDF" quality
4. Save the drawing
5. Verify the generated PDF has all features

## Benefits

### For Users
- Professional-looking PDFs
- A3 size for detailed drawings
- Company branding and contact information
- Choice between basic and enhanced PDFs

### For Administrators
- Fully customizable PDF templates
- Company branding control
- Professional presentation
- Easy template management

### For Business
- Professional image
- Consistent branding
- Better customer experience
- Detailed project documentation

## Troubleshooting

### Common Issues
1. **Enhanced PDF fails to generate**: Check admin settings and fallback to basic PDF
2. **Template not displaying**: Verify dynamic field syntax
3. **Company logo not showing**: Check logo URL accessibility
4. **A3 size not working**: Verify jsPDF version supports A3 format

### Debug Mode
Enable WordPress debug logging to troubleshoot PDF generation issues.

## Future Enhancements

### Planned Features
- PDF template preview in admin
- Multiple template themes
- Custom color schemes
- Advanced typography options
- Watermark support
- Digital signature integration

### Customization Options
- Template import/export
- Bulk template management
- User role-based templates
- Conditional content display

## Support

For technical support or feature requests, please contact the development team or refer to the plugin documentation.

---

**Version**: 2.0  
**Last Updated**: December 2024  
**Compatibility**: WordPress 5.0+, PHP 7.4+
