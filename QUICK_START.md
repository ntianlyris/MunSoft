# MunSoft Employee Mobile Dashboard - Quick Start Guide

## What's New?

The employee dashboard has been completely redesigned with a **mobile-first user experience**. Employees can now:

✅ View their profile  
✅ Access payroll records  
✅ Download payslips  
✅ Manage leave requests  
✅ **Use offline mode** when internet is unavailable  
✅ **Install as app** on mobile devices  

## Files at a Glance

| File | Purpose | Type |
|------|---------|------|
| `employee/index.php` | New dashboard with 4 cards | Modified |
| `css/employee-mobile.css` | Mobile-first responsive styles | New |
| `service-worker.js` | Offline support & caching | New |
| `manifest.json` | PWA app configuration | New |
| `offline.html` | Offline fallback page | New |
| `includes/view/view.php` | 5 new employee functions | Modified |
| `includes/layout/head.php` | PWA meta tags | Modified |

## Quick Setup (5 Minutes)

### 1. Verify Database
```sql
-- Run these commands to check your setup
SHOW TABLES LIKE 'employees%';
SHOW TABLES LIKE 'payroll%';
SHOW TABLES LIKE 'leave%';
```

### 2. Check Session Variable
In your login script, add:
```php
$_SESSION['employee_id'] = $employee_data['employee_id'];
```

### 3. Enable HTTPS
Service Workers require HTTPS (except localhost)
```bash
# For Apache
a2enmod ssl
# Then configure SSL certificates
```

### 4. Test on Browser
```
https://localhost/MunSoft/employee/
```

That's it! Dashboard should load with 4 cards.

## The 4 Dashboard Cards

### 1️⃣ My Profile
- View employee details
- Upload/change photo
- Edit contact info
- **Link**: `profile.php`

### 2️⃣ Payrolls
- View recent payroll records
- See gross/net pay breakdown
- Show last 5 payrolls
- **Link**: Modal with history table

### 3️⃣ Payslips
- Download payslips as PDF
- View earnings breakdown
- Track payslip history
- **Link**: Modal with download option

### 4️⃣ Leave
- Submit leave requests
- Check leave balance
- Track leave status
- **Link**: `leave_application.php`

## New Features Explained

### Offline Support
When internet is unavailable:
- Employee can still view cached payroll data
- See previous payslips and leave history
- Browse profile information
- When online again, fresh data loads automatically

**How to test**:
1. Open Dashboard
2. Open DevTools (F12)
3. Go to Application → Service Workers
4. Check "Offline"
5. Refresh page - content still loads!

### App Installation
Employees can install dashboard as native app:

**Android**: Menu → "Install app"  
**iOS**: Share → "Add to Home Screen"

After installation:
- Appears on home screen like regular app
- Works offline
- Faster loading
- No browser UI clutter

### Mobile Responsive
Automatically adapts to screen size:
- **Phones** (< 576px): Single column, large buttons
- **Tablets** (576-768px): 2 columns
- **Desktops** (768px+): 4 columns with side-by-side layout

## Testing Checklist

```
☐ Dashboard loads on mobile phone
☐ Cards display correctly
☐ Clicking cards opens modal/page
☐ No console errors (F12 → Console)
☐ Service Worker shows "activated" (F12 → Application)
☐ Offline mode works (F12 → check Offline)
☐ Can install as app (mobile menu or Share)
☐ Lighthouse score > 90 (F12 → Lighthouse)
```

## If Something Breaks

### Dashboard Shows 404 or Blank
```php
// Check if employee_id is set in session
// In employee/index.php, line 9:
$employee_id = isset($_SESSION['employee_id']) ? $_SESSION['employee_id'] : '';
```

### Service Worker Not Working
```javascript
// Open DevTools Console and run:
navigator.serviceWorker.getRegistrations().then(registrations => {
  registrations.forEach(r => r.unregister());
});
// Then refresh page and try again
```

### Cards Not Responsive
```html
<!-- Verify this line is in head.php -->
<link rel="manifest" href="../manifest.json">
<link rel="stylesheet" href="../css/employee-mobile.css">
```

### Session Lost After Reload
```php
// Ensure session_start() is first line:
<?php session_start();

// And login sets employee_id:
$_SESSION['employee_id'] = $authenticated_user['employee_id'];
```

## Browser Console Tips

### Check if online/offline
```javascript
console.log(navigator.onLine);  // true or false
```

