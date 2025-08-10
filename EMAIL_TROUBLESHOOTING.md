# Stone Slab Calculator - Email Verification Troubleshooting

## Problem
Verification emails are not being sent to users after registration.

## Quick Fixes to Try

### 1. Test Email System (Admin Only)
- Go to WordPress Admin → Tools → Email Test
- Click "Send Test Email" to check if emails work at all
- This will show detailed debugging information

### 2. Check WordPress Email Configuration
- Verify admin email is set correctly in Settings → General
- Check if SMTP is configured in wp-config.php
- Ensure no email plugins are conflicting

### 3. Check Server Configuration
- Contact hosting provider about email restrictions
- Verify PHP mail() function is available
- Check if server allows outgoing emails

### 4. Test with Test Script
- Place `test-email.php` in WordPress root directory
- Access via browser: `yoursite.com/test-email.php`
- This will show detailed email configuration

## Common Issues & Solutions

### Issue 1: Nonce Verification Failed
**Solution**: Nonce verification is temporarily disabled for testing. Check if AJAX calls are working.

### Issue 2: WordPress wp_mail() Not Working
**Solutions**:
- Install WP Mail SMTP plugin
- Configure SMTP settings
- Check hosting email restrictions

### Issue 3: Emails Going to Spam
**Solutions**:
- Configure proper From headers
- Use SMTP instead of default mail
- Set up SPF/DKIM records

### Issue 4: Registration Works But No Email
**Solutions**:
- Check error logs in wp-content/debug.log
- Verify email function is being called
- Test with admin email first

## Debugging Steps

### Step 1: Enable WordPress Debugging
Add to wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Step 2: Check Error Logs
Look for entries starting with "Stone Slab Calculator:" in:
- wp-content/debug.log
- WordPress admin error logs
- Server error logs

### Step 3: Test Email Function
Use the admin test page to send test emails and see detailed results.

### Step 4: Check AJAX Calls
Verify that the resend verification AJAX call is working in browser developer tools.

## File Locations
- **Main Plugin**: `stone-slab-calculator.php`
- **Email Verification**: `includes/email-verification.php`
- **Admin Settings**: `admin/admin.php`
- **Calculator Template**: `templates/calculator.php`

## Support
If issues persist:
1. Check error logs for specific error messages
2. Test with the provided test scripts
3. Contact hosting provider about email restrictions
4. Consider using a third-party email service (SendGrid, Mailgun, etc.)

## Security Note
Nonce verification is temporarily disabled for testing. Re-enable it in production by uncommenting the nonce verification code in `includes/email-verification.php`.
