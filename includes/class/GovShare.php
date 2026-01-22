<?php 
	include_once("DB_conn.php");

	class GovShare{

		protected $db;
		private $GovShareTypeID = "";
		private $GovShareName = "";
		private $GovShareCode = "";
		private $GovShareRate = "";
		private $IsPercentage = "";
		private $GovShareAcctCode = "";

		public function __construct() {
        	$this->db = new DB_conn();	
    	}

		public function setGovShareTypeID($newValue){ $this->GovShareTypeID = $newValue; }
    	public function setGovShareName($newValue){ $this->GovShareName = $newValue; }
		public function setGovShareCode($newValue){ $this->GovShareCode = $newValue; }
		public function setGovShareRate($newValue){ $this->GovShareRate = $newValue; }
		public function setIsPercentage($newValue){ $this->IsPercentage = $newValue; }
		public function setGovShareAcctCode($newValue){ $this->GovShareAcctCode = $newValue; }

    	public function SaveGovShare(){
			$govshare_type_id = $this->db->escape_string(trim(($this->GovShareTypeID)));
			$govshare_name = $this->db->escape_string(trim(($this->GovShareName)));
			$govshare_code = $this->db->escape_string(trim(($this->GovShareCode)));
			$govshare_rate = $this->db->escape_string(trim(($this->GovShareRate)));
			$is_percentage = $this->db->escape_string(trim(($this->IsPercentage)));
			$govshare_acctcode = $this->db->escape_string(trim(($this->GovShareAcctCode)));
			
			//checking if the service available in db
			$query = "SELECT * FROM govshares 
					  WHERE govshare_name LIKE '%$govshare_name%'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			
			if($count_row == 0){
				$sql = "INSERT INTO govshares (deduction_type_id, govshare_name, govshare_code, govshare_acctcode, govshare_rate, is_percentage) 
							VALUES('".$govshare_type_id."','".$govshare_name."','".$govshare_code."','".$govshare_acctcode."','".$govshare_rate."','".$is_percentage."');";
				$query = $this->db->query($sql) or die($this->db->error);
				if ($query) {
					return true;
				}
				else{ return false; }
			}
			else{
				return false;
			}
		}

		public function EditGovShare($govshare_id){
			$govshare_type_id = $this->db->escape_string(trim(($this->GovShareTypeID)));
			$govshare_name = $this->db->escape_string(trim(($this->GovShareName)));
			$govshare_code = $this->db->escape_string(trim(($this->GovShareCode)));
			$govshare_rate = $this->db->escape_string(trim(($this->GovShareRate)));
			$is_percentage = $this->db->escape_string(trim(($this->IsPercentage)));
			$govshare_acctcode = $this->db->escape_string(trim(($this->GovShareAcctCode)));
			
			//checking if the service available in db
			$query = "SELECT * FROM govshares 
					  WHERE govshare_id = '$govshare_id'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			
			if($count_row > 0){
				$sql = "UPDATE govshares 
						SET deduction_type_id = '".$govshare_type_id."',
							govshare_name = '".$govshare_name."',
							govshare_code = '".$govshare_code."',
							govshare_acctcode = '".$govshare_acctcode."',
							govshare_rate = '".$govshare_rate."',
							is_percentage = '".$is_percentage."'
						WHERE govshare_id = '".$govshare_id."'";
				$query = $this->db->query($sql) or die($this->db->error);
				if($query){ 
					return true; 
				}
				else{ 
					return false; 
				}
			}
			else{
				return false;
			}
		}

		public function GetGovShares(){
			$query = "SELECT * FROM govshares a
						LEFT JOIN deduction_types b
						ON a.deduction_type_id = b.deduction_type_id 
						ORDER BY b.deduction_type_id ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$govshares[] = $row;		
				}
				return $govshares;
			}
			else{return false;}
		}

		public function GetGovShareDetailsByID($govshare_id){
			$query = "SELECT * FROM govshares 
					  	WHERE govshare_id = '$govshare_id'
					  	LIMIT 1";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $govshare = $this->db->fetch_array($result);
			}
			else {return false;}
		}

		public function DeleteGovShare($govshare_id){
			$query = "DELETE FROM govshares WHERE govshare_id = '$govshare_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if ($result) {
				return true;
			}
			else{return false;}
		}

		public function SaveEmployeeGovShares($employee_id, $rate, $data_arr){
			$employee_id = $this->db->escape_string(trim($employee_id));
			$today = date("Y-m-d");

			foreach($data_arr as $item){
				$govshare_id = $this->db->escape_string(trim($item[0]));
				$amount = $this->db->escape_string(trim($item[1]));

				// Close any existing active records for this employee-govshare
				$update_old = "UPDATE employee_govshares 
							SET effective_end = DATE_SUB('$today', INTERVAL 1 DAY)
							WHERE employee_id = '$employee_id' 
							AND govshare_id = '$govshare_id' 
							AND effective_end IS NULL";
				$this->db->query($update_old) or die($this->db->error);

				// Insert the new record as effective today
				$insert_new = "INSERT INTO employee_govshares 
							(employee_id, govshare_id, govshare_amount, employee_rate, effective_start) 
							VALUES('$employee_id', '$govshare_id', '$amount', '$rate', '$today')";
				$this->db->query($insert_new) or die($this->db->error);
			}
			return true;
		}

		public function FetchGovShares(){		//---Purely Govshares table only data
			$query = "SELECT govshare_id, govshare_name 
                               FROM govshares 
                               ORDER BY govshare_id ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$govshares[] = $row;		
				}
				return $govshares;
			}
			else{return false;}
		}

		public function GetEmployeeGovShareRecords(){
			$query = "SELECT * FROM employees_tbl a
						INNER JOIN employee_govshares b
						ON a.employee_id = b.employee_id
						RIGHT JOIN govshares c
						ON b.govshare_id = c.govshare_id
						WHERE b.effective_end IS NULL 
						ORDER BY a.lastname ASC";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row > 0){
				while($row = $this->db->fetch_array($result)){
					$emp_govshares[] = $row;		
				}
				return $emp_govshares;
			}
			else{return false;}
		}
	}
?>