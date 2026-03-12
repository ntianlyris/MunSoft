<?php
/**
 * GAANetPayIntelligence Class
 * 
 * Purpose : AI-powered smart detection layer for employee net pay threshold analysis.
 *           Extends GAANetPayValidator with multi-tier risk classification, trend 
 *           analysis, anomaly detection, predictive scoring, and intelligent alerts.
 * 
 * Status Tiers (proximity to ₱5,000 GAA minimum):
 *   ● CRITICAL  — Net pay BELOW threshold       (< ₱5,000)
 *   ● DANGER    — Critically near threshold      (₱5,000 – ₱5,500  |  0–10% above)
 *   ● WARNING   — At-risk proximity              (₱5,500 – ₱6,500  |  10–30% above)
 *   ● CAUTION   — Moderate proximity             (₱6,500 – ₱7,500  |  30–50% above)
 *   ● STABLE    — Comfortable buffer             (₱7,500 – ₱10,000 |  50–100% above)
 *   ● SAFE      — Well above threshold           (> ₱10,000         |  >100% above)
 * 
 * AI Capabilities:
 *   • Risk score engine     (0–100 composite score with weighted factors)
 *   • Trend intelligence    (rolling N-period net pay slope analysis)
 *   • Anomaly detection     (Z-score based deduction spike identification)
 *   • Predictive engine     (next-period net pay projection via linear regression)
 *   • Deduction pressure    (measures total deduction load as % of gross)
 *   • Smart recommendations (context-aware corrective action suggestions)
 *   • Batch intelligence    (fleet-wide risk heat map & department roll-up)
 *   • Alert severity routing(auto-classify alerts by urgency for notifications)
 * 
 * Dependencies : GAANetPayValidator (pasted base class), DB_conn
 * Author        : AI-assisted — March 7, 2026
 * Version       : 2.0
 */

// ── Assumes GAAModule.php has injected GAANetPayValidator ──

class GAANetPayIntelligence extends GAANetPayValidator {

    // =========================================================================
    //  CONSTANTS — THRESHOLD TIER BOUNDARIES
    // =========================================================================

    /** Percentage bands above MIN_NET_PAY that define each status tier */
    const TIER_DANGER_UPPER_PCT  = 0.10;   // 10%  above threshold  → top of DANGER band
    const TIER_WARNING_UPPER_PCT = 0.30;   // 30%  above threshold  → top of WARNING band
    const TIER_CAUTION_UPPER_PCT = 0.50;   // 50%  above threshold  → top of CAUTION band
    const TIER_STABLE_UPPER_PCT  = 1.00;   // 100% above threshold  → top of STABLE band

    /** Absolute peso ceiling derived from MIN_NET_PAY × band multiplier */
    // Computed dynamically via getTierBoundaries() to keep single source of truth

    /** Risk score weight distribution (must sum to 1.00) */
    const WEIGHT_CURRENT_PROXIMITY   = 0.35;  // How close net pay is to threshold NOW
    const WEIGHT_TREND_DIRECTION     = 0.25;  // Is net pay declining over time?
    const WEIGHT_DEDUCTION_PRESSURE  = 0.20;  // How large is deduction % of gross?
    const WEIGHT_ANOMALY_PRESENCE    = 0.10;  // Is there an unusual deduction spike?
    const WEIGHT_PREDICTION_RISK     = 0.10;  // Does projected next period breach?

    /** Rolling window (number of payroll periods) for trend & anomaly analysis */
    const HISTORY_WINDOW = 6;

    /** Z-score threshold to flag an anomaly */
    const ANOMALY_ZSCORE_THRESHOLD = 2.0;

    // =========================================================================
    //  STATUS & COLOR CONSTANTS
    // =========================================================================

    const STATUS_CRITICAL = 'CRITICAL';
    const STATUS_DANGER   = 'DANGER';
    const STATUS_WARNING  = 'WARNING';
    const STATUS_CAUTION  = 'CAUTION';
    const STATUS_STABLE   = 'STABLE';
    const STATUS_SAFE     = 'SAFE';

    /** UI color mapping for front-end consumption */
    const STATUS_COLORS = [
        'CRITICAL' => '#DC2626',   // Red-600
        'DANGER'   => '#EA580C',   // Orange-600
        'WARNING'  => '#D97706',   // Amber-600
        'CAUTION'  => '#CA8A04',   // Yellow-600
        'STABLE'   => '#2563EB',   // Blue-600
        'SAFE'     => '#16A34A',   // Green-600
    ];

    /** Status icons (Unicode / emoji fallbacks) */
    const STATUS_ICONS = [
        'CRITICAL' => '🔴',
        'DANGER'   => '🟠',
        'WARNING'  => '🟡',
        'CAUTION'  => '🔵',
        'STABLE'   => '🟢',
        'SAFE'     => '✅',
    ];

    // =========================================================================
    //  CONSTRUCTOR
    // =========================================================================

    public function __construct($db = null, $userId = 0) {
        parent::__construct($db, $userId);
    }

    // =========================================================================
    //  SECTION 1 — TIER CLASSIFICATION ENGINE
    // =========================================================================

