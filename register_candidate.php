<?php 
// Start session
session_start();

// Redirect if user is already logged in
if (isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) {
  header("Location: index.php");
  exit();
}

// Set dynamic page title
$pageTitle = "Candidates Registration | JobSeek";

// Include header
include('header.php');
?>

<!-- Content Wrapper -->
<div class="content-wrapper" style="margin-left: 0px;">

  <section class="content-header">
    <div class="container">
      <div class="row latest-job margin-top-50 margin-bottom-20 bg-white">
        <h1 class="text-center margin-bottom-20">CREATE YOUR PROFILE</h1>

        <!-- Display registration success alert -->
        <?php
        if (isset($_SESSION['registerCompleted']) && $_SESSION['registerCompleted']) {
          echo "<script>
            alert('Registration successful! Please check your email for verification.');
            window.location.href = 'login_candidate.php';
          </script>";
          unset($_SESSION['registerCompleted']);
        }
        ?>

        <!-- Registration Form -->
        <form method="post" id="registerCandidates" action="add_candidate.php" enctype="multipart/form-data">

          <!-- Left Section -->
          <div class="col-md-6 latest-job">
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
              <input class="form-control input-lg" type="date" id="dob" min="1960-01-01" max="2005-01-31" name="dob" required>
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

            <!-- Display email error -->
            <?php 
            if (isset($_SESSION['registerError'])) {
              echo '<div class="form-group"><label style="color: red;">Email Already Exists! Choose A Different Email!</label></div>';
              unset($_SESSION['registerError']);
            }
            ?>

            <!-- Display resume upload error -->
            <?php 
            if (isset($_SESSION['uploadError'])) {
              echo '<div class="form-group"><label style="color: red;">'.$_SESSION['uploadError'].'</label></div>';
              unset($_SESSION['uploadError']);
            }
            ?> 
          </div>

          <!-- Right Section -->
          <div class="col-md-6 latest-job">
            <div class="form-group password-container">
              <input class="form-control input-lg" type="password" id="password" name="password" minlength="8" placeholder="Password *" required>
              <i id="eyeIconPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
            </div>
            <div class="form-group password-container">
              <input class="form-control input-lg" type="password" id="cpassword" name="cpassword" minlength="8" placeholder="Confirm Password *" required>
              <i id="eyeIconConfirmPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
            </div>

            <div id="passwordError" class="btn btn-flat btn-danger hide-me" style="font-size:11px">
              Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.
            </div><br>
            <div id="cpasswordError" class="btn btn-flat btn-danger hide-me">Password Mismatch!!</div><br>

            <div class="form-group">
              <input class="form-control input-lg" type="text" id="contactno" name="contactno" minlength="10" maxlength="10" onkeypress="return validatePhone(event);" 
              placeholder="Phone Number *" required>
            </div>

            <div class="form-group">
              <label style="color: red;">File Format PDF Only!</label>
              <input type="file" name="resume" class="btn btn-flat btn-danger" accept=".pdf" required>
            </div>
          </div>
        </form>

      </div>
    </div>
  </section>

</div>

<?php include('footer.php'); ?>

<script>
// Toggle password visibility
function togglePasswordVisibility() {
  const passwordField = document.getElementById('password');
  const confirmPasswordField = document.getElementById('cpassword');
  const eyeIconPassword = document.getElementById('eyeIconPassword');
  const eyeIconConfirmPassword = document.getElementById('eyeIconConfirmPassword');
  const type = passwordField.type === 'password' ? 'text' : 'password';

  passwordField.type = type;
  confirmPasswordField.type = type;

  eyeIconPassword.classList.toggle('fa-eye');
  eyeIconPassword.classList.toggle('fa-eye-slash');
  eyeIconConfirmPassword.classList.toggle('fa-eye');
  eyeIconConfirmPassword.classList.toggle('fa-eye-slash');
}

// Validate only numeric phone input
function validatePhone(event) {
  const key = event.keyCode || event.which;
  if ([8, 46, 37, 39].includes(key)) return true;
  return key >= 48 && key <= 57;
}

// Calculate age on DOB change
document.addEventListener('DOMContentLoaded', () => {
  const dobInput = document.getElementById('dob');
  const ageInput = document.getElementById('age');

  dobInput.addEventListener('change', () => {
    const today = new Date();
    const birthDate = new Date(dobInput.value);
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
    ageInput.value = age;
  });

// Form submit validations
  const form = document.getElementById('registerCandidates');
  const password = document.getElementById('password');
  const cpassword = document.getElementById('cpassword');
  const passwordError = document.getElementById('passwordError');
  const cpasswordError = document.getElementById('cpasswordError');

    form.addEventListener('submit', function (e) {
      const passwordVal = password.value;
      const cpasswordVal = cpassword.value;
      const passwordStrength = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/;

      // Hide errors first
      passwordError.style.display = 'none';
      cpasswordError.style.display = 'none';

      // Validate confirm password
      if (passwordVal !== cpasswordVal) {
        cpasswordError.style.display = 'inline-block';
        e.preventDefault();
        return false;
      }

      // Validate password strength
      if (!passwordStrength.test(passwordVal)) {
        passwordError.style.display = 'inline-block';
        e.preventDefault();
        return false;
      }
    });
  });
</script>

</body>
</html>