<?php 
	include_once("DB_conn.php");

	class Deduction{

		protected $db;
		private $EmployeeID = "";
		private $ConfigDeductionID = "";
		private $EmployeeDeductionID = "";
		private $DeductType = "";
		private $DeductAcctCode = "";
		private $DeductCode = "";
		private $DeductTitle = "";
		private $DeductionParticulars = "";
		private $EffectiveDate = "";
		private $TotalDeduction = "";
		private $DateCreated = "";
		private $IsEmployeeShare = "";
		private $DeductCategory = "";
		
		//private $SubTotalDeduction = "";
		//private $DeductSchedID = "";

		
		
		

		public function __construct() {
        	$this->db = new DB_conn();	
    	}

    	function setEmployeeID($newValue){ $this->EmployeeID = $newValue; }
		function setConfigDeductionID($newValue){ $this->ConfigDeductionID = $newValue; }
		function setEmployeeDeductionID($newValue){ $this->EmployeeDeductionID = $newValue; }
		function setDeductType($newValue){ $this->DeductType = $newValue; }
		function setDeductAcctCode($newValue){ $this->DeductAcctCode = $newValue; }
    	function setDeductCode($newValue){ $this->DeductCode = $newValue; }
    	function setDeductTitle($newValue){ $this->DeductTitle = $newValue; }
		function setDeductionParticulars($newValue){ $this->DeductionParticulars = $newValue; }
		function setEffectiveDate($newValue){ $this->EffectiveDate = $newValue; }
    	function setTotalDeduction($newValue){ $this->TotalDeduction = $newValue; }
    	function setDateCreated($newValue){ $this->DateCreated = $newValue; }
		function setIsEmployeeShare($newValue){ $this->IsEmployeeShare = $newValue; }
		function setDeductCategory($newValue){ $this->DeductCategory = $newValue; }

    	//function setSubTotalDeduction($newValue){ $this->SubTotalDeduction = $newValue; }

    	function getConfigDeductionID(){ return $this->ConfigDeductionID; }
    	function getEmployeeID(){ return $this->EmployeeID; }
    	function getDeductType(){ return $this->DeductType; }
    	function getDeductCode(){ return $this->DeductCode; }
    	function getDeductTitle(){ return $this->DeductTitle; }

    	public function SaveEmployeeDeduction(){
    		$employee_id = $this->db->escape_string(trim(($this->EmployeeID)));
			$deduction_particulars = $this->db->escape_string(trim(($this->DeductionParticulars)));
			$effective_date = $this->db->escape_string(trim(($this->EffectiveDate)));
			$total_deduction = $this->db->escape_string(trim(($this->TotalDeduction)));
			$date_created = $this->db->escape_string(trim(($this->DateCreated)));
			//$earning_status = 'active';

			//checking if the service available in db
			$query = "SELECT employee_id 
					  FROM employee_deductions 
					  WHERE employee_id = '$employee_id'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			if($count_row == 0){
				$query = "INSERT INTO employee_deductions (employee_id, total_deduction, deduction_particulars, effective_date, date_created) 
							VALUES('".$employee_id."','".$total_deduction."','".$deduction_particulars."','".$effective_date."','".$date_created."');";
				
				$result = $this->db->query($query) or die($this->db->error);
				if ($result) {
					$this->setEmployeeDeductionID($this->db->last_id());
					return true;
				}
				else{return false;}
			}
			//if the service is in db then insert to the table and update the previous entry end date a day before
			else{
				 // Step 1: Close the current active deduction (set end_date to 1 day before new deduction)
				$sqlCloseOld = "UPDATE employee_deductions
								SET end_date = DATE_SUB('$effective_date', INTERVAL 1 DAY),
									date_updated = CURDATE()
								WHERE employee_id = '$employee_id' AND end_date IS NULL";
				$query = $this->db->query($sqlCloseOld) or die($this->db->error);
				if($query){
					// Step 2: Insert the new earning setup
					$sqlInsert = "INSERT INTO employee_deductions (employee_id, total_deduction, deduction_particulars, effective_date, date_created)
								VALUES ('".$employee_id."','".$total_deduction."','".$deduction_particulars."','".$effective_date."','".$date_created."')";
					$query = $this->db->query($sqlInsert) or die($this->db->error);
					if ($query) {
						$this->setEmployeeDeductionID($this->db->last_id());
						return true;
					}
					else{return false;}
				}
			}
		}

		public function SaveEmployeeDeductionComponents($employee_deductions_arr){
			$employee_deduction_id = $this->db->escape_string(trim(($this->EmployeeDeductionID)));

			foreach ($employee_deductions_arr as $key => $value) {
				$sql = "INSERT INTO employee_deductions_components (employee_deduction_id, config_deduction_id, deduction_comp_amt)
						VALUES ('".$employee_deduction_id."','".$value[0]."','".$value[1]."');";

				$query = $this->db->query($sql) or die ($this->db->error);
			}
			return true;
		}

		public function EditEmployeeDeduction(){
			include_once '../includes/view/functions.php';

			$employee_deduction_id = $this->db->escape_string(trim(($this->EmployeeDeductionID)));
			$employee_id = $this->db->escape_string(trim(($this->EmployeeID)));
			$deduction_particulars = $this->db->escape_string(trim(($this->DeductionParticulars)));
			$effective_date = $this->db->escape_string(trim(($this->EffectiveDate)));
			$total_deductions = $this->db->escape_string(trim(($this->TotalDeduction)));
			$date_updated = $this->db->escape_string(trim(($this->DateCreated)));
			//$earning_status = 'active';
			//$date_updated = DateToday();


			$sql = "UPDATE employee_deductions
					SET employee_id = '".$employee_id."',
						total_deduction = '".$total_deductions."',
						deduction_particulars = '".$deduction_particulars."',
						effective_date = '".$effective_date."',
						date_updated = '".$date_updated."'
					WHERE employee_deduction_id ='".$employee_deduction_id."'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
				return true;
			}
			else {return false;}
		}
		
		public function AddDeductionConfig(){
			$config_deduction_id = $this->db->escape_string(trim(($this->ConfigDeductionID)));
			$deduction_type_id = $this->DeductType;
			$deduction_acct_code = $this->DeductAcctCode;
			$deduction_code = $this->DeductCode;
			$deduction_title = $this->DeductTitle;
			$is_employee_share = $this->IsEmployeeShare;
			$deduction_category = $this->DeductCategory;
			
			//checking if the service available in db
			$query = "SELECT * FROM config_deductions 
						WHERE deduct_acct_code = '$deduction_acct_code'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
				
			//if the service is not in db then insert to the table
			if($count_row == 0){
				$query = "INSERT INTO config_deductions (deduction_type_id, deduct_acct_code, deduct_code, deduct_title, is_employee_share, deduct_category) 
							VALUES('".$deduction_type_id."','".$deduction_acct_code."','".$deduction_code."','".$deduction_title."','".$is_employee_share."','".$deduction_category."');";
					
				$result = $this->db->query($query) or die($this->db->error);
				if ($result) {
					return true;
				}
				else{
					return false;
				}
			}
		}

		public function EditDeductionConfig(){
			$config_deduction_id = $this->db->escape_string(trim(($this->ConfigDeductionID)));
			$deduction_type_id = $this->DeductType;
			$deduction_acct_code = $this->DeductAcctCode;
			$deduction_code = $this->DeductCode;
			$deduction_title = $this->DeductTitle;
			$is_employee_share = $this->IsEmployeeShare;
			$deduction_category = $this->DeductCategory;

			//checking if the service available in db
			$query = "SELECT * FROM config_deductions 
						WHERE config_deduction_id = '$config_deduction_id'
						OR deduct_acct_code = '$deduction_acct_code'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				//Update the existing data...
				$sql = "UPDATE config_deductions 
						SET deduction_type_id = '".$deduction_type_id."',
							deduct_acct_code = '".$deduction_acct_code."',
							deduct_code = '".$deduction_code."',
							deduct_title = '".$deduction_title."',
							is_employee_share = '".$is_employee_share."',
							deduct_category = '".$deduction_category."'
						WHERE config_deduction_id = '".$config_deduction_id."'";
				$query = $this->db->query($sql) or die($this->db->error);
				if($query){ 
					return true; 
				}
				else{ 
					return 'Update failed'; 
				}
			}
			else{
				return false;
			}
		}

		//---getting all deduction types ---//
		public function GetDeductionTypes(){
			$query = "SELECT * FROM deduction_types ORDER BY deduction_type_id ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$types[] = $row;		
				}
				return $types;
			}
			else{return false;}
		}

		//---getting all deduction configs---//
		public function GetDeductionConfigs(){
			$query = "SELECT * FROM config_deductions a 
						INNER JOIN deduction_types b 
						ON a.deduction_type_id = b.deduction_type_id 
						ORDER BY a.deduct_acct_code ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$deductions[] = $row;		
				}
				return $deductions;
			}
			else{return false;}
		}

		//---getting a specific deduction config details---//
		public function getConfigDeductionByID($config_deduction_id){
			$query = "SELECT * FROM config_deductions a
						INNER JOIN deduction_types b
						ON a.deduction_type_id = b.deduction_type_id
					  	WHERE a.config_deduction_id = '$config_deduction_id'
					  	LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $deduction = $this->db->fetch_array($result);
			}
			else {return false;}
		}

		//---getting all employees deductions---//
		public function GetAllEmployeesDeductions(){
			$query = "SELECT * FROM employee_deductions a
					  INNER JOIN employees_tbl b
					  ON a.employee_id = b.employee_id
					   WHERE a.end_date IS NULL
					  ORDER BY  b.employee_id_num ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$employees_deductions[] = $row;		
				}
				return $employees_deductions;
			}
			else{return false;}
		}

		//---getting employee deductions with details---//
		public function GetEmployeeDeduction($emp_deduction_id){
			$query = "SELECT * FROM employee_deductions
					  WHERE employee_deduction_id = '$emp_deduction_id'
					  LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $deduction = $this->db->fetch_array($result);
			}
			else {return false;}
		}

		//---getting employee deductions compositions with details---//
		public function GetEmployeeDeductionComps($emp_deduction_id){
			$query = "SELECT * FROM employee_deductions_components a
						INNER JOIN config_deductions b
						ON a.config_deduction_id = b.config_deduction_id
						INNER JOIN deduction_types c
						ON b.deduction_type_id = c.deduction_type_id
					  	WHERE a.employee_deduction_id = '$emp_deduction_id'
					  	ORDER BY b.config_deduction_id ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$deductions[] = $row;		
				}
				return $deductions;
			}
			else {return false;}
		}

		public function DeleteDeductionConfig($config_deduction_id){
			$query = "DELETE FROM config_deductions WHERE config_deduction_id = '$config_deduction_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployeeDeduction($emp_deduction_id){
			$query = "DELETE FROM employee_deductions WHERE employee_deduction_id = '$emp_deduction_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployeeDeductionComponents($emp_deduction_id){
			$query = "DELETE FROM employee_deductions_components WHERE employee_deduction_id = '$emp_deduction_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function GetAllDeductionsByEmployee($employee_id){		// get all set employee earnings, use for history
			$query = "SELECT * FROM employee_deductions
                WHERE employee_id = '$employee_id'
            	ORDER BY effective_date DESC";
        
			$result = $this->db->query($query) or die($this->db->error);
			
			$deductions = [];
			while ($row = $this->db->fetch_array($result)) {
				$deductions[] = $row;
			}
			return $deductions;
		}

		/*public function isDeductionAppliedForPeriod($employee_id, $effective_date) {
			include_once("Payroll.php");
			$Payroll = new Payroll();

			// Determine payroll frequency
			$active_frequency = $Payroll->GetCurrentActiveFrequency();
			$frequency = strtoupper($active_frequency['freq_code'] ?? 'MONTHLY');

			// Main check: see if the effective date falls within an applied/finalized payroll period
			$stmt = "SELECT pp.payroll_period_id, pp.date_start, pp.date_end, pp.is_locked
					FROM payroll_entries pe
					INNER JOIN payroll_periods pp ON pp.payroll_period_id = pe.payroll_period_id
					WHERE pe.employee_id = '$employee_id'
					AND '$effective_date' BETWEEN pp.date_start AND pp.date_end
					AND pp.is_locked = 1
					LIMIT 1";

			$result = $this->db->query($stmt) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if ($count_row > 0) {
				return true; // payroll already applied
			}

			// Optional additional check for SEMI-MONTHLY payroll frequency
			if ($frequency === 'SEMI-MONTHLY') {
				$month_start = date('Y-m-01', strtotime($effective_date));
				$mid_month = date('Y-m-15', strtotime($effective_date));
				$month_end = date('Y-m-t', strtotime($effective_date));

				$stmt2 = "SELECT pp.payroll_period_id
						FROM payroll_entries pe
						INNER JOIN payroll_periods pp ON pp.payroll_period_id = pe.payroll_period_id
						WHERE pe.employee_id = '$employee_id'
							AND (
								('$mid_month' BETWEEN pp.date_start AND pp.date_end)
								OR ('$month_end' BETWEEN pp.date_start AND pp.date_end)
							)
							AND pp.is_locked = 1
						LIMIT 1";

				$result2 = $this->db->query($stmt2) or die($this->db->error);
				$count_row2 = $this->db->num_rows($result2);

				if ($count_row2 > 0) {
					return true;
				}
			}

			return false; // safe to edit
		}*/

	}
?>