    /**
     * Classify a net pay value into the appropriate status tier.
     * 
     * This is the core classification method. All other methods that assign
     * a "status" ultimately call this.
     * 
     * @param  float $net_pay  The employee's net pay for the period.
     * @return array {
     *   status, label, color, icon,
     *   net_pay, threshold, buffer_amount, buffer_pct,
     *   tier_lower, tier_upper, description
     * }
     */
    public function classifyNetPayStatus(float $net_pay): array {
        $threshold  = self::MIN_NET_PAY;
        $boundaries = $this->getTierBoundaries();

        // Determine status tier
        if ($net_pay < $threshold) {
            $status = self::STATUS_CRITICAL;
        } elseif ($net_pay < $boundaries['danger_upper']) {
            $status = self::STATUS_DANGER;
        } elseif ($net_pay < $boundaries['warning_upper']) {
            $status = self::STATUS_WARNING;
        } elseif ($net_pay < $boundaries['caution_upper']) {
            $status = self::STATUS_CAUTION;
        } elseif ($net_pay < $boundaries['stable_upper']) {
            $status = self::STATUS_STABLE;
        } else {
            $status = self::STATUS_SAFE;
        }

        // Compute buffer (how far above / below the threshold)
        $buffer_amount = round($net_pay - $threshold, 2);
        $buffer_pct    = $threshold > 0
            ? round((($net_pay - $threshold) / $threshold) * 100, 2)
            : 0;

        // Tier boundary for this specific status
        [$tier_lower, $tier_upper] = $this->getTierRange($status, $boundaries);

        return [
            'status'        => $status,
            'label'         => $this->getStatusLabel($status),
            'color'         => self::STATUS_COLORS[$status],
            'icon'          => self::STATUS_ICONS[$status],
            'net_pay'       => round($net_pay, 2),
            'threshold'     => $threshold,
            'buffer_amount' => $buffer_amount,
            'buffer_pct'    => $buffer_pct,
            'tier_lower'    => $tier_lower,
            'tier_upper'    => $tier_upper,
            'description'   => $this->buildTierDescription($status, $net_pay, $buffer_amount, $buffer_pct),
        ];
    }

    // =========================================================================
    //  SECTION 2 — AI RISK SCORE ENGINE
    // =========================================================================

    /**
     * Compute a composite AI risk score (0–100) for a single employee.
     *
     * Higher scores → greater risk of future threshold breach.
     * Score bands:
     *   0–19   → Minimal risk
     *   20–39  → Low risk
     *   40–59  → Moderate risk
     *   60–79  → High risk
     *   80–100 → Critical risk
     *
     * @param  int   $employee_id
     * @param  float $current_gross
     * @param  float $current_total_deductions
     * @return array {
     *   risk_score, risk_band, risk_label,
     *   component_scores, net_pay, status,
     *   anomaly_detected, trend_slope,
     *   prediction, recommendations
     * }
     */
    public function computeRiskScore(int $employee_id, float $current_gross, float $current_total_deductions): array {

        $net_pay = $current_gross - $current_total_deductions;

        // ---------- Component 1: Current proximity score (0–100) ----------
        $proximity_score = $this->scoreProximity($net_pay);

        // ---------- Component 2: Trend direction score (0–100) ----------
        $history          = $this->getNetPayHistory($employee_id, self::HISTORY_WINDOW);
        $trend_data       = $this->analyzeTrend($history);
        $trend_score      = $this->scoreTrend($trend_data['slope'], $net_pay);

        // ---------- Component 3: Deduction pressure score (0–100) ----------
        $deduction_pct    = $current_gross > 0
            ? ($current_total_deductions / $current_gross) * 100
            : 100;
        $pressure_score   = $this->scoreDeductionPressure($deduction_pct);

        // ---------- Component 4: Anomaly score (0–100) ----------
        $deduction_history = $this->getDeductionHistory($employee_id, self::HISTORY_WINDOW);
        $anomaly_data      = $this->detectAnomaly($current_total_deductions, $deduction_history);
        $anomaly_score     = $anomaly_data['is_anomaly'] ? 100 : 0;

        // ---------- Component 5: Prediction risk score (0–100) ----------
        $prediction        = $this->predictNextPeriodNetPay($employee_id, $history, $trend_data);
        $prediction_score  = $this->scorePrediction($prediction['predicted_net_pay']);

        // ---------- Composite weighted score ----------
        $composite = (
            ($proximity_score  * self::WEIGHT_CURRENT_PROXIMITY)  +
            ($trend_score      * self::WEIGHT_TREND_DIRECTION)     +
            ($pressure_score   * self::WEIGHT_DEDUCTION_PRESSURE)  +
            ($anomaly_score    * self::WEIGHT_ANOMALY_PRESENCE)    +
            ($prediction_score * self::WEIGHT_PREDICTION_RISK)
        );

        $composite = min(100, max(0, round($composite, 1)));

        // ---------- Classification & output ----------
        $classification = $this->classifyNetPayStatus($net_pay);
        $risk_band      = $this->getRiskBand($composite);

        return [
            'employee_id'         => $employee_id,
            'risk_score'          => $composite,
            'risk_band'           => $risk_band['band'],
            'risk_label'          => $risk_band['label'],
            'risk_color'          => $risk_band['color'],
            'net_pay'             => round($net_pay, 2),
            'gross'               => round($current_gross, 2),
            'total_deductions'    => round($current_total_deductions, 2),
            'deduction_pct'       => round($deduction_pct, 2),
            'status'              => $classification['status'],
            'status_label'        => $classification['label'],
            'status_color'        => $classification['color'],
            'status_icon'         => $classification['icon'],
            'buffer_amount'       => $classification['buffer_amount'],
            'buffer_pct'          => $classification['buffer_pct'],
            'component_scores'    => [
                'proximity'         => ['score' => round($proximity_score, 1),  'weight' => self::WEIGHT_CURRENT_PROXIMITY,  'weighted' => round($proximity_score  * self::WEIGHT_CURRENT_PROXIMITY, 2)],
                'trend'             => ['score' => round($trend_score, 1),      'weight' => self::WEIGHT_TREND_DIRECTION,     'weighted' => round($trend_score      * self::WEIGHT_TREND_DIRECTION, 2)],
                'deduction_pressure'=> ['score' => round($pressure_score, 1),   'weight' => self::WEIGHT_DEDUCTION_PRESSURE,  'weighted' => round($pressure_score   * self::WEIGHT_DEDUCTION_PRESSURE, 2)],
                'anomaly'           => ['score' => round($anomaly_score, 1),    'weight' => self::WEIGHT_ANOMALY_PRESENCE,    'weighted' => round($anomaly_score    * self::WEIGHT_ANOMALY_PRESENCE, 2)],
                'prediction'        => ['score' => round($prediction_score, 1), 'weight' => self::WEIGHT_PREDICTION_RISK,     'weighted' => round($prediction_score * self::WEIGHT_PREDICTION_RISK, 2)],
            ],
            'trend'               => $trend_data,
            'anomaly'             => $anomaly_data,
            'prediction'          => $prediction,
            'recommendations'     => $this->generateRecommendations($composite, $classification['status'], $anomaly_data, $trend_data, $deduction_pct, $net_pay),
        ];
    }

