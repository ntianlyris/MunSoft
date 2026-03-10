<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IntelliGov Nexus | Register</title>

  <link rel="icon" href="includes/images/polanco_logo.png" type="image/png" />
	<link rel="shortcut icon" href="includes/images/coin.png" type="image/png" />

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
            background-image: linear-gradient( rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5) ), url('./includes/images/background_1.png');
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
<body class="hold-transition register-page">
  <div class="row">
    <div class="pol_logo"><img src="includes/images/polanco_logo.png" alt="polanco logo" width="128" height="128"></div>
  </div>
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a class="h1"><b>INTELLIGOV</b> NEXUS</a><br>
      <span style="font-size: 1rem; color: #6c757d; font-style: italic;">Smart Governance. Seamless Operations.</span>
    </div>
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register</p>

      <form action="register.php" method="post" name="register" action="register.php" onsubmit="return(validateForm());">
        <div class="input-group mb-3">
          <input type="text" class="form-control inputs" name="username" id="inputUsername" placeholder="Desired username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="row col-sm-12" style="display:absolute; top:-15px; margin-left:5px;">
            <small><span class="control-label input-help" id="username-help"></span></small>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control inputs" name="mobile" id="inputMobile" placeholder="Mobile No.">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-phone"></span>
            </div>
          </div>
        </div>
        <div class="row col-sm-12" style="display:absolute; top:-15px; margin-left:5px;">
            <small><span class="control-label input-help" id="mobile-help"></span></small>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control inputs" name="password" id="inputPassword" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row col-sm-12" style="display:absolute; top:-15px; margin-left:5px;">
            <small><span class="control-label input-help" id="password-help"></span></small>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" name="register" value="register" class="btn btn-primary btn-block btn-flat">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <p class="mb-0" style="padding-top:10px;">
        <a href="./" class="text-center">I already have an account</a>
      </p>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script language="javascript">
   $(document).ready(function () {
    var form = document.register;
        var query = getQuery();
        if (query !== undefined && query.reg == 'failed') {
        
            if(query.reason == 'username_exist'){
                $("#inputUsername").addClass("is-invalid");
                $('#username-help').html("Username already taken. Please choose another one.").addClass("text-danger");
                form.username.focus();
                return false;
            }
        }
        else if(query !== undefined && query.reg == 'success'){
            Swal.fire({
                title: 'Success',
                text: 'Refer to the system administrator to grant you access.',
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ok'
                }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = './';
                    }
            });
        }

        $('#inputMobile').keyup(function () { 
        var mobileText = this.value;
        var temp = false;
        //var numbers = /^[0-9]+$/;  
        var numbers = /^\d*(\.\d{0,2})?$/;
            if (mobileText == "") {
                $("#inputMobile").removeClass("is-invalid");
                $('#mobile-help').empty();
            }
            else{
                if(mobileText.match(numbers)){
                    temp = true;
                    $("#inputMobile").removeClass("is-invalid");
                    $('#mobile-help').empty();
                }
                else{
                    $("#inputMobile").addClass("is-invalid");
                    $('#mobile-help').html("Mobile must contain numeric characters only.").addClass("text-red");
                    temp = false;
                }
            }
            return temp;
       });

       $('#inputUsername').focus(function () { 
            $("#inputUsername").removeClass("is-invalid");
       });

       $('#inputUsername').keyup(function () { 
        var userText = this.value;
        var temp = false;
        var anum = /^[0-9a-zA-Z]+$/;
            if (userText == "") {
                $("#inputUsername").removeClass("is-invalid");
                $('#username-help').empty();
            }
            else{
                if(userText.match(anum)){
                    temp = true;
                    $("#inputUsername").removeClass("is-invalid");
                    $('#username-help').empty();
                }
                else{
                    $("#inputUsername").addClass("is-invalid");
                    $('#username-help').html("Username must contain alphanumeric characters only.").addClass("text-red");
                    temp = false;
                }
            }
            return temp;
       });

       $('#inputPassword').keyup(function () { 
        var userPass = this.value;
        var temp = false;
        var anum = /^[0-9a-zA-Z]+$/;
            if (userPass == "") {
                $("#inputPassword").removeClass("is-invalid");
                $('#password-help').empty();
            }
            else{
                if(userPass.match(anum) || userPass.lenght < 6){
                    temp = true;
                    $("#inputPassword").removeClass("is-invalid");
                    $('#password-help').empty();
                }
                else{
                    $("#inputPassword").addClass("is-invalid");
                    $('#password-help').html("Password be 11 alphanumeric characters.").addClass("text-red");
                    temp = false;
                }
            }
            return temp;
       });
   });

   function validateForm(){
            var form = document.register;
            var inputs, index;
            var userText = form.username;
            var mobileText = form.mobile;
            var password = form.password;

            var anum = /^[0-9a-zA-Z]+$/;
            var phoneno = /^\d{11}$/;

            inputs = form.getElementsByTagName('input');

            for (index = 0; index < inputs.length; ++index) {
                // deal with inputs[index] element.
                var inputs_count = 0;
                if (inputs[index].value==null || inputs[index].value==""){
                    console.log(inputs[index].value);
                    inputs_count++;
                    $(".inputs").addClass("is-invalid");
                    $('.input-help').html("Fill out this field.").addClass("text-red");
                    return false;
                }
                else {
                    if(userText.value.match(anum)){
                        if(mobileText.value.match(phoneno)){
                            if(password.value.match(anum)){
                                if(password.value==null || password.value=="" || password.value.length < 6){
                                        $("#inputPassword").addClass("is-invalid");
                                        $('#password-help').html("Password must be at least 6 characters long.").addClass("text-red");
                                        form.password.focus();
                                        return false;
                                }
                                else{
                                        return true;
                                }
                            }
                            else{
                                $("#inputPassword").addClass("is-invalid");
                                $('#password-help').html("Password must be alphanumeric characters only.").addClass("text-red");
                                form.password.value = "";
                                form.password.focus();
                                return false;
                            }
                        }
                        else{
                            $("#inputMobile").addClass("is-invalid");
                            $('#mobile-help').html("Mobile must be 11 numeric characters only.").addClass("text-red");
                            form.mobile.focus();
                            return false;
                        }   
                        
                     
                    }
                    else {
                        $("#inputUsername").addClass("is-invalid");
                        $('#username-help').html("Username must contain alphanumeric characters only.").addClass("text-red");
                        form.username.focus();
                        return false;   
                    }
                }
            }
        }
</script>

<?php include_once 'includes/layout/mainfooter.php'; ?>
