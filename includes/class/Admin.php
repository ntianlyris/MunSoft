<?php 
	include_once '../includes/class/Employee.php';

	class Admin extends Employee{

        private $Status;

		public function __construct(){
            parent::__construct();
            $this->Status = "active";
    	}

        public function setAdminStatus($value){$this->Status = $value;}

    	public function getUserByMemberID($member_id){
    		$query = "SELECT * FROM members_tbl a
    				INNER JOIN users_tbl b
    				ON a.userID = b.userID
    				WHERE a.memberID = '$member_id' LIMIT 1";

    		$result = $this->db->query($query) or die($this->db->error);

    		$count_row = $this->db->num_rows($result);

    		if($count_row == 1){
    			return $row = $this->db->fetch_array($result);
    		}
    		else { return false; }
    	}

        public function AddAdminUser($employee_id){
            $userID = $this->getUserID();
            $employeeID = $employee_id;
            $status = $this->Status;

            if($this->isAdminExist($userID)){
                $sql = "UPDATE admins_tbl 
                        SET employee_id = '$employeeID',
                            status = '$status'
                        WHERE userID = '$userID';";

                $query = $this->db->query($sql) or die($this->db->error);

                if($query){
                    return true;
                }
                else {return false;}
            }
            else{
                $sql = "INSERT INTO admins_tbl (userID, employee_id, status)
                    VALUES ('".$userID."','".$employeeID."','".$status."');";

                $query = $this->db->query($sql) or die($this->db->error);

                if($query){
                    return true;
                }
                else{return false;}
            }   
        }

        private function isAdminExist($userID){

            $sql = "SELECT * FROM admins_tbl WHERE userID = '$userID';";

            $result = $this->db->query($sql) or die($this->db->error);

            $count_row = $this->db->num_rows($result);

            if($count_row > 1){
                return true;
            }
            else{return false;}
        }

        public function getAdminUsers(){
            $query = "SELECT * FROM admins_tbl a
                        INNER JOIN users_tbl b
                        ON a.userID = b.userID
                        LEFT JOIN employees_tbl c
                        ON a.employee_id = c.employee_id
                        WHERE a.status != 'removed'";

            $result = $this->db->query($query) or die($this->db->error);

            $count_row = $this->db->num_rows($result);

            if($count_row > 0){
                    
               while($user = $this->db->fetch_array($result)){
                    $users[] = $user;
                }
                return $users;
            }

            else{
                return false;
            }
        }

        public function getAdminUsersCount(){
            $query = "SELECT * FROM admins_tbl a
                        INNER JOIN users_tbl b
                        ON a.userID = b.userID
                        INNER JOIN members_tbl c
                        ON a.memberID = c.memberID
                        WHERE a.status != 'removed'";
            
            $result = $this->db->query($query) or die($this->db->error);
            
            $count_row = $this->db->num_rows($result);
            
            if($count_row > 0){     
                $admins = $count_row;
            }
            else{ $admins = 0; }

            return $admins;   
        }

        public function getRegisteredUsers(){
            $query = "SELECT * FROM users_tbl a
                    INNER JOIN user_role_tbl b
                    ON a.userID = b.userID
                    WHERE b.roleID = 0";
            
            $result = $this->db->query($query) or die($this->db->error);
            
            $count_row = $this->db->num_rows($result);
            
            if($count_row > 0){
                while($user = $this->db->fetch_array($result)){
                    $users[] = $user;
                }
                return $users;
            }
            else{return false;} 
        }

        public function getNonAdminApprovedMembers(){ 
            $sql = "SELECT a.memberID, a.firstname, a.middlename, a.lastname
                    FROM members_tbl a
                    LEFT JOIN admins_tbl b
                    ON a.memberID = b.memberID
                    WHERE a.membership = 'Standard' AND b.memberID IS NULL;";

            $result = $this->db->query($sql) or die($this->db->error);
            
            $count_row = $this->db->num_rows($result);

            if($count_row > 0){
                while ($row = $this->db->fetch_array($result)) {
                    $non_admins[] = $row;
                }
                return $non_admins;
            }
            else{return false;}
        }

        public function getAdminInfo($user_id){

            $query = "SELECT * FROM members_tbl a
                        INNER JOIN address_tbl b
                        ON a.memberID = b.memberID
                        INNER JOIN admins_tbl c
                        ON b.memberID = c.memberID
                        INNER JOIN users_tbl d
                        ON c.userID = d.userID
                        WHERE d.userID = '$user_id';";

            $result = $this->db->query($query) or die($this->db->error);

            $count_row = $this->db->num_rows($result);

            if($count_row == 1){
                 return $row = $this->db->fetch_array($result);
            }
            else{
                return false;
            }
        }

        public function getAdminInfoByAdminID($admin_id){

            $query = "SELECT * FROM admins_tbl a
                        INNER JOIN users_tbl b
                        ON a.userID = b.userID
                        INNER JOIN employees_tbl c
                        ON a.employee_id = c.employee_id
                        WHERE a.adminID = '$admin_id'
                        AND a.status != 'removed';";

            $result = $this->db->query($query) or die($this->db->error);

            $count_row = $this->db->num_rows($result);

            if($count_row == 1){
                 return $row = $this->db->fetch_array($result);
            }
            else{
                return false;
            }
        }

        public function RemoveAdminUser($admin_id){
            $user_id = '';
            if($admin = $this->getAdminInfoByAdminID($admin_id)){
                $user_id = $admin['userID'];
            }

            $sql = "DELETE FROM admins_tbl WHERE adminID = '$admin_id'";
            //$sql = "UPDATE admins_tbl SET status = 'removed' WHERE adminID = '$admin_id';";
            $query = $this->db->query($sql) or die($this->db->error);

            if($query){
                include_once 'Role.php';
                if(Role::deleteUserRoles($user_id)){
                    // Delete user account from users_tbl
                    $sql_user = "DELETE FROM users_tbl WHERE userID = '$user_id'";
                    $query_user = $this->db->query($sql_user) or die($this->db->error);
                    
                    if($query_user){
                        return true;
                    }
                    else{return false;}
                }
                else{return false;}
            }
            else{return false;}
        }

        public function isSystemDeveloper($user_id){
            $query = "SELECT * FROM users_tbl a
                        INNER JOIN user_role_tbl b
                        ON a.userID = b.userID
                        INNER JOIN roles_tbl c
                        ON b.roleID = c.roleID
                        WHERE a.userID = '$user_id' 
                        AND c.roleName = 'SysDeveloper'
                        LIMIT 1";

            $result = $this->db->query($query) or die($this->db->error);

            $count_row = $this->db->num_rows($result);

            if($count_row == 1){
                 return true;
            }
            else{
                return false;
            }
        }


	}

	




?>