    // =========================================================================
    //  SECTION 3 — TREND INTELLIGENCE
    // =========================================================================

    /**
     * Analyze net pay trend over the rolling history window.
     *
     * Uses simple linear regression (least squares) to compute:
     *   • slope          — change in net pay per period (negative = declining)
     *   • direction      — DECLINING | STABLE | IMPROVING
     *   • velocity       — rate of change label (FAST / MODERATE / SLOW)
     *   • periods_to_breach — estimated periods before threshold is crossed (if declining)
     *
     * @param  array $history  Array of net_pay floats ordered oldest → newest
     * @return array
     */
    public function analyzeTrend(array $history): array {
        $n = count($history);

        if ($n < 2) {
            return [
                'slope'             => 0,
                'intercept'         => $n === 1 ? round($history[0], 2) : 0,
                'direction'         => 'STABLE',
                'velocity'          => 'SLOW',
                'periods_to_breach' => null,
                'r_squared'         => 0,
                'data_points'       => $n,
                'average_net_pay'   => $n === 1 ? round($history[0], 2) : 0,
                'history'           => array_map(fn($v) => round($v, 2), $history),
                'trend_note'        => 'Insufficient history for trend analysis.',
            ];
        }

        // Least-squares linear regression
        $x_vals = range(1, $n);
        $x_mean = array_sum($x_vals) / $n;
        $y_mean = array_sum($history) / $n;

        $numerator   = 0;
        $denominator = 0;
        $ss_res      = 0;
        $ss_tot      = 0;

        foreach ($x_vals as $i => $x) {
            $y = $history[$i];
            $numerator   += ($x - $x_mean) * ($y - $y_mean);
            $denominator += ($x - $x_mean) ** 2;
        }

        $slope     = $denominator != 0 ? $numerator / $denominator : 0;
        $intercept = $y_mean - ($slope * $x_mean);

        // R² (coefficient of determination)
        foreach ($x_vals as $i => $x) {
            $y_pred  = $intercept + ($slope * $x);
            $ss_res += ($history[$i] - $y_pred) ** 2;
            $ss_tot += ($history[$i] - $y_mean) ** 2;
        }
        $r_squared = $ss_tot != 0 ? round(1 - ($ss_res / $ss_tot), 4) : 0;

        // Direction
        $direction = abs($slope) < 50 ? 'STABLE'
            : ($slope < 0 ? 'DECLINING' : 'IMPROVING');

        // Velocity label
        $abs_slope = abs($slope);
        $velocity  = $abs_slope > 500 ? 'FAST'
            : ($abs_slope > 150 ? 'MODERATE' : 'SLOW');

        // Periods until threshold breach (only meaningful if declining)
        $periods_to_breach = null;
        $last_net          = end($history);
        if ($slope < 0 && $last_net > self::MIN_NET_PAY) {
            $gap               = $last_net - self::MIN_NET_PAY;
            $periods_to_breach = $gap / abs($slope);
            $periods_to_breach = round($periods_to_breach, 1);
        }

        return [
            'slope'             => round($slope, 2),
            'intercept'         => round($intercept, 2),
            'direction'         => $direction,
            'velocity'          => $velocity,
            'r_squared'         => $r_squared,
            'periods_to_breach' => $periods_to_breach,
            'data_points'       => $n,
            'average_net_pay'   => round($y_mean, 2),
            'history'           => array_map(fn($v) => round($v, 2), $history),
            'trend_note'        => $this->buildTrendNote($direction, $velocity, $periods_to_breach, $slope),
        ];
    }

    // =========================================================================
    //  SECTION 4 — ANOMALY DETECTION
    // =========================================================================

    /**
     * Detect if the current deduction amount is an anomaly vs. historical pattern.
     *
     * Uses Z-score (standard score) against the historical deduction distribution.
     * A Z-score beyond ANOMALY_ZSCORE_THRESHOLD flags an unusual spike.
     *
     * @param  float $current_deduction   Current period's total deductions
     * @param  array $deduction_history   Historical total deductions (oldest → newest)
     * @return array {
     *   is_anomaly, z_score, mean, std_dev,
     *   deviation_amount, deviation_pct, severity, note
     * }
     */
    public function detectAnomaly(float $current_deduction, array $deduction_history): array {
        $n = count($deduction_history);

        if ($n < 3) {
            return [
                'is_anomaly'       => false,
                'z_score'          => 0,
                'mean'             => $n > 0 ? round(array_sum($deduction_history) / $n, 2) : 0,
                'std_dev'          => 0,
                'deviation_amount' => 0,
                'deviation_pct'    => 0,
                'severity'         => 'UNKNOWN',
                'note'             => 'Insufficient deduction history for anomaly detection.',
            ];
        }

        $mean    = array_sum($deduction_history) / $n;
        $variance = array_sum(
            array_map(fn($v) => ($v - $mean) ** 2, $deduction_history)
        ) / $n;
        $std_dev = sqrt($variance);

        $z_score = $std_dev > 0
            ? ($current_deduction - $mean) / $std_dev
            : 0;

        $is_anomaly        = abs($z_score) >= self::ANOMALY_ZSCORE_THRESHOLD;
        $deviation_amount  = round($current_deduction - $mean, 2);
        $deviation_pct     = $mean > 0 ? round(($deviation_amount / $mean) * 100, 2) : 0;

        // Severity tiers
        $abs_z    = abs($z_score);
        $severity = 'NORMAL';
        if ($is_anomaly) {
            $severity = $abs_z >= 3.5 ? 'EXTREME'
                : ($abs_z >= 3.0 ? 'HIGH'
                : 'MODERATE');
        }

        return [
            'is_anomaly'       => $is_anomaly,
            'z_score'          => round($z_score, 3),
            'mean'             => round($mean, 2),
            'std_dev'          => round($std_dev, 2),
            'current_value'    => round($current_deduction, 2),
            'deviation_amount' => $deviation_amount,
            'deviation_pct'    => $deviation_pct,
            'severity'         => $severity,
            'note'             => $is_anomaly
                ? sprintf(
                    'Deduction spike detected: ₱%.2f is ₱%.2f (%.1f%%) above historical average ₱%.2f. Z-score: %.2f.',
                    $current_deduction, $deviation_amount, $deviation_pct, $mean, $z_score
                )
                : 'Deduction amount is within normal historical range.',
        ];
    }