### View cached files
```javascript
caches.open('munsoft-employee-v1').then(cache => {
  cache.keys().then(requests => {
    requests.forEach(req => console.log(req.url));
  });
});
```

### Clear all caches
```javascript
caches.keys().then(names => {
  names.forEach(name => caches.delete(name));
});
```

### Check service worker status
```javascript
navigator.serviceWorker.ready.then(reg => {
  console.log('Service Worker active:', reg.active);
});
```

## Customization

### Change Colors
In `css/employee-mobile.css`:
```css
:root {
  --primary-color: #007bff;      /* Blue */
  --success-color: #28a745;      /* Green */
  --warning-color: #ffc107;      /* Yellow */
  --danger-color: #dc3545;       /* Red */
  --info-color: #17a2b8;         /* Cyan */
}
```

### Change Card Layout
In `employee/index.php`:
```html
<!-- Change from 4 cards per row to 2 or 3 -->
<div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-3">
<!--              ↑      ↑       ↑        ↑      
                Mobile  Tablet  Medium   Desktop
                (100%)  (50%)  (50%)   (25%)
-->
```

### Add More Quick Actions
In `employee/index.php`, add to quick actions grid:
```html
<div class="col-6 col-sm-4">
  <button class="btn btn-light border w-100 py-3" 
          onclick="yourFunction()" 
          title="Your Action">
    <i class="fas fa-icon fa-2x mb-2"></i>
    <div class="small">Your Text</div>
  </button>
</div>
```

## Performance Tips

1. **Cache images** - Payroll icons and avatars
2. **Lazy load tables** - Use DataTables with server-side processing
3. **Compress assets** - CSS and JS minified
4. **CDN static files** - Serve CSS/JS/fonts from CDN
5. **Database indexes** - Added indexes on employee_id

## Database Functions Available

All these functions are now available in `includes/view/view.php`:

```php
// Get employee data
$profile = GetEmployeeProfileSummary($employee_id);
$payrolls = GetEmployeePayrollRecords($employee_id, 5);
$payslips = GetEmployeePayslipHistory($employee_id, 6);
$leave_apps = GetEmployeeLeaveApplications($employee_id, 3);
$leave_balance = GetEmployeeLeaveBalance($employee_id);
```

## Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Cards not showing data | $employee_id not set | Check session variable |
| Service Worker fails | HTTP (not HTTPS) | Enable HTTPS in production |
| Offline page blank | offline.html missing | Verify file exists |
| Photos not loading | Wrong path | Check image URLs in DB |
| Session expires too fast | Wrong timeout | Edit php.ini `session.gc_maxlifetime` |
| Cards not responsive | CSS not loaded | Clear browser cache |

## Next Steps for Admins

1. **Backup Database**
   ```bash
   mysqldump -u root -p munsoft_polanco > munsoft_backup_$(date +%Y%m%d).sql
   ```

2. **Test on Real Device**
   - Use ngrok or open from phone on same WiFi
   - Test on iPhone and Android
   - Test offline mode

3. **Train Employees**
   - Show how to access dashboard
   - Explain offline mode
   - Demonstrate app installation
   - Show where to find payslips

4. **Monitor Performance**
   - Check error logs: `/var/log/apache2/error.log`
   - Monitor database queries
   - Watch server CPU/memory usage

5. **Get Feedback**
   - Ask employees what works/doesn't
   - Track common support issues
   - Plan improvements

## Resources

- **Setup Guide**: See `MOBILE_SETUP_GUIDE.md` (complete documentation)
- **Deployment**: See `DEPLOYMENT_CHECKLIST.md` (pre-launch checklist)
- **CSS Styles**: See `css/employee-mobile.css` (all styles explained)
- **Service Worker**: See `service-worker.js` (offline logic)
- **Manifest**: See `manifest.json` (PWA configuration)

## Support

Having issues? Check:
1. Browser console for errors (F12)
2. DevTools Application tab for Service Worker
3. Network tab to see failed requests
4. Mobile_SETUP_GUIDE.md for detailed help
5. DEPLOYMENT_CHECKLIST.md for deployment issues

---

**Version**: 1.0  
**Last Updated**: February 2026  
**Status**: Production Ready ✅

For detailed technical information, see:
- `MOBILE_SETUP_GUIDE.md` - Complete setup instructions
- `DEPLOYMENT_CHECKLIST.md` - Pre-deployment tasks
