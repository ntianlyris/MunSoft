<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HRMS | Login</title>

  <link rel="icon" href="includes/images/polanco_logo.png" type="image/png" />
	<link rel="shortcut icon" href="includes/images/polanco_logo.png" type="image/png" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- My style -->
  <link rel="stylesheet" href="css/my_style.css">
  <style type="text/css">
        body {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: auto;
            text-align: center;
            background-image: linear-gradient( rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5) ), url('./includes/images/bg3.jpg');
            background-position: center;
            background-attachment: fixed;
            background-size: cover;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
        }

        .pol_logo {
          position: relative;
          top:-28px;
        }

        .ms_logo {
          position: relative;
          top:-20px;
        }
  </style>
  <!-- Sweetalert -->
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">
  <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="dist/js/urlcheck.js"></script>
</head>
<body class="hold-transition login-page">
  <div class="row">
    <div class="pol_logo"><img src="includes/images/polanco_logo.png" alt="polanco logo" width="128" height="128"></div>
  </div>

<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a class="h1"><b>HR</b>MS</a>
    </div>
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="login.php" method="post" name="login" action="login.php">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" id="inputUsername" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="row col-sm-12" style="display:absolute; top:-15px; margin-left:5px;">
            <small><span class="control-label" id="username-help"></span></small>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row col-sm-12" style="display:absolute; top:-15px; margin-left:5px;">
            <small><span class="control-label" id="password-help"></span></small>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-12">
            <button type="button" class="btn btn-primary btn-block btn-flat" onclick="return(submitlogin());">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mb-0" style="padding-top:10px;">
        <a href="register_form.php" class="text-center">Register a new account</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script type="text/javascript">

function submitlogin() {
  var form = document.login;
  if (form.username.value == null || form.username.value == "") {          
            $("#inputUsername").addClass("is-invalid");
            $('#username-help').html("Please provide your registered username.").addClass("text-danger");
            form.username.focus();
            return false;
  } 
  else if (form.password.value == null || form.password.value == "") {
            $("#inputPassword").addClass("is-invalid");
            $('#password-help').html("Password should not be empty.").addClass("text-danger");
            form.password.focus();
            return false;
  }
  else{
    form.submit();
  }
}

$( document ).ready(function(){
    var form = document.login;
    var query = getQuery();
    if (query !== undefined && query.login == 'failed') {
      
        if(query.reason == 'wrong_username'){
            $("#inputUsername").addClass("is-invalid");
            $('#username-help').html('You have entered an invalid username. Please try again.').addClass("text-danger");
            form.username.focus();
        }
        if(query.reason == 'wrong_password'){
            var user = query.user;
            $("#inputUsername").val(query.user);
            $("#inputPassword").addClass("is-invalid");
            $('#password-help').html('You have entered a wrong password. Please try again.').addClass("text-danger");
            form.password.focus();
        }
        if(query.reason == 'not_privuser'){
            Swal.fire({
                title: 'Error',
                text: 'Access Denied!',
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ok'
                }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = './';
                    }
            });
        }

    } 
       
});
</script>

<?php include_once 'includes/layout/mainfooter.php'; ?>