    // =========================================================================
    //  SECTION 5 — PREDICTIVE ENGINE
    // =========================================================================

    /**
     * Predict the next-period net pay using linear regression projection.
     *
     * @param  int   $employee_id
     * @param  array $history     Net pay history (oldest → newest); fetched if empty
     * @param  array $trend_data  Pre-computed trend (optional; computed if empty)
     * @return array {
     *   predicted_net_pay, confidence, will_breach,
     *   predicted_shortfall, periods_ahead, basis
     * }
     */
    public function predictNextPeriodNetPay(int $employee_id, array $history = [], array $trend_data = []): array {
        if (empty($history)) {
            $history = $this->getNetPayHistory($employee_id, self::HISTORY_WINDOW);
        }
        if (empty($trend_data)) {
            $trend_data = $this->analyzeTrend($history);
        }

        $n = count($history);

        if ($n === 0) {
            return [
                'predicted_net_pay' => null,
                'confidence'        => 0,
                'will_breach'       => null,
                'predicted_shortfall' => null,
                'periods_ahead'     => 1,
                'basis'             => 'NO_DATA',
                'note'              => 'No payroll history available for prediction.',
            ];
        }

        // Predict period N+1 using regression line: y = intercept + slope*(n+1)
        $next_x        = $n + 1;
        $predicted     = $trend_data['intercept'] + ($trend_data['slope'] * $next_x);
        $predicted     = round($predicted, 2);

        // Confidence: based on R² and number of data points (0–100)
        $r2_weight     = min(1.0, $trend_data['r_squared']);
        $data_weight   = min(1.0, $n / self::HISTORY_WINDOW);
        $confidence    = round(($r2_weight * 0.6 + $data_weight * 0.4) * 100, 1);

        $will_breach       = $predicted < self::MIN_NET_PAY;
        $predicted_shortfall = $will_breach
            ? round(self::MIN_NET_PAY - $predicted, 2)
            : null;

        return [
            'predicted_net_pay'   => $predicted,
            'confidence'          => $confidence,
            'will_breach'         => $will_breach,
            'predicted_shortfall' => $predicted_shortfall,
            'periods_ahead'       => 1,
            'basis'               => $n >= 3 ? 'LINEAR_REGRESSION' : 'LIMITED_DATA',
            'note'                => $will_breach
                ? sprintf(
                    'Projected net pay of ₱%.2f next period will breach GAA threshold. Shortfall: ₱%.2f.',
                    $predicted, $predicted_shortfall
                )
                : sprintf(
                    'Projected net pay of ₱%.2f next period is above GAA threshold.',
                    $predicted
                ),
        ];
    }

    // =========================================================================
    //  SECTION 6 — FULL EMPLOYEE INTELLIGENCE PROFILE
    // =========================================================================

    /**
     * Generate a complete AI intelligence profile for a single employee.
     *
     * Combines:
     *   • Base validation (from parent GAANetPayValidator)
     *   • Tier classification
     *   • Risk score & components
     *   • Trend analysis
     *   • Anomaly detection
     *   • Next-period prediction
     *   • Smart recommendations
     *   • Alert routing
     *
     * @param  int    $employee_id
     * @param  float  $gross
     * @param  float  $total_deductions
     * @param  string $payroll_frequency
     * @return array  Full intelligence profile
     */
    public function analyzeEmployee(int $employee_id, float $gross, float $total_deductions, string $payroll_frequency = 'monthly'): array {

        // ---- Base validation from parent ----
        $base_validation = $this->validatePayrollEntry($employee_id, $gross, $total_deductions, $payroll_frequency);

        // ---- Core calculations ----
        $net_pay = $gross - $total_deductions;

        // ---- Tier classification ----
        $classification = $this->classifyNetPayStatus($net_pay);

        // ---- AI Risk Score (full suite) ----
        $risk_profile = $this->computeRiskScore($employee_id, $gross, $total_deductions);

        // ---- Alert routing ----
        $alert = $this->routeAlert($classification['status'], $risk_profile['risk_score']);

        return [
            'employee_id'       => $employee_id,
            'payroll_frequency' => $payroll_frequency,
            'analyzed_at'       => date('Y-m-d H:i:s'),

            // Financial summary
            'financials' => [
                'gross'            => round($gross, 2),
                'total_deductions' => round($total_deductions, 2),
                'net_pay'          => round($net_pay, 2),
                'threshold'        => self::MIN_NET_PAY,
                'deduction_ratio'  => $gross > 0 ? round(($total_deductions / $gross) * 100, 2) : 0,
            ],

            // GAA compliance from parent
            'compliance' => [
                'is_compliant'   => $base_validation['is_valid'],
                'violations'     => $base_validation['violations'],
                'compliance_msg' => $base_validation['message'],
            ],

            // AI status tier
            'classification'  => $classification,

            // AI risk profile (includes trend, anomaly, prediction, recommendations)
            'risk_profile'    => $risk_profile,

            // Alert routing
            'alert'           => $alert,
        ];
    }

