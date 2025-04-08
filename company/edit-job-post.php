<?php

// To Handle Session Variables on This Page
session_start();

// If user Not logged in then redirect them back to homepage.
if (empty($_SESSION['id_company'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");


// Check if the ID of the job post is set and fetch the job details from the database
if (isset($_GET['id'])) {
  $id_jobpost = $_GET['id'];
  $sql = "SELECT * FROM job_post WHERE id_jobpost=? AND id_company=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id_jobpost, $_SESSION['id_company']);
  $stmt->execute();
  $result = $stmt->get_result();

  // If the job post exists, fetch the data
  if ($result->num_rows > 0) {
    $job = $result->fetch_assoc();
  } else {
    echo "No job post found with the specified ID.";
    exit();
  }
} else {
  echo "No job post ID specified.";
  exit();
}

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
    $jobtitle = $_POST['jobtitle'];
    $description = $_POST['description'];
    $experience = $_POST['experience'];
    $job_type = $_POST['job_type'];
    $category = $_POST['category'];
    $location = $_POST['location'];

    // Update the job post in the database using prepared statements
    $sql = "UPDATE job_post SET 
            jobtitle=?, description=?, minimumsalary=?, maximumsalary=?, 
            experience=?, job_type=?, category=?, location=?, maxage=?, deadline=?
            WHERE id_jobpost=? AND id_company=?";
            //updated_date = CURRENT_DATE()
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiisssisii", 
      $jobtitle, $description, $minimumSalary, $maximumSalary, $experience, 
      $job_type, $category, $location, $maxAge, $deadline, $id_jobpost, $_SESSION['id_company']
    );

    if ($stmt->execute()) {
      echo "<script>
            alert('Job updated successfully!');
            window.location.href = 'my-job-post.php';
        </script>";
    } else {
      echo "<script>
            alert('Error updating job: " . $stmt->error . "');
        </script>";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Edit Job</title>
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
      <span class="logo-mini"><b>J</b>P</span>
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
                <li><a href="create-job-post.php"><i class="fa fa-file-o"></i> Create Job Post</a></li>
                <li class="active"><a href="my-job-post.php"><i class="fa fa-list"></i> My Job Post</a></li>
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
          <h2><i>Edit Job Post</i></h2>
          <div class="row">
            <form id="jobEditForm" method="post" action="">
              <div class="col-md-12 latest-job">
                <div class="form-group">
                  <input class="form-control input-lg" type="text" id="jobtitle" name="jobtitle" placeholder="Job Title" value="<?php echo $job['jobtitle']; ?>" required>
                </div>
                <div class="form-group">
                  <textarea class="form-control input-lg" id="description" name="description" placeholder="Job Description" required><?php echo $job['description']; ?></textarea>
                </div>
                <div class="form-group">
                  <input type="number" class="form-control input-lg" id="minimumsalary" min="1000" autocomplete="off" name="minimumsalary" placeholder="Minimum Salary"  value="<?php echo $job['minimumsalary']; ?>" required>
                </div>
                <div class="form-group">
                  <input type="number" class="form-control input-lg" id="maximumsalary"  min="1001" name="maximumsalary" placeholder="Maximum Salary"  value="<?php echo $job['maximumsalary']; ?>" required>
                </div>
                <div class="form-group">
                  <input type="number" class="form-control input-lg" id="experience" autocomplete="off" name="experience" min="0" placeholder="Experience (in Years) Optional"  value="<?php echo $job['experience']; ?>">
                </div>
                <div class="form-group">
                <select class="form-control input-lg" id="job_type" name="job_type">
                  <option value="Full Time" <?php if ($job['job_type'] == 'Full Time') echo 'selected'; ?>>Full Time</option>
                  <option value="Part Time" <?php if ($job['job_type'] == 'Part Time') echo 'selected'; ?>>Part Time</option>
                  <option value="Internship" <?php if ($job['job_type'] == 'Internship') echo 'selected'; ?>>Internship</option>
                  <option value="Contract" <?php if ($job['job_type'] == 'Contract') echo 'selected'; ?>>Contract</option>
                </select>
                </div>
                <div class="form-group">
                  <select class="form-control input-lg" id="category" name="category" required>
                  <option value="" disabled>Select Category</option>
                      <option value="Accounting & Finance" <?php if ($job['category'] == 'Accounting & Finance') echo 'selected'; ?>>Accounting & Finance</option>
                      <option value="Administrative & Office Support" <?php if ($job['category'] == 'Administrative & Office Support') echo 'selected'; ?>>Administrative & Office Support</option>
                      <option value="Aerospace & Defense" <?php if ($job['category'] == 'Aerospace & Defense') echo 'selected'; ?>>Aerospace & Defense</option>
                      <option value="Agriculture & Farming" <?php if ($job['category'] == 'Agriculture & Farming') echo 'selected'; ?>>Agriculture & Farming</option>
                      <option value="Arts, Design, & Media" <?php if ($job['category'] == 'Arts, Design, & Media') echo 'selected'; ?>>Arts, Design, & Media</option>
                      <option value="Consulting" <?php if ($job['category'] == 'Consulting') echo 'selected'; ?>>Consulting</option>
                      <option value="Construction & Trades" <?php if ($job['category'] == 'Construction & Trades') echo 'selected'; ?>>Construction & Trades</option>
                      <option value="Customer Service" <?php if ($job['category'] == 'Customer Service') echo 'selected'; ?>>Customer Service</option>
                      <option value="Education & Training" <?php if ($job['category'] == 'Education & Training') echo 'selected'; ?>>Education & Training</option>
                      <option value="Energy & Utilities" <?php if ($job['category'] == 'Energy & Utilities') echo 'selected'; ?>>Energy & Utilities</option>
                      <option value="Engineering" <?php if ($job['category'] == 'Engineering') echo 'selected'; ?>>Engineering</option>
                      <option value="Environmental Science" <?php if ($job['category'] == 'Environmental Science') echo 'selected'; ?>>Environmental Science</option>
                      <option value="Fitness & Wellness" <?php if ($job['category'] == 'Fitness & Wellness') echo 'selected'; ?>>Fitness & Wellness</option>
                      <option value="Government & Public Sector" <?php if ($job['category'] == 'Government & Public Sector') echo 'selected'; ?>>Government & Public Sector</option>
                      <option value="Healthcare & Medical" <?php if ($job['category'] == 'Healthcare & Medical') echo 'selected'; ?>>Healthcare & Medical</option>
                      <option value="Hospitality & Tourism" <?php if ($job['category'] == 'Hospitality & Tourism') echo 'selected'; ?>>Hospitality & Tourism</option>
                      <option value="Human Resources" <?php if ($job['category'] == 'Human Resources') echo 'selected'; ?>>Human Resources</option>
                      <option value="Information Technology" <?php if ($job['category'] == 'Information Technology') echo 'selected'; ?>>Information Technology (IT)</option>
                      <option value="Insurance" <?php if ($job['category'] == 'Insurance') echo 'selected'; ?>>Insurance</option>
                      <option value="Legal" <?php if ($job['category'] == 'Legal') echo 'selected'; ?>>Legal</option>
                      <option value="Legal & Compliance" <?php if ($job['category'] == 'Legal & Compliance') echo 'selected'; ?>>Legal & Compliance</option>
                      <option value="Logistics & Supply Chain" <?php if ($job['category'] == 'Logistics & Supply Chain') echo 'selected'; ?>>Logistics & Supply Chain</option>
                      <option value="Manufacturing & Production" <?php if ($job['category'] == 'Manufacturing & Production') echo 'selected'; ?>>Manufacturing & Production</option>
                      <option value="Marketing & Advertising" <?php if ($job['category'] == 'Marketing & Advertising') echo 'selected'; ?>>Marketing & Advertising</option>
                      <option value="Media & Entertainment" <?php if ($job['category'] == 'Media & Entertainment') echo 'selected'; ?>>Media & Entertainment</option>
                      <option value="Nonprofit & Volunteer" <?php if ($job['category'] == 'Nonprofit & Volunteer') echo 'selected'; ?>>Nonprofit & Volunteer</option>
                      <option value="Pharmaceuticals" <?php if ($job['category'] == 'Pharmaceuticals') echo 'selected'; ?>>Pharmaceuticals</option>
                      <option value="Public Relations" <?php if ($job['category'] == 'Public Relations') echo 'selected'; ?>>Public Relations</option>
                      <option value="Publishing & Printing" <?php if ($job['category'] == 'Publishing & Printing') echo 'selected'; ?>>Publishing & Printing</option>
                      <option value="Real Estate" <?php if ($job['category'] == 'Real Estate') echo 'selected'; ?>>Real Estate</option>
                      <option value="Real Estate Development" <?php if ($job['category'] == 'Real Estate Development') echo 'selected'; ?>>Real Estate Development</option>
                      <option value="Retail & Hospitality" <?php if ($job['category'] == 'Retail & Hospitality') echo 'selected'; ?>>Retail & Hospitality</option>
                      <option value="Retail Management" <?php if ($job['category'] == 'Retail Management') echo 'selected'; ?>>Retail Management</option>
                      <option value="Sales & Business Development" <?php if ($job['category'] == 'Sales & Business Development') echo 'selected'; ?>>Sales & Business Development</option>
                      <option value="Science & Research" <?php if ($job['category'] == 'Science & Research') echo 'selected'; ?>>Science & Research</option>
                      <option value="Startups & Entrepreneurship" <?php if ($job['category'] == 'Startups & Entrepreneurship') echo 'selected'; ?>>Startups & Entrepreneurship</option>
                      <option value="Telecommunications" <?php if ($job['category'] == 'Telecommunications') echo 'selected'; ?>>Telecommunications</option>
                      <option value="Transportation & Logistics" <?php if ($job['category'] == 'Transportation & Logistics') echo 'selected'; ?>>Transportation & Logistics</option>
                      <option value="Transportation Management" <?php if ($job['category'] == 'Transportation Management') echo 'selected'; ?>>Transportation Management</option>
                  </select>
                </div>
                <div class="form-group">
                    <label> Deadline </label>
                    <input class="form-control input-lg" type="date" id="deadline" name="deadline" placeholder="Deadline" value="<?php echo $job['deadline']; ?>" required>
                </div>
                <div class="form-group">
                    <select class="form-control input-lg" id="location" name="location" required>
                        <option value="" disabled>Select a Location</option>
                          <?php
                          // Fetch cities from the database
                          $sql = "SELECT name FROM cities";
                          $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    $selected = ($job['location'] == $row['name']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['name']) . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                                }
                            } else {
                                echo '<option value="" disabled>No cities available</option>';
                            }
                          ?>
                    </select>
                </div>
                <div class="form-group">
                  <input type="number" class="form-control input-lg" id="maxage"  min="18" autocomplete="off" name="maxage" placeholder="Max Age (in Years) Required"  value="<?php echo $job['maxage']; ?>" required>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-flat btn-success">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    </br>
    </br>
  </div>
  <div class="control-sidebar-bg"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/adminlte.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
  const dateInput = document.getElementById('deadline');
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  const minDate = tomorrow.toISOString().split('T')[0];
  dateInput.setAttribute('min', minDate);

  document.getElementById('jobEditForm').addEventListener('submit', function(e) {
    const minimumSalary = parseFloat(document.getElementById('minimumsalary').value);
    const maximumSalary = parseFloat(document.getElementById('maximumsalary').value);
    const maxAge = parseInt(document.getElementById('maxage').value, 10);
    const deadline = document.getElementById('deadline').value;
    const description = tinymce.get('description').getContent().trim();
    const errors = [];

    if (maximumSalary <= minimumSalary) {
      errors.push('Maximum salary must be greater than minimum salary.');
    }

    if (maxAge < 18) {
      errors.push('Age should be above 18.');
    }

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
