<?php 
	include_once("DB_conn.php");

    class Signatory {
        
        protected $db;
        private $SignatoryData = "";

        public function __construct() {
        	$this->db = new DB_conn();	
    	}

        function setSignatoryData($newValue){ $this->SignatoryData = $newValue; }

        public function SaveSignatory(){
            $signatory_data = $this->SignatoryData;
            $signatory_id = $this->db->escape_string(trim($signatory_data['signatory_id']));
            $employee_name = $this->db->escape_string(trim($signatory_data['employee_name']));
            $position_title = $this->db->escape_string(trim($signatory_data['position_title']));
            $role_type = $this->db->escape_string(trim($signatory_data['role_type']));
            $report_type = $this->db->escape_string(trim($signatory_data['report_code']));
            $dept_id = $this->db->escape_string(trim($signatory_data['dept_id']));
            $sign_order = $this->db->escape_string(trim($signatory_data['sign_order']));
            $sign_particulars = $this->db->escape_string(trim($signatory_data['sign_particulars']));
            $is_active = isset($signatory_data['is_active']) && $signatory_data['is_active'] == 'on' ? 1 : 0;

            //-------Note: Include checks if signatory already exists before saving-----------//
            $query = "SELECT * FROM signatories WHERE signatory_id = '$signatory_id'";
            $result = $this->db->query($query) or die($this->db->error);
            $count_row = $this->db->num_rows($result);

            if ($count_row == 0) {
                if (isset($signatory_data)) {
                    $sql = "INSERT INTO signatories
                            (full_name, position_title, role_type, report_type, dept_id, sign_order, sign_particulars, is_active, created_at) 
                            VALUES(
                                '".$employee_name."',
                                '".$position_title."',
                                '".$role_type."',
                                '".$report_type."',
                                '".$dept_id."',
                                '".$sign_order."',
                                '".$sign_particulars."',
                                '".$is_active."',
                                NOW()
                            );";
                    $query = $this->db->query($sql) or die($this->db->error);
                    if ($query) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            else {
                $row = $this->db->fetch_array($result);
                $signatory_id = $row['signatory_id'];
                return $this->UpdateSignatory($signatory_id);  // call update method if exists
            }
        }

        public function UpdateSignatory($signatory_id){
			$signatory_data = $this->SignatoryData;
            $signatory_id = $this->db->escape_string(trim($signatory_data['signatory_id']));
            $employee_name = $this->db->escape_string(trim($signatory_data['employee_name']));
            $position_title = $this->db->escape_string(trim($signatory_data['position_title']));
            $role_type = $this->db->escape_string(trim($signatory_data['role_type']));
            $report_type = $this->db->escape_string(trim($signatory_data['report_code']));
            $dept_id = $this->db->escape_string(trim($signatory_data['dept_id']));
            $sign_order = $this->db->escape_string(trim($signatory_data['sign_order']));
            $sign_particulars = $this->db->escape_string(trim($signatory_data['sign_particulars']));
            $is_active = isset($signatory_data['is_active']) && $signatory_data['is_active'] == 'on' ? 1 : 0;

			$sql = "UPDATE signatories 
						SET full_name='$employee_name',
                            position_title='$position_title', 
                            role_type='$role_type', 
                            report_type='$report_type',
                            dept_id='$dept_id',
                            sign_order='$sign_order',
                            sign_particulars='$sign_particulars',
                            is_active='$is_active', 
                            updated_at=NOW()
						WHERE signatory_id = '$signatory_id'";

			$query = $this->db->query($sql) or die($this->db->error);
			if($query){
			    return true;
			}
			else {return false;}
		}

        public function FetchAllSignatories(){
            $sql = "SELECT * FROM signatories a 
                    LEFT JOIN departments_tbl b ON a.dept_id = b.dept_id
                    ORDER BY a.signatory_id DESC";
            $result = $this->db->query($sql) or die($this->db->error);
            if($result){
                return $result;
            }
            else {
                return false;
            }
        }

        public function FetchActiveSignatoriesByReportType($report_type) {
            $query = "SELECT * FROM signatories 
                    WHERE report_type = '$report_type' 
                    AND is_active = 1 
                    AND sign_order != ''
                    ORDER BY sign_order ASC";
            $result = $this->db->query($query);
            
            $signatories = array();
            while($row = $result->fetch_assoc()) {
                $signatories[] = $row;
            }
            return $signatories;
        }

        public function GetSignatoryDetails($signatory_id){
            $sql = "SELECT * FROM signatories WHERE signatory_id = '$signatory_id'";
            $result = $this->db->query($sql) or die($this->db->error);
            $details = $this->db->fetch_array($result);
            if($details){
                return $details;
            }
            else {
                return false;
            }
        }

        public function checkSignatoryOrderExistsForReport($report_type, $sign_order, $exclude_id = null, $dept_id = null) {
            $sign_order = $this->db->escape_string($sign_order);
            $report_type = $this->db->escape_string($report_type);

            $sql = "SELECT COUNT(*) AS cnt FROM signatories 
                    WHERE sign_order = '{$sign_order}' 
                    AND report_type = '{$report_type}'
                    AND (dept_id = '{$dept_id}' OR dept_id IS NULL)";
            if ($exclude_id !== null) {
                $sql .= " AND signatory_id != " . intval($exclude_id);
            }

            $res = $this->db->query($sql);
            if ($res) {
                $row = $res->fetch_assoc();
                return intval($row['cnt']) > 0; // returns true if duplicate exists
            }
            return false;
        }

        public function DeleteSignatory($signatory_id){
            $sql = "DELETE FROM signatories WHERE signatory_id = '$signatory_id'";
            $query = $this->db->query($sql) or die($this->db->error);
            if($query){
                return true;
            }
            else {
                return false;
            }
        }

    }
    
?>