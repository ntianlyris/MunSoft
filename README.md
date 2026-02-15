# MunSoft - Employee Dashboard & Administration System

> Built with **[AdminLTE - Bootstrap 4 Admin Dashboard](https://adminlte.io)** Framework

An integrated HRIS & Payroll system with **mobile-first responsive design**, **offline support**, and **Progressive Web App (PWA)** capabilities.

---

## 🎯 Quick Navigation

### 👨‍💼 For Employee Dashboard Users
- **[QUICK_START.md](QUICK_START.md)** - Get running in 5 minutes
- **[MOBILE_SETUP_GUIDE.md](MOBILE_SETUP_GUIDE.md)** - Complete feature guide
- **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - Pre-deployment tasks

### 👨‍💻 For Developers & Administrators
- **[IMPLEMENTATION_REPORT.md](IMPLEMENTATION_REPORT.md)** - Technical summary
- **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - What was done
- **Employee Dashboard Location**: `/employee/index.php`
- **Admin Dashboard Location**: `/admin/index.php`
- **Payroll Module Location**: `/payroll/index.php`
- **HRIS Module Location**: `/hris/index.php`

---

## ✨ What's New (Latest Release)

### Employee Mobile Dashboard v1.0 ✅
- **4 Dashboard Cards**: Profile, Payrolls, Payslips, Leave
- **Mobile-First Design**: Responsive from 320px to 4K
- **Offline Support**: Service Worker caching & sync
- **Installable App**: PWA on home screen
- **Touch Optimized**: 44x44px min buttons
- **Dark Mode**: Respects system preferences
- **5 New Functions**: Data retrieval layer
- **Production Ready**: Complete documentation

---

## 📁 Project Structure

```
/MunSoft
├── /employee/                          # ✨ NEW: Mobile Employee Dashboard
│   └── index.php                       # Four-card mobile dashboard
├── /admin/                             # Admin portal
├── /payroll/                           # Payroll management
├── /hris/                              # HR management
├── /includes/
│   ├── view/view.php                   # ✨ +5 new functions
│   └── layout/head.php                 # ✨ PWA meta tags
├── /css/
│   └── employee-mobile.css             # ✨ NEW: Mobile styles (450+ lines)
├── service-worker.js                   # ✨ NEW: Offline support
├── offline.html                        # ✨ NEW: Offline fallback
├── manifest.json                       # ✨ NEW: PWA config
└── Documentation/
    ├── QUICK_START.md                  # ✨ Quick setup
    ├── MOBILE_SETUP_GUIDE.md           # ✨ Complete guide
    ├── DEPLOYMENT_CHECKLIST.md         # ✨ Pre-deployment
    ├── IMPLEMENTATION_REPORT.md        # ✨ Executive summary
    └── IMPLEMENTATION_SUMMARY.md       # ✨ Technical overview
```

---

## 🚀 Getting Started (5 Minutes)

### For Employee Dashboard
1. Read **[QUICK_START.md](QUICK_START.md)**
2. Verify database: Check payroll_entries, payroll_periods, leave_applications tables
3. Enable HTTPS on server (or use localhost for testing)
4. Visit: `https://localhost/MunSoft/employee/`

### For Admin Portal
1. Visit: `https://localhost/MunSoft/admin/`
2. Use admin credentials from system setup

### For Payroll Module
1. Visit: `https://localhost/MunSoft/payroll/`
2. Configure earnings/deductions
3. Create payroll periods

---

## 📱 Employee Mobile Dashboard Features

### Four Main Cards
| Card | Purpose | Link |
|------|---------|------|
| **Profile** | View/edit employee details | profile.php |
| **Payrolls** | Recent payroll records | payroll history |
| **Payslips** | Download payslips as PDF | print_payslip.php |
| **Leave** | Submit/track leave requests | leave_application.php |

### Quick Actions
- Edit Profile
- Payroll History
- Download Payslip
- File Leave Request

### Smart Features
- ✅ Offline data viewing (Service Worker)
- ✅ Auto-sync when connected
- ✅ Mobile app installation
- ✅ Dark mode support
- ✅ Responsive: Mobile → Tablet → Desktop
- ✅ Touch-friendly UI

---

## 🔧 System Requirements

### Server
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx with mod_rewrite
- OpenSSL for HTTPS

### Browser
| Browser | Support | Version |
|---------|---------|---------|
| Chrome | ✅ Yes | 51+ |
| Firefox | ✅ Yes | 44+ |
| Safari | ⚠️ Partial | 11.1+ |
| Edge | ✅ Yes | 79+ |
| Mobile | ✅ Full | Latest |

### Development
- Composer (for PHP dependencies)
- npm (optional, for assets)
- Git (version control)

---

## 📊 Database Tables

### Required Tables
```sql
-- Employee data
employees
employee_photos

-- Payroll data
payroll_periods
payroll_entries
payroll_earnings
payroll_deductions

-- Leave data
leave_types
leave_applications
manage_leave_credits

-- User management
users
departments
positions
```

---

## 🔐 Security Features

✅ Session-based authentication  
✅ HTTPS/SSL required (production)  
✅ CSRF protection with tokens  
✅ XSS prevention via escaping  
✅ Input validation & sanitization  
✅ SQL injection protection  
✅ Sensitive data not cached  
✅ Secure session timeout (30 min)  

---

## 📈 Performance Targets

### Lighthouse Scores (Goal: 90+)
- **Performance**: 90+
- **Accessibility**: 90+
- **Best Practices**: 90+
- **SEO**: 90+

### Load Times
- First Paint: ~1.5s
- Largest Paint: ~2.5s
- Time to Interactive: ~3s

### Offline Experience
- Service Worker: <100ms install
- Cache Hit Rate: >85%
- Offline Load: <500ms

---

## 🎓 Documentation

### For Quick Setup (5-10 minutes)
→ **[QUICK_START.md](QUICK_START.md)**
- Overview of features
- 5-minute setup
- Testing checklist
- Common issues

### For Complete Setup (30-60 minutes)
→ **[MOBILE_SETUP_GUIDE.md](MOBILE_SETUP_GUIDE.md)**
- Installation steps
- Configuration guide
- Feature documentation
- Troubleshooting

### For Production Deployment (1-2 hours)
→ **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)**
- Pre-deployment verification
- Security hardening
- Performance testing
- Monitoring setup

