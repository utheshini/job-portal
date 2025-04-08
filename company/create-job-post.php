<?php

//To Handle Session Variables on This Page
session_start();

//If user Not logged in then redirect them back to homepage. 
if(empty($_SESSION['id_company'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $errors = [];

  // Validate max salary is greater than min salary
  $minimumSalary = (float)$_POST['minimumsalary'];
  $maximumSalary = (float)$_POST['maximumsalary'];
  if ($maximumSalary <= $minimumSalary) {
      $errors[] = "Maximum salary must be greater than minimum salary.";
  }

  // Validate age is above 18
  $maxAge = (int)$_POST['maxage'];
  if ($maxAge < 18) {
      $errors[] = "Age should be above 18.";
  }

  // Validate deadline is a future date
  $deadline = $_POST['deadline'];
  $today = date("Y-m-d");
  if ($deadline <= $today) {
      $errors[] = "The deadline must be a future date.";
  }

  // Display errors if any
  if (!empty($errors)) {
      echo '<div class="alert alert-danger"><ul>';
      foreach ($errors as $error) {
          echo "<li>$error</li>";
      }
      echo '</ul></div>';
  } else {
      // Continue with form processing if no errors
      // Your form submission logic here
  }
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Post A Job</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="../css/custom.css">

  <script src="../js/tinymce/tinymce.min.js"></script>

<script>
tinymce.init({
  selector: '#description',
  height: 300,
  plugins: 'lists',
  toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
  menubar: false
});
</script>


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
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
      
      <?php include 'notifications.php'; ?>            
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
                <h3 class="box-title">Welcome <b><?php echo $_SESSION['name']; ?></b></h3>
              </div>
              <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                  <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                  <li><a href="edit-company.php"><i class="fa fa-tv"></i> My Company</a></li>
                  <li class="active"><a href="create-job-post.php"><i class="fa fa-file-o"></i> Create Job Post</a></li>
                  <li><a href="my-job-post.php"><i class="fa fa-list"></i> My Job Post</a></li>
                  <li><a href="job-applications.php"><i class="fa fa-file-o"></i> Job Application</a></li>
                  <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                  <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                  <li><a href="company-feedback.php"><i class="fa fa-book"></i> Feedback</a></li>
                  <li><a href="resume-database.php"><i class="fa fa-user"></i> Resume Database</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-9 bg-white padding-2">
            <h2><i>Create Job Post</i></h2>
            <div class="row">
              <form id="jobPostForm" method="post" action="addpost.php">
                <div class="col-md-12 latest-job ">
                  <div class="form-group">
                    <input class="form-control input-lg" type="text" id="jobtitle" name="jobtitle" placeholder="Job Title *" required>
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control input-lg" id="description" name="description" placeholder="Job Description"></textarea>
                  </div>
                  <div class="form-group">
                    <input type="number" class="form-control  input-lg" id="minimumsalary" min="1000" autocomplete="off" name="minimumsalary" placeholder="Minimum Salary *" required="">
                  </div>
                  <div class="form-group">
                    <input type="number" class="form-control  input-lg" id="maximumsalary" name="maximumsalary" min="1001" placeholder="Maximum Salary *" required="">
                  </div>
                  <div class="form-group">
                <input type="number" class="form-control  input-lg" id="experience" autocomplete="off" name="experience" min="0" placeholder="Experience (in Years) Optional" >
                  </div>
                  <div class="form-group">
                    <select class="form-control input-lg" id="job_type" name="job_type" required>
                      <option value="">Select Job Type</option>
                      <option value="Full Time">Full Time</option>
                      <option value="Part Time">Part Time</option>
                      <option value="Internship">Internship</option>
                      <option value="Contract">Contract</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <select class="form-control input-lg" id="category" name="category" required>
                      <option value="">Select Category</option>
                      <option value="Accounting & Finance">Accounting & Finance</option>
                      <option value="Administrative & Office Support">Administrative & Office Support</option>
                      <option value="Aerospace & Defense">Aerospace & Defense</option>
                      <option value="Agriculture & Farming">Agriculture & Farming</option>
                      <option value="Arts, Design, & Media">Arts, Design, & Media</option>
                      <option value="Consulting">Consulting</option>
                      <option value="Construction Management">Construction Management</option>
                      <option value="Construction & Trades">Construction & Trades</option>
                      <option value="Customer Service">Customer Service</option>
                      <option value="Education & Training">Education & Training</option>
                      <option value="Energy & Utilities">Energy & Utilities</option>
                      <option value="Engineering">Engineering</option>
                      <option value="Environmental Science">Environmental Science</option>
                      <option value="Fitness & Wellness">Fitness & Wellness</option>
                      <option value="Government & Public Sector">Government & Public Sector</option>
                      <option value="Healthcare & Medical">Healthcare & Medical</option>
                      <option value="Hospitality & Tourism">Hospitality & Tourism</option>
                      <option value="human-resources">Human Resources</option>
                      <option value="Information Technology ">Information Technology (IT)</option>
                      <option value="Insurance">Insurance</option>
                      <option value="Legal">Legal</option>
                      <option value="Legal & Compliance">Legal & Compliance</option>
                      <option value="Logistics & Supply Chain">Logistics & Supply Chain</option>
                      <option value="Manufacturing & Production">Manufacturing & Production</option>
                      <option value="Marketing & Advertising">Marketing & Advertising</option>
                      <option value="Media & Entertainment">Media & Entertainment</option>
                      <option value="Nonprofit & Volunteer">Nonprofit & Volunteer</option>
                      <option value="Pharmaceuticals">Pharmaceuticals</option>
                      <option value="Public Relations">Public Relations</option>
                      <option value="Publishing & Printing">Publishing & Printing</option>
                      <option value="Real Estate">Real Estate</option>
                      <option value="Real Estate Development">Real Estate Development</option>
                      <option value="Retail & Hospitality">Retail & Hospitality</option>
                      <option value="Retail Management">Retail Management</option>
                      <option value="Sales & Business Development">Sales & Business Development</option>
                      <option value="Science & Research">Science & Research</option>
                      <option value="Startups & Entrepreneurship">Startups & Entrepreneurship</option>
                      <option value="Telecommunications">Telecommunications</option>
                      <option value="Transportation & Logistics">Transportation & Logistics</option>
                      <option value="Transportation Management">Transportation Management</option>
                    </select>
                  </div>
                  <div class="form-group">
                      <label> Deadline </label> <input class="form-control input-lg" type="date" id="deadline" name="deadline" placeholder="Deadline" required>
                  </div>
                  <div class="form-group">
                    <select class="form-control input-lg" id="location" name="location" required="">
                        <option value="" disabled selected>Select a Location</option>
                        <?php
                          // Fetch cities from the database
                          $sql = "SELECT name FROM cities";
                          $result = $conn->query($sql);
                          if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($row["name"]) . '">' . htmlspecialchars($row["name"]) . '</option>';
                            }
                        } else {
                            echo '<option value="">No locations available</option>';
                        }
                        ?>
                    </select>
                </div>

                  <div class="form-group">
                    <input type="number" class="form-control  input-lg" id="maxage" autocomplete="off" name="maxage" min="18" placeholder="Max Age (in Years) Required" required="">
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-flat btn-success">Create</button>
                  </div>
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
    </section> </br>

    </br>

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
<script src="../js/adminlte.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
  const dateInput = document.getElementById('deadline');
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const minDate = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', minDate);

    document.getElementById('jobPostForm').addEventListener('submit', function(e) {
        const minimumSalary = parseFloat(document.getElementById('minimumsalary').value);
        const maximumSalary = parseFloat(document.getElementById('maximumsalary').value);
        const maxAge = parseInt(document.getElementById('maxage').value, 10);
        const deadline = document.getElementById('deadline').value;
        const description = tinymce.get('description').getContent().trim();
        const errors = [];

        // Check if max salary is greater than min salary
        if (maximumSalary <= minimumSalary) {
            errors.push('Maximum salary must be greater than minimum salary.');
        }

        // Check if age is above 18
        if (maxAge < 18) {
            errors.push('Age should be above 18.');
        }

        // Check if deadline is a future date
        if (deadline <= today) {
            errors.push('The deadline must be a future date.');
        }

        // Check if description is provided
        if (description.length === 0) {
            errors.push('Description is required.');
        }

        if (errors.length > 0) {
            alert(errors.join('\n'));
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
<?php
include('footer.php');
?>