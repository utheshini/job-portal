<?php
session_start();

if (empty($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    $user_id = $_SESSION['id_user'];
    $user_type = 'job_seeker';

    $sql = "INSERT INTO feedback (user_id, user_type, feedback) VALUES ('$user_id', '$user_type', '$feedback')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Feedback Form</title>
    <!-- Add CSS links from the first page -->
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

    <!-- Logo -->
      <a href="home.php" class="logo logo-bg">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">
              <b>J</b>P
          </span>
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
          <li>
            <a href="jobs.php">Jobs</a>
          </li>
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
                <h3 class="box-title">Welcome <b><?php echo $_SESSION['name']; ?></b></h3>
              </div>
              <div class="box-body no-padding">
               <ul class="nav nav-pills nav-stacked">
                  <li><a href="edit-profile.php"><i class="fa fa-user"></i> Edit Profile</a></li>
                  <li><a href="index.php"><i class="fa fa-address-card-o"></i> My Applications</a></li>
                  <li><a href="jobs.php"><i class="fa fa-list-ul"></i> Jobs</a></li>
                  <li><a href="saved-jobs.php"><i class="fa fa-heart"></i> Saved Jobs</a></li>
                  <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                  <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                  <li class="active"><a href="../feedback.php"><i class="fa fa-book"></i> Feedback</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Feedback Form</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label for="feedback">Your Feedback:</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
          
        </div>
      </div>
      
    </section> </br>
        
    </div>

    <div class="control-sidebar-bg"></div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/adminlte.min.js"></script>
</body>
</html>

<?php
include('footer.php');
?>