### For Technical Overview
→ **[IMPLEMENTATION_REPORT.md](IMPLEMENTATION_REPORT.md)**
- Project statistics
- What was implemented
- Technical decisions
- Success criteria

### For Feature Details
→ **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)**
- Feature breakdown
- Code changes
- Function documentation
- Architecture overview

---

## 💻 Development

### Local Development Setup
```bash
# 1. Clone/Navigate to project
cd /xampp/htdocs/MunSoft

# 2. Setup database
# Import SQL file in /db/ folder

# 3. Configure PHP
# Set session variables, database connection

# 4. Enable HTTPS (Windows/XAMPP)
# See DEPLOYMENT_CHECKLIST.md for steps

# 5. Start local server
# XAMPP: Start Apache & MySQL

# 6. Test
# Visit https://localhost/MunSoft/employee/
```

### File Changes
```
✅ Created: 7 new files (service-worker.js, offline.html, manifest.json, etc.)
✅ Modified: 3 existing files (employee/index.php, view.php, head.php)
✅ Preserved: All other files unchanged
```

---

## 🚀 Deployment

### Step-by-Step
1. Read **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)**
2. Verify all prerequisites
3. Enable HTTPS on production server
4. Upload files to server
5. Configure database indexes
6. Test Service Worker registration
7. Monitor error logs
8. Train support staff

### Production Checklist
- [ ] HTTPS enabled
- [ ] Database verified
- [ ] Session management working
- [ ] Permissions set correctly
- [ ] Cache cleared
- [ ] Tested on devices
- [ ] Logs monitored
- [ ] Performance baseline set

---

## 📞 Support & Help

### Quick Help
1. Check **[QUICK_START.md](QUICK_START.md)** - 5 min
2. Read **[MOBILE_SETUP_GUIDE.md](MOBILE_SETUP_GUIDE.md)** - 30 min

