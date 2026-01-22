
<?php 
session_start();

include_once 'includes/class/PrivilegedUser.php';

if (isset($_POST['username']) && isset($_POST['password'])) { 

	$emailusername = $_POST['username'];
	$password = $_POST['password'];

		$user = new PrivilegedUser();
		$login = $user->checkPrivilege($emailusername, $password);

	    if($login = $user->checkPrivilege($emailusername, $password)){
	    	return true;
	    	//print_r($login);
	    }
	    else{
	    	if(strpos($emailusername, "@")===FALSE){
                      echo "<script language='javascript'>
                      			window.location='./index.php?login=failed&reason=wrong_username';
                      		</script>";
	        }
	        else{
	        		echo "<script language='javascript'>
	                     		window.location='./index.php?login=failed&reason=wrong_email';
                      		</script>";
	        }
	    }
}

?>