    // =========================================================================
    //  SECTION 7 — BATCH INTELLIGENCE (Department / Period Roll-Up)
    // =========================================================================

    /**
     * Run AI intelligence across all employees in a payroll period.
     *
     * Returns per-employee profiles plus aggregate statistics:
     *   • Distribution by status tier
     *   • Average risk score
     *   • Department risk heat map
     *   • Top at-risk employees list
     *
     * @param  int      $payroll_period_id
     * @param  int|null $dept_id   Optional department filter
     * @return array    Batch intelligence report
     */
    public function analyzeBatch(int $payroll_period_id, ?int $dept_id = null): array {

        // ---- Fetch all payroll entries for the period ----
        $query = "SELECT 
                    pe.payroll_entry_id,
                    pe.employee_id,
                    CONCAT(e.lastname, ', ', e.firstname) AS employee_name,
                    e.employee_id_num,
                    pe.gross,
                    pe.total_deductions,
                    (pe.gross - pe.total_deductions) AS net_pay,
                    dp.dept_id,
                    dp.dept_title
                  FROM payroll_entries pe
                  INNER JOIN employees_tbl e ON pe.employee_id = e.employee_id
                  LEFT JOIN departments_tbl dp ON pe.dept_id = dp.dept_id
                  WHERE pe.payroll_period_id = '" . intval($payroll_period_id) . "'";

        if ($dept_id !== null) {
            $query .= " AND pe.dept_id = '" . intval($dept_id) . "'";
        }

        $result = $this->db->query($query) or die($this->db->error);

        $profiles          = [];
        $tier_distribution = array_fill_keys(
            [self::STATUS_CRITICAL, self::STATUS_DANGER, self::STATUS_WARNING,
             self::STATUS_CAUTION,  self::STATUS_STABLE, self::STATUS_SAFE],
            0
        );
        $dept_risk_map     = [];
        $risk_scores       = [];
        $at_risk_employees = [];

        while ($row = $this->db->fetch_array($result)) {
            $emp_id     = intval($row['employee_id']);
            $gross      = floatval($row['gross']);
            $deductions = floatval($row['total_deductions']);
            $net_pay    = floatval($row['net_pay']);

            // Classify
            $cls   = $this->classifyNetPayStatus($net_pay);
            $score = $this->computeRiskScore($emp_id, $gross, $deductions);

            $profile = [
                'payroll_entry_id' => intval($row['payroll_entry_id']),
                'employee_id'      => $emp_id,
                'employee_name'    => $row['employee_name'],
                'employee_id_num'  => $row['employee_id_num'],
                'department'       => $row['dept_title'],
                'dept_id'          => $row['dept_id'],
                'net_pay'          => round($net_pay, 2),
                'gross'            => round($gross, 2),
                'total_deductions' => round($deductions, 2),
                'status'           => $cls['status'],
                'status_label'     => $cls['label'],
                'status_color'     => $cls['color'],
                'status_icon'      => $cls['icon'],
                'buffer_amount'    => $cls['buffer_amount'],
                'buffer_pct'       => $cls['buffer_pct'],
                'risk_score'       => $score['risk_score'],
                'risk_band'        => $score['risk_band'],
                'risk_label'       => $score['risk_label'],
                'anomaly_detected' => $score['anomaly']['is_anomaly'],
                'trend_direction'  => $score['trend']['direction'],
                'predicted_breach' => $score['prediction']['will_breach'],
                'recommendations'  => $score['recommendations'],
            ];

            $profiles[]   = $profile;
            $risk_scores[] = $score['risk_score'];
            $tier_distribution[$cls['status']]++;

            // Department roll-up
            $dept_key = $row['dept_title'] ?? 'Unknown';
            if (!isset($dept_risk_map[$dept_key])) {
                $dept_risk_map[$dept_key] = ['scores' => [], 'violations' => 0, 'dept_id' => $row['dept_id']];
            }
            $dept_risk_map[$dept_key]['scores'][] = $score['risk_score'];
            if (in_array($cls['status'], [self::STATUS_CRITICAL, self::STATUS_DANGER])) {
                $dept_risk_map[$dept_key]['violations']++;
            }

            // Flag at-risk employees
            if (in_array($cls['status'], [self::STATUS_CRITICAL, self::STATUS_DANGER, self::STATUS_WARNING])) {
                $at_risk_employees[] = [
                    'employee_id'    => $emp_id,
                    'employee_name'  => $row['employee_name'],
                    'employee_id_num'=> $row['employee_id_num'],
                    'department'     => $row['dept_title'],
                    'net_pay'        => round($net_pay, 2),
                    'status'         => $cls['status'],
                    'risk_score'     => $score['risk_score'],
                ];
            }
        }

        // Sort at-risk by risk_score descending
        usort($at_risk_employees, fn($a, $b) => $b['risk_score'] <=> $a['risk_score']);

        // Department heat map averages
        $dept_heat_map = [];
        foreach ($dept_risk_map as $dept_name => $data) {
            $avg_score = count($data['scores']) > 0
                ? round(array_sum($data['scores']) / count($data['scores']), 1)
                : 0;
            $dept_heat_map[] = [
                'department'     => $dept_name,
                'dept_id'        => $data['dept_id'],
                'avg_risk_score' => $avg_score,
                'employee_count' => count($data['scores']),
                'violations'     => $data['violations'],
                'risk_band'      => $this->getRiskBand($avg_score),
            ];
        }
        usort($dept_heat_map, fn($a, $b) => $b['avg_risk_score'] <=> $a['avg_risk_score']);

        // Aggregates
        $total          = count($profiles);
        $avg_risk_score = $total > 0
            ? round(array_sum($risk_scores) / $total, 1)
            : 0;

        return [
            'payroll_period_id' => $payroll_period_id,
            'analyzed_at'       => date('Y-m-d H:i:s'),
            'total_employees'   => $total,
            'summary' => [
                'avg_risk_score'      => $avg_risk_score,
                'overall_risk_band'   => $this->getRiskBand($avg_risk_score),
                'critical_count'      => $tier_distribution[self::STATUS_CRITICAL],
                'danger_count'        => $tier_distribution[self::STATUS_DANGER],
                'warning_count'       => $tier_distribution[self::STATUS_WARNING],
                'at_risk_total'       => $tier_distribution[self::STATUS_CRITICAL]
                                       + $tier_distribution[self::STATUS_DANGER]
                                       + $tier_distribution[self::STATUS_WARNING],
                'tier_distribution'   => $tier_distribution,
            ],
            'at_risk_employees'   => $at_risk_employees,
            'department_heat_map' => $dept_heat_map,
            'employee_profiles'   => $profiles,
        ];
    }