### Deployment Help
1. Review **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - 1-2 hrs
2. Check troubleshooting section

### Technical Questions
1. See **[IMPLEMENTATION_REPORT.md](IMPLEMENTATION_REPORT.md)**
2. Review **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)**
3. Check inline code comments

---

## 🆕 New Functions (PHP)

Added to `/includes/view/view.php`:

```php
// Get employee payroll records
GetEmployeePayrollRecords($employee_id, $limit=5)

// Get employee leave balance
GetEmployeeLeaveBalance($employee_id)

// Get leave applications
GetEmployeeLeaveApplications($employee_id, $limit=3)

// Get employee profile summary
GetEmployeeProfileSummary($employee_id)

// Get payslip history
GetEmployeePayslipHistory($employee_id, $limit=6)
```

---

## 🧪 Testing

### Quick Test (5 min)
```
☐ Load employee dashboard
☐ Verify 4 cards display
☐ Click cards/buttons
☐ Check console (no errors)
☐ Verify data shows
```

### Full Test (1 hour)
```
☐ Test on mobile phone
☐ Test offline mode
☐ Test app installation
☐ Test responsive layout
☐ Run Lighthouse audit
☐ Test all browsers
```

---

## 📊 What's Included

### Code
✅ Mobile-first responsive HTML/CSS  
✅ Service Worker (92 lines)  
✅ Offline fallback page  
✅ PWA manifest  
✅ 5 new PHP functions  
✅ Mobile CSS (450+ lines)  

### Documentation
✅ Quick Start Guide  
✅ Mobile Setup Guide  
✅ Deployment Checklist  
✅ Implementation Report  
✅ Technical Summary  

### Features
✅ 4 dashboard cards  
✅ Quick actions grid  
✅ Recent activity sections  
✅ Modal dialogs  
✅ Offline support  
✅ App installation  
✅ Dark mode  
✅ Responsive design  

---

## 🎉 Ready to Go!

Everything is set up and tested. Start with the appropriate guide for your role:

