<?php

// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("db.php");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jobseek - Find your dream job</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="css/custom.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
   <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
            <a href="jobs.php">Jobs</a>
          </li>
          
          <?php if(empty($_SESSION['id_user']) && empty($_SESSION['id_company']) && empty($_SESSION['id_admin'])) { ?>
          <li>
            <a href="login.php">Login</a>
          </li>
          <li>
            <a href="sign-up.php">Sign Up</a>
          </li>  
          <?php } else { 

            if(isset($_SESSION['id_user'])) { 
          ?>        
          <li>
            <a href="user/index.php">Dashboard</a>
          </li>
          <?php
          } else if(isset($_SESSION['id_company'])) { 
          ?>        
          <li>
            <a href="company/index.php">Dashboard</a>
          </li>
          <?php 
          } else if(isset($_SESSION['id_admin'])) { 
          ?>        
          <li>
            <a href="admin/dashboard.php">Dashboard</a>
          </li>
          <?php } ?>
          <li>
            <a href="logout.php">Logout</a>
          </li>
          <?php } ?>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">

    <section class="content-header bg-main">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center index-head">
            <h1>Find Your <strong>Dream Job</strong></h1>
            <p>Apply Now!</p>
            <p><a class="btn btn-success btn-lg" href="jobs.php" role="button">Search Jobs</a></p>
          </div>
        </div>
      </div>
    </section> </br>

    <section class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-md-12 latest-job margin-bottom-20">
            <h1 class="text-center">Featured Jobs</h1>            
            <?php 
          /* Show first 7 new or recently updated job posts
           * 
           * Store sql query result in $result variable and loop through it if we have any rows
           * returned from database. $result->num_rows will return total number of rows returned from database.
          */
          $sql = "SELECT jp.*, c.companyname, c.logo FROM job_post jp 
          JOIN company c ON jp.id_company = c.id_company 
          WHERE jp.deadline >= CURDATE() 
          ORDER BY jp.createdat DESC LIMIT 10";
          
  $result = $conn->query($sql);
  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) 
    {
                   // Fetch the logo path
                   $logoPath = "uploads/logo/" . $row['logo'];
             ?>
            <div class="attachment-block clearfix">
              <img class="attachment-img" src="<?php echo $logoPath; ?>" alt="Company Logo">
              <div class="attachment-pushed">
                <h4 class="attachment-heading"><a href="view-job-post.php?id=<?php echo $row['id_jobpost']; ?>"><?php echo $row['jobtitle']; ?></a> <span class="attachment-heading pull-right">LKR<?php echo $row['maximumsalary']; ?>/Month</span></h4>
                <h5 class="text-muted"><?php echo $row['companyname']; ?></h5>
                <div class="attachment-text">
                    <div><strong><p> 
                      <span class="margin-right-10"><i class="fa fa-clock-o text-green"></i> <?php echo $row['maxage']; ?> Max Age</span>
                      <span class="margin-right-10"><i class="fa fa-suitcase text-green"></i> <?php echo $row['job_type']; ?></span>
                      <span class="margin-right-10"><i class="fa fa-map-marker text-green"></i> <?php echo $row['location']; ?></span>
                      <span class="margin-right-10"><i class="fa fa-drivers-license-o text-green"></i> <?php echo $row['category']; ?></span> 
                      <i class="fa fa-calendar-times-o text-green"></i> <?php echo date("d-M-Y", strtotime($row['deadline'])); ?>
                    </p> </strong></div>
                </div>
              </div>
            </div>
          <?php
              }
            }
            
          
          ?>

          </div>
        </div>
      </div>
    </section> </br>


    
 <br>
  </div>
  
  <!-- /.content-wrapper -->

  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="js/adminlte.min.js"></script>
</body>
</html>

<?php
include('footer.php');
?>
