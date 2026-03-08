# GAA Net Pay Intelligence Module
**Version 1.0 · March 2026**

A self-contained, reusable module for AI-powered GAA net pay threshold detection and validation. Drop it anywhere in your project and it works.

---

## Folder Structure

```
gaa_netpay_module/
├── GAAModule.php                   ← Single entry point (include only this)
├── config/
│   └── GAAConfig.php               ← All constants & settings
├── core/
│   ├── GAANetPayValidator.php      ← Base validator (Stage 1–3) — paste original here
│   └── GAANetPayIntelligence.php   ← AI intelligence layer
├── handlers/
│   ├── GAABridgeHandler.php        ← PHP → UI AJAX dispatcher
│   └── GAAResponseBuilder.php     ← Standardized JSON response builder
├── api/
│   └── gaa_api.php                 ← Public HTTP endpoint (only file exposed to web)
├── js/
│   ├── gaa.core.js                 ← AJAX engine (fetch wrapper)
│   ├── gaa.ui.js                   ← DOM renderers (badges, cards, charts)
│   └── gaa.handlers.js             ← Event bindings (data-gaa-* auto-bind)
├── assets/css/
│   └── gaa.css                     ← Component styles (zero dependencies)
└── utils/
    └── GAALogger.php               ← Audit & request logger
```

---

## Setup (3 steps)

### 1. Place the module folder
Drop `gaa_netpay_module/` anywhere inside your project. A recommended location:
```
/your_project/modules/gaa_netpay_module/
```

### 2. Add the base validator class
Open `core/GAANetPayValidator.php` and paste the original `GAANetPayValidator` class body, **or** replace the include path to point to your existing file.

### 3. Adjust DB_conn path
In `config/GAAConfig.php` or `core/GAANetPayValidator.php`, set `DB_CONN_PATH` to the absolute path of your `DB_conn.php`.

---

## Using from ANY folder in your project

Any PHP file in your project — regardless of its depth — can use the module with one line:

```php
require_once '/absolute/path/to/gaa_netpay_module/GAAModule.php';
$gaa = GAAModule::getInstance();

// Classify a net pay value
$result = $gaa->classify(5300);
// → ['status' => 'DANGER', 'label' => 'Danger — Critically Near Threshold', ...]

// Full AI analysis
$profile = $gaa->analyze(employeeId: 42, gross: 18000, deductions: 12000);

// Batch period analysis
$batch = $gaa->analyzeBatch(periodId: 15);

// Deduction headroom check
$headroom = $gaa->headroom(employeeId: 42, gross: 18000, currentDeductions: 12000, proposed: 500);
```

**Tip:** Define the path once in a bootstrap/config file:
```php
// config.php or bootstrap.php
define('GAA_MODULE', '/var/www/html/modules/gaa_netpay_module/GAAModule.php');
```
Then in any feature:
```php
require_once GAA_MODULE;
$gaa = GAAModule::getInstance();
```

---

## API Endpoint

The file `api/gaa_api.php` is the only file that should be web-accessible. All other files are PHP includes only.

**POST** `/gaa_netpay_module/api/gaa_api.php`

| action | Required params | Optional params |
|---|---|---|
| `classify` | `net_pay` | — |
| `validate_entry` | `employee_id`, `proposed_total_deductions` | `current_gross` |
| `validate_payroll` | `employee_id`, `gross`, `total_deductions` | `payroll_frequency` |
| `validate_batch` | `payroll_period_id` | `dept_id` |
| `analyze_employee` | `employee_id`, `gross`, `total_deductions` | `payroll_frequency` |
| `analyze_batch` | `payroll_period_id` | `dept_id` |
| `risk_score` | `employee_id`, `gross`, `total_deductions` | — |
| `headroom` | `employee_id`, `gross`, `current_deductions` | `proposed_new_deduction` |
| `predict` | `employee_id` | — |

---

## JavaScript Usage

Include the three JS files in order. The CSS is optional but recommended:

```html
<link  href="/gaa_netpay_module/assets/css/gaa.css" rel="stylesheet">
<script src="/gaa_netpay_module/js/gaa.core.js"></script>
<script src="/gaa_netpay_module/js/gaa.ui.js"></script>
<script src="/gaa_netpay_module/js/gaa.handlers.js"></script>
```

### Auto-bind with data attributes (zero JS needed)

```html
<!-- Deduction input: validates on every keystroke -->
<input type="number" name="deduction_amount"
       data-gaa-action="validate_entry"
       data-gaa-employee-id="42"
       data-gaa-gross-selector="#gross_field"
       data-gaa-target="#gaa_badge">

<!-- Save button: validates before submit -->
<button data-gaa-action="validate_payroll"
        data-gaa-employee-id="42"
        data-gaa-gross-selector="#gross_field"
        data-gaa-deductions-selector="#deductions_field"
        data-gaa-target="#validation_result">
  Save Payroll
</button>

<!-- Results render here automatically -->
<div id="gaa_badge"></div>
<div id="validation_result"></div>
```

### Manual JavaScript API

```javascript
// Real-time deduction validation
GAACore.validateEntry(42, 13500).then(res => GAAui.renderBadge('#badge', res));

// Full AI profile
GAACore.analyzeEmployee(42, 18000, 13000).then(res => {
    GAAui.renderStatusCard('#card', res);
    GAAui.renderRiskMeter('#meter', res);
    GAAui.renderRecommendations('#recs', res);
    GAAui.renderTrendSparkline('#chart', res);
});

// Live headroom bar
GAAHandlers.onHeadroomChange('#new_deduction_input', {
    employeeId: 42,
    gross: 18000,
    currentDeductions: 12000,
    barTarget: '#headroom_bar',
});

// Batch analysis with full UI
GAAHandlers.onBatchAnalyze(15, {
    table:   '#at_risk_table',
    heatmap: '#dept_heatmap',
    summary: '#batch_summary',
});
```

---

## Status Tiers

| Status | Net Pay Range | Color |
|---|---|---|
| 🔴 CRITICAL | < ₱5,000 | `#DC2626` |
| 🟠 DANGER | ₱5,000 – ₱5,500 | `#EA580C` |
| 🟡 WARNING | ₱5,500 – ₱6,500 | `#D97706` |
| 🔵 CAUTION | ₱6,500 – ₱7,500 | `#CA8A04` |
| 🟢 STABLE | ₱7,500 – ₱10,000 | `#2563EB` |
| ✅ SAFE | > ₱10,000 | `#16A34A` |

---

## Changing the threshold or tier bands

All values are in one place: `config/GAAConfig.php`. No other file needs to change.

```php
const MIN_NET_PAY     = 5000.00;   // Change GAA threshold here
const TIER_DANGER_PCT = 0.10;      // 10% above threshold = top of DANGER band
```
