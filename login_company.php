<?php
// Start session
session_start();

// Redirect to homepage if user is already logged in
if (isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) {
  header("Location: index.php");
  exit();
}

// Set dynamic page title
$pageTitle = "Login - Employer | JobSeek";

// Include header
include('header.php');
?>

<div class="login-box">
  <div class="login-logo">
    <p><b>LOGIN</b></p>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Employer</p>

        <!-- Employer Login Form -->
    <form method="post" action="check_company_login.php">
      <!-- Email Input -->
      <div class="form-group has-feedback">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>

      <!-- Password Input -->
      <div class="form-group has-feedback">
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
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
          <a href="register_company.php">Sign Up Here!</a>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        </div>
    </form>

          <!-- Session Messages -->
          <?php
          if (isset($_SESSION['registerCompleted'])) {
            ?>
            <div>
              <p class="text-center">You Have Registered Successfully! Your Account Approval Is Pending By Admin</p>
            </div>
            <?php
            unset($_SESSION['registerCompleted']);
          }
          ?>
          <?php
          if (isset($_SESSION['loginError'])) {
            ?>
            <div>
              <p class="text-center">Invalid Email/Password! Try Again!</p>
            </div>
            <?php
            unset($_SESSION['loginError']);
          }
          ?>
          <?php
          if (isset($_SESSION['companyLoginError'])) {
            ?>
            <div>
              <p class="text-center"><?php echo htmlspecialchars($_SESSION['companyLoginError']); ?></p>
            </div>
            <?php
            unset($_SESSION['companyLoginError']);
          }
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
