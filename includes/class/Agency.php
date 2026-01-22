<?php 
	include_once("DB_conn.php");

	class Agency{

		protected $db;
		private $AgencyCode = "";
		private $AgencyName = "";

		public function __construct() {
        	$this->db = new DB_conn();	
    	}

    	function setAgencyCode($newValue){ $this->AgencyCode = $newValue; }
    	function setAgencyName($newValue){ $this->AgencyName = $newValue; }

    	//function getDocTypeID(){ return $this->DocTypeID; }
    	function getDeptCode(){ return $this->AgencyCode; }
    	function getDeptName(){ return $this->AgencyName; }

    	public function AddAgencyCategory(){
			$agency_category_code = $this->db->escape_string(trim(($this->AgencyCode)));
			$agency_category_name = $this->db->escape_string(trim(($this->AgencyName)));

			//return $doctype_code.$doctype_name;
			
			//checking if the service available in db
			$query = "SELECT category_code, category_name 
					  FROM agency_categories_tbl 
					  WHERE category_code LIKE '%$agency_category_code%'
					  OR category_name LIKE '%$agency_category_name%'";
			
			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			
			//if the service is not in db then insert to the table
			
			if($count_row == 0){
				$query = "INSERT INTO agency_categories_tbl (category_code, category_name) 
							VALUES('".$agency_category_code."','".$agency_category_name."');";
				
				$result = $this->db->query($query) or die($this->db->error);
				
				return true;
			}
			else{return false;}
		}

		public function GetAgencies(){

			$query = "SELECT * FROM agency_categories_tbl ORDER BY agency_category_id ASC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$agencies[] = $row;		
				}
				return $agencies;
			}
			else{return false;}
		}

		public function UpdateDepartment($dept_id){
			$dept_code = $this->db->escape_string(trim(($this->DeptCode)));
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
						SET dept_code='$dept_code', dept_name='$dept_name'
						WHERE dept_id = '$dept_id_from_db'";

				$query = $this->db->query($sql) or die($this->db->error);
				if($this->db->affectedRows($query) == 1){
					return true;
				}
				else {return false;}
			}
			else {return false;}
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

		


	}
?>