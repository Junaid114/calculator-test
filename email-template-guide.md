# Editable HTML Email Template Guide

## Overview
The email template system has been upgraded with a fully editable HTML template in the admin panel that supports dynamic fields for personalized emails.

## Admin Setup

### 1. Access Email Template Settings
1. Go to **WordPress Admin** → **Slab Calculator Settings**
2. Scroll down to the **"Email Template"** section
3. You'll see a rich text editor with the email template

### 2. Available Dynamic Fields
The following dynamic fields are automatically replaced when emails are sent:

| Field | Description | Example Output |
|-------|-------------|----------------|
| `{{customer_name}}` | Customer name from user account | "John Smith" or "jsmith" |
| `{{slab_name}}` | Project/slab name from URL | "Kitchen Island" |
| `{{total_cutting_mm}}` | Total cutting area | "1,234 mm²" |
| `{{drawing_link}}` | Link back to calculator | Full URL with parameters |

### 3. Template Features
- **Rich Text Editor**: WYSIWYG editor with formatting tools
- **HTML Support**: Full HTML and CSS styling capability
- **Default Template**: Professional template with company branding
- **Fallback**: Falls back to static template if none configured

## Using the Template

### 1. Basic Usage
Simply use the placeholder syntax in your template:
```html
<p>Hi {{customer_name}},</p>
<p>Your project "{{slab_name}}" requires {{total_cutting_mm}} mm² of cutting.</p>
```

### 2. Advanced Styling
You can use full HTML and CSS:
```html
<div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
    <h3 style="color: #333;">Project: {{slab_name}}</h3>
    <p><strong>Total Area:</strong> {{total_cutting_mm}} mm²</p>
</div>
```

### 3. Professional Layout
The default template includes:
- Company header and branding
- Project details section
- Payment terms and next steps
- Contact information
- Legal disclaimer

## Data Sources

### Customer Name
- **Source**: WordPress user account
- **Priority**: Display name → Username
- **Registration**: Only registered users can receive emails

### Slab Name
- **Source**: URL parameter `name`
- **Example**: `?name=Kitchen%20Island` → "Kitchen Island"
- **Default**: "Custom Slab" if not provided

### Total Cutting MM
- **Source**: Real-time calculation from canvas
- **Components**: Only Cut Area + Mitred Edge Area
- **Format**: Comma-separated number (e.g., "1,234")

### Drawing Link
- **Source**: Current page URL with all parameters
- **Purpose**: Customer can return to exact same drawing
- **Includes**: All shape data and settings

## Testing the Template

### 1. Admin Testing
1. Modify the template in admin settings
2. Add/remove dynamic fields
3. Save changes
4. Test with actual email

### 2. Email Testing Steps
1. Open calculator with parameters:
   ```
   ?name=Test%20Project&slab_width=1000&slab_heigth=600&pad_width=2000&pad_heigth=1500&edges=...
   ```
2. Create shapes on canvas
3. Use Download → Send Email
4. Enter registered email address
5. Verify email received with correct dynamic content

### 3. Validation Checklist
- [ ] Customer name appears correctly
- [ ] Slab name matches URL parameter
- [ ] Total cutting MM matches calculator display
- [ ] Drawing link works and returns to calculator
- [ ] PDF attachment is included
- [ ] Template formatting looks professional

## Common Issues & Solutions

### Dynamic Fields Not Replacing
**Issue**: Fields show as `{{customer_name}}` instead of actual names
**Solution**: 
- Check template syntax (double curly braces)
- Ensure user is registered for customer_name
- Verify data is being sent from frontend

### Missing Customer Name
**Issue**: Shows empty or "Guest User"
**Solution**: User must be registered and logged in

### Incorrect Total MM
**Issue**: Wrong calculation in email
**Solution**: 
- Ensure shapes are properly drawn
- Check that `getTotalMM()` is called before sending
- Verify calculation includes all shape types

### Template Not Applying
**Issue**: Emails use old static template
**Solution**:
- Save template in admin settings
- Clear any caching
- Check for PHP errors in logs

## Backup & Recovery

### Export Template
Copy template content from admin editor and save to file

### Import Template
Paste saved content back into admin editor

### Reset to Default
Delete all content and save - system will regenerate default template

## Email Delivery

### Requirements
- **WordPress Mail**: Properly configured
- **SMTP**: Recommended for reliable delivery
- **User Registration**: Only registered users receive emails
- **File Permissions**: Upload directory must be writable

### Attachments
- PDF is automatically generated and attached
- High-resolution canvas export
- Professional formatting
- Includes all shape measurements

## Security Notes

- Only registered users can receive emails
- All input is sanitized before processing
- Email addresses validated before sending
- Temporary files cleaned up after sending
- No sensitive data exposed in templates

## Customization Examples

### 1. Add Company Logo
```html
<div style="text-align: center; margin-bottom: 30px;">
    <img src="https://yoursite.com/logo.png" alt="Company Logo" style="max-width: 200px;">
</div>
```

### 2. Custom Styling
```html
<style>
.project-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
}
</style>
<div class="project-header">
    <h2>Project: {{slab_name}}</h2>
</div>
```

### 3. Conditional Content
```html
<!-- Note: Conditional logic requires custom PHP modifications -->
<p>Thank you {{customer_name}} for choosing our services!</p>
```

This email template system provides a powerful, flexible solution for personalized customer communications while maintaining professional standards and security.
