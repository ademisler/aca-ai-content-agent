# Common Issues & Troubleshooting

Quick solutions to the most frequently encountered issues with AI Content Agent (ACA).

## 🚨 Quick Diagnostics

### Plugin Health Check
Before troubleshooting specific issues, run these basic checks:

1. **Plugin Status**
   ```
   WordPress Admin → Plugins → AI Content Agent (ACA)
   ```
   - Ensure plugin is activated
   - Check for any error messages
   - Verify version is 2.4.6

2. **System Requirements**
   - WordPress 5.0+ (6.7+ recommended)
   - PHP 7.4+ (8.0+ recommended)
   - Memory: 256MB+ (512MB recommended)
   - Gemini AI API key configured

3. **Basic Functionality Test**
   - Try generating 1-2 test ideas
   - Create a test draft
   - Check settings pages load correctly

## 🔧 Installation & Activation Issues

### Plugin Won't Activate
**Error**: "Plugin could not be activated"

**Common Causes & Solutions:**

#### PHP Version Too Old
```
Solution: Upgrade to PHP 7.4 or higher
- Contact hosting provider to upgrade PHP
- Check current version: WordPress Admin → Site Health
```

#### Memory Limit Too Low
```
Solution: Increase PHP memory limit
- Add to wp-config.php: ini_set('memory_limit', '256M');
- Or contact hosting provider to increase limit
```

#### Plugin Conflicts
```
Solution: Identify conflicting plugins
1. Deactivate all other plugins
2. Try activating ACA
3. If successful, reactivate plugins one by one
4. Identify the conflicting plugin
```

#### File Permissions
```
Solution: Fix file permissions
- Plugin folder should be 755
- Plugin files should be 644
- Contact hosting provider if needed
```

### Plugin Activated But Not Working
**Symptoms**: Plugin active but admin menu missing or broken

**Solutions:**

1. **Clear All Caching**
   - Clear WordPress cache
   - Clear browser cache
   - Clear CDN cache if applicable

2. **Check for JavaScript Errors**
   - Open browser developer tools (F12)
   - Look for JavaScript errors in console
   - If errors present, try different browser

3. **Theme Conflicts**
   - Switch to default WordPress theme temporarily
   - If ACA works, theme is conflicting
   - Contact theme developer or use different theme

## 🤖 API & Content Generation Issues

### Gemini API Key Problems

#### Invalid API Key Error
**Error**: "Invalid API key" or "API key not working"

**Solutions:**
1. **Verify API Key**
   - Check key starts with "AIza"
   - Ensure no extra spaces or characters
   - Copy-paste directly from Google AI Studio

2. **Test API Key**
   ```
   Settings → Integrations → Test API Key
   ```
   - Should show green checkmark if valid
   - Red X indicates invalid key

3. **Generate New Key**
   - Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Create new API key
   - Replace old key in ACA settings

#### API Quota Exceeded
**Error**: "Quota exceeded" or "Rate limit reached"

**Solutions:**
1. **Check Usage**
   - Visit Google AI Studio dashboard
   - Review current usage and limits
   - Wait for quota reset (usually daily)

2. **Upgrade Plan**
   - Consider upgrading to paid Google AI plan
   - Higher quotas and faster processing
   - Better for high-volume content creation

### Content Generation Failures

#### Ideas Not Generating
**Problem**: "Generate Ideas" button doesn't work

**Solutions:**
1. **Check API Connection**
   - Verify Gemini API key is valid
   - Test internet connection
   - Check for firewall blocking API calls

2. **Browser Issues**
   - Try different browser
   - Disable ad blockers temporarily
   - Clear browser cache and cookies

3. **Server Issues**
   - Check WordPress error logs
   - Verify PHP memory limit (256MB+)
   - Contact hosting provider about API restrictions

#### Poor Content Quality
**Problem**: Generated content is low quality or irrelevant

**Solutions:**
1. **Improve Style Guide**
   - Provide more detailed style guide
   - Include examples of good content
   - Specify tone, voice, and format preferences

2. **Adjust Settings**
   - Increase content length requirements
   - Specify target audience more clearly
   - Provide more context about your niche

3. **Manual Editing**
   - Use generated content as starting point
   - Edit and improve before publishing
   - AI learns from your editing patterns

## 🔗 SEO Plugin Integration Issues

### SEO Plugin Not Detected
**Problem**: ACA shows "No SEO plugin detected"

**Solutions:**
1. **Verify SEO Plugin Active**
   - Ensure SEO plugin is activated
   - Update to latest version
   - Check plugin compatibility

2. **Manual Selection**
   ```
   Settings → Integrations → SEO Plugins → Manual Selection
   ```
   - Choose your SEO plugin manually
   - Test integration after selection

3. **Plugin Compatibility**
   - Supported: Yoast SEO, RankMath, AIOSEO
   - Ensure plugin version is compatible
   - Contact support for other SEO plugins

### Meta Data Not Transferring
**Problem**: SEO data not appearing in SEO plugin

**Solutions:**
1. **Check Integration Status**
   ```
   Settings → Integrations → SEO Plugins
   ```
   - Should show green checkmark for detected plugin
   - Test integration if available

2. **Clear Cache**
   - Clear all caching plugins
   - Clear browser cache
   - Refresh page and check again