    // =========================================================================
    //  SECTION 8 — SMART RECOMMENDATIONS ENGINE
    // =========================================================================

    /**
     * Generate context-aware, prioritized corrective action recommendations.
     *
     * @param  float  $risk_score
     * @param  string $status
     * @param  array  $anomaly_data
     * @param  array  $trend_data
     * @param  float  $deduction_pct
     * @param  float  $net_pay
     * @return array  Array of recommendation objects {priority, action, rationale}
     */
    public function generateRecommendations(
        float  $risk_score,
        string $status,
        array  $anomaly_data,
        array  $trend_data,
        float  $deduction_pct,
        float  $net_pay
    ): array {
        $recommendations = [];
        $threshold       = self::MIN_NET_PAY;

        // ---- Recommendation 1: Immediate action for threshold breach ----
        if ($status === self::STATUS_CRITICAL) {
            $shortfall = round($threshold - $net_pay, 2);
            $recommendations[] = [
                'priority'  => 'URGENT',
                'action'    => "Reduce total deductions by at least ₱{$shortfall} to restore GAA compliance.",
                'rationale' => "Current net pay ₱{$net_pay} is below the ₱{$threshold} minimum. Payroll save is blocked.",
            ];
        }

        // ---- Recommendation 2: Anomaly spike handling ----
        if ($anomaly_data['is_anomaly']) {
            $dev = abs($anomaly_data['deviation_amount']);
            $recommendations[] = [
                'priority'  => 'HIGH',
                'action'    => "Review deduction entries for unusual charges (spike: ₱{$dev} above average). Verify or reverse the anomalous entry.",
                'rationale' => "Z-score of {$anomaly_data['z_score']} indicates a statistically significant deduction spike ({$anomaly_data['severity']} anomaly).",
            ];
        }

        // ---- Recommendation 3: Declining trend alert ----
        if ($trend_data['direction'] === 'DECLINING') {
            $ptb = $trend_data['periods_to_breach'] !== null
                ? "Estimated threshold breach in {$trend_data['periods_to_breach']} period(s)."
                : "Net pay is trending downward.";
            $recommendations[] = [
                'priority'  => $trend_data['velocity'] === 'FAST' ? 'HIGH' : 'MEDIUM',
                'action'    => "Conduct deduction review to identify and address recurring increases.",
                'rationale' => "Net pay declining at ₱{$trend_data['slope']} per period. {$ptb}",
            ];
        }

        // ---- Recommendation 4: High deduction pressure ----
        if ($deduction_pct >= 60) {
            $recommendations[] = [
                'priority'  => 'MEDIUM',
                'action'    => "Audit deduction components. Consider spreading loan obligations across more periods.",
                'rationale' => "Total deductions represent {$deduction_pct}% of gross pay, creating significant financial pressure.",
            ];
        } elseif ($deduction_pct >= 45) {
            $recommendations[] = [
                'priority'  => 'LOW',
                'action'    => "Monitor deduction growth. Any additional deductions could risk threshold proximity.",
                'rationale' => "Deduction load at {$deduction_pct}% of gross — approaching high-pressure territory.",
            ];
        }

        // ---- Recommendation 5: Warning/Caution proximity advisory ----
        if (in_array($status, [self::STATUS_DANGER, self::STATUS_WARNING])) {
            $buffer = round($net_pay - $threshold, 2);
            $recommendations[] = [
                'priority'  => $status === self::STATUS_DANGER ? 'HIGH' : 'MEDIUM',
                'action'    => "Restrict additional deductions. Current buffer of ₱{$buffer} leaves minimal safety margin.",
                'rationale' => "Net pay is in the {$status} zone — any additional deduction will likely trigger a GAA violation.",
            ];
        }

        // ---- Default: No action needed ----
        if (empty($recommendations)) {
            $recommendations[] = [
                'priority'  => 'NONE',
                'action'    => 'No corrective action required at this time.',
                'rationale' => "Net pay and risk indicators are within acceptable parameters (Risk Score: {$risk_score}/100).",
            ];
        }

        return $recommendations;
    }

    // =========================================================================
    //  SECTION 9 — ALERT ROUTING
    // =========================================================================

    /**
     * Determine the appropriate alert channel and urgency for a given status & risk.
     *
     * @param  string $status
     * @param  float  $risk_score
     * @return array  { level, channel, should_notify, escalate_to_approver, message }
     */
    public function routeAlert(string $status, float $risk_score): array {
        $channels = [];
        $level    = 'INFO';
        $escalate = false;

        switch ($status) {
            case self::STATUS_CRITICAL:
                $level    = 'CRITICAL';
                $channels = ['system_block', 'email', 'dashboard_banner', 'approver_notify'];
                $escalate = true;
                break;
            case self::STATUS_DANGER:
                $level    = 'URGENT';
                $channels = ['email', 'dashboard_warning'];
                $escalate = $risk_score >= 75;
                break;
            case self::STATUS_WARNING:
                $level    = 'WARNING';
                $channels = ['dashboard_warning'];
                $escalate = false;
                break;
            case self::STATUS_CAUTION:
                $level    = 'CAUTION';
                $channels = ['dashboard_info'];
                $escalate = false;
                break;
            default:
                $level    = 'INFO';
                $channels = [];
                $escalate = false;
        }

        $should_notify = !empty($channels);

        return [
            'level'                 => $level,
            'channels'              => $channels,
            'should_notify'         => $should_notify,
            'escalate_to_approver'  => $escalate,
            'message'               => $this->buildAlertMessage($status, $risk_score),
        ];
    }

