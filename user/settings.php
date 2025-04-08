<?php

//To Handle Session Variables on This Page
session_start();

//If user Not logged in then redirect them back to homepage. 
if(empty($_SESSION['id_user'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

// Check if password change was successful
$passwordChanged = isset($_GET['password_changed']) && $_GET['password_changed'] == 'success';

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Settings</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="../css/custom.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
   <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .logo img {
      height: 50px;
      width: auto;
    }
    .password-container {
      position: relative;
    }
    .password-container input {
      padding-right: 40px; /* Add padding to make space for the eye icon */
    }
    .password-container .eye-icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      z-index: 2;
    }
</style>

<script>
    window.onload = function() {
        <?php if ($passwordChanged): ?>
            alert('Changes made  successfully!');
            window.location.href = 'index.php';
        <?php endif; ?>
    }

    function togglePasswordVisibility() {
      var passwordField = document.getElementById('password');
      var confirmPasswordField = document.getElementById('cpassword');
      var eyeIconPassword = document.getElementById('eyeIconPassword');
      var eyeIconConfirmPassword = document.getElementById('eyeIconConfirmPassword');
      var type = passwordField.type === 'password' ? 'text' : 'password';
      passwordField.type = type;
      confirmPasswordField.type = type;
      eyeIconPassword.classList.toggle('fa-eye');
      eyeIconPassword.classList.toggle('fa-eye-slash');
      eyeIconConfirmPassword.classList.toggle('fa-eye');
      eyeIconConfirmPassword.classList.toggle('fa-eye-slash');
    }

    function validatePassword(password) {
      var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
      return regex.test(password);
    }

    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("changePassword").addEventListener("submit", function(e) {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('cpassword').value;

        if (password !== confirmPassword) {
          document.getElementById('passwordError').innerText = "Passwords do not match!";
          document.getElementById('passwordError').style.display = 'block';
          e.preventDefault();
        } else if (!validatePassword(password)) {
          document.getElementById('passwordError').innerText = "Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.";
          document.getElementById('passwordError').style.display = 'block';
          e.preventDefault();
        } else {
          document.getElementById('passwordError').style.display = 'none';
        }
      });
    });
    </script>

</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

<header class="main-header">
    <!-- Logo -->
    <a href="home.php" class="logo logo-bg">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>J</b>P</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">
        <img src="../img/logo.jpg" alt="Jobseek Logo">
      </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li><a href="../jobs.php">Jobs</a></li>
          <?php include 'notification.php'; ?>            
        </ul>
      </div>
    </nav>
  </header>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">

    <section id="candidates" class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Welcome <b><?php echo $_SESSION['name']; ?></b></h3>
              </div>
              <div class="box-body no-padding">
                 <ul class="nav nav-pills nav-stacked">
                 <li><a href="edit-profile.php"><i class="fa fa-user"></i> Edit Profile</a></li>
                 <li><a href="index.php"><i class="fa fa-address-card-o"></i> My Applications</a></li>
                  <li><a href="../jobs.php"><i class="fa fa-list-ul"></i> Jobs</a></li>
                  <li><a href="saved-jobs.php"><i class="fa fa-heart"></i> Saved Jobs</a></li>
                  <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                  <li class="active"><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                  <li><a href="user-feedback.php"><i class="fa fa-book"></i> Feedback</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
               
              </div>
            </div>
          </div>
          <div class="col-md-9 bg-white padding-2">
            <h2><i>Change Password</i></h2>
            <p>Type in new password that you want to use</p>
            <div class="row">
              <div class="col-md-6">
                <form id="changePassword" action="change-password.php" method="post">
                <div class="form-group password-container">
                    <input id="password" class="form-control input-lg" type="password" name="password" autocomplete="off" minlength="8" placeholder="Password"  required>
                    <i id="eyeIconPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
                  </div>
                  <div class="form-group password-container">
                    <input id="cpassword" class="form-control input-lg" type="password" autocomplete="off" minlength="8" placeholder="Confirm Password" required>
                    <i id="eyeIconConfirmPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-flat btn-success">Change Password</button>
                  </div>
                  <div id="passwordError" class="color-red text-center hide-me">
                    Password Mismatch!!
                  </div>
                </form>
              </div>
             <!-- <div class="col-md-6">
                <form action="deactivate-account.php" method="post">
                  <label><input type="checkbox" required> I Want To Deactivate My Account</label>
                  <button type="submit" class="btn btn-danger btn-flat btn-lg">Deactivate My Account</button>
                </form>
              </div>-->
            </div>
            
          </div>
        </div>
      </div>
    </section> </br></br>

    

  </div>
  <!-- /.content-wrapper -->

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>

</body>
</html>

<?php
include('footer.php');
?>