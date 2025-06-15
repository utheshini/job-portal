<?php
session_start();

// Redirect if not logged in
if (empty($_SESSION['id_admin']) && empty($_SESSION['id_candidate']) && empty($_SESSION['id_company'])) {
    header("Location: login.php");
    exit();
}

// Identify role for display or processing
$role = '';
if (!empty($_SESSION['id_admin'])) {
    $role = 'admin';
} elseif (!empty($_SESSION['id_candidate'])) {
    $role = 'candidate';
} elseif (!empty($_SESSION['id_company'])) {
    $role = 'company';
}

// Check if password change was successful
$passwordChanged = isset($_GET['password_changed']) && $_GET['password_changed'] == 'success';

$pageTitle = "Settings | JobSeek";
include('header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2">
    <h2><i>Account Settings</i></h2>
    <?php if ($role === 'company'): ?>
      <p>In this section you can change your account password and Account holder's name</p>
    <?php else: ?>
      <p>In this section you can change your account password</p>
    <?php endif; ?>

    <div class="row">
        <?php if ($role === 'company'): ?>
        <div class="col-md-6">
            <form action="../company/update_name.php" method="post">
                <div class="form-group">
                    <label>Your Name (Full Name)</label>
                    <input class="form-control input-lg" name="name" type="text" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-flat btn-primary btn-lg">Change Name</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="col-md-6">
            <form id="changePassword" action="change_password.php" method="post">
                <div class="form-group password-container">
                    <input id="password" class="form-control input-lg" type="password" name="password" autocomplete="off" placeholder="Password" required>
                    <i id="eyeIconPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
                </div>
                <div class="form-group password-container">
                    <input id="cpassword" class="form-control input-lg" type="password" autocomplete="off" placeholder="Confirm Password" required>
                    <i id="eyeIconConfirmPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-flat btn-success btn-lg">Change Password</button>
                </div>
                <div id="passwordError" class="color-red text-center hide-me">
                    Password Mismatch!!
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</section><br>
</div>

<?php include('../footer.php'); ?>

<script>
  window.onload = function () {
    <?php if ($passwordChanged): ?>
      alert('Password changed successfully!');
      window.location.href = 'dashboard.php';
    <?php endif; ?>

    const form = document.getElementById('changePassword');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('cpassword');
    const passwordError = document.getElementById('passwordError');

    // Password strength regex pattern
    const strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    form.addEventListener('submit', function (e) {
      let errors = [];

      // Check if passwords match
      if (password.value !== confirmPassword.value) {
        errors.push("Passwords do not match.");
        passwordError.textContent = "Passwords do not match!";
        passwordError.style.display = 'block';
      } else if (!strongPasswordPattern.test(password.value)) {
        errors.push("Password is not strong enough.");
        passwordError.textContent = "Password must be at least 8 characters, include upper and lower case letters, a number, and a special character.";
        passwordError.style.display = 'block';
      } else {
        passwordError.style.display = 'none';
      }

      // Prevent form submission if errors exist
      if (errors.length > 0) {
        e.preventDefault();
      }
    });
  };

  function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('cpassword');
    const eyeIconPassword = document.getElementById('eyeIconPassword');
    const eyeIconConfirmPassword = document.getElementById('eyeIconConfirmPassword');

    const newType = passwordField.type === 'password' ? 'text' : 'password';
    passwordField.type = newType;
    confirmPasswordField.type = newType;

    eyeIconPassword.classList.toggle('fa-eye');
    eyeIconPassword.classList.toggle('fa-eye-slash');
    eyeIconConfirmPassword.classList.toggle('fa-eye');
    eyeIconConfirmPassword.classList.toggle('fa-eye-slash');
  }
</script>

</body>
</html>
