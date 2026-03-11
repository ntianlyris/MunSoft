# IntelliGov
### Intelligent Payroll & HRIS Management System for Local Government Units

> **"Smart Governance. Seamless Operations."**

**IntelliGov** is an integrated Human Resource Information System (HRIS) and Payroll Management platform purpose-built for local government units. It streamlines employee records management, payroll processing, remittance handling, leave administration, and employee self-service — all accessible through a modern web-based interface with mobile PWA support.

Powered by an intelligent computation engine and AI-assisted compliance modules, IntelliGov delivers end-to-end workforce management with real-time decision support, audit-grade data integrity, and a seamless experience across all government roles.

Built on **AdminLTE 3** (Bootstrap 4), the system provides role-based access across four main portals: **Admin**, **HRIS**, **Payroll**, and **Employee Self-Service**.

---

## ⚙️ System Requirements

| Component | Requirement |
|---|---|
| **Server** | PHP 7.4+, MySQL 5.7+ / MariaDB 10.2+, Apache or Nginx |
| **Client** | Chrome 51+, Firefox 44+, Safari 11.1+, Edge 79+, modern mobile browsers |
| **Security** | HTTPS required for production (PWA/Service Worker requirement) |

---

## 🧩 Modules & Features

### 1. 🗂️ Employee Records Management
Centralized employee master data with personal info, employment history, and position tracking. Includes a photo upload feature with automatic square crop/resize and a photo viewer modal.

### 2. 🏛️ HRIS Configuration
Management of departments, positions, and organizational structure. Role-based access control restricts Employee users from edit/delete operations at both the frontend (CSS/JS hiding) and backend (HTTP 403) layers.

### 3. 💰 Payroll Processing
Full payroll computation engine supporting **monthly** and **semi-monthly** pay frequencies, separated by employment type (Regular vs. Casual). Features include:
- Automatic earnings, deductions, and government shares computation
- Payroll snapshot saving for audit history
- Configurable employee inclusion/exclusion per pay period
- Journal entry auto-generation (separate for Regular and Casual)
- **Intelligent Workflow Status System:** `DRAFT` → `REVIEW` → `APPROVED` → `PAID` with full audit trail logging

### 4. 🤖 GAA Net Pay Intelligence *(AI-Powered)*
An AI-powered compliance module that enforces the ₱5,000 General Appropriations Act (GAA) net pay threshold. It provides:
- Real-time UI validation and alerts
- Six intelligent risk status tiers: `CRITICAL` → `HIGH` → `MODERATE` → `LOW` → `MINIMAL` → `SAFE`
- Batch analysis to prevent non-compliant payroll deductions
- Proactive flagging before payroll approval

### 5. 🔒 Payroll Edit Blocking (Two-Layer Intelligence)
Protects payroll data integrity through dual-layer smart protection:
- **Period-based blocking** prevents configuration edits during the 1st half of semi-monthly cycles to ensure calculation consistency.
- **Status-based blocking** prevents edits once payroll advances beyond `DRAFT` status.

### 6. 🏦 Remittance Management
Generates remittance records grouped by type — Government (GSIS, PhilHealth, Pag-IBIG, BIR/Tax) and Payroll Deductions (Loans, Other Payables). Per-employee breakdowns and PDF printing supported.

### 7. 🧾 Payslip Generation
Per-employee payslips with full earnings and deductions breakdowns. PDF generation and download via TCPDF. Summary List of Payroll (SLP) report also available.

### 8. 📅 Leave Management
Leave application submission, approval workflow, balance tracking, and credit management with support for multiple leave types.

### 9. 📱 Employee Self-Service Portal *(Mobile PWA)*
Mobile-first responsive dashboard — **IntelliGov ESS** — with four main cards: **Profile**, **Payrolls**, **Payslips**, and **Leave**. Features include:
- Offline support via Service Workers
- Installable as a native-like app on mobile devices
- Quick actions grid and recent activity sections
- Dark mode, accessibility enhancements, and responsive design

### 10. 🛡️ Security
Session-based authentication, CSRF protection, XSS prevention, SQL injection protection, input sanitization, role-based access control, and audit logging with IP tracking.

### 11. 📊 Reporting & Printing
PDF generation for payroll records, payslips, remittance details, service records, and journal entries via TCPDF.

---

## 🗃️ Project Structure

```
/MunSoft
├── /admin/             # Admin portal
├── /hris/              # IntelliGov HRIS module
├── /payroll/           # IntelliGov Payroll module
├── /employee/          # IntelliGov ESS (PWA dashboard)
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

## 🌐 System Portals

| Portal | Description | Access |
|---|---|---|
| **Admin** | System configuration and user management | `/admin` |
| **IntelliGov HRIS** | HR records, departments, positions | `/hris` |
| **IntelliGov Payroll** | Payroll computation and approval workflow | `/payroll` |
| **IntelliGov ESS** | Employee self-service mobile PWA | `/employee` |

---

## 🔮 Future Roadmap

| Feature | Description |
|---|---|
| **Remittance Granularity** | Enhanced `remittance_details` schema for per-deduction-level storage |
| **Permission Matrix** | Expand role-based access to per-operation granularity across all user roles |
| **Smart Notifications** | Automated alerts for payroll releases, leave approvals, and status changes |
| **Export Capabilities** | CSV/Excel export for payroll, remittance, and service record data |
| **AI Audit Dashboard** | Enhanced admin reporting for workflow transitions and system activity |
| **Multi-language Support** | Internationalization for broader government deployment |
| **Biometric / 2FA** | Stronger authentication mechanisms for production environments |
| **Predictive Analytics** | AI-driven workforce cost forecasting and payroll trend insights |

---

## 🏆 Conclusion

**IntelliGov** provides a comprehensive, production-ready solution for local government HRIS and payroll operations. The system covers the full payroll lifecycle — from employee configuration through intelligent computation, approval workflows, remittance processing, and payslip distribution — while maintaining data integrity through multi-layer blocking, AI-assisted compliance enforcement, and a complete audit trail mechanism.

The mobile PWA dashboard extends seamless accessibility to every employee on any device, embodying the core mission of IntelliGov:

> **"Smart Governance. Seamless Operations."**

---

## 📄 License

See [LICENSE](LICENSE) for details.
Built with [AdminLTE 3](https://adminlte.io) — Bootstrap 4 Admin Dashboard Framework.

---

<div align="center">
  <strong>IntelliGov</strong> — Intelligent Payroll & HRIS Management System for Local Government Units<br/>
  <em>Smart Governance. Seamless Operations.</em>
</div>
