# Troubleshooting Guide

Common issues and solutions for AI Content Agent plugin.

## 🚨 Critical Issues

### Plugin Activation Problems

**Plugin Won't Activate**
- **Symptoms**: Error during activation, plugin doesn't appear in admin menu
- **Solutions**:
  1. Check WordPress version (requires 5.0+)
  2. Verify PHP version (requires 7.4+)
  3. Deactivate conflicting plugins
  4. Check file permissions (755 for directories, 644 for files)
  5. Clear any caching plugins
  6. Re-upload plugin files

**White Screen After Activation**
- **Symptoms**: Blank page, site becomes inaccessible
- **Solutions**:
  1. Access via FTP and rename plugin folder temporarily
  2. Check PHP error logs
  3. Increase PHP memory limit (512MB recommended)
  4. Check for plugin conflicts
  5. Restore from backup if necessary

### API Configuration Issues

**Gemini API Key Problems**
- **Symptoms**: "API key not configured" warnings, content generation fails
- **Solutions**:
  1. Verify API key is correct (check Google AI Studio)
  2. Ensure API key has proper permissions
  3. Check for extra spaces or characters
  4. Verify Gemini API is enabled in Google Cloud Console
  5. Check API usage limits and billing

**API Connection Timeouts**
- **Symptoms**: "Request timed out" errors, slow responses
- **Solutions**:
  1. Check internet connection stability
  2. Verify firewall isn't blocking requests
  3. Increase PHP timeout limits
  4. Try during off-peak hours
  5. Contact hosting provider about external API access

## 🔧 Content Generation Issues

### Language Detection Problems

**Wrong Language Detected**
- **Symptoms**: Content generated in wrong language
- **Solutions**:
  1. Check WordPress locale in **Settings → General**
  2. Verify locale code format (e.g., `de_DE` for German)
  3. Clear caching plugins
  4. Manually override in plugin settings
  5. Check for conflicting multilingual plugins

**Content Still in English**
- **Symptoms**: Content generated in English despite other language settings
- **Solutions**:
  1. Verify your language is in supported list (50+ languages)
  2. Check WordPress locale is properly saved
  3. Clear browser cache and plugin cache
  4. Try manual language override
  5. Contact support if language not supported

### Category Selection Issues

**Wrong Categories Selected**
- **Symptoms**: AI selects inappropriate or parent categories
- **Solutions**:
  1. Review WordPress category hierarchy
  2. Add clear category descriptions
  3. Use descriptive category names
  4. Ensure proper parent-child relationships
  5. Make content more specific to desired category

**Categories Not Available**
- **Symptoms**: AI doesn't see all available categories
- **Solutions**:
  1. Check category visibility settings
  2. Verify categories aren't empty
  3. Ensure proper user permissions
  4. Clear any category caching
  5. Check category hierarchy depth

### Content Quality Issues

**Generated Content Too Generic**
- **Symptoms**: Content lacks specificity or relevance
- **Solutions**:
  1. Create detailed style guides
  2. Use more specific idea prompts
  3. Add industry-specific keywords to style guide
  4. Review and edit generated content
  5. Provide more context in content briefs

**SEO Metadata Missing**
- **Symptoms**: No meta descriptions, titles, or tags generated
- **Solutions**:
  1. Verify SEO plugin is active and supported
  2. Check SEO plugin compatibility
  3. Review plugin integration settings
  4. Clear SEO plugin cache
  5. Manually add missing metadata

## 🎨 User Interface Issues

### Settings Page Problems

**Settings Won't Save**
- **Symptoms**: Settings revert after saving
- **Solutions**:
  1. Check user permissions (admin required)
  2. Verify nonce validation isn't failing
  3. Clear browser cache
  4. Disable browser extensions
  5. Check for JavaScript errors in console

**Settings Page Scroll Jumping**
- **Symptoms**: Page jumps to top when opening dropdowns
- **Solutions**:
  1. Update to v2.2.6+ (includes fix)
  2. Clear browser cache
  3. Disable conflicting CSS/JS
  4. Check for theme conflicts
  5. Try different browser

**Dashboard Not Loading**
- **Symptoms**: Blank dashboard, loading indicators stuck
- **Solutions**:
  1. Check browser console for JavaScript errors
  2. Verify assets are loading correctly
  3. Clear browser cache and cookies
  4. Disable browser extensions
  5. Check network connectivity

### Content Calendar Issues

**Calendar Not Displaying**
- **Symptoms**: Empty calendar, no posts showing
- **Solutions**:
  1. Check if you have any scheduled posts
  2. Verify date range settings
  3. Clear calendar cache
  4. Check user permissions for post viewing
  5. Refresh the page

**Drag and Drop Not Working**
- **Symptoms**: Can't move posts on calendar
- **Solutions**:
  1. Check browser compatibility (modern browsers required)
  2. Disable conflicting JavaScript
  3. Clear browser cache
  4. Try different browser
  5. Check for theme conflicts

## 📊 Performance Issues

### Slow Loading Times

**Plugin Interface Slow**
- **Symptoms**: Long loading times for plugin pages
- **Solutions**:
  1. Check hosting server performance
  2. Optimize WordPress database
  3. Clear all caching plugins
  4. Disable unnecessary plugins
  5. Upgrade hosting plan if needed

**Content Generation Slow**
- **Symptoms**: Long wait times for AI responses
- **Solutions**:
  1. Check API rate limits
  2. Verify internet connection speed
  3. Try during off-peak hours
  4. Reduce content length requirements
  5. Check Gemini API status

