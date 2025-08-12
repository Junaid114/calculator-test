# Stone Slab Calculator - Production Deployment Checklist

## ✅ Pre-Deployment Changes Made

### 1. Version Update
- Updated plugin version from 1.0 to 1.1

### 2. Debug Code Removal
- Removed all `error_log()` statements
- Removed debug console.log statements
- Removed debug functions (`debugCanvasObjects`)
- Removed debug comments and test functions

### 3. Security Features Enabled
- Enabled nonce verification for all AJAX handlers
- Enabled email verification system
- Enabled database table creation and management
- Enabled PDF download and viewing functionality

### 4. Development Features Removed
- Removed email testing admin menu
- Removed debug email functions
- Removed test email functionality
- Cleaned up temporarily disabled features

## 🚀 Production Deployment Steps

### 1. Backup Current Production Site
```bash
# Backup database
wp db export backup_before_update.sql

# Backup plugin files
cp -r stone-slab-calculator stone-slab-calculator-backup
```

### 2. Update Plugin Files
- Upload the new plugin files to replace the existing ones
- Ensure all file permissions are correct (644 for files, 755 for directories)

### 3. Database Updates
- The plugin will automatically create/update database tables on activation
- No manual database changes required

### 4. Test Critical Functions
- [ ] Calculator loads correctly
- [ ] User registration works
- [ ] Email verification sends emails
- [ ] User login works
- [ ] Drawing save/load works
- [ ] PDF generation works
- [ ] PDF download works

### 5. Email Configuration
- Ensure WordPress email is configured properly
- Test email verification system
- Consider using SMTP plugin for reliable email delivery

## 🔧 Post-Deployment Configuration

### 1. Admin Settings
- Go to WordPress Admin → Slab Calculator Settings
- Configure edge profiles
- Set calculator dimensions
- Configure access permissions

### 2. Email Template
- Customize email template in admin settings
- Test email delivery

### 3. User Access
- Configure which user roles can access the calculator
- Set up public quote access if needed

## ⚠️ Important Notes

1. **Email Verification**: Users must verify their email before logging in
2. **Security**: All AJAX requests now require valid nonces
3. **Database**: Tables are created automatically on plugin activation
4. **Compatibility**: Requires WooCommerce plugin to be active

## 🆘 Troubleshooting

### Common Issues:
1. **Emails not sending**: Check WordPress email configuration
2. **Calculator not loading**: Verify WooCommerce is active
3. **Permission errors**: Check user role settings in admin
4. **PDF issues**: Verify upload directory permissions

### Support:
- Check WordPress error logs
- Verify plugin settings in admin panel
- Test with default WordPress theme

## 📋 Rollback Plan

If issues occur:
1. Deactivate the new plugin
2. Restore backup files
3. Restore database backup if needed
4. Investigate issues before re-deploying

---
**Plugin Version**: 1.1  
**Deployment Date**: [Insert Date]  
**Deployed By**: [Insert Name]