    // =========================================================================
    //  SECTION 10 — DEDUCTION HEADROOM CALCULATOR
    // =========================================================================

    /**
     * Calculate how much additional deduction headroom exists before threshold.
     *
     * Useful for HR deciding whether a new deduction can safely be added.
     *
     * @param  int   $employee_id
     * @param  float $gross
     * @param  float $current_deductions
     * @param  float $proposed_new_deduction  New deduction to evaluate
     * @return array {
     *   headroom_amount, headroom_pct,
     *   can_add_deduction, new_net_pay_after, new_status,
     *   risk_delta, recommendation
     * }
     */
    public function calculateDeductionHeadroom(
        int   $employee_id,
        float $gross,
        float $current_deductions,
        float $proposed_new_deduction = 0
    ): array {
        $net_pay_before   = $gross - $current_deductions;
        $headroom         = max(0, $net_pay_before - self::MIN_NET_PAY);
        $headroom_pct     = $net_pay_before > 0
            ? round(($headroom / $net_pay_before) * 100, 2)
            : 0;

        $can_add_deduction = $proposed_new_deduction <= $headroom;
        $new_net_pay       = $net_pay_before - $proposed_new_deduction;
        $new_status        = $this->classifyNetPayStatus($new_net_pay);

        // Risk delta (how much does adding this deduction change the risk score?)
        $before_risk = $this->computeRiskScore($employee_id, $gross, $current_deductions);
        $after_risk  = $proposed_new_deduction > 0
            ? $this->computeRiskScore($employee_id, $gross, $current_deductions + $proposed_new_deduction)
            : $before_risk;

        $risk_delta = round($after_risk['risk_score'] - $before_risk['risk_score'], 1);

        return [
            'current_net_pay'      => round($net_pay_before, 2),
            'headroom_amount'      => round($headroom, 2),
            'headroom_pct'         => $headroom_pct,
            'proposed_deduction'   => round($proposed_new_deduction, 2),
            'can_add_deduction'    => $can_add_deduction,
            'new_net_pay_after'    => round($new_net_pay, 2),
            'new_status'           => $proposed_new_deduction > 0 ? $new_status['status'] : null,
            'new_status_label'     => $proposed_new_deduction > 0 ? $new_status['label'] : null,
            'risk_score_before'    => $before_risk['risk_score'],
            'risk_score_after'     => $after_risk['risk_score'],
            'risk_delta'           => $risk_delta,
            'recommendation'       => $can_add_deduction
                ? ($risk_delta >= 15
                    ? "Deduction is permissible but significantly increases risk score by {$risk_delta} points. Proceed with caution."
                    : "Deduction can be safely added. Remaining headroom: ₱" . round($headroom - $proposed_new_deduction, 2) . ".")
                : "Deduction of ₱{$proposed_new_deduction} exceeds available headroom of ₱{$headroom}. GAA threshold will be breached.",
        ];
    }

    // =========================================================================
    //  PRIVATE HELPER — DATA RETRIEVAL
    // =========================================================================

