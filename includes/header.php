<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $pageTitle; ?></title> 
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- External CSS Libraries -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

  <!-- Theme and Custom Styles -->
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/_all-skins.min.css">
  <link rel="stylesheet" href="css/custom.css">
</head>

<body class="hold-transition skin-green sidebar-mini">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="index.php" class="logo logo-bg">
      <span class="logo-mini"><b>J</b>S</span>
      <span class="logo-lg">
        <img src="./img/logo.jpg" alt="Jobseek Logo">
      </span>
    </a>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php if (empty($_SESSION['id_candidate']) && empty($_SESSION['id_company']) && empty($_SESSION['id_admin'])): ?>
            <li><a href="jobs.php">Jobs</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="sign_up.php">Sign Up</a></li>
          <?php else: ?>
            <?php if (isset($_SESSION['id_candidate'])): ?>
              <li><a href="candidate/index.php">Dashboard</a></li>
            <?php elseif (isset($_SESSION['id_company'])): ?>
              <li><a href="company/index.php">Dashboard</a></li>
            <?php elseif (isset($_SESSION['id_admin'])): ?>
              <li><a href="admin/dashboard.php">Dashboard</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </nav>
  </header>
