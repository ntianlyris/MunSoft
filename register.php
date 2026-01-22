<?php 
include_once 'includes/class/User.php';
$user = new User();

    if (isset($_POST['register'])){
        $username = $_POST['username'];
        $mobile = $_POST['mobile'];
        $password = $_POST['password'];

        $user->setUserName($username);
        $user->setMobile($mobile);			
        $user->setPassword($password);		
        
        if ($register = $user->reg_user()){ 
             // Registration Success
            echo "<script language='javascript'>
                            window.location='./register_form.php?reg=success';
                    </script>";    
        }
        else{
            echo "<script language='javascript'>
                            window.location='./register_form.php?reg=failed';
                    </script>"; 
        }   
    }
    
?>