3. **Plugin Priority**
   - If multiple SEO plugins installed
   - ACA uses priority: RankMath > Yoast > AIOSEO
   - Deactivate unused SEO plugins

## 📅 Publishing & Scheduling Issues

### Scheduled Posts Not Publishing
**Problem**: Posts scheduled but not going live

**Solutions:**
1. **Check WordPress Cron**
   ```
   Settings → Automation → System Status
   ```
   - Should show "WordPress Cron: Active"
   - If inactive, contact hosting provider

2. **Timezone Issues**
   - Verify WordPress timezone setting
   - Check scheduled time is in future
   - Adjust timezone if needed

3. **Server Resources**
   - Ensure sufficient memory for cron jobs
   - Check for server timeout issues
   - Contact hosting provider if persistent

### Publishing Errors
**Problem**: Error messages when trying to publish

**Solutions:**
1. **User Permissions**
   - Ensure user has "publish_posts" capability
   - Check user role has publishing permissions
   - Contact admin to adjust permissions

2. **Content Issues**
   - Check for special characters in content
   - Verify content length isn't too long
   - Remove any problematic formatting

## 🔍 Google Search Console Issues

### GSC Won't Connect
**Problem**: Cannot connect Google Search Console

**Solutions:**
1. **Check Google Account**
   - Ensure you're logged into correct Google account
   - Verify account has GSC access for your site
   - Try logging out and back in

2. **Site Verification**
   - Ensure site is verified in Google Search Console
   - Check verification hasn't expired
   - Re-verify site if needed

3. **OAuth Issues**
   - Clear browser cookies for Google
   - Try incognito/private browsing mode
   - Disable browser extensions temporarily

### GSC Data Not Loading
**Problem**: Connected but no data showing

**Solutions:**
1. **Wait for Data**
   - GSC data has 2-3 day delay
   - New sites may take weeks to show data
   - Be patient with new installations

2. **Check Permissions**
   - Ensure GSC account has "Full" permission
   - "Restricted" users may not see all data
   - Contact site owner to upgrade permissions

## 💳 License & Pro Features Issues

### Pro License Not Working
**Problem**: Valid license but Pro features not accessible

**Solutions:**
1. **Verify License Key**
   ```
   Settings → License → Verify License
   ```
   - Should show "Active" status
   - Green checkmark indicates valid license

2. **Clear License Cache**
   - Deactivate and reactivate license
   - Clear all caching plugins
   - Wait 5 minutes and try again

3. **Site Binding Issues**
   - License tied to specific domain
   - Contact support to transfer license
   - Ensure using correct domain format

### Pro Features Not Appearing
**Problem**: Pro license active but features missing

**Solutions:**
1. **Refresh Browser**
   - Hard refresh (Ctrl+F5 or Cmd+Shift+R)
   - Clear browser cache
   - Try different browser

2. **Check Plugin Version**
   - Ensure running latest version (2.4.6)
   - Update plugin if needed
   - Pro features require current version

## 🚀 Performance Issues

### Slow Loading
**Problem**: ACA interface loads slowly

**Solutions:**
1. **Server Resources**
   - Check hosting plan resources
   - Upgrade hosting if needed
   - Monitor server response times

2. **Plugin Conflicts**
   - Deactivate other plugins temporarily
   - Identify slow/conflicting plugins
   - Keep only essential plugins active

3. **Optimize Database**
   - Clean up WordPress database
   - Remove unused plugins/themes
   - Use database optimization plugins

### High Resource Usage
**Problem**: ACA using too much server resources

**Solutions:**
1. **Reduce Automation Frequency**
   - Lower content generation frequency
   - Increase time between automated tasks
   - Use manual mode if necessary

2. **Optimize Settings**
   - Reduce content length limits
   - Limit concurrent operations
   - Enable resource optimization features

## 📞 Getting Additional Help

### Self-Help Resources
- **[Performance Optimization](performance.md)** - Detailed performance tuning
- **[API Issues](api-issues.md)** - API-specific troubleshooting
- **[License Issues](license-issues.md)** - License and Pro feature problems

### Support Channels

#### Free Support
- **WordPress Forums** - Community discussions
- **GitHub Issues** - Bug reports and feature requests
- **Documentation** - Comprehensive guides

#### Pro Support
- **Priority Email** - Direct developer access
- **Advanced Troubleshooting** - Expert technical assistance
- **Custom Configuration** - Personalized setup help

### Before Contacting Support
Please provide:
1. **WordPress Version** - Found in Dashboard → Updates
2. **PHP Version** - Found in Site Health
3. **ACA Version** - Found in Plugins page
4. **Error Messages** - Exact error text or screenshots
5. **Steps to Reproduce** - What you were doing when error occurred
6. **Browser/Device** - What browser and device you're using

### Emergency Troubleshooting
If ACA is causing site issues:
1. **Deactivate Plugin** - Via WordPress Admin → Plugins
2. **Rename Plugin Folder** - Via FTP if admin not accessible
3. **Contact Support** - Report critical issues immediately
4. **Restore from Backup** - If site is completely broken

---

**Still having issues?** Don't hesitate to reach out for help! Our support team is here to get you up and running. 🛠️

[Contact Support](mailto:support@example.com) | [Community Forums](https://wordpress.org/support/) | [Pro Support](https://ademisler.gumroad.com/l/ai-content-agent-pro)

*Last updated: 2025-02-01 | Version: 2.4.6*