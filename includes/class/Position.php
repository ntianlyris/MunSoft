<?php 
	include_once("DB_conn.php");

    class Position {
        
        protected $db;
        private $PositionItemData = "";

        public function __construct() {
        	$this->db = new DB_conn();	
    	}

        function setPositionItemData($newValue){ $this->PositionItemData = $newValue; }

        public function returnPositionStatus($status_code){
            $position_status = "";
            switch ($status_code) {
            case '0':
                $position_status = "vacant";
                break;
            case '1':
                $position_status = "filled";
                break;
            case '2':
                $position_status = "unfunded";
                break;
           }
           return $position_status;
        }

        public function returnPositionType($type_code){
            $position_type = "";
            switch ($type_code) {
            case '0':
                $position_type = "Permanent";
                break;
            case '1':
                $position_type = "Temporary";
                break;
            case '2':
                $position_type = "Casual";
                break;
           }
           return $position_type;
        }

        public function SavePositionItem(){
            $position_data = $this->PositionItemData;
            $position_id = $this->db->escape_string(trim($position_data['position_id']));
            $position_refnum = $this->db->escape_string(trim($position_data['position_refnum']));
			$position_itemnum = $this->db->escape_string(trim($position_data['position_itemnum']));
			$position_title = $this->db->escape_string(trim($position_data['position_title']));
            $salary_grade = $this->db->escape_string(trim($position_data['salary_grade']));
            $position_type = $this->db->escape_string(trim($position_data['position_type']));
            $dept_id = $this->db->escape_string(trim($position_data['dept_id']));
            $position_status = $this->db->escape_string(trim($position_data['position_status']));

            //-------Note: Include checks if item already exist before saving-----------//
            $query = "SELECT * FROM positions_tbl WHERE position_id = '$position_id'";
            $result = $this->db->query($query) or die($this->db->error);
            $count_row = $this->db->num_rows($result);
            if ($count_row == 0) {
                if (isset($position_data)) {
                    $sql = "INSERT INTO positions_tbl (position_refnum, position_itemnum,position_title,salary_grade, position_type, dept_id, position_status) 
                        VALUES('".$position_refnum."','".$position_itemnum."','".$position_title."','".$salary_grade."','".$position_type."','".$dept_id."','".$position_status."');";
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
                $row = $this->db->fetch_array($result);
                $position_id = $row['position_id'];
				return $this->UpdatePosition($position_id);		//update
			}
        }

        public function UpdatePosition($position_id){
			$position_data = $this->PositionItemData;
            $position_refnum = $this->db->escape_string(trim($position_data['position_refnum']));
			$position_itemnum = $this->db->escape_string(trim($position_data['position_itemnum']));
			$position_title = $this->db->escape_string(trim($position_data['position_title']));
            $salary_grade = $this->db->escape_string(trim($position_data['salary_grade']));
            $position_type = $this->db->escape_string(trim($position_data['position_type']));
            $dept_id = $this->db->escape_string(trim($position_data['dept_id']));
            $position_status = $this->db->escape_string(trim($position_data['position_status']));

			$sql = "UPDATE positions_tbl 
						SET position_refnum='$position_refnum',
                            position_itemnum='$position_itemnum', 
                            position_title='$position_title', 
                            salary_grade='$salary_grade',
                            position_type='$position_type',
                            dept_id='$dept_id', 
                            position_status='$position_status'
						WHERE position_id = '$position_id'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
			    return true;
			}
			else {return false;}
		}

        ///---For Filling and vacating position ---///
        public function UpdatePositionStatus($position_id,$status){
			$position_status = $status;

			$sql = "UPDATE positions_tbl 
						SET position_status='$position_status'
						WHERE position_id = '$position_id'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
			    return true;
			}
			else {return false;}
		}

        public function GetPositions(){
			$query = "SELECT * FROM positions_tbl a
                        INNER JOIN departments_tbl b
                        ON a.dept_id = b.dept_id
                        ORDER BY position_refnum ASC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$positions[] = $row;		
				}
				return $positions;
			}
			else{return false;}
		}

        public function GetVacantPositions(){
			$query = "SELECT * FROM positions_tbl a
                        INNER JOIN departments_tbl b
                        ON a.dept_id = b.dept_id
                        WHERE position_status = 0
                        ORDER BY position_id ASC";

			$result = $this->db->query($query) or die($this->db->error);
			
			$count_row = $this->db->num_rows($result);
			if($count_row>0){
				while($row = $this->db->fetch_array($result)){
					$positions[] = $row;		
				}
				return $positions;
			}
			else{return false;}
		}

        public function GetPositionDetails($position_id){
			$query = "SELECT * FROM positions_tbl WHERE position_id = '$position_id'";
			$result = $this->db->query($query) or die($this->db->error);
			$count_row = $this->db->num_rows($result);
			if($count_row==1){
				return $row = $this->db->fetch_array($result);
			}
			else{return false;}
		}

        public function isPositionCurrentlyFilled($position_id){
            $query = "SELECT a.position_id, a.position_status, b.employment_status
                FROM positions_tbl a
                INNER JOIN employee_employments_tbl b ON a.position_id = b.position_id
                WHERE a.position_id = '$position_id'
                  AND a.position_status = 1
                  AND b.employment_status = 1";
            $result = $this->db->query($query) or die($this->db->error);
            if($this->db->num_rows($result) > 0){
                return true;
            }
            else{
                return false;
            }
        }

        public function DeletePosition($position_id){
			$sql = "DELETE FROM positions_tbl WHERE position_id = '$position_id'";
			$query = $this->db->query($sql) or die($this->db->error);
			if ($query) {
				return true;
			}
			else{return false;}
		}
    }
    
?>