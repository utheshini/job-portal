<?php
session_start();

// Check if the user is logged in and is an administrator, if not, redirect to login page
if (empty($_SESSION['id_admin'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../db.php");

// Function to delete feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $feedback_id = $_POST['feedback_id'];
    $sql_delete = "DELETE FROM feedback WHERE id = $feedback_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "Feedback deleted successfully";
        exit();
    } else {
        echo "Error deleting feedback: " . $conn->error;
        exit();
    }
}

// Fetch feedback entries from the database
$sql = "SELECT * FROM feedback";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>User Feedback</title>
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
  <style>
    .logo img {
        height: 50px;
        width: auto; 
    }
 </style>
  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-green sidebar-mini">

<div class="wrapper">

    <!-- Header -->
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
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                </ul>
            </div>
        </nav>
    </header>

    <!-- Content Wrapper -->
    <div class="content-wrapper" style="margin-left: 0px;">
        <section id="candidates" class="content-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <!-- Sidebar content -->
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
                                    <li><a href="report.php"><i class="fa fa-print"></i> Reporting</a></li>
                                    <li class="active"><a href="feedback.php"><i class="fa fa-book"></i> Feedbacks </a></li>
                                    <li><a href="settings.php"><i class="fa fa-gear"></i> setting</a></li>
                                    <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 bg-white padding-2">
                        <h2>User Feedbacks</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>User Type</th>
                                    <th>Feedback</th>
                                    <th>Submitted Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['user_id'] . "</td>";
                                        echo "<td>" . $row['user_type'] . "</td>";
                                        echo "<td>" . $row['feedback'] . "</td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "<td><button class='btn btn-danger delete-feedback' data-feedback-id='" . $row['id'] . "'>Delete</button></td>"; // Provide delete functionality
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No feedback found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section> </br>
    </div>



    <div class="control-sidebar-bg"></div>

</div>

<!-- Add your JavaScript links here -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>

<script>
    $(document).ready(function() {
        // Delete feedback
        $('.delete-feedback').click(function() {
            var feedbackId = $(this).data('feedback-id');
            if (confirm("Are you sure you want to delete this feedback?")) {
                $.ajax({
                    type: 'POST',
                    url: 'feedback.php',
                    data: { delete_feedback: true, feedback_id: feedbackId },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>
</body>
</html>
<?php
include('footer.php');
?>