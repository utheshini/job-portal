<?php 
// Start session
session_start();

// Include database connection
require_once("db.php");

// Redirect logged-in users to homepage
if (isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) {
  header("Location: index.php");
  exit();
}

// Dynamic page title
$pageTitle = "Candidates Registration | JobSeek";

// Include header
include('header.php');
?>

<!-- Main Content Wrapper -->
<div class="content-wrapper" style="margin-left: 0px;">

  <section class="content-header">
    <div class="container">
      <div class="row latest-job margin-top-50 margin-bottom-20 bg-white">
        <h1 class="text-center margin-bottom-20">CREATE EMPLOYER PROFILE</h1>

        <!-- Employer Registration Form -->
        <form method="post" id="registerCompanies" action="add_company.php" enctype="multipart/form-data">
          <div class="col-md-6 latest-job ">
            <!-- Full Name -->
            <div class="form-group">
              <input class="form-control input-lg" type="text" name="name" placeholder="Full Name *" required>
            </div>

            <!-- Company Name -->
            <div class="form-group">
              <input class="form-control input-lg" type="text" name="companyname" placeholder="Company Name *" required>
            </div>

            <!-- Website (optional) -->
            <div class="form-group">
              <input class="form-control input-lg" type="url" name="website" placeholder="Website">
            </div>

            <!-- Email -->
            <div class="form-group">
              <input class="form-control input-lg" type="email" name="email" placeholder="Email *" required>
            </div>

            <!-- City Dropdown -->
            <div class="form-group">
              <select class="form-control input-lg" id="city" name="city" required>
                <option selected disabled value="">Select City</option>
                <?php
                  $sql = "SELECT * FROM cities";
                  $result = $conn->query($sql);
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<option value='" . htmlspecialchars($row['city_name']) . "' data-id='" . $row['city_id'] . "'>" . htmlspecialchars($row['city_name']) . "</option>";
                    }
                  }
                ?>
              </select>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-group checkbox">
              <label><input type="checkbox" name="terms" required> I accept terms & conditions</label>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
              <button type="submit" class="btn btn-flat btn-success">Register</button>
            </div>

            <!-- Display Error Messages -->
            <?php 
              if (isset($_SESSION['registerError'])) {
                echo "<p class='text-center' style='color: red;'>Email Already Exists! Choose A Different Email!</p>";
                unset($_SESSION['registerError']);
              }

              if (isset($_SESSION['uploadError'])) {
                echo "<p class='text-center' style='color: red;'>". $_SESSION['uploadError'] ."</p>";
                unset($_SESSION['uploadError']);
              }
            ?>
          </div>

          <!-- Right Column -->
          <div class="col-md-6 latest-job ">

            <!-- Password -->
            <div class="form-group password-container">
              <input class="form-control input-lg" type="password" id="password" name="password" minlength="8" placeholder="Password *" required>
              <i id="eyeIconPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
            </div>

            <!-- Confirm Password -->
            <div class="form-group password-container">
              <input class="form-control input-lg" type="password" id="cpassword" name="cpassword" minlength="8" placeholder="Confirm Password *" required>
              <i id="eyeIconConfirmPassword" class="fa fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
            </div>

            <!-- Password Errors -->
            <div id="passwordError" class="btn btn-flat btn-danger hide-me" style="font-size:11px">
              Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.
            </div><br>
            <div id="cpasswordError" class="btn btn-flat btn-danger hide-me">Password Mismatch!!</div>

            <!-- Phone Number -->
            <div class="form-group">
              <input class="form-control input-lg" type="text" name="contactno" placeholder="Phone Number *" minlength="10" maxlength="10" autocomplete="off" onkeypress="return validatePhone(event);" required>
            </div>  

            <!-- Company Logo Upload -->
            <div class="form-group">
              <label>Attach Company Logo</label>
              <input type="file" name="image" class="form-control input-lg" accept="image/png, image/jpeg, image/jpg" required>
            </div>
          </div>
        </form>
        
      </div>
    </div>
  </section>

</div>

<?php include('footer.php'); ?>

<script>
// Toggle password visibility for both fields
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

// Validate only numbers for phone
function validatePhone(event) {
  const key = event.keyCode || event.which;
  if ([8, 46, 37, 39].includes(key)) return true;
  return key >= 48 && key <= 57;
}

// Form submit validations
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerCompanies'); 
  const password = document.getElementById('password');
  const cpassword = document.getElementById('cpassword');
  const passwordError = document.getElementById('passwordError');
  const cpasswordError = document.getElementById('cpasswordError');

  form.addEventListener('submit', function (e) {
    const passwordVal = password.value;
    const cpasswordVal = cpassword.value;

    // Password must be at least 10 chars with upper, lower, number, special char
    const passwordStrength = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/;

    // Hide errors initially
    passwordError.style.display = 'none';
    cpasswordError.style.display = 'none';

    // Check for password mismatch
    if (passwordVal !== cpasswordVal) {
      cpasswordError.style.display = 'inline-block';
      e.preventDefault();
      return false;
    }

    // Validate strength
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
