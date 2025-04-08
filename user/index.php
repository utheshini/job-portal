<?php

//To Handle Session Variables on This Page
session_start();

//If user Not logged in then redirect them back to homepage. 
if(empty($_SESSION['id_user'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");


?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>My Applications</title>
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
          <li><a href="jobs.php">Jobs</a></li>
          <?php include 'notification.php'; ?>            
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
                  <li><a href="edit-profile.php"><i class="fa fa-user"></i> Edit Profile</a></li>
                  <li class="active"><a href="index.php"><i class="fa fa-address-card-o"></i> My Applications</a></li>
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

          <div class="col-md-9 bg-white padding-2" id="printableArea">
            <h2><i>My Applications</i></h2>
            <p>Below you will find Job Posts you have applied for</p>

            <table id="example3" class="table table-bordered">
              <thead>
                <tr>
                  <th>Job Role</th>
                  <th>Company</th>
                  <th>Applied Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT job_post.jobtitle, company.companyname, apply_job_post.apply_date, apply_job_post.status, apply_job_post.id_jobpost 
                        FROM job_post 
                        INNER JOIN apply_job_post ON job_post.id_jobpost = apply_job_post.id_jobpost 
                        INNER JOIN company ON job_post.id_company = company.id_company 
                        WHERE apply_job_post.id_user = '$_SESSION[id_user]' 
                        ORDER BY apply_job_post.apply_date DESC";
                $result = $conn->query($sql);

                if($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {     
                ?>
                <tr>
                  <td><a href="view-job-post.php?id=<?php echo $row['id_jobpost']; ?>"><?php echo $row['jobtitle']; ?></a></td>
                  <td><?php echo $row['companyname']; ?></td>
                  <td><?php echo $row['apply_date']; ?></td>  
                  <td>
                    <?php 
                    if($row['status'] == "pending") {
                      echo '<strong class="text-orange">Pending</strong>';
                    } else if ($row['status'] == "rejected") {
                      echo '<strong class="text-red">Rejected</strong>';
                    } else if ($row['status'] == "selected") {
                      echo '<strong class="text-olive";>Selected</strong>';
                    }
                    ?>
                  </td>
                </tr>
                <?php
                  }
                } else {
                  echo '<tr><td colspan="3">No recent applications found.</td></tr>';
                }
                ?>
              </tbody>
            </table>
            
            <div class="no-print">
              <button onclick="printTable()" class="btn btn-primary btn-lg btn-flat margin-top-20"><i class="fa fa-print"></i> Print</button>
            </div>
          </div>
        </div>
      </div>
    </section> </br> </br>
  </div>

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

$(function () {
    $('#example3').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    });
  });
  function printTable() {
    // Create a new print frame
    var printFrame = document.createElement('iframe');
    printFrame.style.position = 'absolute';
    printFrame.style.width = '0px';
    printFrame.style.height = '0px';
    printFrame.style.border = 'none';
    document.body.appendChild(printFrame);

    // Get current date and time
    var now = new Date();
    var dateTime = now.toLocaleString(); // Format date and time as a string

    // Write content to the print frame
    var printDoc = printFrame.contentWindow.document;
    printDoc.open();
    printDoc.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @media print {
                    /* Hide header and footer in the print preview */
                    @page {
                        margin: 0;
                    }
                    body {
                        margin: 0;
                    }
                        .print-date-time {
                        position: absolute;
                        top: 0;
                        left: 0;
                        margin: 10px;
                        font-size: 12px;
                        color: #000;
                    }
                    .no-print {
                        display: none;
                    }
                     a {
                        text-decoration: none;
                        color: inherit;
                    }
                }
                table {  width: calc(100% - 20px); border-collapse: collapse; margin-left: 10px; margin-right: 10px; }
                th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                h3 { text-align: center; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="print-date-time">Date and Time: ${dateTime}</div>
            <h3>My Applications</h3>
            ${document.getElementById('example3').outerHTML}
        </body>
        </html>
    `);
    printDoc.close();

    // Print the content of the frame
    printFrame.contentWindow.focus();
    printFrame.contentWindow.print();

    // Remove the print frame after printing
    document.body.removeChild(printFrame);
}

</script>
</body>
</html>
<?php
include('footer.php');
?>
