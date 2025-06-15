<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $pageTitle; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <link rel="stylesheet" href="../css/custom.css">
</head>

<body class="hold-transition skin-green sidebar-mini">

  <header class="main-header">
    <!-- Logo -->
    <a href="../index.php" class="logo logo-bg">
      <span class="logo-mini"><b>J</b>S</span>
      <span class="logo-lg">
        <img src="../img/logo.jpg" alt="Jobseek Logo">
      </span>
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php
            // Show notifications only to candidates and companies
            if (isset($_SESSION['id_candidate']) || isset($_SESSION['id_company'])) {
                include 'notification.php';
            }
          ?>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Conditional Sidebar -->
  <div class="content-wrapper" style="margin-left: 0px;">
    <section id="candidates" class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">
                  Welcome <b>
                    <?php
                    if (isset($_SESSION['id_candidate'])) {
                        // Fetch firstname and lastname from candidates table
                        $stmt = $conn->prepare("SELECT first_name, last_name FROM candidates WHERE candidate_id = ?");
                        $stmt->bind_param("i", $_SESSION['id_candidate']);
                        $stmt->execute();
                        $stmt->bind_result($firstname, $lastname);
                        if ($stmt->fetch()) {
                            echo htmlspecialchars($firstname . " " . $lastname);
                        }
                        $stmt->close();

                    } elseif (isset($_SESSION['id_company'])) {
                        // Fetch companyname from company table
                        $stmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
                        $stmt->bind_param("i", $_SESSION['id_company']);
                        $stmt->execute();
                        $stmt->bind_result($companyname);
                        if ($stmt->fetch()) {
                            echo htmlspecialchars($companyname);
                        }
                        $stmt->close();

                    } elseif (isset($_SESSION['admin_id'])) {
                        echo "Admin";
                    }
                    ?>
                  </b>
                </h3>
              </div>

              <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

              <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">

                  <?php if (isset($_SESSION['id_candidate'])): ?>
                    <li class="<?= ($currentPage == 'edit_profile.php') ? 'active' : ''; ?>">
                      <a href="../candidate/edit_profile.php"><i class="fa fa-user"></i> Edit Profile</a>
                    </li>
                    <li class="<?= ($currentPage == 'index.php') ? 'active' : ''; ?>">
                      <a href="../candidate/index.php"><i class="fa fa-address-card-o"></i> My Applications</a>
                    </li>
                    <li class="<?= ($currentPage == 'jobs.php') ? 'active' : ''; ?>">
                      <a href="../jobs.php"><i class="fa fa-list-ul"></i> Jobs</a>
                    </li>
                    <li class="<?= ($currentPage == 'saved_jobs.php') ? 'active' : ''; ?>">
                      <a href="../candidate/saved_jobs.php"><i class="fa fa-heart"></i> Saved Jobs</a>
                    </li>
                    <li class="<?= ($currentPage == 'mailbox.php') ? 'active' : ''; ?>">
                      <a href="../shared/mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a>
                    </li>
                    <li class="<?= ($currentPage == 'settings.php') ? 'active' : ''; ?>">
                      <a href="../shared/settings.php"><i class="fa fa-gear"></i> Settings</a>
                    </li>
                    <li class="<?= ($currentPage == 'feedback.php') ? 'active' : ''; ?>">
                      <a href="../shared/feedback.php"><i class="fa fa-book"></i> Feedback</a>
                    </li>
                    <li>
                      <a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a>
                    </li>

                  <?php elseif (isset($_SESSION['id_company'])): ?>
                    <li class="<?= ($currentPage == 'index.php') ? 'active' : ''; ?>">
                      <a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="<?= ($currentPage == 'edit_company_profile.php') ? 'active' : ''; ?>">
                      <a href="../company/edit_company_profile.php"><i class="fa fa-tv"></i> Edit Profile</a>
                    </li>
                    <li class="<?= ($currentPage == 'create_job.php') ? 'active' : ''; ?>">
                      <a href="../company/create_job.php"><i class="fa fa-file-o"></i> Post a Job</a>
                    </li>
                    <li class="<?= ($currentPage == 'my_job_postings.php') ? 'active' : ''; ?>">
                      <a href="../company/my_job_postings.php"><i class="fa fa-list"></i> My Posted jobs</a>
                    </li>
                    <li class="<?= ($currentPage == 'job_applications.php') ? 'active' : ''; ?>">
                      <a href="../company/job_applications.php"><i class="fa fa-file-o"></i> Job Applications</a>
                    </li>
                    <li class="<?= ($currentPage == 'mailbox.php') ? 'active' : ''; ?>">
                      <a href="../shared/mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a>
                    </li>
                    <li class="<?= ($currentPage == 'settings.php') ? 'active' : ''; ?>">
                      <a href="../shared/settings.php"><i class="fa fa-gear"></i> Settings</a>
                    </li>
                    <li class="<?= ($currentPage == 'feedback.php') ? 'active' : ''; ?>">
                      <a href="../shared/feedback.php"><i class="fa fa-book"></i> Feedback</a>
                    </li>
                    <li class="<?= ($currentPage == 'resume_database.php') ? 'active' : ''; ?>">
                      <a href="../company/resume_database.php"><i class="fa fa-user"></i> Resume Database</a>
                    </li>
                    <li>
                      <a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a>
                    </li>

                  <?php elseif (isset($_SESSION['id_admin'])): ?>
                    <li class="<?= ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                      <a href="../admin/dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a>
                    </li>
                    <li class="<?= ($currentPage == 'manage_jobs.php') ? 'active' : ''; ?>">
                      <a href="../admin/manage_jobs.php"><i class="fa fa-briefcase"></i> Manage Jobs</a>
                    </li>
                    <li class="<?= ($currentPage == 'manage_candidates.php') ? 'active' : ''; ?>">
                      <a href="../admin/manage_candidates.php"><i class="fa fa-address-card-o"></i> Manage Candidates</a>
                    </li>
                    <li class="<?= ($currentPage == 'manage_companies.php') ? 'active' : ''; ?>">
                      <a href="../admin/manage_companies.php"><i class="fa fa-building"></i> Manage Companies</a>
                    </li>
                    <li class="<?= ($currentPage == 'report.php') ? 'active' : ''; ?>">
                      <a href="../admin/report.php"><i class="fa fa-print"></i> Reporting</a>
                    </li>
                    <li class="<?= ($currentPage == 'manage_feedback.php') ? 'active' : ''; ?>">
                      <a href="../admin/manage_feedback.php"><i class="fa fa-book"></i> Feedbacks</a>
                    </li>
                    <li class="<?= ($currentPage == 'settings.php') ? 'active' : ''; ?>">
                      <a href="../shared/settings.php"><i class="fa fa-gear"></i> Settings</a>
                    </li>
                    <li>
                      <a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a>
                    </li>
                  <?php endif; ?>

                </ul>
              </div> 
            </div> 
          </div> 
