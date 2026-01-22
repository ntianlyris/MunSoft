<?php 
	include_once("DB_conn.php");
	
	class User{
		
		protected $db;
		private $UserID = "";
		private $UserName = "";
		private $Password = "";
		private $Email = "";
		private $Mobile = "";
		private $Email_Username = "";
		private $Status = "";
		private $Role = null;

		public function __construct(){
			$this->db = new DB_conn();
			$this->Status = "pending";
			$this->Role = "";
		}

		function setUserID($newValue){ $this->UserID=$newValue; }
		function setUserName($newValue){ $this->UserName=$newValue; }
		function setPassword($newValue){ $this->Password=$newValue; }
		function setEmail($newValue){ $this->Email=$newValue; }
		function setMobile($newValue){ $this->Mobile=$newValue; }
		function setEmail_Username($newValue){ $this->Email_Username=$newValue; }
		function setRole($newValue){ $this->Role=$newValue; }

		function getUserID(){ return $this->UserID; }
		function getUserName(){ return $this->UserName; }
		function getPassword(){ return $this->Password; }
		function getEmail(){ return $this->Email; }
		function getMobile(){ return $this->Mobile; }
		function getEmail_Username(){ return $this->Email_Username; }
		function getStatus(){ return $this->Status; }

		
/////********** for registration process ********************************/////
		
	public function reg_user(){
			
		$username = $this->sanitize($this->UserName);
		$email = $this->sanitize($this->Email);
		$mobile = $this->sanitize($this->Mobile);
		$password = $this->sanitize($this->Password);
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$role_id = $this->Role;

		$emailusername = array($username, $email);
		$new_user = false;

		if($this->isUserNotExist($username) && $this->isUserNotExist($email)){
			$query = "INSERT INTO users_tbl (username, password, email, mobile) 
									VALUES('".$username."','".$hash."','".$email."','".$mobile."');";
							
			if($result = $this->db->query($query) or die($this->db->error)){

				$user_id = $this->db->last_id();

				include_once 'Role.php';

				if($username == "superuser"){
					$role_id = 1;
				}
				if(Role::AddUserRoles($user_id, $role_id)){
					return true;
				}
				else{return false;}
				
			}
			else {return false;}

		}
		else{
			return false;
		}		
	}

	//method to check if username or email already existing in db
	private function isUserNotExist($emailusername){

			//$query = "SELECT username,email FROM users_tbl WHERE username='$emailusername' OR email='$emailusername'";		//avoid duplicate usernames and email
			$query = "SELECT username FROM users_tbl WHERE username='$emailusername'";			//avoid duplicate usernames
			$result = $this->db->query($query) or die($this->db->error);

			if ($this->db->num_rows($result) > 0) {
		        // output data of each row
		        $row = $this->db->fetch_array($result);

		        if ($emailusername == $row['username']){
		            header("Location: ./register_form.php?reg=failed&reason=username_exist");
					return false;
		        }
		        return false;
			}
			else{
				return true;
			}

	}	

			
/////******************* for login process *****************************************/////
	
	public function check_login(){

		$emailusername = stripslashes($this->Email_Username);
		$password = stripslashes($this->Password);

		$emailusername = $this->sanitize($emailusername);
		$password = $this->sanitize($password);

			if ($this->getUserDetails($emailusername)) {

						if($this->check_pass($password)){
							$_SESSION['login'] = true;
							$_SESSION['emailusername'] = $emailusername;
							$_SESSION['uid'] = $this->getUserID();
							if($this->RedirectUsersByRole($this->Role)){
								return true;
							}
							else{
								return false;
							}	
						}
						else{
							header("Location: ./index.php?login=failed&reason=wrong_password&user=$emailusername");
							return false;
						}
			}
			else{ return false; }	     
	}

	public function android_login($emailusername, $password){
		if ($this->getUserDetails($emailusername) && $this->check_pass($password)) {
			$query = "SELECT * from users_tbl 
    			  WHERE email='$emailusername'
    			  OR username='$emailusername'";

	    	$result = $this->db->query($query) or die($this->db->error);

			$count_row = $this->db->num_rows($result);

			if ($count_row == 1) {
				return $user_data = $this->db->fetch_array($result);
			}
			else{return false;}
		}
		else{return false;}
	}

	public function RedirectUsersByRole($role){
		$temp = false;
			if($role == 1 || $role == 2){
				header("Location: ./admin");
				$temp = true;
			}
			elseif ($role == 3) {
				header("Location: ./employee");
				$temp = true;
			}
			elseif ($role == 4) {
				header("Location: ./hris");
				$temp = true;
			}
			elseif ($role == 5) {
				header("Location: ./payroll");
				$temp = true;
			}
		return $temp;
	}


    //Method for verifying hash password
	protected function check_pass($password){
		$hash_pass = $this->Password;
			
		return password_verify($password, $hash_pass);	
	}

	// Method for sanitizing user input
    protected function sanitize($input){
       	$sanitizedInput = $this->db->escape_string(trim($input));
        return $sanitizedInput;
    }

    // check if logged in
	public function isLoggedIn(){
		if(isset($_SESSION['login']) && $_SESSION['login'] != ''){
			
			return true;
		}
		else {return false;}	
	}
	
	/*** check session uid ***/
	public function getSessionUID(){
	    if(isset($_SESSION['uid']) && ($_SESSION['uid']!='')){
	    	return $_SESSION['uid'];
	    }
	    else{return false;}
	}

/////******************* for logout process *****************************************/////

	public function logout() {

			// Unset all of the session variables.
			$_SESSION = array();

			// If it's desired to kill the session, also delete the session cookie.
			// Note: This will destroy the session, and not just the session data!
			if (ini_get("session.use_cookies")) {
			    $params = session_get_cookie_params();
			    setcookie(session_name(), '', time() - 42000,
			        $params["path"], $params["domain"],
			        $params["secure"], $params["httponly"]
			    );
			}

			// Finally, destroy the session.
			session_destroy();
			header("location:./");
			exit();
	    }
	
	
/////***************Fetching data for all users**********************************/////

    //fetch user info from DB
    public function getUserDetails($emailusername){
    	
    	$query = "SELECT * from users_tbl 
    			  WHERE email='$emailusername'
    			  OR username='$emailusername'";

    	$result = $this->db->query($query) or die($this->db->error);

		$user_data = $this->db->fetch_array($result);
		$count_row = $this->db->num_rows($result);

		if ($count_row == 1) {
			$this->UserID = $user_data['userID'];
			$this->UserName = $user_data['username'];
			$this->Password = $user_data['password'];
			$this->Email = $user_data['email'];
			$this->Mobile = $user_data['mobile'];
		
			return true;
		}
		else{return false;}

    }

    public function getUserInfo(){
    	$user_id = $this->UserID;

    	$query = "SELECT * from users_tbl 
    			  WHERE userID = '$user_id'";

    	$result = $this->db->query($query) or die($this->db->error);
    	if($this->db->num_rows($result) == 1){
    		$row = $this->db->fetch_array($result);
    		return $row;
    	}
    	else { return false; }

    }
	

	public function getRegisteredUsers(){
		$query = "SELECT a.userID, a.username, a.email, a.mobile, b.application_status 
					FROM users_tbl a
					LEFT JOIN members_tbl b
					ON a.userID = b.userID
					INNER JOIN user_role_tbl c
					ON a.userID = c.userID
					WHERE c.roleID = 1 AND b.application_status IS NULL
					OR b.application_status IS NOT NULL";
		
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

	

}

?>