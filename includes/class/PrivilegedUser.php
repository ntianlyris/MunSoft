<?php
include_once 'User.php';
include_once 'Role.php';

class PrivilegedUser extends User
{
    private $roles;
  
    public function __construct() {
        parent::__construct();
    }

    // check privilege
    public function checkPrivilege($emailusername, $password) {
        $query = "SELECT userID from users_tbl
                  WHERE email='$emailusername' 
                  OR username='$emailusername'";

                        $result = $this->db->query($query) or die($this->db->error);
                        $user_data = $this->db->fetch_array($result);
                        
                        $count_row = $this->db->num_rows($result);

                        if ($count_row == 1) {

                            $privUser = new PrivilegedUser();
                           
                            $privUser->setUserID($user_data['userID']);
                            $user_id = $privUser->getUserID();
                            //verify if role exist in db
                            if($role = $privUser->getRole($user_id)){ 
                                
                                $privUser->setEmail_Username($emailusername);
                                $privUser->setPassword($password);
                                $privUser->setRole($role['roleID']);
                                if($privUser->check_login()){
                                    return true;
                                }
                                else{ return false; }
                                
                            }
                            else{ 
                                header("Location: ./index.php?login=failed&reason=not_privuser");
                                return false; 
                            }
                       }
                        else{ return false; }            
    }

  // get role
    public function getRole($user_id) {
        $this->roles = array();
        $sql = "SELECT a.roleID, b.roleName 
                FROM user_role_tbl a
                JOIN roles_tbl b ON a.roleID = b.roleID
                WHERE a.userID = '$user_id'";
        
        $result = $this->db->query($sql) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        if($count_row > 0){
            return $row = $this->db->fetch_array($result);
        }
        else{return false;}

    }

    // populate roles with their associated permissions
    public function initRoles($user_id) {
        $this->roles = array();
        $sql = "SELECT a.roleID, b.roleName 
                FROM user_role_tbl a
                JOIN roles_tbl b ON a.roleID = b.roleID
                WHERE a.userID = '$user_id'";
        
        $result = $this->db->query($sql) or die($this->db->error);
        $count_row = $this->db->num_rows($result);
        
        while($row = $this->db->fetch_array($result)) {
            $this->roles[$row["roleName"]] = Role::getRolePerms($row["roleID"]);
        }
        return $this->roles;
    }

    public function getUserRoles($user_id){
        $query = "SELECT * FROM roles_tbl a 
                    INNER JOIN user_role_tbl b
                    ON a.roleID = b.roleID
                    WHERE b.userID = '$user_id';";

        $result = $this->db->query($query) or die($this->db->error);

        $count_row = $this->db->num_rows($result);

        if($count_row > 0){

            while($row = $this->db->fetch_array($result)){
                $roles[] = $row;
            }
            return $roles;

        }
        else {return false;}
    }

    // insert a new role permission association
    public static function insertPerm($role_id, $perm_id) {
        $privUser = new PrivilegedUser();
        $role_id = $privUser->db->escape_string($role_id);
        $perm_id = $privUser->db->escape_string($perm_id);
        $sql = "INSERT INTO role_perm_tbl (roleID, perm_id) VALUES ('".$role_id."', '".$perm_id."')";
        $query = $privUser->db->query($sql) or die($privUser->db->error);
        if($query){
            return true;
        }
        else{return false;}
    }

    // check if a user has a specific role
    public function hasRole($role_name) {
        return isset($this->roles[$role_name]);
    }

    // check if user has a specific privilege
    public function hasPrivilege($perm) {
        foreach ($this->roles as $rolePerms) {
            if (is_array($rolePerms)) {
                // Master Bypass: 'Manage System' grants all permissions
                if (in_array("Manage System", $rolePerms)) {
                    return true;
                }
                if (in_array($perm, $rolePerms)) {
                    return true;
                }
            }
        }
        return false;
    }
}





?>