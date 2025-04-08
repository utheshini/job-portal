<?php
session_start();

if(empty($_SESSION['id_admin'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

// Check for a 'no_results' message in the query string
//if (isset($_GET['no_results'])) {
  //echo '<script>alert("No results found. Please try different filters.");</script>';
//}

// Fetch distinct locations from the job_post table
$locationsQuery = "SELECT DISTINCT location FROM job_post ORDER BY location ASC";
$locationsResult = $conn->query($locationsQuery);

// Fetch distinct job types from the job_post table
$jobTypesQuery = "SELECT DISTINCT job_type FROM job_post ORDER BY job_type ASC";
$jobTypesResult = $conn->query($jobTypesQuery);

// Fetch distinct categories from the job_post table
$categoriesQuery = "SELECT DISTINCT category FROM job_post ORDER BY category ASC";
$categoriesResult = $conn->query($categoriesQuery);

// Fetch distinct cities from the company table
$citiesQuery = "SELECT DISTINCT city FROM company ORDER BY city ASC";
$citiesResult = $conn->query($citiesQuery);

// Prepare arrays
$locations = array();
$jobTypes = array();
$categories = array();
$cities = array();

// Prepare locations array
$locations = array();
if ($locationsResult) {
    while ($row = $locationsResult->fetch_assoc()) {
        $locations[] = $row['location'];
    }
}

if ($jobTypesResult) {
    while ($row = $jobTypesResult->fetch_assoc()) {
        $jobTypes[] = $row['job_type'];
    }
}

if ($categoriesResult) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

if ($citiesResult) {
  while ($row = $citiesResult->fetch_assoc()) {
      $cities[] = $row['city'];
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Reporting</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="../css/custom.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .logo img {
      height: 50px;
      width: auto; 
    }
    .hidden {
      display: none;
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
        <b>J</b>S
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
        </ul>
      </div>
    </nav>
  </header>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="margin-left: 0px;">
    <section id="candidates" class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Welcome <b>Admin</b></h3>
              </div>
              <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                  <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                  <li><a href="active-jobs.php"><i class="fa fa-briefcase"></i> Jobs</a></li>
                  <li><a href="applications.php"><i class="fa fa-address-card-o"></i> Candidates</a></li>
                  <li><a href="companies.php"><i class="fa fa-building"></i> Employers</a></li>
                  <li class="active"><a href="report.php"><i class="fa fa-print"></i> Reporting</a></li>
                  <li><a href="feedback.php"><i class="fa fa-book"></i> Feedbacks </a></li>
                  <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-9 bg-white padding-2">

            <div class="row margin-top-20">
            <div class="col-md-12">
            <h3>Job Reprting</h3><br>
                <!-- Dropdown for selecting report type -->
                <form id="report-form" method="post" action="job-report.php" target="blank"> <!--automatically open the form submission in a new tab.-->
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="report-type"> Select Report Type:</label>
                    <select class="form-control" id="report-type" name="report-type">
                      <option value="">Select...</option>
                      <option value="monthly">Monthly Report</option>
                      <option value="yearly">Yearly Report</option>
                    </select>
                  </div>                  
                  <div class="form-group hidden" id="month-group">
                    <label for="month">Select Month:</label>
                    <select class="form-control" id="month" name="month">
                      <option value="">Select...</option>
                      <option value="1">January</option>
                      <option value="2">February</option>
                      <option value="3">March</option>
                      <option value="4">April</option>
                      <option value="5">May</option>
                      <option value="6">June</option>
                      <option value="7">July</option>
                      <option value="8">August</option>
                      <option value="9">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
                    </select>
                  </div>
                  <div class="form-group hidden" id="year-group">
                    <label for="year">Select Year:</label>
                    <select class="form-control" id="year" name="year">
                      <option value="">Select...</option>
                      <?php
                      $currentYear = date("Y");
                      for ($i = $currentYear; $i >= 2000; $i--) {
                          echo "<option value=\"$i\">$i</option>";
                      }
                      ?>
                    </select>
                  </div>
                  </div>

                  <div class="col-md-3">
                  <div class="form-group">
                    <label for="location">Select Location:</label>
                    <select class="form-control" id="location" name="location">
                      <option value="">Select...</option>
                      <?php
                      foreach ($locations as $location) {
                          echo "<option value=\"$location\">$location</option>";
                      }
                      ?>
                    </select>
                  </div>
                    </div>

                    <div class="col-md-3">
                  <div class="form-group">
                    <label for="job-type">Select Job Type:</label>
                    <select class="form-control" id="job-type" name="job-type">
                      <option value="">Select...</option>
                      <?php
                      foreach ($jobTypes as $jobType) {
                          echo "<option value=\"$jobType\">$jobType</option>";
                      }
                      ?>
                    </select>
                  </div>
                    </div>

                    <div class="col-md-3">
                  <div class="form-group">
                    <label for="category">Select Category:</label>
                    <select class="form-control" id="category" name="category">
                      <option value="">Select...</option>
                      <?php
                      foreach ($categories as $category) {
                          echo "<option value=\"$category\">$category</option>";
                      }
                      ?>
                    </select>
                  </div>
                    </div>
                    <div class="col-md-12">
                          <!-- Print button -->
                      <button type="submit"  class="btn btn-primary" id="print-btn"><i class="fa fa-print"></i> Print</button>
                  </div>
                </form>
                <!-- End of dropdowns -->
            </div>
          </div><br>

          <!-- User Reporting Section -->
          <div class="row margin-top-20">
            <div class="col-md-12">
              <h3>User Reporting</h3><br>
              <form id="user-report-form" method="post" action="user-report.php" target="blank">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="user-report-type">Select Report Type:</label>
                    <select class="form-control" id="user-report-type" name="user-report-type">
                      <option value="">Select...</option>
                      <option value="monthly">Monthly Report</option>
                      <option value="yearly">Yearly Report</option>
                    </select>
                  </div>
                  <div class="form-group hidden" id="user-month-group">
                    <label for="user-month">Select Month:</label>
                    <select class="form-control" id="user-month" name="user-month">
                      <option value="">Select...</option>
                      <option value="1">January</option>
                      <option value="2">February</option>
                      <option value="3">March</option>
                      <option value="4">April</option>
                      <option value="5">May</option>
                      <option value="6">June</option>
                      <option value="7">July</option>
                      <option value="8">August</option>
                      <option value="9">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
                    </select>
                  </div>
                  <div class="form-group hidden" id="user-year-group">
                    <label for="user-year">Select Year:</label>
                    <select class="form-control" id="user-year" name="user-year">
                      <option value="">Select...</option>
                      <?php
                      $currentYear = date("Y");
                      for ($i = $currentYear; $i >= 2000; $i--) {
                          echo "<option value=\"$i\">$i</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="user-role">Select User:</label>
                    <select class="form-control" id="user-role" name="user-role">
                      <option value="">Select...</option>
                      <option value="job-seeker">Job seeker</option>
                      <option value="employer">Employer</option>
                    </select>
                  </div>

                      <!-- Add new location dropdown for Jobseeker -->
                  <div class="form-group hidden" id="age">
                    <label for="age">Select Age:</label>
                    <select class="form-control" id="age" name="age">
                      <option value="">Select...</option>
                      <option value="&lt; 40">&lt; 40 Years </option>
                      <option value="&gt;= 40">&gt;= 40 Years</option>
                    </select>
                  </div>
                  <!-- Add new location dropdown for Employer -->
                  <div class="form-group hidden" id="employer-location-group">
                    <label for="employer-location">Select Employer Location:</label>
                    <select class="form-control" id="employer-location" name="employer-location">
                      <option value="">Select...</option>
                      <?php
                      foreach ($cities as $city) {
                           echo "<option value=\"$city\">$city</option>";
                      }
                      ?>
                    </select>
                  </div>


                </div>

                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary" id="user-print-btn"><i class="fa fa-print"></i> Print</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section> <br>
  </div>
  <!-- /.content-wrapper -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
  $('#report-type').change(function() {
    var reportType = $(this).val();
    if (reportType == 'monthly') {
      $('#month-group').removeClass('hidden');
      $('#year-group').addClass('hidden');
    } else if (reportType == 'yearly') {
      $('#year-group').removeClass('hidden');
      $('#month-group').addClass('hidden');

    } else {
      $('#month-group').addClass('hidden');
      $('#year-group').addClass('hidden');

    }
  });

  $('#user-report-type').change(function() {
    var reportType = $(this).val();
    if (reportType === 'monthly') {
      $('#user-month-group').removeClass('hidden');
      $('#user-year-group').addClass('hidden');
    } else if (reportType === 'yearly') {
      $('#user-month-group').addClass('hidden');
      $('#user-year-group').removeClass('hidden');
    } else {
      $('#user-month-group').addClass('hidden');
      $('#user-year-group').addClass('hidden');
    }
  });

// Event listener for user role employer dropdown
$('#user-role').change(function() {
    if ($(this).val() == 'employer') {
      $('#employer-location-group').removeClass('hidden');
    } else  if ($(this).val() == 'job-seeker') {
      $('#employer-location-group').addClass('hidden');
    } else {
      $('#employer-location-group').addClass('hidden');
    }
  });

  // Event listener for user role jobseeker dropdown
$('#user-role').change(function() {
    if ($(this).val() == 'job-seeker') {
      $('#age').removeClass('hidden');
    } else  if ($(this).val() == 'employer') {
      $('#age').addClass('hidden');
    } else {
      $('#age').addClass('hidden');
    }
  });

});
</script>

</body>
</html>
<?php
include('footer.php');
?>
