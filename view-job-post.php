<?php
// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("db.php");

// Check if user is logged in and set the 'dob' session variable
if (isset($_SESSION["id_user"]) && empty($_SESSION['companyLogged'])) {
    $userID = $_SESSION["id_user"];
    // Retrieve the user's date of birth from the database
    $query = "SELECT dob FROM users WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $userDOB = $row['dob'];
    } else {
        $userDOB = ''; // Set a default value or handle the error accordingly
    }
    $_SESSION['dob'] = $userDOB;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>view Job</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <link rel="stylesheet" href="css/custom.css">
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
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php if (empty($_SESSION["id_user"]) && empty($_SESSION["companyLogged"])) { ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="sign-up.php">Sign Up</a></li>
          <?php } ?>
        </ul>
      </div>
    </nav>
  </header>

  <div class="content-wrapper" style="margin-left: 0px;">
  <?php
    $sql = "SELECT * FROM job_post INNER JOIN company ON job_post.id_company=company.id_company WHERE id_jobpost=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
  ?>
    <section id="candidates" class="content-header">
      <div class="container">
        <div class="row">          
          <div class="col-md-9 bg-white padding-2">
            <div class="pull-left">
              <h2><b><i><?php echo $row['jobtitle']; ?></i></b></h2>
            </div>
            <div class="pull-right">
              <a href="#" onclick="goBack()" class="btn btn-default btn-lg btn-flat margin-top-20"><i class="fa fa-arrow-circle-left"></i> Back</a>
            </div>
            <div class="clearfix"></div>
            <hr>
            <div>
              <p>
              <?php 
                  // Check and display experience only if it's not empty
                  if (!empty($row['experience'])) {
                    echo '<span class="margin-right-10"><i class="fa fa-location-arrow text-green"></i> ' . htmlspecialchars($row['experience']) . ' Years Experience</span>';
                  }
                ?> 
                <span class="margin-right-10"><i class="fa fa-clock-o text-green"></i> <?php echo $row['maxage']; ?> Max Age</span>
                <span class="margin-right-10"><i class="fa fa-suitcase text-green"></i> <?php echo $row['job_type']; ?></span>
                <span class="margin-right-10"><i class="fa fa-map-marker text-green"></i> <?php echo $row['location']; ?></span>
                <span class="margin-right-10"><i class="fa fa-drivers-license-o text-green"></i> <?php echo $row['category']; ?></span> 
                <span class="margin-right-10"><i class="fa fa-money text-green"></i> Salary: <?php echo htmlspecialchars($row['minimumsalary']) . ' - ' . htmlspecialchars($row['maximumsalary']); ?></span>
                <i class="fa fa-calendar-times-o text-green"></i> <?php echo date("d-M-Y", strtotime($row['deadline'])); ?></p>              
            </div>
            <div>
              <?php echo stripcslashes($row['description']); ?>
            </div>
           
              <div id="apply-button-container">
                <button id="apply-button" class="btn btn-success btn-flat margin-top-50">Apply</button>
              </div>
          </div>
          <div class="col-md-3">
            <div class="thumbnail">
              <img src="uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="companylogo">
              <div class="caption text-center">
                <h3><a href="view-company.php?id=<?php echo $row['id_company']; ?>" target="_blank"><?php echo $row['companyname']; ?></a></h3>
                <hr>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> </br>
    <?php 
        }
    }
    ?>
  </div>
  
  <div class="control-sidebar-bg"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="js/adminlte.min.js"></script>

<!-- JavaScript for handling Apply button click -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var applyButton = document.getElementById("apply-button");

    applyButton.addEventListener("click", function() {
        var isLoggedIn = <?php echo isset($_SESSION["id_user"]) ? 'true' : 'false'; ?>;
        
        if (!isLoggedIn) {
            alert("Please log in to apply for the job.");
        } else {
            window.location.href = "apply.php?id=<?php echo htmlspecialchars($_GET['id']); ?>";
        }
    });
});

function goBack() {
    if (document.referrer) {
        window.location.href = document.referrer;
    } else {
        window.location.href = 'jobs.php';
    }
}
</script>
</script>
</body>
</html>
<?php
include('footer.php');