<?php 
	include_once("DB_conn.php");

	class Earning{

		protected $db;
		private $EmployeeID = "";
		private $EmployeeEarningID = "";
		private $EmploymentID = "";
		private $EarningParticulars = "";
		private $EffectiveDate = "";
		private $LockedRate = "";
		private $Gross = "";
		private $ConfigEarningID = "";
		private $EarningAcctCode = "";
		private $EarningCode = "";
		private $EarningTitle = "";
		private $DateCreated = "";
		private $DateUpdated = "";

		public function __construct() {
        	$this->db = new DB_conn();	
    	}

		function setEmployeeID($newValue){ $this->EmployeeID = $newValue; }
		function setEmployeeEarningID($newValue){ $this->EmployeeEarningID = $newValue; }
		function setEmploymentID($newValue){ $this->EmploymentID = $newValue; }
		function setEarningParticulars($newValue){ $this->EarningParticulars = $newValue; }
		function setEffectiveDate($newValue){ $this->EffectiveDate = $newValue; }
		function setLockedRate($newValue){ $this->LockedRate = $newValue; }
    	function setGross($newValue){ $this->Gross = $newValue; }
		function setConfigEarningID($newValue){ $this->ConfigEarningID = $newValue; }
		function setEarningAcctCode($newValue){ $this->EarningAcctCode = $newValue; }
    	function setEarningCode($newValue){ $this->EarningCode = $newValue; }
    	function setEarningTitle($newValue){ $this->EarningTitle = $newValue; }
		function setDateCreated($newValue){ $this->DateCreated = $newValue; }
		function setDateUpdated($newValue){ $this->DateUpdated = $newValue; }

    	//function getDocTypeID(){ return $this->DocTypeID; }
    	function getEarningCategory(){ return $this->EarningCategory; }
    	function getEarningCode(){ return $this->EarningCode; }
    	function getEarningTitle(){ return $this->EarningTitle; }

    	public function SaveEarning(){
    		$employee_id = $this->db->escape_string(trim(($this->EmployeeID)));
			$employment_id = $this->db->escape_string(trim(($this->EmploymentID)));
			$earning_particulars = $this->db->escape_string(trim(($this->EarningParticulars)));
			$effective_date = $this->db->escape_string(trim(($this->EffectiveDate)));
			$locked_rate = $this->db->escape_string(trim(($this->LockedRate)));
			$gross = $this->db->escape_string(trim(($this->Gross)));
			$date_created = $this->db->escape_string(trim(($this->DateCreated)));
			//$earning_status = 'active';

			//checking if the service available in db
			$query = "SELECT employee_id 
					  FROM employee_earnings 
					  WHERE employee_id = '$employee_id'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			if($count_row == 0){
				$query = "INSERT INTO employee_earnings (employee_id, gross_amount, employment_id, locked_rate, earning_particulars, effective_date, date_created) 
							VALUES('".$employee_id."','".$gross."','".$employment_id."','".$locked_rate."','".$earning_particulars."','".$effective_date."','".$date_created."');";
				
				$result = $this->db->query($query) or die($this->db->error);
				if ($result) {
					$this->setEmployeeEarningID($this->db->last_id());
					return true;
				}
				else{return false;}
			}
			//if the service is in db then insert to the table and update the previous entry end date a day before
			else{
				 // Step 1: Close the current active earning (set end_date to 1 day before new earning)
				$sqlCloseOld = "UPDATE employee_earnings
								SET end_date = DATE_SUB('$effective_date', INTERVAL 1 DAY),
									date_updated = CURDATE()
								WHERE employee_id = '$employee_id' AND end_date IS NULL";
				$query = $this->db->query($sqlCloseOld) or die($this->db->error);
				if($query){
					// Step 2: Insert the new earning setup
					$sqlInsert = "INSERT INTO employee_earnings (employee_id, gross_amount, employment_id, locked_rate, earning_particulars, effective_date, date_created)
								VALUES ('".$employee_id."','".$gross."','".$employment_id."','".$locked_rate."','".$earning_particulars."','".$effective_date."','".$date_created."')";
					$query = $this->db->query($sqlInsert) or die($this->db->error);
					if ($query) {
						$this->setEmployeeEarningID($this->db->last_id());
						return true;
					}
					else{return false;}
				}
			}
		}

		public function SaveEmployeeEarningComponents($employee_earnings_arr){
			$employee_earning_id = $this->db->escape_string(trim(($this->EmployeeEarningID)));

			foreach ($employee_earnings_arr as $key => $value) {
				$sql = "INSERT INTO employee_earnings_components (employee_earning_id, config_earning_id, earning_comp_amt)
						VALUES ('".$employee_earning_id."','".$value[0]."','".$value[1]."');";

				$query = $this->db->query($sql) or die ($this->db->error);
			}
			return true;
		}

		public function EditEarning(){
			include_once '../includes/view/functions.php';

			$employee_earning_id = $this->db->escape_string(trim(($this->EmployeeEarningID)));
			//$employee_id = $this->db->escape_string(trim(($this->EmployeeID)));						//we will not touch the already save employee id on db
			//$earning_particulars = $this->db->escape_string(trim(($this->EarningParticulars)));
			//$effective_date = $this->db->escape_string(trim(($this->EffectiveDate)));
			$gross = $this->db->escape_string(trim(($this->Gross)));
			//$date_created = $this->db->escape_string(trim(($this->DateCreated)));
			//$earning_status = 'active';
			$date_updated = DateToday();


			$sql = "UPDATE employee_earnings
					SET gross_amount = '".$gross."',
						date_updated = '".$date_updated."'
					WHERE employee_earning_id='".$employee_earning_id."'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
				return true;
			}
			else {return false;}
		}

		public function AddEarningConfig(){
			$config_earning_id = $this->db->escape_string(trim(($this->ConfigEarningID)));
			$earning_acct_code = $this->db->escape_string(trim(($this->EarningAcctCode)));
			$earning_code = $this->db->escape_string(trim(($this->EarningCode)));
			$earning_title = $this->db->escape_string(trim(($this->EarningTitle)));
			
			//checking if the service available in db
			$query = "SELECT config_earning_id
					  FROM config_earnings 
					  WHERE config_earning_id = '$config_earning_id'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			
			if($count_row == 0){
				$query = "INSERT INTO config_earnings (earning_acct_code,earning_code, earning_title) 
							VALUES('".$earning_acct_code."','".$earning_code."','".$earning_title."');";
				
				$result = $this->db->query($query) or die($this->db->error);
				if ($result) {
					return true;
				}
				else{return false;}
			}
			else{
				//Update the existing data...
				$sql = "UPDATE config_earnings 
						SET earning_acct_code = '".$earning_acct_code."',
							earning_code = '".$earning_code."',
							earning_title = '".$earning_title."'
						WHERE config_earning_id = '".$config_earning_id."'";
				$query = $this->db->query($sql) or die($this->db->error);
				if($query){ 
					return true; 
				}
				else{ 
					return false; 
				}
			}
		}

		public function getConfigEarningByID($config_earning_id){
			$query = "SELECT * FROM config_earnings
					  WHERE config_earning_id = '$config_earning_id'
					  LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $earning = $this->db->fetch_array($result);
			}
			else {return false;}
		}

		public function GetEarningConfigs(){

			$query = "SELECT * FROM config_earnings ORDER BY config_earning_id ASC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$earnings[] = $row;		
				}
				return $earnings;
			}
			else{return false;}
		}

		public function GetAllEmployeesEarnings(){		//----Fetch all employee with earnings that is current active, end dates = null,meaning no end date
			$query = "SELECT * FROM employee_earnings a
					  INNER JOIN employees_tbl b
					  ON a.employee_id = b.employee_id
					  WHERE a.end_date IS NULL
					  ORDER BY  b.employee_id_num ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$employees_earnings[] = $row;		
				}
				return $employees_earnings;
			}
			else{return false;}
		}

		public function GetEmployeeEarning($emp_earning_id){
			$query = "SELECT * FROM employee_earnings
					  WHERE employee_earning_id = '$emp_earning_id'
					  LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $earning = $this->db->fetch_array($result);
			}
			else {return false;}
		}

		public function GetEmployeeEarningLinkedToEmployment($emp_earning_id){
			$query = "SELECT * FROM employee_earnings a
						LEFT JOIN employee_employments_tbl b
						ON a.employment_id = b.employment_id
						LEFT JOIN positions_tbl c
						ON b.position_id = c.position_id
					WHERE a.employee_earning_id = '$emp_earning_id'";

			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);

			if($count_row == 1){
				return $earning = $this->db->fetch_array($result);
			} else {
				return false;
			}
		}

		public function GetEmployeeEarningComps($emp_earning_id){
			$query = "SELECT * FROM employee_earnings_components a
						INNER JOIN config_earnings b
						ON a.config_earning_id = b.config_earning_id
					  	WHERE a.employee_earning_id = '$emp_earning_id'
					  	ORDER BY b.config_earning_id ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$earnings[] = $row;		
				}
				return $earnings;
			}
			else {return false;}
		}

		public function DeleteEarningConfig($config_earning_id){
			$query = "DELETE FROM config_earnings WHERE config_earning_id = '$config_earning_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployeeEarning($emp_earning_id){
			$query = "DELETE FROM employee_earnings WHERE employee_earning_id = '$emp_earning_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployeeEarningComponents($emp_earning_id){
			$query = "DELETE FROM employee_earnings_components WHERE employee_earning_id = '$emp_earning_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function GetAllEarningsByEmployee($employee_id){		// get all set employee earnings, use for history
			$query = "SELECT * FROM employee_earnings a
					LEFT JOIN employee_employments_tbl b
						ON a.employment_id = b.employment_id
					LEFT JOIN positions_tbl c
						ON b.position_id = c.position_id
					WHERE a.employee_id = '$employee_id'
					ORDER BY a.effective_date DESC";
        
			$result = $this->db->query($query) or die($this->db->error);
			
			$earnings = [];
			while ($row = $this->db->fetch_array($result)) {
				$earnings[] = $row;
			}
			return $earnings;
		}
		
	}
?>