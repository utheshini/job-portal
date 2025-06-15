<?php
// Start session
session_start();

// Redirect to homepage if user is already logged in
if(isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) { 
  header("Location: index.php");
  exit();
}

// Set dynamic page title
$pageTitle = "Login - Candidates | JobSeek";

// Include header
include('header.php');
?>

<div class="login-box">
  <div class="login-logo">
    <p><b>LOGIN</b> </p>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Candidate</p>

    <!-- Candidate Login Form -->
    <form method="post" action="check_candidate_login.php">
      <!-- Email Input -->
      <div class="form-group has-feedback">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>

      <!-- Password Input -->
      <div class="form-group has-feedback">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <!-- Show Password Toggle -->
      <div class="form-group">
        <input type="checkbox" id="showPassword"> Show Password
      </div>

      <!-- Forgot Password Link -->
      <div>
        <a href="shared/forgot_password.php">Forgot Password?</a>
      </div>
      <br>

      <div class="row">
        <div class="col-xs-8">
          <a href="register_candidate.php">Sign Up Here!</a>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
      </div>
    </form>

    <!-- Session Messages -->
    <?php 
    if(isset($_SESSION['registerCompleted'])) {
      ?>
      <div>
        <p id="successMessage" class="text-center">Check your email!</p>
      </div>
    <?php
     unset($_SESSION['registerCompleted']); }
    ?>   
    <?php 
    if(isset($_SESSION['loginError'])) {
      ?>
      <div>
        <p class="text-center">Invalid Email/Password! Try Again!</p>
      </div>
    <?php
     unset($_SESSION['loginError']); }
    ?>      

    <?php 
    if(isset($_SESSION['userActivated'])) {
      ?>
      <div>
        <p class="text-center">Your Account Is Active. You Can Login</p>
      </div>
    <?php
     unset($_SESSION['userActivated']); }
    ?>    

     <?php 
    if(isset($_SESSION['loginActiveError'])) {
      ?>
      <div>
        <p class="text-center"><?php echo htmlspecialchars($_SESSION['loginActiveError']); ?></p>
      </div>
    <?php
     unset($_SESSION['loginActiveError']); }
    ?>   

  </div>

</div>

<?php include('footer.php'); ?>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Show/Hide password toggle
    var showPasswordCheckbox = document.getElementById("showPassword");
    var passwordField = document.getElementById("password");

    showPasswordCheckbox.addEventListener("change", function () {
      passwordField.type = this.checked ? "text" : "password";
    });

    // Fade out success message after 8 seconds
    var successMessage = document.getElementById("successMessage");
    if (successMessage) {
      setTimeout(function () {
        successMessage.style.transition = "opacity 1s ease";
        successMessage.style.opacity = 0;
      }, 8000);
    }
  });
</script>

</body>
</html>
