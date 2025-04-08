<?php
// To Handle Session Variables on This Page
session_start();

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>View Company</title>
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
            
        </ul>
      </div>
    </nav>
  </header>

  <div class="content-wrapper" style="margin-left: 0px;">
  <?php
    // Query to retrieve company details based on job ID
    $sql = "SELECT * FROM company WHERE id_company=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
  ?>
    <section id="company-details" class="content-header">
      <div class="container">
        <div class="row">          
          <div class="col-md-12 bg-white padding-2">
            <div class="col-md-3">
                <div class="thumbnail">
                <img src="../uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="Company Logo">
                </div>
            </div>
            <div class="pull-left">
              <h2><b><i><?php echo htmlspecialchars($row['companyname']); ?></i></b></h2>
            </div>

            <div class="clearfix"></div>
            <hr>

            <div>
              <h4><b>About Us</b></h4>
              <h4><?php echo htmlspecialchars($row['aboutme']); ?></h4>
            </div><br>
            <div>
                <div><span class="margin-right-10"><i class="fa fa-map-marker text-green"></i><b>  Company Location: </b> <?php echo htmlspecialchars($row['address']); ?></span></div><br>
                <div><span class="margin-right-10"><i class="fa fa-envelope text-green"></i><b>  Email: </b> <?php echo htmlspecialchars($row['email']); ?></span></div><br>
                <div><span class="margin-right-10"><i class="fa fa-phone text-green"></i> <b>  Phone: </b> <?php echo htmlspecialchars($row['contactno']); ?></span></div><br>
                <div>
                    <?php if (!empty($row['website'])): ?>
                        <span class="margin-right-10">
                            <i class="fa fa-globe text-green"></i>
                            <b>Website: </b>
                            <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank">
                                <?php echo htmlspecialchars($row['website']); ?>
                            </a>
                        </span><br>
                    <?php endif; ?>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section><br>
    <?php 
        }
    }
    ?>
  </div>
  
  <div class="control-sidebar-bg"></div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/adminlte.min.js"></script>

<script>

</script>
</body>
</html>
<?php
include('footer.php');
?>
