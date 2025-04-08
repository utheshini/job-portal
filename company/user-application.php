<?php

// To Handle Session Variables on This Page
session_start();

// If user Not logged in then redirect them back to homepage.
// This is required if user tries to manually enter view-job-post.php in URL.
if (empty($_SESSION['id_company'])) {
    header("Location: ../index.php");
    exit();
}

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

// Get the status of the application
$id_user = $_GET['id'];
$id_jobpost = $_GET['id_jobpost'];

// Fetch the status
$status_sql = "SELECT status FROM apply_job_post WHERE id_company='$_SESSION[id_company]' AND id_user='$id_user' AND id_jobpost='$id_jobpost'";
$status_result = $conn->query($status_sql);
$status_row = $status_result->fetch_assoc();
$status = $status_row['status'];

$sql = "SELECT * FROM users WHERE id_user='$id_user'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>User Application</title>
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
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .logo img {
            height: 50px;
            width: auto;
        }
        .disabled-link {
            pointer-events: none;
            color: gray;
            text-decoration: none;
        }
    </style>
</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="ihome.php" class="logo logo-bg">
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
                                    <li><a href="my-job-post.php"><i class="fa fa-list"></i> My Job Post</a></li>
                                    <li class="active"><a href="job-applications.php"><i class="fa fa-file-o"></i> Job Application</a></li>
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
                        <div class="row margin-top-20">
                            <div class="col-md-12">
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <div class="pull-left">
                                            <h2><b><i><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></i></b></h2>
                                        </div>
                                        <div class="pull-right">
                                            <a href="job-applications.php" class="btn btn-default btn-lg btn-flat margin-top-20"><i class="fa fa-arrow-circle-left"></i> Back</a>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr>
                                        <div>
                                            <?php
                                            echo 'Email: ' . $row['email'];
                                            echo '<br>';
                                            echo 'Date Of Birth: ' . $row['dob'];
                                            echo '<br>';
                                            echo 'Age: ' . $row['age'] . ' Years';
                                            echo '<br>';
                                            echo 'Address: ' . $row['address'];
                                            echo '<br>';
                                            echo 'Phone Number: ' . $row['contactno'];
                                            echo '<br>';
                                            echo '<br>';
                                            echo 'Education: ' . $row['education'];
                                            echo '<br>';
                                            echo '<br>';
                                            echo 'Experience: ' . $row['experience'];
                                            echo '<br>';
                                            echo '<br>';

                                            // Display skills with each skill on a new line
                                            $skills = $row['skills'];
                                            
                                            // Replace delimiter or add <br> for newline characters
                                            $formatted_skills = nl2br(htmlspecialchars($skills));
                                            
                                            echo 'Skills:<br>' . $formatted_skills;
                                            echo '<br>';
                                            echo '<br>';
                                            if ($row['resume'] != "") {
                                                echo '<a href="../uploads/resume/' . $row['resume'] . '" class="btn btn-info" download="' . $row['firstname'] . ' ' . $row['lastname'] . ' Resume">Download Resume</a>';
                                            }
                                            echo '<br>';
                                            echo '<br>';
                                            echo '<br>';
                                            echo '<br>';
                                            ?>
                                            <div class="row">
                                                <div class="col-md-3 pull-left">
                                                    <a href="under-review.php?id=<?php echo $row['id_user']; ?>&id_jobpost=<?php echo $id_jobpost; ?>" class="btn btn-success <?php echo ($status === 'selected' || $status === 'rejected') ? 'disabled-link' : ''; ?>" <?php echo ($status === 'selected' || $status === 'rejected') ? 'onclick="return false;"' : ''; ?>>Select Application</a>
                                                </div>
                                                <div class="col-md-3 pull-right">
                                                    <a href="reject.php?id=<?php echo $row['id_user']; ?>&id_jobpost=<?php echo $id_jobpost; ?>" class="btn btn-danger <?php echo ($status === 'selected' || $status === 'rejected') ? 'disabled-link' : ''; ?>" <?php echo ($status === 'selected' || $status === 'rejected') ? 'onclick="return false;"' : ''; ?>>Reject Application</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                } else { ?>
                                    <h2>No User Found</h2>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><br>
    </div>
</div>

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<!-- SlimScroll -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-slimscroll/1.3.8/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fastclick/1.0.6/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>
</body>
</html>
<?php
include('footer.php');
?>