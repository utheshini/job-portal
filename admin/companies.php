<?php

session_start();

if(empty($_SESSION['id_admin'])) {
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
  <title>Jobseek - Employers</title>
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
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
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
                  <li class="active"><a href="companies.php"><i class="fa fa-building"></i> Employers</a></li>
                  <li><a href="report.php"><i class="fa fa-print"></i> Reporting</a></li>
                  <li><a href="feedback.php"><i class="fa fa-book"></i> Feedbacks </a></li>
                  <li><a href="settings.php"><i class="fa fa-gear"></i> setting</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-9 bg-white padding-2">
            <h3>Employers</h3>
            <div class="row margin-top-20">
              <div class="col-md-12">
                <div class="box-body table-responsive no-padding">
                  <table id="example2" class="table table-hover">
                    <thead>
                      <th>Company Name</th>
                      <th>Account Creator Name</th>
                      <th>Email</th>
                      <th>Phone No</th>
                      <th>City</th>
                      <th>Created Date</th>
                      <th class="no-print">Status</th>
                      <th class="no-print">Delete</th>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM company";
                      $result = $conn->query($sql);
                      if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                      ?>
                      <tr>
                        <td><?php echo $row['companyname']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['contactno']; ?></td>
                        <td><?php echo $row['city']; ?></td>
                        <td><?php echo $row['createdAt']; ?></td>
                        <td class="no-print">
                        <?php
                          if($row['active'] == "approved") {
                            echo "<span style='color: green;'>Activated</span>";
                          } else if($row['active'] == "pending") {
                            ?>
                            <a style='color: red;' href="reject-company.php?id=<?php echo $row['id_company']; ?>" onclick="return confirm('Are you sure you want to reject this employer?');"><strong>Reject</strong></a> 
                            <a style='color: green;' href="approve-company.php?id=<?php echo $row['id_company']; ?>" onclick="return confirm('Are you sure you want to approve this employer?');"><strong>Approve</strong></a>
                            <?php
                          } else if ($row['active'] == "deactivated") {
                            echo "<span style='color: blue;'>Deactivated</span>";
                            ?>
                              
                            <?php
                          } else if($row['active'] == "rejected") {
                            echo "<span style='color: red;'>Rejected</span>";
                          }
                        ?>                          
                        </td>
                        <td class="no-print"><a href="delete-company.php?id=<?php echo $row['id_company']; ?>" onclick="return confirm('Are you sure you want to deactivated this employer?');"><i class="fa fa-trash"></i></a></td>
                      </tr>  
                     <?php
                        }
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
          </div>
        </div>
      </div>
    </section> </br>
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
  $(function () {
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
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
      .no-print, .no-print * {
        display: none !important;
      }
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
            <h3>JobSeek - Employers</h3>
            ${document.getElementById('example2').outerHTML}
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
