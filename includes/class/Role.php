<?php

include_once("DB_conn.php");

class Role
{
    protected $permissions;
    private $db;

    public function __construct() {
        $this->permissions = array();
        $this->db = new DB_conn();
    }

    public function setPermissions($value){ $this->permissions = $value; }

    public static function getRolePerms($roleID) {
        $role = new Role();
        $sql = "SELECT b.perm_desc FROM role_perm_tbl a
                JOIN permissions_tbl b 
                ON a.perm_id = b.perm_id
                WHERE a.roleID = '$roleID'";
        
        $result = $role->db->query($sql) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            while($row = $role->db->fetch_array($result)) {
                $perms[] = $row['perm_desc'];
            }
            return $perms;
        }
        else{return false;}     
    }

    // return a perm id with associated permissions
    public static function getRolePermID($roleID) {
        $role = new Role();
        $sql = "SELECT b.perm_id FROM role_perm_tbl a
                JOIN permissions_tbl b 
                ON a.perm_id = b.perm_id
                WHERE a.roleID = '$roleID'";
        
        $result = $role->db->query($sql) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            while($row = $role->db->fetch_array($result)) {
                $perms[] = $row['perm_id'];
            }
            return $perms;
        }
        else{return false;}     
    }

    public static function getPerms(){
        $role = new Role();
        $sql = "SELECT * FROM permissions_tbl";

        $result = $role->db->query($sql) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            while($row = $role->db->fetch_array($result)) {
                $perms[] = $row;
            }
            return $perms;
        }
        else{return false;}        
    }

    public static function getPermID($perm_id){
        $role = new Role();
        $sql = "SELECT perm_id FROM permissions_tbl WHERE perm_id = '$perm_id'";

        $result = $role->db->query($sql) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            while ($row = $role->db->fetch_array($result)) {
                $perms = $row['perm_id'];
            }   
            return $perms;    
        }
        else{return false;}        
    }

    public static function getRoles(){
        $role = new Role();
        $sql = "SELECT * FROM roles_tbl WHERE roleID >= 1";

        $result = $role->db->query($sql) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            while($row = $role->db->fetch_array($result)) {
                $roles[] = $row;
            }
            return $roles;
        }
        else{return false;}        
    }

    // insert array of roles for specified user id
    public static function AddUserRoles($user_id, $role_id) {
        $role = new Role();

        $query = "SELECT * FROM user_role_tbl WHERE userID = '$user_id';";

        $result = $role->db->query($query) or die($role->db->error);

        $row = $role->db->fetch_array($result);

        $count_row = $role->db->num_rows($result);

        if($count_row == 1 && $row['roleID'] == 1){
            if($role->UpdateUserRole($user_id, $role_id)){
                return true;
            }
            else{return false;}
        }
        else{    
            $sql = "INSERT INTO user_role_tbl (userID, roleID) VALUES ('$user_id', '$role_id')";

            if($result = $role->db->query($sql) or die($role->db->error)){
                return true;
            }
            else {return false;}    
        }          
    }

    // update roles for specified user id
    public function UpdateUserRole($user_id, $role_id) {
        $role = new Role();

            $sql = "UPDATE user_role_tbl
                        SET roleID = '".$role_id."'
                        WHERE userID ='".$user_id."'";

            if($result = $role->db->query($sql) or die($role->db->error)){
                return true;
            }
            else {return false;}
            
    } 

    // insert a new role
    public static function insertRole($role_name) {
        $role = new Role();

        $query = "SELECT * FROM roles_tbl WHERE roleName = '$role_name'";

        $result = $role->db->query($query) or die($role->db->error);

        $count_row = $role->db->num_rows($result);

        if($count_row > 0){
            $row = $role->db->fetch_array($result);
            return $row['roleID'];
        }
        else{
            $sql = "INSERT INTO roles_tbl (roleName) VALUES ('$role_name')";

            $query = $role->db->query($sql) or die($role->db->error);
            if($query){
                return $role->db->last_id();
            }
            else{return false;}
        }

    }

    public function AddRolePerms($user_role_id){

        $permissions = $this->permissions;

        $arrlength=count($permissions);

        for($i=0;$i<$arrlength;$i++){

            $sql = "INSERT INTO role_perm_tbl (user_role_id, perm_id)
                    VALUES ('".$user_role_id."', '".$permissions[$i]."');";

            $query = $this->db->query($sql) or die($this->db->error);
             
          }
          return true;
        
    }

    // delete ALL roles for specified user id
    public static function deleteUserRoles($user_id) {
        $role = new Role();
        $sql = "DELETE FROM user_role_tbl WHERE userID = '$user_id'";
        
        $query = $role->db->query($sql) or die($role->db->error);

        if($query){
            return true;
        }
        else{return false;}

    }

     // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }
}