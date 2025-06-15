<?php
session_start();

// Redirect if admin is already logged in
if (isset($_SESSION['id_admin'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | JobSeek</title>

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../css/AdminLTE.min.css">

  <style>
    .login-box {
      margin-top: 80px;
    }
    .text-danger {
      color: #dd4b39;
    }
  </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
    <a href="../index.php"><b>Jobseek</b></a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Admin Login</p>

    <!-- Login form -->
    <form action="check_admin_login.php" method="post" autocomplete="off">

      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="username" placeholder="Username" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="form-group">
        <label>
          <input type="checkbox" id="showPassword"> Show Password
        </label>
      </div>

      <div class="row">
        <div class="col-xs-4 col-xs-offset-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
      </div>

      <!-- Display login error -->
      <?php if (isset($_SESSION['loginError'])): ?>
        <div class="text-center">
          <p class="text-danger">Invalid Username or Password. Please try again.</p>
        </div>
        <?php unset($_SESSION['loginError']); ?>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- JavaScript -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const showPassword = document.getElementById('showPassword');
    const passwordField = document.getElementById('password');

    showPassword.addEventListener('change', function () {
      passwordField.type = this.checked ? 'text' : 'password';
    });
  });
</script>

</body>
</html>
