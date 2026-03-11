<?php
include_once '../includes/class/PrivilegedUser.php';

class Employee extends PrivilegedUser
{
	protected $db;
	private $EmployeeData = "";
	private $EmployeeID = "";

	public function __construct()
	{
		parent::__construct();
	}

	function setEmployeeData($newValue)
	{
		$this->EmployeeData = $newValue;
	}
	function setEmployeeID($newValue)
	{
		$this->EmployeeID = $newValue;
	}

	function getEmployeeID()
	{
		return $this->EmployeeID;
	}

	function getEmployeeIDByUserId($userID)
	{
		$query = "SELECT * FROM employees_tbl WHERE userID = '$userID'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			$row = $this->db->fetch_array($result);
			return $row['employee_id'];
		} else {
			return false;
		}
	}

	public function SaveEmployee()
	{
		$employee_data = $this->EmployeeData;
		$employee_id_num = $this->db->escape_string(trim($employee_data['employee_id_num']));
		$firstname = $this->db->escape_string(trim($employee_data['firstname']));
		$middlename = $this->db->escape_string(trim($employee_data['middlename']));
		$lastname = $this->db->escape_string(trim($employee_data['lastname']));
		$extension = $this->db->escape_string(trim($employee_data['extension']));
		$birthdate = $this->db->escape_string(trim($employee_data['birthdate']));
		$gender = $this->db->escape_string(trim($employee_data['gender']));
		$civil_status = $this->db->escape_string(trim($employee_data['civil_status']));
		$address = $this->db->escape_string(trim($employee_data['address']));
		$prof_expertise = $this->db->escape_string(trim($employee_data['prof_expertise']));
		$date_hired = $this->db->escape_string(trim($employee_data['date_hired']));
		$employee_status = $this->db->escape_string(trim($employee_data['employee_status']));

		//-------Note: Include checks if item already exist before saving-----------//
		$query = "SELECT * FROM employees_tbl WHERE employee_id_num = '$employee_id_num'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 0) {
			if (isset($employee_data)) {
				$sql = "INSERT INTO employees_tbl (employee_id_num, firstname, middlename, lastname, extension, birthdate, gender, civil_status, address, prof_expertise, hire_date, employee_status) 
                        VALUES('" . $employee_id_num . "','" . $firstname . "','" . $middlename . "','" . $lastname . "','" . $extension . "','" . $birthdate . "','" . $gender . "','" . $civil_status . "','" . $address . "','" . $prof_expertise . "','" . $date_hired . "','" . $employee_status . "');";
				$query = $this->db->query($sql) or die($this->db->error);
				if ($query) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function UpdateEmployee($employee_id)
	{
		$employee_data = $this->EmployeeData;
		$employee_id_num = $this->db->escape_string(trim($employee_data['employee_id_num']));
		$firstname = $this->db->escape_string(trim($employee_data['firstname']));
		$middlename = $this->db->escape_string(trim($employee_data['middlename']));
		$lastname = $this->db->escape_string(trim($employee_data['lastname']));
		$extension = $this->db->escape_string(trim($employee_data['extension']));
		$birthdate = $this->db->escape_string(trim($employee_data['birthdate']));
		$gender = $this->db->escape_string(trim($employee_data['gender']));
		$civil_status = $this->db->escape_string(trim($employee_data['civil_status']));
		$address = $this->db->escape_string(trim($employee_data['address']));
		$prof_expertise = $this->db->escape_string(trim($employee_data['prof_expertise']));
		$date_hired = $this->db->escape_string(trim($employee_data['date_hired']));
		$employee_status = $this->db->escape_string(trim($employee_data['employee_status']));

		$sql = "UPDATE employees_tbl 
						SET employee_id_num='$employee_id_num', 
                            firstname='$firstname', 
                            middlename='$middlename',
                            lastname='$lastname',
                            extension='$extension',
                            birthdate='$birthdate', 
                            gender='$gender',
							civil_status='$civil_status',
							address='$address',
							prof_expertise='$prof_expertise',
							hire_date='$date_hired',
							employee_status='$employee_status'
						WHERE employee_id = '$employee_id'";

		$query = $this->db->query($sql) or die($this->db->error);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function UpdateUserIDOfEmployee($employee_id, $user_id)
	{
		$sql = "UPDATE employees_tbl 
						SET userID='$user_id'
						WHERE employee_id = '$employee_id'";

		$query = $this->db->query($sql) or die($this->db->error);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function UpdateEmployeeGovNumbers($employee_id, $gov_numbers)
	{
		$bir_tin = $this->db->escape_string(trim($gov_numbers['bir_tin']));
		$gsis_bp = $this->db->escape_string(trim($gov_numbers['gsis_bp']));
		$pagibig_mid = $this->db->escape_string(trim($gov_numbers['pagibig_mid']));
		$philhealth_no = $this->db->escape_string(trim($gov_numbers['philhealth_no']));
		$sss_no = $this->db->escape_string(trim($gov_numbers['sss_no']));

		$sql = "UPDATE employees_tbl 
						SET tin='$bir_tin', 
							gsis_bp='$gsis_bp', 
							pagibig_mid='$pagibig_mid',
							philhealth_no='$philhealth_no',
							sss_no='$sss_no'
						WHERE employee_id = '$employee_id'";

		$query = $this->db->query($sql) or die($this->db->error);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function GetEmployees()
	{
		$query = "SELECT * FROM employees_tbl ORDER BY lastname ASC";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row > 0) {
			while ($row = $this->db->fetch_array($result)) {
				$employees[] = $row;
			}
			return $employees;
		} else {
			return false;
		}
	}

	public function GetEmployeeDetails($employee_id)
	{
		$query = "SELECT * FROM employees_tbl WHERE employee_id = '$employee_id'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			return $row = $this->db->fetch_array($result);
		} else {
			return false;
		}
	}

	public function GetEmployeeGovNumbers($employee_id)
	{
		$query = "SELECT tin, gsis_bp, pagibig_mid, philhealth_no, sss_no 
					FROM employees_tbl 
					WHERE employee_id = '$employee_id'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			return $row = $this->db->fetch_array($result);
		} else {
			return false;
		}
	}

	public function GetEmployeeFullNameByID($employee_id)
	{
		$query = "SELECT CONCAT(lastname, ', ', firstname, ' ' ,IF(LENGTH(middlename) > 0, CONCAT(LEFT(middlename, 1), '. '), ''), ' ', extension) AS full_name 
						FROM employees_tbl WHERE employee_id = '$employee_id'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			return $row = $this->db->fetch_array($result);
		} else {
			return false;
		}
	}

	public function DeleteEmployee($employee_id)
	{
		$sql = "DELETE FROM employees_tbl WHERE employee_id = '$employee_id'";
		$query = $this->db->query($sql) or die($this->db->error);
		if ($query) {
			return true;
		} else {
			return false;
		}
	}

	public function GetEmployeeDataUsers()
	{
		$query = "SELECT * FROM user_role_tbl a
						INNER JOIN users_tbl b
						ON a.userID = b.userID
						LEFT JOIN employees_tbl c
						ON b.userID = c.userID
						WHERE a.roleID = 0
						OR a.roleID = 3
						ORDER BY a.userID DESC";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row > 0) {
			while ($row = $this->db->fetch_array($result)) {
				$employees[] = $row;
			}
			return $employees;
		} else {
			return false;
		}
	}

	public function GetEmployeesWithoutUser()
	{
		$query = "SELECT * FROM employees_tbl WHERE userID = '' ORDER BY employee_id ASC";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row > 0) {
			while ($row = $this->db->fetch_array($result)) {
				$employees[] = $row;
			}
			return $employees;
		} else {
			return false;
		}
	}

	public function GetEmployeeUser($employee_id)
	{
		$query = "SELECT * FROM employees_tbl a
						INNER JOIN users_tbl b
						ON a.userID = b.userID 
						WHERE a.employee_id = '$employee_id'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			return $row = $this->db->fetch_array($result);
		} else {
			return false;
		}
	}

	public function FetchEmployedEmployeesWithEarnings()
	{
		$query = "SELECT a.employee_id,
                    	CONCAT(a.lastname, ', ', a.firstname, ' ' ,IF(LENGTH(a.middlename) > 0, CONCAT(LEFT(a.middlename, 1), '. '), ''), ' ', a.extension) AS full_name
						FROM employees_tbl a
						INNER JOIN employee_earnings b
						ON a.employee_id = b.employee_id
						INNER JOIN employee_employments_tbl c
						ON b.employment_id = c.employment_id
						WHERE b.end_date IS NULL
						AND c.employment_status = '1'
						ORDER BY full_name ASC";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row > 0) {
			while ($row = $this->db->fetch_array($result)) {
				$employees[] = $row;
			}
			return $employees;
		} else {
			return false;
		}
	}

	public function FetchBasicRateOfEmployedEmployeesWithEarning($employee_id)
	{
		$query = "SELECT c.rate FROM employees_tbl a
						INNER JOIN employee_earnings b
						ON a.employee_id = b.employee_id
						INNER JOIN employee_employments_tbl c
						ON b.employment_id = c.employment_id
						WHERE b.end_date IS NULL
						AND c.employment_status = '1'
						AND a.employee_id = '$employee_id'";
		$result = $this->db->query($query) or die($this->db->error);
		$count_row = $this->db->num_rows($result);
		if ($count_row == 1) {
			return $row = $this->db->fetch_array($result);
		} else {
			return false;
		}
	}

}
?>