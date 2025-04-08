<?php 

session_start();


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Register - Candidates</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="css/custom.css">
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
    </script>

</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <header class="main-header">

    <!-- Logo -->
    <a href="index.php" class="logo logo-bg">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">
              <b>J</b>P
          </span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">
            <img src="./img/logo.jpg" alt="Jobseek Logo">
          </span>
      </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
            <a href="jobs.php">Jobs</a>
          </li>
          <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
          <li>
            <a href="login.php">Login</a>
          </li>
          <li>
            <a href="sign-up.php">Sign Up</a>
          </li>  
          <?php } else { 

            if(isset($_SESSION['id_user'])) { 
          ?>        
          <li>
            <a href="user/index.php">Dashboard</a>
          </li>
          <?php
          } else if(isset($_SESSION['id_company'])) { 
          ?>        
          <li>
            <a href="company/index.php">Dashboard</a>
          </li>
          <?php } ?>
          <li>
            <a href="logout.php">Logout</a>
          </li>
          <?php } ?>          
        </ul>
      </div>
    </nav>
  </header>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">

    <section class="content-header">
      <div class="container">

        <div class="row latest-job margin-top-50 margin-bottom-20 bg-white">
          <h1 class="text-center margin-bottom-20">CREATE YOUR PROFILE</h1>

          <!-- Show alerts messages -->
          <?php
          if (isset($_SESSION['registerCompleted']) && $_SESSION['registerCompleted']) {
            echo "<script>
              alert('Registration successful! Please check your email for verification.');
              setTimeout(function() {
                window.location.href = 'login-candidates.php';
              }, 0000);
            </script>";
            unset($_SESSION['registerCompleted']);
          }
          ?>

          <form method="post" id="registerCandidates" action="adduser.php" enctype="multipart/form-data">
            <div class="col-md-6 latest-job ">
              <div class="form-group">
                <input class="form-control input-lg" type="text" id="fname" name="fname" placeholder="First Name *" required>
              </div>
              <div class="form-group">
                <input class="form-control input-lg" type="text" id="lname" name="lname" placeholder="Last Name *" required>
              </div>
              <div class="form-group">
                <input class="form-control input-lg" type="email" id="email" name="email" placeholder="Email *" required>
              </div>
              <div class="form-group">
                <label>Date Of Birth</label>
                <input class="form-control input-lg" type="date" id="dob" min="1960-01-01" max="2005-01-31" name="dob" placeholder="Date Of Birth" required>
              </div>
              <div class="form-group">
                <input class="form-control input-lg" type="text" id="age" name="age" placeholder="Age" readonly>
              </div>
                             
              <div class="form-group checkbox">
                <label><input type="checkbox" name="terms" required> I accept terms & conditions</label>
              </div>
              <div class="form-group">
                <button class="btn btn-flat btn-success">Register</button>
              </div>
        
              <?php 
              //If User already registered with this email then show error message.
              if(isset($_SESSION['registerError'])) {
                ?>
                <div class="form-group">
                  <label style="color: red;">Email Already Exists! Choose A Different Email!</label>
                </div>
              <?php
               unset($_SESSION['registerError']); }
              ?> 

              <?php if(isset($_SESSION['uploadError'])) { ?>
              <div class="form-group">
                  <label style="color: red;"><?php echo $_SESSION['uploadError']; ?></label>
              </div>
              <?php unset($_SESSION['uploadError']); } ?>     

            </div>            
            <div class="col-md-6 latest-job ">
              <div class="form-group password-container">
                <input class="form-control input-lg" type="password" id="password" name="password" minlength="8" placeholder="Password *" required>
                <i id="eyeIconPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
              </div>
              <div class="form-group password-container">
                <input class="form-control input-lg" type="password" id="cpassword" name="cpassword" minlength="8" placeholder="Confirm Password *" required>
                <i id="eyeIconConfirmPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
              </div>
              <div id="passwordError" class="btn btn-flat btn-danger hide-me" style= "font-size:11px">
                    Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.
              </div><br>
              <div id="cpasswordError" class="btn btn-flat btn-danger hide-me" >
                    Password Mismatch!! 
                </div><br>

              <div class="form-group">
                <input class="form-control input-lg" type="text" id="contactno" name="contactno" minlength="10" maxlength="10" onkeypress="return validatePhone(event);" placeholder="Phone Number *" required>
              </div>             
              

              <div class="form-group">
                <label style="color: red;">File Format PDF Only!</label>
                <input type="file" name="resume" class="btn btn-flat btn-danger" accept=".pdf" required>
              </div>
            </div>
          </form>
          
        </div>
      </div>
    </section> </br>

    

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
<script src="js/adminlte.min.js"></script>

<script type="text/javascript">
    // Validate phone number input
      function validatePhone(event) {

        //event.keycode will return unicode for characters and numbers like a, b, c, 5 etc.
        //event.which will return key for mouse events and other events like ctrl alt etc. 
        var key = window.event ? event.keyCode : event.which;

        if(event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39) {
          // 8 means Backspace
          //46 means Delete
          // 37 means left arrow
          // 39 means right arrow
          return true;
        } else if( key < 48 || key > 57 ) {
          // 48-57 is 0-9 numbers on your keyboard.
          return false;
        } else return true;
      }
</script>


<script type="text/javascript">
  // Calculate age from DOB
  $('#dob').on('change', function() {
    var today = new Date();
    var birthDate = new Date($(this).val());
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();

    if(m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }

    $('#age').val(age);
  });
</script>

<script>
  // Validate password strength and match
  $("#registerCandidates").on("submit", function(e) {
    var password = $('#password').val();
    var cpassword = $('#cpassword').val();
    var passwordError = $('#passwordError');
    var cpasswordError = $('#cpasswordError');

    // Password strength regex
    var passwordStrength = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=]).{10,}$/;

    // Reset error visibility
    passwordError.hide();
    cpasswordError.hide();

    // Validate confirm password
    if (password !== cpassword) {
      cpasswordError.show();
      e.preventDefault();
      return false;
    }

    // Validate password strength
    if (!passwordStrength.test(password)) {
      passwordError.show();
      e.preventDefault();
      return false;
    }
  });
</script>

</body>
</html>
<?php
include('footer.php');
?>