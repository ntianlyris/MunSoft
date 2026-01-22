<?php 

	if($action = isset($_GET['action'])?$_GET['action']:''){
		
		switch ($action) {
			case 'add':

					if($Data = isset($_POST['Data'])?$_POST['Data']:''){

						include_once('../includes/class/Admin.php');
						$UserAdmin = new Admin();

						$Data = explode("|",$Data);

						$user_id = $Data[0];
						$employee_id = $Data[1];
						$roles = $Data[2];

						//new selected roles for user
						$added_roles = explode(",",$roles);


						//get existing roles for user
						if($curr_roles = $UserAdmin->getUserRoles($user_id)){
							$user_roles = array();
							foreach ($curr_roles as $value) {
								$user_roles[] = $value['roleID'];
							}
							//current existing roles of user
							$curr_roles = $user_roles;

							$new_roles = array_diff($added_roles, $curr_roles);

							foreach ($new_roles as $value) {
								include_once('../includes/class/Role.php');
								Role::AddUserRoles($user_id, $value);		
							}
						
							$UserAdmin->setEmployeeID($employee_id);
							$UserAdmin->setUserID($user_id);
										
							if($UserAdmin->AddAdminUser()){
								echo "System User was added successfully.";
							}
							else{ echo "ERROR: System User failed to be added."; }	

						}	
						/**/
					}

					
				break;
			
			case 'get':

					include_once('../includes/class/Admin.php');
					$SysUser = new Admin();

					$Data =  isset($_GET["Data"])?$_GET["Data"]:"" ;
					
					$jsonData = '{"User":{"UserID":"x-x", "Username":"", "MemberID":"", "Name":"", "Position":""}}';

						if($user = $SysUser->getAdminInfo($Data)){
							
								$jsonData = '{"User":{	"UserID":"'. $user['userID'].'", 
													"Username":"'. $user['username'].'",
													"MemberID":"'. $user['memberID'].'",
												 	"Name":"'.$user['firstname'].' '.$user['middlename'].' '.$user['lastname'].' '.'",
												 	"Position":"'.$user['position'].'"
												}
										 }';
							
							 
						}
						
					echo $jsonData;
				break;

			case 'delete':

					include_once('../includes/class/Admin.php');
					$DeletedUser = new Admin();

					$Data =  isset($_GET["Data"])?$_GET["Data"]:"" ;

					if($DeletedUser->RemoveAdminUser($Data)){
						echo "You have successfully remove User.";
					}
					else{echo "Failed to remove User.";}

					
				break;
		}
	}

	

?>