- **👤 Employee**: [QUICK_START.md](QUICK_START.md)
- **👨‍💻 Developer**: [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)
- **🚀 DevOps**: [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
- **📊 Manager**: [IMPLEMENTATION_REPORT.md](IMPLEMENTATION_REPORT.md)

---

## 📋 AdminLTE Documentation

For AdminLTE framework documentation, visit:
- **[AdminLTE Official Site](https://adminlte.io)**
- **[Bootstrap 4 Docs](https://getbootstrap.com/docs/4.0/)**
- **[jQuery Documentation](https://jquery.com/)**

---

## 📝 License

See [LICENSE](LICENSE) file for details.

---

## 🆘 Issues & Support

1. **Dashboard not loading?** → See QUICK_START.md → Troubleshooting
2. **Offline not working?** → See MOBILE_SETUP_GUIDE.md → Service Worker
3. **Deployment issues?** → See DEPLOYMENT_CHECKLIST.md → Troubleshooting
4. **General help?** → Start with IMPLEMENTATION_SUMMARY.md

---

**Last Updated**: February 2026  
**Version**: 1.0  
**Status**: Production Ready ✅  

---

**🚀 [Read QUICK_START.md to get started in 5 minutes!](QUICK_START.md)**

## Looking for Premium Templates?

AdminLTE.io just opened a new premium templates page. Hand picked to ensure the best quality and the most affordable
prices. Visit <https://adminlte.io/premium> for more information.

!["AdminLTE Presentation"](https://adminlte.io/AdminLTE3.png "AdminLTE Presentation")

**AdminLTE** has been carefully coded with clear comments in all of its JS, SCSS and HTML files.
SCSS has been used to increase code customizability.

## Quick start
There are multiple ways to install AdminLTE.

### Download & Changelog:
Always Recommended to download from GitHub latest release [AdminLTE 3](https://github.com/ColorlibHQ/AdminLTE/releases/latest) for bug free and latest features.\
Visit the [releases](https://github.com/ColorlibHQ/AdminLTE/releases) page to view the changelog.\
Legacy Releases are [AdminLTE 2](https://github.com/ColorlibHQ/AdminLTE/releases/tag/v2.4.18) / [AdminLTE 1](https://github.com/ColorlibHQ/AdminLTE/releases/tag/1.3.1).

## Stable release
### Grab from [jsdelivr](https://www.jsdelivr.com/package/npm/admin-lte) CDN:
_**Important Note**: You needed to add separately cdn links for plugins in your project._
```html
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
```
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
```
### Using The Command Line:
_**Important Note**: To install it via npm/Yarn, you need at least Node.js 10 or higher._
#### Via npm
```bash
npm install admin-lte@^3.1 --save
```
#### Via Yarn
```bash
yarn add admin-lte@^3.1
```
#### Via Composer
```bash
composer require "almasaeed2010/adminlte=~3.1"
```
#### Via Git
```bash
git clone https://github.com/ColorlibHQ/AdminLTE.git
```

## Unstable release
### Grab from [jsdelivr](https://www.jsdelivr.com/package/npm/admin-lte) CDN:
_**Important Note**: You needed to add separately cdn links for plugins in your project._
```html
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
```
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">
```
### Using The Command Line:
_**Important Note**: To install it via npm/Yarn, you need at least Node.js 10 or higher._
#### Via npm
```bash
npm install admin-lte@^3.1.0 --save
```
#### Via Yarn
```bash
yarn add admin-lte@^3.1.0
```
#### Via Composer
```bash
composer require "almasaeed2010/adminlte=~3.1.0"
```
#### Via Git
```bash
git clone https://github.com/ColorlibHQ/AdminLTE.git
```

## Documentation

Visit the [online documentation](https://adminlte.io/docs/3.1/) for the most
updated guide. Information will be added on a weekly basis.

## Browsers support

| [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/edge/edge_48x48.png" alt="IE / Edge" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>IE / Edge | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/firefox/firefox_48x48.png" alt="Firefox" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Firefox | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/chrome/chrome_48x48.png" alt="Chrome" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Chrome | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/safari/safari_48x48.png" alt="Safari" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Safari | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/safari-ios/safari-ios_48x48.png" alt="iOS Safari" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>iOS Safari | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/samsung-internet/samsung-internet_48x48.png" alt="Samsung" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Samsung | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/opera/opera_48x48.png" alt="Opera" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Opera | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/vivaldi/vivaldi_48x48.png" alt="Vivaldi" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Vivaldi | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/electron/electron_48x48.png" alt="Electron" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)<br/>Electron |
| --------- | --------- | --------- | --------- | --------- | --------- | --------- | --------- | --------- |
| IE10, IE11, Edge| last 2 versions| last 2 versions| last 2 versions| last 2 versions| last 2 versions| last 2 versions| last 2 versions| last 2 versions

### Compile dist files

To compile the dist files you need Node.js/npm, clone/download the repo then:

1. `npm install` (install npm deps)
2. _Optional:_ `npm run dev` (developer mode, autocompile with browsersync support for live demo)
3. `npm run production` (compile css/js files)


## Contributing

Please read through our [contributing guidelines](https://github.com/ColorlibHQ/AdminLTE/tree/master/.github/CONTRIBUTING.md). Included are directions for opening issues, coding standards, and notes on development.

Editor preferences are available in the [editor config](https://github.com/twbs/bootstrap/blob/main/.editorconfig) for easy use in common text editors. Read more and download plugins at <https://editorconfig.org/>.


## License

AdminLTE is an open source project by [AdminLTE.io](https://adminlte.io) that is licensed under [MIT](https://opensource.org/licenses/MIT).
AdminLTE.io reserves the right to change the license of future releases.

## Image Credits

- [Pixeden](http://www.pixeden.com/psd-web-elements/flat-responsive-showcase-psd)
- [Graphicsfuel](https://www.graphicsfuel.com/2013/02/13-high-resolution-blur-backgrounds/)
- [Pickaface](https://pickaface.net/)
- [Unsplash](https://unsplash.com/)
- [Uifaces](http://uifaces.com/)
