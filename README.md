# MunSoft — Municipal HRIS & Payroll Management System

**MunSoft** is an integrated Human Resource Information System (HRIS) and Payroll Management platform designed for local government units. It streamlines employee records management, payroll processing, remittance handling, leave administration, and employee self-service — all accessible through a modern web-based interface with mobile PWA support.

Built on **AdminLTE 3** (Bootstrap 4), the system provides role-based access across four main portals: **Admin**, **HRIS**, **Payroll**, and **Employee Self-Service**.

---

## System Requirements

- **Server:** PHP 7.4+, MySQL 5.7+ / MariaDB 10.2+, Apache or Nginx
- **Client:** Chrome 51+, Firefox 44+, Safari 11.1+, Edge 79+, modern mobile browsers
- **HTTPS** required for production (PWA/Service Worker requirement)

---

## Modules & Features

### 1. Employee Records Management
Centralized employee master data with personal info, employment history, and position tracking. Includes a photo upload feature with automatic square crop/resize and a photo viewer modal.

### 2. HRIS Configuration
Management of departments, positions, and organizational structure. Role-based access control restricts Employee users from edit/delete operations at both the frontend (CSS/JS hiding) and backend (HTTP 403) layers.

### 3. Payroll Processing
Full payroll computation engine supporting **monthly** and **semi-monthly** pay frequencies, separated by employment type (Regular vs. Casual). Features include:
- Automatic earnings, deductions, and government shares computation
- Payroll snapshot saving for audit history
- Configurable employee inclusion/exclusion per pay period
- Journal entry auto-generation (separate for Regular and Casual)
- **Workflow status system:** DRAFT → REVIEW → APPROVED → PAID with audit trail logging

### 4. Payroll Edit Blocking (Two-Layer System)
Protects payroll data integrity through dual protection:
- **Period-based blocking** prevents config edits during the 1st half of semi-monthly cycles to ensure calculation consistency.
- **Status-based blocking** prevents edits once payroll advances beyond DRAFT status.

### 5. Remittance Management
Generates remittance records grouped by type — Government (GSIS, PhilHealth, Pag-IBIG, BIR/Tax) and Payroll Deductions (Loans, Other Payables). Per-employee breakdowns and PDF printing supported.

### 6. Payslip Generation
Per-employee payslips with full earnings and deductions breakdowns. PDF generation and download via TCPDF. Summary List of Payroll (SLP) report also available.

### 7. Leave Management
Leave application submission, approval workflow, balance tracking, and credit management with support for multiple leave types.

### 8. Employee Self-Service Portal (Mobile PWA)
Mobile-first responsive dashboard with four main cards — **Profile**, **Payrolls**, **Payslips**, and **Leave**. Includes:
- Offline support via Service Workers
- Installable as a native-like app on mobile devices
- Quick actions grid and recent activity sections
- Dark mode, accessibility enhancements, and responsive design

### 9. Security
Session-based authentication, CSRF protection, XSS prevention, SQL injection protection, input sanitization, role-based access control, and audit logging with IP tracking.

### 10. Reporting & Printing
PDF generation for payroll records, payslips, remittance details, service records, and journal entries via TCPDF.

---

## Project Structure

```
/MunSoft
├── /admin/             # Admin portal
├── /hris/              # HR management module
├── /payroll/           # Payroll processing module
├── /employee/          # Employee self-service (PWA dashboard)
├── /includes/
│   ├── /class/         # PHP classes (Employee, Payroll, Remittance, etc.)
│   ├── /view/          # View helpers & data functions
│   ├── /layout/        # Shared layout (head, navbar, sidebar, footer)
│   └── /modals/        # Modal dialog templates
├── /prints/            # Print templates (payslip, payroll, remittance, SLP, etc.)
├── /css/               # Stylesheets (including employee-mobile.css)
├── /assets/            # Static assets (images, uploads)
├── /db/                # Database schemas & migration files
├── /devdoc/            # Development documentation
├── service-worker.js   # PWA offline caching
├── manifest.json       # PWA app manifest
├── offline.html        # Offline fallback page
└── index.php           # Application entry point
```

---

## Getting Started

1. **Import the database** from `db/munsoft_polanco.sql` into MySQL/MariaDB.
2. **Run migration files** in `db/` folder for additional schema updates (photos, workflow).
3. **Configure** database connection in `includes/class/DB_conn.php`.
4. **Start** Apache and MySQL via XAMPP (or equivalent).
5. **Access** the system at `http://localhost/MunSoft/`.

---

## Development Documentation

All development notes, implementation plans, and technical details have been consolidated into:

> **[devdoc/DEVELOPMENT_DOCUMENTATION.md](devdoc/DEVELOPMENT_DOCUMENTATION.md)**

This includes module history, bug fixes, deployment guides, security details, and architecture decisions.

---

## Conclusion

MunSoft provides a comprehensive, production-ready solution for local government HRIS and payroll operations. The system covers the full payroll lifecycle — from employee configuration through computation, approval workflows, remittance processing, and payslip distribution — while maintaining data integrity through multi-layer blocking and audit trail mechanisms. The mobile PWA dashboard extends accessibility to employees on any device.

## Future Recommendations

- **Remittance granularity** — Enhance the `remittance_details` schema for per-deduction-level storage.
- **Permission matrix** — Expand role-based access to per-operation granularity across different user roles.
- **Email & push notifications** — Automated alerts for payroll releases, leave approvals, and status changes.
- **Export capabilities** — CSV/Excel export for payroll, remittance, and service record data.
- **Enhanced reporting** — Admin audit dashboard for workflow transitions and system activity.
- **Multi-language support** — Internationalization for broader deployment.
- **Biometric/2FA** — Stronger authentication for production environments.

---

## License

See [LICENSE](LICENSE) for details.  
Built with [AdminLTE 3](https://adminlte.io) — Bootstrap 4 Admin Dashboard Framework.
