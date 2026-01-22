<?php
	include_once("DB_conn.php");

	class Employment {
		protected $db;
		private $EmploymentData = "";

		public function __construct(){
			$this->db = new DB_conn();
		}

		function setEmploymentData($newValue){ $this->EmploymentData = $newValue; }

		public function SaveEmployment(){
			$employment_data = $this->EmploymentData;
			$employee_id = $this->db->escape_string(trim($employment_data['employee_id']));
			$employment_refnum = $this->db->escape_string(trim($employment_data['employment_refnum']));
			$employment_type = $this->db->escape_string(trim($employment_data['employment_type']));
			$employment_start = $this->db->escape_string(trim($employment_data['employment_start']));
			$employment_end = $this->db->escape_string(trim($employment_data['employment_end']));
			$position = $this->db->escape_string(trim($employment_data['position']));
			$department_assigned = $this->db->escape_string(trim($employment_data['department_assigned']));
			$designation = $this->db->escape_string(trim($employment_data['designation']));
			$work_nature = $this->db->escape_string(trim($employment_data['work_nature']));
            $work_specifics = $this->db->escape_string(trim($employment_data['work_specifics']));
			$employment_rate = $this->db->escape_string(trim($employment_data['employment_rate']));
			$employment_particulars = $this->db->escape_string(trim($employment_data['employment_particulars']));
			$employment_status = $this->db->escape_string(trim($employment_data['employment_status']));

			//-------Note: Include checks if item already exist before saving-----------//
            $query = "SELECT * FROM employee_employments_tbl WHERE employment_refnum = '$employment_refnum'";
            $result = $this->db->query($query) or die($this->db->error);
            $count_row = $this->db->num_rows($result);
            if ($count_row == 0) {
                if (isset($employment_data)) {
                    $sql = "INSERT INTO employee_employments_tbl (employee_id, employment_refnum, employment_type, employment_start, employment_end, position_id, dept_assigned, designation, work_nature, work_specifics, rate , employment_particulars , employment_status) 
                        VALUES('".$employee_id."','".$employment_refnum."','".$employment_type."','".$employment_start."','".$employment_end."','".$position."','".$department_assigned."','".$designation."','".$work_nature."','".$work_specifics."','".$employment_rate."','".$employment_particulars."','".$employment_status."');";
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
            else{
				return false;
			}
		}

		public function EditEmployment(){
			$employment_data = $this->EmploymentData;
			$employee_id = $this->db->escape_string(trim($employment_data['employee_id']));
			$employment_id = $this->db->escape_string(trim($employment_data['employment_id']));
			$employment_refnum = $this->db->escape_string(trim($employment_data['employment_refnum']));
			$employment_type = $this->db->escape_string(trim($employment_data['employment_type']));
			$employment_start = $this->db->escape_string(trim($employment_data['employment_start']));
			$employment_end = $this->db->escape_string(trim($employment_data['employment_end']));
			$position = $this->db->escape_string(trim($employment_data['position']));
			$department_assigned = $this->db->escape_string(trim($employment_data['department_assigned']));
			$designation = $this->db->escape_string(trim($employment_data['designation']));
			$work_nature = $this->db->escape_string(trim($employment_data['work_nature']));
            $work_specifics = $this->db->escape_string(trim($employment_data['work_specifics']));
			$employment_particulars = $this->db->escape_string(trim($employment_data['employment_particulars']));
			$employment_rate = $this->db->escape_string(trim($employment_data['employment_rate']));

			$sql = "UPDATE employee_employments_tbl 
						SET employment_refnum='$employment_refnum', 
                            employment_type='$employment_type', 
                            employment_start='$employment_start',
                            employment_end='$employment_end',
                            position_id='$position',
                            dept_assigned='$department_assigned', 
                            designation='$designation',
							work_nature='$work_nature',
							work_specifics='$work_specifics',
							employment_particulars='$employment_particulars',
							rate='$employment_rate'
						WHERE employment_id = '$employment_id'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
			    return true;
			}
			else {return false;}
		}

		public function hasCurrentActiveEmployment($employee_id){
			$query = "SELECT * FROM employee_employments_tbl a
						INNER JOIN positions_tbl b
						ON a.position_id = b.position_id
						WHERE a.employee_id = '$employee_id'
						AND a.employment_status = '1'
						AND b.position_status = '1'";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				return true;
			}
			else{return false;}
		}

		public function FetchActiveEmploymentDetails($employee_id){
			$query = "SELECT * FROM employee_employments_tbl a
						INNER JOIN positions_tbl b
						ON a.position_id = b.position_id
						INNER JOIN departments_tbl c
						ON b.dept_id = c.dept_id
						WHERE a.employee_id = '$employee_id'
						AND a.employment_status = '1'";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $row = $this->db->fetch_array($result);
			}
			else{return false;}
		}

		//-- Fetch employment details by employee ID and employment ID regardless if active for tracing payroll data of previous positions --//
		public function FetchEmployeeEmploymentDetailsByIDs($employee_id, $employment_id){
			$query = "SELECT * FROM employee_employments_tbl a
						INNER JOIN positions_tbl b
						ON a.position_id = b.position_id
						WHERE a.employee_id = '$employee_id'
						AND a.employment_id = '$employment_id'";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row == 1){
				return $row = $this->db->fetch_array($result);
			}
			else{
				return false;
			}
		}

		///---For changing employment status ---///
        public function UpdateEmploymentStatus($employment_id,$status){
			$employment_status = $status;

			$sql = "UPDATE employee_employments_tbl 
						SET employment_status='$employment_status'
						WHERE employment_id = '$employment_id'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
			    return true;
			}
			else {return false;}
		}

        public function GetEmployeeEmployments($employee_id){
            $query = "SELECT * FROM employee_employments_tbl a
						LEFT JOIN positions_tbl b
						ON a.position_id = b.position_id
						LEFT JOIN departments_tbl c
						ON b.dept_id = c.dept_id
						WHERE a.employee_id = '$employee_id' ORDER BY a.employment_id DESC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$employments[] = $row;		
				}
				return $employments;
			}
			else{return false;}
        }

		public function GetEmployeeEmploymentDetails($employment_id){
            $query = "SELECT * FROM employee_employments_tbl a
						INNER JOIN positions_tbl b
						ON a.position_id = b.position_id
						INNER JOIN departments_tbl c
						ON b.dept_id = c.dept_id
						WHERE a.employment_id = '$employment_id'";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row==1){
				return $row = $this->db->fetch_array($result);
			}
			else{return false;}
        }

		public function isEmployeeEmployed($employee_id){
			 $query = "SELECT * FROM employee_employments_tbl
						WHERE employee_id = '$employee_id'";
			$result = $this->db->query($query) or die($this->db->error);
			if($this->db->num_rows($result) > 0){
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployeeEmployments($employee_id){
			$sql = "DELETE FROM employee_employments_tbl WHERE employee_id = '$employee_id'";
			$query = $this->db->query($sql) or die($this->db->error);
			if ($query) {
				return true;
			}
			else{return false;}
		}

		public function DeleteEmployment($employment_id){
			$sql = "DELETE FROM employee_employments_tbl WHERE employment_id = '$employment_id'";
			$query = $this->db->query($sql) or die($this->db->error);
			if ($query) {
				return true;
			}
			else{return false;}
		}
		
	}
?>