    /**
     * Retrieve historical net pay for an employee over N most recent periods.
     *
     * @param  int $employee_id
     * @param  int $limit  Number of periods
     * @return float[]  Ordered oldest → newest
     */
    private function getNetPayHistory(int $employee_id, int $limit = 6): array {
        $query = "SELECT (gross - total_deductions) AS net_pay
                  FROM payroll_entries
                  WHERE employee_id = '" . intval($employee_id) . "'
                  ORDER BY created_at DESC
                  LIMIT " . intval($limit);

        $result = $this->db->query($query);
        $history = [];

        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $history[] = floatval($row['net_pay']);
            }
            // Reverse so order is oldest → newest
            $history = array_reverse($history);
        }

        return $history;
    }

    /**
     * Retrieve historical total deduction amounts for an employee.
     *
     * @param  int $employee_id
     * @param  int $limit
     * @return float[]  Ordered oldest → newest
     */
    private function getDeductionHistory(int $employee_id, int $limit = 6): array {
        $query = "SELECT total_deductions
                  FROM payroll_entries
                  WHERE employee_id = '" . intval($employee_id) . "'
                  ORDER BY created_at DESC
                  LIMIT " . intval($limit);

        $result = $this->db->query($query);
        $history = [];

        if ($this->db->num_rows($result) > 0) {
            while ($row = $this->db->fetch_array($result)) {
                $history[] = floatval($row['total_deductions']);
            }
            $history = array_reverse($history);
        }

        return $history;
    }

    // =========================================================================
    //  PRIVATE HELPERS — SCORING COMPONENTS
    // =========================================================================

    /** Proximity score: how far is current net pay from threshold? (0=safe, 100=breach) */
    private function scoreProximity(float $net_pay): float {
        $threshold = self::MIN_NET_PAY;
        if ($net_pay <= 0)         return 100;
        if ($net_pay < $threshold) return 100;

        // Score decays from 100 as net pay moves further above threshold
        // Full score=100 at threshold; score=0 when net pay is 2× threshold
        $ratio = ($net_pay - $threshold) / $threshold;
        return max(0, round((1 - min($ratio, 1)) * 100, 1));
    }

    /** Trend score: declining trends score higher (0=improving, 100=fast decline) */
    private function scoreTrend(float $slope, float $current_net_pay): float {
        if ($slope >= 0) return 0;  // Stable or improving

        $abs_slope    = abs($slope);
        $pct_per_period = $current_net_pay > 0 ? ($abs_slope / $current_net_pay) * 100 : 100;

        // 0% decline/period → 0 score; 5%+ decline/period → 100 score
        return min(100, round(($pct_per_period / 5) * 100, 1));
    }

    /** Deduction pressure score: 0% deduction→0 score, 80%+ deduction→100 score */
    private function scoreDeductionPressure(float $deduction_pct): float {
        return min(100, round(($deduction_pct / 80) * 100, 1));
    }

    /** Prediction score: 100 if next period will breach, else 0 */
    private function scorePrediction(float $predicted_net_pay = null): float {
        if ($predicted_net_pay === null) return 0;
        return $predicted_net_pay < self::MIN_NET_PAY ? 100 : 0;
    }

    // =========================================================================
    //  PRIVATE HELPERS — CLASSIFICATION UTILITIES
    // =========================================================================

    /** Return tier boundary absolutes derived from MIN_NET_PAY */
    private function getTierBoundaries(): array {
        $t = self::MIN_NET_PAY;
        return [
            'danger_upper'  => $t * (1 + self::TIER_DANGER_UPPER_PCT),
            'warning_upper' => $t * (1 + self::TIER_WARNING_UPPER_PCT),
            'caution_upper' => $t * (1 + self::TIER_CAUTION_UPPER_PCT),
            'stable_upper'  => $t * (1 + self::TIER_STABLE_UPPER_PCT),
        ];
    }

    /** Return [lower, upper] peso range for a given status tier */
    private function getTierRange(string $status, array $boundaries): array {
        $t = self::MIN_NET_PAY;
        return match ($status) {
            self::STATUS_CRITICAL => [0,                        $t],
            self::STATUS_DANGER   => [$t,                       $boundaries['danger_upper']],
            self::STATUS_WARNING  => [$boundaries['danger_upper'],  $boundaries['warning_upper']],
            self::STATUS_CAUTION  => [$boundaries['warning_upper'], $boundaries['caution_upper']],
            self::STATUS_STABLE   => [$boundaries['caution_upper'], $boundaries['stable_upper']],
            self::STATUS_SAFE     => [$boundaries['stable_upper'],  null],
            default               => [0, null],
        };
    }

    /** Map status constant to human-readable label */
    private function getStatusLabel(string $status): string {
        return match ($status) {
            self::STATUS_CRITICAL => 'Critical — Below Threshold',
            self::STATUS_DANGER   => 'Danger — Critically Near Threshold',
            self::STATUS_WARNING  => 'Warning — At-Risk Proximity',
            self::STATUS_CAUTION  => 'Caution — Moderate Proximity',
            self::STATUS_STABLE   => 'Stable — Comfortable Buffer',
            self::STATUS_SAFE     => 'Safe — Well Above Threshold',
            default               => 'Unknown',
        };
    }

    /** Build a descriptive sentence for the tier */
    private function buildTierDescription(string $status, float $net_pay, float $buffer_amount, float $buffer_pct): string {
        if ($status === self::STATUS_CRITICAL) {
            return sprintf(
                'Net pay ₱%.2f is below the GAA minimum of ₱%.2f (shortfall: ₱%.2f). Payroll is non-compliant.',
                $net_pay, self::MIN_NET_PAY, abs($buffer_amount)
            );
        }
        return sprintf(
            'Net pay ₱%.2f is ₱%.2f (%.1f%%) above the GAA threshold of ₱%.2f. Status: %s.',
            $net_pay, $buffer_amount, $buffer_pct, self::MIN_NET_PAY, $this->getStatusLabel($status)
        );
    }

    /** Classify a composite risk score into a named band */
    private function getRiskBand(float $score): array {
        if ($score >= 80) return ['band' => 'CRITICAL_RISK', 'label' => 'Critical Risk',  'color' => '#DC2626'];
        if ($score >= 60) return ['band' => 'HIGH_RISK',     'label' => 'High Risk',      'color' => '#EA580C'];
        if ($score >= 40) return ['band' => 'MODERATE_RISK', 'label' => 'Moderate Risk',  'color' => '#D97706'];
        if ($score >= 20) return ['band' => 'LOW_RISK',      'label' => 'Low Risk',       'color' => '#2563EB'];
        return                    ['band' => 'MINIMAL_RISK',  'label' => 'Minimal Risk',   'color' => '#16A34A'];
    }

    /** Build a narrative trend note */
    private function buildTrendNote(string $direction, string $velocity, ?float $ptb, float $slope): string {
        if ($direction === 'IMPROVING') {
            return "Net pay is improving (₱" . abs($slope) . "/period). No threshold risk from trend.";
        }
        if ($direction === 'STABLE') {
            return "Net pay is stable with minimal fluctuation.";
        }
        $ptb_note = $ptb !== null
            ? "Projected breach in approximately {$ptb} period(s)."
            : "Already below or at threshold.";
        return "Net pay is declining at ₱" . abs($slope) . "/period ({$velocity} decline). {$ptb_note}";
    }

    /** Build alert message text */
    private function buildAlertMessage(string $status, float $risk_score): string {
        return match ($status) {
            self::STATUS_CRITICAL => "PAYROLL BLOCKED: Employee net pay is below GAA minimum threshold (₱5,000). Immediate action required.",
            self::STATUS_DANGER   => "URGENT: Employee net pay is critically close to GAA threshold. Risk Score: {$risk_score}/100. Review deductions now.",
            self::STATUS_WARNING  => "WARNING: Employee net pay is approaching GAA threshold. Risk Score: {$risk_score}/100. Monitor deductions.",
            self::STATUS_CAUTION  => "CAUTION: Employee net pay has a moderate buffer above GAA threshold. Risk Score: {$risk_score}/100.",
            default               => "Employee net pay is within safe parameters. Risk Score: {$risk_score}/100.",
        };
    }
}
?>
