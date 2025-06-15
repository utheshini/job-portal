<?php
session_start();

// Redirect if user is not logged in
if (empty($_SESSION['id_candidate'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../db.php");

// Set page title for HTML
$pageTitle = "Edit Profile | JobSeek";

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM candidates WHERE candidate_id = ?");
$stmt->bind_param("i", $_SESSION['id_candidate']);
$stmt->execute();
$result = $stmt->get_result();

// Include header file
include('../shared/header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2">
  <h2><i>Edit Profile</i></h2>

  <?php
  // Display success alert and redirect
  if (isset($_SESSION['updateSuccess']) && $_SESSION['updateSuccess']) {
    echo "<script>
      alert('Profile updated successfully!');
      window.location.href = 'index.php';
    </script>";
    unset($_SESSION['updateSuccess']);
  }
  ?>

  <!-- Form for profile update -->
  <form action="update_profile.php" method="post" enctype="multipart/form-data">
    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
    ?>
    <div class="row">
      <div class="col-md-6 latest-job">
        <!-- First Name -->
        <div class="form-group">
          <label for="fname">First Name</label>
          <input type="text" class="form-control input-lg" id="fname" name="fname" placeholder="First Name"
                 value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
        </div>

        <!-- Last Name -->
        <div class="form-group">
          <label for="lname">Last Name</label>
          <input type="text" class="form-control input-lg" id="lname" name="lname" placeholder="Last Name"
                 value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
        </div>

        <!-- Email (Read-only) -->
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" class="form-control input-lg" id="email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
        </div>

        <!-- Age (Read-only) -->
        <div class="form-group">
          <label for="age">Age</label>
          <input type="number" class="form-control input-lg" id="age" value="<?php echo htmlspecialchars($row['age']); ?>" readonly>
        </div>

        <!-- Address -->
        <div class="form-group">
          <label for="address">Address</label>
          <textarea id="address" name="address" class="form-control input-lg" rows="5" placeholder="Address" required><?php echo htmlspecialchars($row['address']); ?></textarea>
        </div>

        <!-- Contact Number -->
        <div class="form-group">
          <label for="contactno">Contact Number</label>
          <input type="text" class="form-control input-lg" id="contactno" name="contactno" placeholder="Contact Number"
                 minlength="10" value="<?php echo htmlspecialchars($row['contact_no']); ?>" required>
        </div>

        <!-- About Me -->
        <div class="form-group">
          <label>About Me</label>
          <textarea class="form-control input-lg" rows="4" name="aboutme" required><?php echo htmlspecialchars($row['about_me']); ?></textarea>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-flat btn-success">Update Profile</button>
        </div>
      </div>

      <div class="col-md-6 latest-job">
        <!-- Education -->
        <div class="form-group">
          <label for="education">Education</label>
          <textarea id="education" name="education" class="form-control input-lg" rows="5" placeholder="Education" required><?php echo htmlspecialchars($row['education']); ?></textarea>
        </div>

        <!-- Work Experience -->
        <div class="form-group">
          <label for="experience">Work Experience</label>
          <textarea id="experience" name="experience" class="form-control input-lg" rows="5" placeholder="Work Experience"><?php echo htmlspecialchars($row['experience']); ?></textarea>
        </div>

        <!-- Skills -->
        <div class="form-group">
          <label>Skills</label>
          <textarea class="form-control input-lg" rows="4" name="skills" required><?php echo htmlspecialchars($row['skills']); ?></textarea>
        </div>

        <!-- Resume Upload -->
        <div class="form-group">
          <label>Upload/Change Resume</label>
          <input type="file" name="resume" class="btn btn-default" accept=".pdf"><br>
          <?php if (!empty($row['resume'])): ?>
            <!-- Hidden field to preserve current resume if not changed -->
            <input type="hidden" name="current_resume" value="<?php echo htmlspecialchars($row['resume']); ?>">
            <p><b>Current Resume: <a href="../uploads/resume/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank"><?php echo htmlspecialchars($row['resume']); ?></a></b></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
        }
      }
    ?>
  </form>

  <!-- Display upload error message if set -->
  <?php if (isset($_SESSION['uploadError'])): ?>
    <div class="row">
      <div class="col-md-12 text-center">
        <p class="text-danger"><?php echo $_SESSION['uploadError']; ?></p>
      </div>
    </div>
    <?php unset($_SESSION['uploadError']); ?>
  <?php endif; ?>
</div>
</div>
</div>
</section><br>
</div>

<?php include('../footer.php'); ?>

</body>
</html>
