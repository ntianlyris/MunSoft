<?php
include_once("DB_conn.php");

class Leave
{
    protected $db;
    private $LeaveData = "";
    private $LeaveTypeData = "";

    function setLeaveData($leaveDataArr)
    {
        $this->LeaveData = $leaveDataArr;
    }
    function setLeaveTypeData($leaveTypeDataArr)
    {
        $this->LeaveTypeData = $leaveTypeDataArr;
    }

    public function __construct()
    {
        $this->db = new DB_conn();
    }

    public function SaveLeaveApplication()
    {
        $leave_data = $this->LeaveData;

        // Extract variables from $leave_data array
        $emp_id = $this->db->escape_string($leave_data['emp_id']);
        $leave_type = $this->db->escape_string($leave_data['leave_type']);
        $dates_json = $this->db->escape_string($leave_data['dates']); // JSON string of dates
        $reason = $this->db->escape_string($leave_data['reason']);
        $attachmentPath = $this->db->escape_string($leave_data['attachmentPath']);
        $status = $this->db->escape_string($leave_data['status']);
        $date_filed = $this->db->escape_string($leave_data['date_filed']);

        $sql = "INSERT INTO leave_applications 
                        (employee_id, leave_type_id, dates, reason, attach_path, status, date_filed) 
                    VALUES (
                        '$emp_id',
                        '$leave_type',
                        '$dates_json',
                        '$reason',
                        '$attachmentPath',
                        '$status',
                        '$date_filed'
                    );";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getLeaveApplicationsByEmployee($emp_id)
    {
        $emp_id = $this->db->escape_string($emp_id);
        $sql = "SELECT * FROM leave_applications a
                    INNER JOIN leave_types b
                    ON a.leave_type_id = b.leave_type_id 
                    WHERE a.employee_id = '$emp_id' ORDER BY a.date_filed DESC";
        $result = $this->db->query($sql);

        $applications = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['dates'] = json_decode($row['dates'], true);
                $applications[] = $row;
            }
        }
        return $applications;
    }

    public function getLeaveApplications()
    {
        $sql = "SELECT a.*,
                    CONCAT(
                        b.lastname, ', ', b.firstname, ' ',
                        IF(LENGTH(b.middlename) > 0, CONCAT(LEFT(b.middlename, 1), '. '), ''), 
                        ' ', b.extension
                    ) AS employee, 
                    c.leave_name 
                    FROM leave_applications a
                    INNER JOIN employees_tbl b
                    ON a.employee_id = b.employee_id 
                    INNER JOIN leave_types c
                    ON a.leave_type_id = c.leave_type_id
                    ORDER BY a.date_filed DESC";
        $result = $this->db->query($sql);

        $applications = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['dates'] = json_decode($row['dates'], true);
                $applications[] = $row;
            }
        }
        return $applications;
    }

    public function AddLeaveType()
    {
        $d = $this->LeaveTypeData;
        $leave_code = $this->db->escape_string($d['leave_code']);
        $leave_name = $this->db->escape_string($d['leave_name']);
        $yearly_allotment = $this->db->escape_string($d['yearly_allotment']);
        $monthly_accrual = $this->db->escape_string($d['monthly_accrual']);
        $is_accumulative = $this->db->escape_string($d['is_accumulative']);
        $max_accumulation = $this->db->escape_string($d['max_accumulation']);
        $gender_restriction = $this->db->escape_string($d['gender_restriction']);
        $reset_policy = $this->db->escape_string($d['reset_policy']);
        $requires_attachment = $this->db->escape_string($d['requires_attachment']);
        $active = $this->db->escape_string($d['active']);
        $frequency_limit = $this->db->escape_string($d['frequency_limit']);
        $description = $this->db->escape_string($d['description']);

        $sql = "INSERT INTO leave_types (
                leave_code, leave_name, yearly_allotment, monthly_accrual, is_accumulative, max_accumulation, gender_restriction, reset_policy, requires_attachment, active, frequency_limit, description
            ) VALUES (
                '$leave_code', '$leave_name', '$yearly_allotment', '$monthly_accrual', '$is_accumulative', '$max_accumulation', '$gender_restriction', '$reset_policy', '$requires_attachment', '$active', '$frequency_limit', '$description'
            );";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function UpdateLeaveType()
    {
        $d = $this->LeaveTypeData;
        $leave_type_id = $this->db->escape_string($d['leave_type_id']);
        $leave_code = $this->db->escape_string($d['leave_code']);
        $leave_name = $this->db->escape_string($d['leave_name']);
        $yearly_allotment = $this->db->escape_string($d['yearly_allotment']);
        $monthly_accrual = $this->db->escape_string($d['monthly_accrual']);
        $is_accumulative = $this->db->escape_string($d['is_accumulative']);
        $max_accumulation = $this->db->escape_string($d['max_accumulation']);
        $gender_restriction = $this->db->escape_string($d['gender_restriction']);
        $reset_policy = $this->db->escape_string($d['reset_policy']);
        $requires_attachment = $this->db->escape_string($d['requires_attachment']);
        $active = $this->db->escape_string($d['active']);
        $frequency_limit = $this->db->escape_string($d['frequency_limit']);
        $description = $this->db->escape_string($d['description']);

        $sql = "UPDATE leave_types SET 
                leave_code = '$leave_code',
                leave_name = '$leave_name',
                yearly_allotment = '$yearly_allotment',
                monthly_accrual = '$monthly_accrual',
                is_accumulative = '$is_accumulative',
                max_accumulation = '$max_accumulation',
                gender_restriction = '$gender_restriction',
                reset_policy = '$reset_policy',
                requires_attachment = '$requires_attachment',
                active = '$active',
                frequency_limit = '$frequency_limit',
                description = '$description'
            WHERE leave_type_id = '$leave_type_id';";
        $query = $this->db->query($sql);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function FetchAllLeaveTypes()
    {
        $sql = "SELECT * FROM leave_types ORDER BY is_accumulative DESC, leave_name ASC";
        $result = $this->db->query($sql);
        $leave_types = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $leave_types[] = $row;
            }
        }
        return $leave_types;
    }

    public function GetLeaveTypeDetails($leave_type_id)
    {
        $query = "SELECT * FROM leave_types WHERE leave_type_id = '$leave_type_id'";
        $result = $this->db->query($query) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        if ($count_row == 1) {
            return $row = $this->db->fetch_array($result);
        } else {
            return false;
        }
    }

    public function DeleteLeaveType($leave_type_id)
    {
        $query = "DELETE FROM leave_types WHERE leave_type_id = '$leave_type_id'";
        $result = $this->db->query($query) or die($this->db->error);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function initializeEmployeeLeaveBalances($emp_id, $year)
    {
        $emp_id = $this->db->escape_string($emp_id);
        $year = $this->db->escape_string($year);

        // Fetch all active leave types
        $sql = "SELECT * FROM leave_types WHERE active = 1";
        $result = $this->db->query($sql);

        if (!$result) {
            return false;
        }

        $allSuccess = true;

        if ($result->num_rows > 0) {
            while ($lt = $result->fetch_assoc()) {
                $leave_type_id = $lt['leave_type_id'];
                $yearly_allotment = floatval($lt['yearly_allotment']);
                $monthly_accrual = intval($lt['monthly_accrual']);
                $is_accumulative = intval($lt['is_accumulative']);
                $reset_policy = $lt['reset_policy'];

                // Check if balance already exists for this employee, leave type, and year
                $check_sql = "SELECT emp_leave_bal_id FROM employee_leave_balances 
                        WHERE employee_id = '$emp_id' AND leave_type_id = '$leave_type_id' AND year = '$year'";
                $check_result = $this->db->query($check_sql);

                if ($check_result && $check_result->num_rows > 0) {
                    // Already exists, skip
                    continue;
                } elseif (!$check_result) {
                    $allSuccess = false;
                    continue;
                }

                // Get previous year carryover
                $carryover = 0;
                $prevYear = $year - 1;
                $prev_sql = "SELECT remaining, accumulated FROM employee_leave_balances 
                        WHERE employee_id = '$emp_id' AND leave_type_id = '$leave_type_id' AND year = '$prevYear'";
                $prev_result = $this->db->query($prev_sql);
                if ($prev_result && $prev_result->num_rows > 0) {
                    $prevRow = $prev_result->fetch_assoc();
                    $carryover = floatval($prevRow['remaining']) + floatval($prevRow['accumulated']);
                }

                // Logic for starting balance
                $starting_balance = 0;
                $accumulated = 0;
                $remaining = 0;

                if ($monthly_accrual == 0) {
                    // Full allotment at start of year
                    if ($reset_policy == 'carry_over' && $is_accumulative) {
                        $starting_balance = $yearly_allotment + $carryover;
                        $accumulated = $carryover;
                    } elseif ($reset_policy == 'reset') {
                        $starting_balance = $yearly_allotment;
                        $accumulated = 0;
                    } elseif ($reset_policy == 'none') {
                        // Custom business rule, default to yearly_allotment
                        $starting_balance = $yearly_allotment;
                        $accumulated = 0;
                    } else {
                        $starting_balance = $yearly_allotment;
                        $accumulated = 0;
                    }
                    $remaining = $starting_balance;
                } else {
                    // Monthly accrual: only carryover credited at start
                    $starting_balance = $carryover;
                    $accumulated = $carryover;

                    // Calculate earned so far (pro-rated by days in year)
                    $startOfYear = strtotime("$year-01-01");
                    $today = time();
                    $daysElapsed = ($today > $startOfYear) ? ceil(($today - $startOfYear) / 86400) : 0;
                    $daysInYear = date('L', $year) ? 366 : 365;
                    $earned_so_far = ($yearly_allotment / $daysInYear) * $daysElapsed;

                    // Used so far is 0 at initialization
                    $remaining = $starting_balance + $earned_so_far;
                }

                // Insert new balance record
                $insert_sql = "INSERT INTO employee_leave_balances 
                        (employee_id, leave_type_id, year, allotted, used, remaining, accumulated) 
                        VALUES (
                            '$emp_id',
                            '$leave_type_id',
                            '$year',
                            '$yearly_allotment',
                            0,
                            '$remaining',
                            '$accumulated'
                        )";
                $insert_result = $this->db->query($insert_sql);
                if (!$insert_result) {
                    $allSuccess = false;
                }
            }
        }
        return $allSuccess;
    }

    public function FetchEmployeeLeaveBalances($employee_id, $year)
    {
        $employee_id = $this->db->escape_string($employee_id);
        $year = $this->db->escape_string($year);

        $sql = "SELECT elb.*, lt.leave_code, lt.leave_name, lt.yearly_allotment, lt.monthly_accrual, lt.is_accumulative, lt.max_accumulation, lt.gender_restriction, lt.reset_policy, lt.requires_attachment, lt.active, lt.frequency_limit
                FROM employee_leave_balances elb
                INNER JOIN leave_types lt ON elb.leave_type_id = lt.leave_type_id
                WHERE elb.employee_id = '$employee_id' AND elb.year = '$year'
                ORDER BY lt.is_accumulative DESC, lt.leave_name ASC";
        $result = $this->db->query($sql);

        $balances = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $balances[] = $row;
            }
        }
        return $balances;
    }

    public function isEmployeeLeaveBalancesInitialized($employee_id, $year)
    {
        $employee_id = $this->db->escape_string($employee_id);
        $year = $this->db->escape_string($year);

        $sql = "SELECT emp_leave_bal_id FROM employee_leave_balances 
                WHERE employee_id = '$employee_id' AND year = '$year' LIMIT 1";
        $result = $this->db->query($sql);

        return ($result && $result->num_rows > 0);
    }

    public function GetLeaveApplicationDetails($leave_application_id)
    {
        $query = "SELECT * FROM leave_applications WHERE leave_application_id = '$leave_application_id'";
        $result = $this->db->query($query) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        if ($count_row == 1) {
            $row = $this->db->fetch_array($result);
            $row['dates'] = json_decode($row['dates'], true);
            return $row;
        } else {
            return false;
        }
    }

    public function getCurrentLeaveBalance($employee_id, $leave_type_id, $date = null)
    {
        $employee_id = $this->db->escape_string($employee_id);
        $leave_type_id = $this->db->escape_string($leave_type_id);
        $date = $date ? strtotime($date) : time();
        $year = date('Y', $date);

        // Get leave type config
        $lt_sql = "SELECT * FROM leave_types WHERE leave_type_id = '$leave_type_id' LIMIT 1";
        $lt_result = $this->db->query($lt_sql);
        if (!$lt_result || $lt_result->num_rows == 0)
            return null;
        $lt = $lt_result->fetch_assoc();

        $yearly_allotment = floatval($lt['yearly_allotment']);
        $monthly_accrual = intval($lt['monthly_accrual']);

        // Get employee leave balance row
        $elb_sql = "SELECT * FROM employee_leave_balances 
                        WHERE employee_id = '$employee_id' AND leave_type_id = '$leave_type_id' AND year = '$year' LIMIT 1";
        $elb_result = $this->db->query($elb_sql);
        if (!$elb_result || $elb_result->num_rows == 0)
            return null;
        $elb = $elb_result->fetch_assoc();

        $carry = floatval($elb['accumulated']);
        $allotted = floatval($elb['allotted']);

        // Calculate earned leave
        if ($monthly_accrual == 1) {
            $startOfYear = strtotime("$year-01-01");
            $daysElapsed = ($date > $startOfYear) ? ceil(($date - $startOfYear) / 86400) : 0;
            $daysInYear = date('L', $year) ? 366 : 365;
            $earned = ($yearly_allotment / $daysInYear) * $daysElapsed;
            $start_credit = $carry;
        } else {
            $earned = 0;
            $start_credit = $carry + $allotted;
        }

        // Calculate used leave for this year and leave type
        $used_sql = "SELECT SUM(JSON_LENGTH(dates)) AS used_days FROM leave_applications 
                        WHERE employee_id = '$employee_id' AND leave_type_id = '$leave_type_id' 
                        AND status = 'Approved' AND YEAR(date_filed) = '$year'";
        $used_result = $this->db->query($used_sql);
        $used_days = 0;
        if ($used_result && $used_row = $used_result->fetch_assoc()) {
            $used_days = floatval($used_row['used_days']);
        }

        // Compute current balance
        $current_balance = $start_credit + $earned - $used_days;
        $current_balance = max(0, round($current_balance, 2)); // Example: no negative balance

        // Return all relevant data for deduction and calculation
        return [
            'leave_type_id' => $lt,
            'leave_balance' => $elb,
            'earned' => $earned,
            'start_credit' => $start_credit,
            'used_days' => $used_days,
            'current_balance' => $current_balance
        ];
    }

    public function ApproveLeaveApplication($leave_application_id)
    {
        $leave_application_id = $this->db->escape_string($leave_application_id);

        // Fetch the leave application to get employee_id
        $app_details = $this->GetLeaveApplicationDetails($leave_application_id);
        if (!$app_details) {
            return ['success' => false, 'error' => 'application_not_found'];
        }

        $employee_id = $app_details['employee_id'];
        $leave_type_id = $app_details['leave_type_id'];
        $current_year = date('Y');

        if (!$this->isEmployeeLeaveBalancesInitialized($employee_id, $current_year)) {
            return ['success' => false, 'error' => 'uninitialized_balances'];
        }

        // Check if the specific leave type exists in balances
        $sql = "SELECT emp_leave_bal_id FROM employee_leave_balances 
                    WHERE employee_id = '$employee_id' AND leave_type_id = '$leave_type_id' AND year = '$current_year' LIMIT 1";
        $result = $this->db->query($sql);
        if (!$result || $result->num_rows == 0) {
            // Initialize only the missing leave type
            return ['success' => false, 'error' => 'leave_type_not_initialized'];
        }

        // Note: Further logic to deduct leave balances can be implemented here before approval
        // Calculate total leave days applied
        $dates = $app_details['dates']; // array of dates
        $days_applied = count($dates); // or sum durations if partial days

        $curr_employee_leave_balance = $this->getCurrentLeaveBalance($employee_id, $leave_type_id);
        if ($curr_employee_leave_balance === null) {
            return ['success' => false, 'error' => 'leave_type_not_initialized'];
        }
        // Use the 'current_balance' key from getCurrentLeaveBalance result
        if ($curr_employee_leave_balance['current_balance'] < $days_applied) {
            return ['success' => false, 'error' => 'insufficient_balance'];
        } else {
            // Deduct the leave days from the balance
            $elb = $curr_employee_leave_balance['leave_balance'];
            $new_used = floatval($elb['used']) + $days_applied;
            $new_remaining = floatval($elb['remaining']) - $days_applied;

            $update_sql = "UPDATE employee_leave_balances SET 
                                used = '$new_used',
                                remaining = '$new_remaining'
                               WHERE emp_leave_bal_id = '{$elb['emp_leave_bal_id']}'";
            $update_result = $this->db->query($update_sql);
            if (!$update_result) {
                return ['success' => false, 'error' => 'update_failed'];
            } else {
                // Proceed to approval
                $sql = "UPDATE leave_applications SET status = 'Approved' WHERE leave_application_id = '$leave_application_id'";
                $query = $this->db->query($sql);

                if ($query) {
                    return ['success' => true];
                } else {
                    return ['success' => false, 'error' => 'update_failed'];
                }
            }
        }
    }

    public function DisapproveLeaveApplication($leave_application_id)
    {
        $leave_application_id = $this->db->escape_string($leave_application_id);
        $sql = "UPDATE leave_applications SET status = 'Disapproved' WHERE leave_application_id = '$leave_application_id'";
        $query = $this->db->query($sql);
        return $query ? true : false;
        // Note: Further logic to restore leave balances can be implemented here
    }
}

?>