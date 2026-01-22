<?php 
	include_once("DB_conn.php");

	class Department{

		protected $db;
		private $DeptID = "";
		private $DeptCode = "";
		private $DeptTitle = "";
		private $DeptName = "";

		public function __construct() {
        	$this->db = new DB_conn();	
    	}

    	function setDeptID($newValue){ $this->DeptID = $newValue; }
		function setDeptCode($newValue){ $this->DeptCode = $newValue; }
		function setDeptTitle($newValue){ $this->DeptTitle = $newValue; }
    	function setDeptName($newValue){ $this->DeptName = $newValue; }

    	function getDeptID(){ return $this->DeptID; }
    	function getDeptCode(){ return $this->DeptCode; }
		function getDeptTitle(){ return $this->DeptTitle; }
    	function getDeptName(){ return $this->DeptName; }

    	public function AddDepartment(){
			$dept_id = $this->db->escape_string(trim(($this->DeptID)));
			$dept_code = $this->db->escape_string(trim(($this->DeptCode)));
			$dept_title = $this->db->escape_string(trim(($this->DeptTitle)));
			$dept_name = $this->db->escape_string(trim(($this->DeptName)));

			//return $doctype_code.$doctype_name;
			
			//checking if the service available in db
			$query = "SELECT * FROM departments_tbl 
					  WHERE dept_id = '$dept_id'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			
			if($count_row == 0){
				$sql = "INSERT INTO departments_tbl (dept_code, dept_title, dept_name) 
							VALUES('".$dept_code."','".$dept_title."','".$dept_name."');";
				$query = $this->db->query($sql) or die($this->db->error);
				if ($query) {
					return true;
				}
				else{ return false; }
			}
			else{
				return $this->UpdateDepartment($dept_id);		//update
			}
		}

		public function UpdateDepartment($dept_id){
			$dept_code = $this->db->escape_string(trim(($this->DeptCode)));
			$dept_title = $this->db->escape_string(trim(($this->DeptTitle)));
			$dept_name = $this->db->escape_string(trim(($this->DeptName)));
			
			//checking if the data available in db
			$query = "SELECT * FROM departments_tbl 
					  WHERE dept_id = '$dept_id'
					  LIMIT 1";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			if($this->db->num_rows($result)==1){

				$row = $this->db->fetch_array($result);
				$dept_id_from_db = $row['dept_id'];

				$sql = "UPDATE departments_tbl 
						SET dept_code='$dept_code', dept_title='$dept_title', dept_name='$dept_name'
						WHERE dept_id = '$dept_id_from_db'";

				$query = $this->db->query($sql) or die($this->db->error);
				if($query){
					return true;
				}
				else {return false;}
			}
			else {return false;}
		}

		public function GetDepartmentDetails($dept_id){
			$query = "SELECT * FROM departments_tbl WHERE dept_id = '$dept_id'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row==1){
				return $row = $this->db->fetch_array($result);
			}
			else{return false;}
		}

		public function GetDepartments(){

			$query = "SELECT * FROM departments_tbl ORDER BY dept_id ASC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$departments[] = $row;		
				}
				return $departments;
			}
			else{return false;}
		}

		public function getDeptByID($dept_id){

			$query = "SELECT * FROM departments_tbl WHERE dept_id = '$dept_id'";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				$department = $this->db->fetch_array($result);
				//$this->DocTypeID = $doctype["doctype_id"];
				$this->setDeptCode($department["dept_code"]);
				$this->setDeptName($department["dept_name"]);

				return true;
			}
			else{return false;}
		}

		public function DeleteDepartment($dept_id){
			$sql = "DELETE FROM departments_tbl WHERE dept_id = '$dept_id'";
			$query = $this->db->query($sql) or die($this->db->error);
			if ($query) {
				return true;
			}
			else{return false;}
		}

		##########-------User Agency----------##############
		public function AddUserAgency($agency_name,$address){
			$user_agency_name = $this->db->escape_string(trim(($agency_name)));
			$user_agency_address = $this->db->escape_string(trim(($address)));

			//return $doctype_code.$doctype_name;
			
			//checking if the service available in db
			$query = "SELECT user_agency_name 
					  FROM user_agency_tbl 
					  WHERE user_agency_name LIKE '%$user_agency_name%'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			
			if($count_row == 0){
				$query = "INSERT INTO user_agency_tbl (user_agency_name, user_agency_address) 
							VALUES('".$user_agency_name."','".$user_agency_address."');";
				
				$result = $this->db->query($query) or die($this->db->error);
				
				return true;
			}
			else{return false;}
		}

		public function GetUserAgency(){

			$query = "SELECT * FROM user_agency_tbl";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row==1){
				return $row = $this->db->fetch_array($result);

			}
			else{return false;}
		}


	}
?>