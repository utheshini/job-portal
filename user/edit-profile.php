<?php
//To Handle Session Variables on This Page
session_start();

// If user Not logged in then redirect them back to homepage.
if(empty($_SESSION['id_user'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

// Prepared statement to fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>My Profile</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <link rel="stylesheet" href="../css/custom.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .logo img {
      height: 50px;
      width: auto;
    }
  </style>
</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

<header class="main-header">
  <a href="home.php" class="logo logo-bg">
    <span class="logo-mini"><b>JobSeek</b></span>
    <span class="logo-lg">
      <img src="../img/logo.jpg" alt="Jobseek Logo">
    </span>
  </a>
  <nav class="navbar navbar-static-top">
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li><a href="jobs.php">Jobs</a></li>
        <?php include 'notification.php'; ?>            
      </ul>
    </div>
  </nav>
</header>

<div class="content-wrapper" style="margin-left: 0px;">
  <section id="candidates" class="content-header">
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Welcome <b><?php echo htmlspecialchars($_SESSION['name']); ?></b></h3>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="edit-profile.php"><i class="fa fa-user"></i> Edit Profile</a></li>
                <li><a href="index.php"><i class="fa fa-address-card-o"></i> My Applications</a></li>
                <li><a href="jobs.php"><i class="fa fa-list-ul"></i> Jobs</a></li>
                <li><a href="saved-jobs.php"><i class="fa fa-heart"></i> Saved Jobs</a></li>
                <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                <li><a href="user-feedback.php"><i class="fa fa-book"></i> Feedback</a></li>
                <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-9 bg-white padding-2">
          <h2><i>Edit Profile</i></h2>

          <?php
          if (isset($_SESSION['updateSuccess']) && $_SESSION['updateSuccess']) {
            echo "<script>
              alert('Profile updated successfully!');
              setTimeout(function() {
                window.location.href = 'index.php';
              }, 0000);
            </script>";
            unset($_SESSION['updateSuccess']);
          }
          ?>

          <form action="update-profile.php" method="post" enctype="multipart/form-data">
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
          ?>
            <div class="row">
              <div class="col-md-6 latest-job">
                <div class="form-group">
                  <label for="fname">First Name</label>
                  <input type="text" class="form-control input-lg" id="fname" name="fname" placeholder="First Name" value="<?php echo htmlspecialchars($row['firstname']); ?>" required="">
                </div>
                <div class="form-group">
                  <label for="lname">Last Name</label>
                  <input type="text" class="form-control input-lg" id="lname" name="lname" placeholder="Last Name" value="<?php echo htmlspecialchars($row['lastname']); ?>" required="">
                </div>
                <div class="form-group">
                  <label for="email">Email address</label>
                  <input type="email" class="form-control input-lg" id="email" placeholder="Email" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="age">Age</label>
                  <input type="number" class="form-control input-lg" id="age" placeholder="age" value="<?php echo htmlspecialchars($row['age']); ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea id="address" name="address" class="form-control input-lg" rows="5" placeholder="Address" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                </div>
                <div class="form-group">
                  <label for="contactno">Contact Number</label>
                  <input type="text" class="form-control input-lg" id="contactno" name="contactno" placeholder="Contact Number" minlength="10"value="<?php echo htmlspecialchars($row['contactno']); ?>" required>
                </div>
                <div class="form-group">
                  <label>About Me</label>
                  <textarea class="form-control input-lg" rows="4" name="aboutme"><?php echo htmlspecialchars($row['aboutme']); ?></textarea>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-flat btn-success">Update Profile</button>
                </div>
              </div>
              <div class="col-md-6 latest-job">
                <div class="form-group">
                  <label for="education">Education</label>
                  <textarea id="education" name="education" class="form-control input-lg" rows="5" placeholder="Education" required><?php echo htmlspecialchars($row['education']); ?></textarea>
                </div>
                <div class="form-group">
                  <label for="experience">Work Experience</label>
                  <textarea id="experience" name="experience" class="form-control input-lg" rows="5" placeholder="Work Experience"><?php echo htmlspecialchars($row['experience']); ?></textarea>
                </div>
                <div class="form-group">
                  <label>Skills</label>
                  <textarea class="form-control input-lg" rows="4" name="skills"><?php echo htmlspecialchars($row['skills']); ?></textarea>
                </div>
                <div class="form-group">
                  <label>Upload/Change Resume</label>
                  <input type="file" name="resume" class="btn btn-default" accept=".pdf"><br>
                  <?php if (!empty($row['resume'])): ?>
                  <input type="hidden" name="current_resume" value="<?php echo htmlspecialchars($row['resume']); ?>">
                  <p><b>Current Resume: <a href="../uploads/resume/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank"><?php echo htmlspecialchars($row['resume']); ?></b></a></p>
                <?php endif; ?>
                </div>
              </div>
            </div>
            <?php
              }
            }
            ?>
          </form>
          <?php if (isset($_SESSION['uploadError'])) { ?>
          <div class="row">
            <div class="col-md-12 text-center">
              <p class="text-danger"><?php echo $_SESSION['uploadError']; ?></p>
            </div>
          </div>
          <?php unset($_SESSION['uploadError']); } ?>
        </div>
      </div>
    </div>
  </section><br>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/adminlte.min.js"></script>

</body>
</html>
<?php
include('footer.php');
?>