### Memory Issues

**PHP Memory Limit Errors**
- **Symptoms**: "Fatal error: Allowed memory size exhausted"
- **Solutions**:
  1. Increase PHP memory limit (512MB recommended)
  2. Contact hosting provider
  3. Optimize other plugins
  4. Check for memory leaks
  5. Consider hosting upgrade

**Browser Memory Issues**
- **Symptoms**: Browser becomes slow or unresponsive
- **Solutions**:
  1. Close unnecessary browser tabs
  2. Clear browser cache and data
  3. Restart browser
  4. Update browser to latest version
  5. Try different browser

## 🔌 Integration Issues

### SEO Plugin Integration

**Yoast SEO Not Working**
- **Symptoms**: Metadata not transferring to Yoast
- **Solutions**:
  1. Verify Yoast SEO is active and updated
  2. Check plugin compatibility version
  3. Clear Yoast cache
  4. Review meta field mappings
  5. Check for Yoast API changes

**RankMath Integration Issues**
- **Symptoms**: RankMath not receiving AI metadata
- **Solutions**:
  1. Ensure RankMath is properly activated
  2. Check RankMath version compatibility
  3. Verify meta field permissions
  4. Clear RankMath cache
  5. Review integration logs

### Google Search Console Issues

**GSC Connection Failed**
- **Symptoms**: Can't connect to Google Search Console
- **Solutions**:
  1. Verify Google credentials are correct
  2. Check Google API client library installation
  3. Ensure proper OAuth setup
  4. Verify site is verified in GSC
  5. Check API quotas and limits

**GSC Data Not Loading**
- **Symptoms**: No search console data appearing
- **Solutions**:
  1. Check date range settings
  2. Verify site has sufficient data in GSC
  3. Check API permissions
  4. Clear GSC cache
  5. Try different date range

## 🛡️ Security Issues

### API Key Security

**API Key Exposed**
- **Symptoms**: API key visible in logs or frontend
- **Solutions**:
  1. Regenerate API key immediately
  2. Check server logs and clean
  3. Review code for key exposure
  4. Implement proper key storage
  5. Monitor API usage for abuse

**Unauthorized Access**
- **Symptoms**: Unexpected API usage or content generation
- **Solutions**:
  1. Change all API keys
  2. Review user permissions
  3. Check for compromised accounts
  4. Enable two-factor authentication
  5. Monitor access logs

## 🔄 Update and Compatibility Issues

### Plugin Update Problems

**Update Failed**
- **Symptoms**: Plugin won't update, stuck on old version
- **Solutions**:
  1. Deactivate plugin before updating
  2. Clear any caching
  3. Check file permissions
  4. Manual update via FTP
  5. Backup before updating

**Compatibility Issues After Update**
- **Symptoms**: Features broken after plugin update
- **Solutions**:
  1. Clear all caches
  2. Deactivate/reactivate plugin
  3. Check for theme conflicts
  4. Review error logs
  5. Rollback to previous version if needed

### WordPress Compatibility

**WordPress Version Conflicts**
- **Symptoms**: Plugin doesn't work with WordPress version
- **Solutions**:
  1. Update WordPress to latest version
  2. Check plugin compatibility requirements
  3. Contact support for compatibility updates
  4. Use compatible WordPress version
  5. Wait for plugin update

## 📞 Getting Help

### Before Contacting Support

1. **Check This Guide**: Review relevant troubleshooting sections
2. **Check Documentation**: Review setup guides and user guides
3. **Test in Safe Mode**: Deactivate other plugins to test
4. **Clear Caches**: Clear all caching plugins and browser cache
5. **Check Error Logs**: Review WordPress and server error logs

### Information to Provide

When contacting support, include:
- Plugin version number
- WordPress version
- PHP version
- Description of the issue
- Steps to reproduce
- Error messages (exact text)
- Screenshots if applicable
- List of active plugins

### Support Resources

- **Documentation**: Check [docs directory](../README.md) for guides
- **Changelog**: Review [changelog](CHANGELOG.md) for known issues
- **Release Notes**: Check [releases](RELEASES.md) for version-specific info
- **Development Guide**: See [developer docs](../developer/) for technical issues

## 🔍 Diagnostic Tools

### Browser Developer Tools
1. **Console**: Check for JavaScript errors
2. **Network**: Monitor API requests and responses
3. **Application**: Check local storage and cookies
4. **Performance**: Monitor loading times

### WordPress Debug Mode
```php
// Add to wp-config.php for debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Plugin-Specific Debugging
```php
// Enable ACA debugging
define('ACA_DEBUG', true);
```

## ⚡ Quick Fixes

### Common Quick Solutions
1. **Clear All Caches**: Plugin, WordPress, browser, CDN
2. **Deactivate/Reactivate**: Plugin deactivation and reactivation
3. **Update Everything**: Plugin, WordPress, themes, other plugins
4. **Check Permissions**: File permissions and user permissions
5. **Restart Services**: Web server, PHP, database if possible

### Emergency Recovery
1. **Backup First**: Always backup before making changes
2. **Safe Mode**: Deactivate all plugins except ACA
3. **Default Theme**: Switch to default WordPress theme
4. **Fresh Install**: Re-upload plugin files if corrupted
5. **Restore Backup**: Use backup if all else fails

---

**Still having issues?** Check the specific setup guides in the [docs directory](../README.md) or contact support with detailed information about your problem! 🔧