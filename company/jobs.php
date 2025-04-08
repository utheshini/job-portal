<?php
// Start the session
session_start();

//If user Not logged in then redirect them back to homepage. 
if(empty($_SESSION['id_company'])) {
  header("Location: ../index.php");
  exit();
}

// Include the database connection
require_once("../db.php");

// Define the number of jobs per page
$jobsPerPage = 10;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting point for the SQL query
$offset = ($page - 1) * $jobsPerPage;

// Fetch unique locations
$locationsSql = "SELECT DISTINCT location FROM job_post";
$locationsResult = $conn->query($locationsSql);

// Fetch unique job types
$jobTypesSql = "SELECT DISTINCT job_type FROM job_post";
$jobTypesResult = $conn->query($jobTypesSql);

// Fetch unique categories
$categoriesSql = "SELECT DISTINCT category FROM job_post";
$categoriesResult = $conn->query($categoriesSql);


// Build the WHERE clause for the search query
$whereClauses = ["jp.deadline >= CURDATE()"];

// Add search keyword condition
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if (!empty($search)) {
    $whereClauses[] = "(jp.jobtitle LIKE '%$search%' OR jp.description LIKE '%$search%')";
}

// Add filter conditions
if (isset($_GET['location']) && is_array($_GET['location'])) {
    $locations = array_map([$conn, 'real_escape_string'], $_GET['location']);
    $locationsCondition = "jp.location IN ('" . implode("','", $locations) . "')";
    $whereClauses[] = $locationsCondition;
}

if (isset($_GET['job_type']) && is_array($_GET['job_type'])) {
    $jobTypes = array_map([$conn, 'real_escape_string'], $_GET['job_type']);
    $jobTypesCondition = "jp.job_type IN ('" . implode("','", $jobTypes) . "')";
    $whereClauses[] = $jobTypesCondition;
}

if (isset($_GET['category']) && is_array($_GET['category'])) {
    $categories = array_map([$conn, 'real_escape_string'], $_GET['category']);
    $categoriesCondition = "jp.category IN ('" . implode("','", $categories) . "')";
    $whereClauses[] = $categoriesCondition;
}

if (isset($_GET['experience'])) {
  $experience = (int)$_GET['experience'];
  $experienceCondition = "jp.experience >= $experience";
  $whereClauses[] = $experienceCondition;
}

// Build the SQL query
$whereSql = implode(' AND ', $whereClauses);
$sql = "SELECT jp.jobtitle, jp.location, jp.job_type, jp.maximumsalary, c.logo, c.companyname, jp.id_jobpost 
        FROM job_post jp 
        JOIN company c ON jp.id_company = c.id_company 
        WHERE $whereSql
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $jobsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the total number of jobs for pagination calculation
$totalJobsSql = "SELECT COUNT(*) as total FROM job_post jp 
                  JOIN company c ON jp.id_company = c.id_company 
                  WHERE $whereSql";
$totalJobsStmt = $conn->prepare($totalJobsSql);
$totalJobsStmt->execute();
$totalJobsResult = $totalJobsStmt->get_result();
$totalJobs = $totalJobsResult->fetch_assoc()['total'];
$totalPages = ceil($totalJobs / $jobsPerPage);

function getQueryString() {
  $query = [];
  foreach ($_GET as $key => $value) {
      if ($key !== 'page') { // Exclude 'page' parameter
          if (is_array($value)) {
              foreach ($value as $v) {
                  $query[] = urlencode($key) . '[]=' . urlencode($v);
              }
          } else {
              $query[] = urlencode($key) . '=' . urlencode($value);
          }
      }
  }
  return !empty($query) ? '&' . implode('&', $query) : '';
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jobs</title>
  <!-- Responsive design -->
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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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
      <span class="logo-mini"><b>J</b>P</span>
      <span class="logo-lg"><img src="../img/logo.jpg" alt="Jobseek Logo"></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li>
            <a href="jobs.php">Jobs</a>
          </li>

          <?php if (empty($_SESSION['id_user']) && empty($_SESSION['id_company'])) { ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="sign-up.php">Sign Up</a></li>  
          <?php } else { 
            if (isset($_SESSION['id_user'])) { 
                $userId = $_SESSION['id_user'];
          ?>        
          <li><a href="user/index.php">Dashboard</a></li>
          <?php } else if (isset($_SESSION['id_company'])) { ?>        
          <li><a href="index.php">Dashboard</a></li>
          <?php } ?>
          <li><a href="../logout.php">Logout</a></li>
          <?php } ?>          
        </ul>
      </div>
    </nav>
  </header>

  <!-- Content Wrapper -->
  <div class="content-wrapper" style="margin-left: 0px;">
    <section class="content-header">
      <div class="container">
        <div class="row">
          <div class="col-md-12 latest-job margin-top-50 margin-bottom-20">
            <h1 class="text-center">Available Jobs</h1>  
          </div>
        </div>
      </div>
    </section> 

      <section id="candidates" class="content-header">
        <div class="container">
          <div class="row">
            <div class="col-md-3">
              <div class="box box-solid">
                <!-- Search Bar -->
                <form id="search-form" method="GET" action="">
                  <div class="box-header with-border">
                  <div class="input-group">
                    <input type="text" class="form-control" name="search" id="search-input" placeholder="Search jobs: Keywords" value="<?php echo htmlspecialchars($search); ?>">
                    <span class="input-group-btn">
                      <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </span>
                  </div>
                  </div>

                  <div class="box-body">
                    <div class="form-group">
                      <label><i class="fa fa-map text-red"></i> City </label><br>
                      <?php while ($row = $locationsResult->fetch_assoc()) { ?>
                        <label style="color: #a8a2a2;" class="checkbox-inline">
                        <input type="checkbox" name="location[]" value="<?php echo htmlspecialchars($row['location']); ?>"
                          <?php if (isset($_GET['location']) && in_array($row['location'], $_GET['location'])) echo 'checked'; ?>>
                        <?php echo htmlspecialchars($row['location']); ?>
                        </label><br>
                      <?php } ?>
                    </div>

                    <div class="form-group">
                      <label><i class="fa fa-filter text-red"></i> Experience </label><br>
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                      <label style="color: #a8a2a2;" class="checkbox-inline">
                        <input type="checkbox" name="experience" value="<?php echo $i; ?>"
                          <?php if (isset($_GET['experience']) && $_GET['experience'] == $i) echo 'checked'; ?>> >= <?php echo $i; ?> years
                      </label><br>
                    <?php endfor; ?>
                    </div>

                    <div class="form-group">
                      <label><i class="fa fa-briefcase text-red"></i> Job Type </label><br>
                      <?php while ($row = $jobTypesResult->fetch_assoc()) { ?>
                        <label style="color: #a8a2a2;" class="checkbox-inline">
                        <input type="checkbox" name="job_type[]" value="<?php echo htmlspecialchars($row['job_type']); ?>"
                          <?php if (isset($_GET['job_type']) && in_array($row['job_type'], $_GET['job_type'])) echo 'checked'; ?>>
                        <?php echo htmlspecialchars($row['job_type']); ?>
                        </label><br>
                      <?php } ?>
                    </div>

                    <div class="form-group">
                      <label><i class="fa fa-folder text-red"></i> Category </label><br>
                      <?php while ($row = $categoriesResult->fetch_assoc()) { ?>
                        <label style="color: #a8a2a2;" class="checkbox-inline">
                        <input type="checkbox" name="category[]" value="<?php echo htmlspecialchars($row['category']); ?>"
                          <?php if (isset($_GET['category']) && in_array($row['category'], $_GET['category'])) echo 'checked'; ?>>
                        <?php echo htmlspecialchars($row['category']); ?>
                        </label><br>
                      <?php } ?>
                    </div>
                  </div>
                </form>
              </div>
            
            </div>

          
          <div class="col-md-9">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php
              ?>
         <div class="attachment-block clearfix">
            <img class="attachment-img" src="../uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="Attachment Image">
            <div class="attachment-pushed">
                <h4 class="attachment-heading">
                    <a href="view-jobs.php?id=<?php echo htmlspecialchars($row['id_jobpost']); ?>"><?php echo htmlspecialchars($row['jobtitle']); ?></a>
                    <span class="attachment-heading pull-right">LKR<?php echo htmlspecialchars($row['maximumsalary']); ?>/Month</span>
                </h4>
                <div class="attachment-text">
                    <div><strong><?php echo htmlspecialchars($row['companyname']); ?> | <?php echo htmlspecialchars($row['location']); ?> | <?php echo htmlspecialchars($row['job_type']); ?> </strong></div>
                   
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No jobs found.</p>
    <?php endif; ?>

    <!-- Pagination -->
<div class="container">
  <div class="row">
    <div class="col-md-9 text-center">
    <ul class="pagination">
  <?php if ($page > 1): ?>
    <li><a href="?page=<?php echo ($page - 1); ?><?php echo getQueryString(); ?>">&laquo; Previous</a></li>
  <?php endif; ?>
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <li class="<?php if ($i == $page) echo 'active'; ?>"><a href="?page=<?php echo $i; ?><?php echo getQueryString(); ?>"><?php echo $i; ?></a></li>
  <?php endfor; ?>
  <?php if ($page < $totalPages): ?>
    <li><a href="?page=<?php echo ($page + 1); ?><?php echo getQueryString(); ?>">Next &raquo;</a></li>
  <?php endif; ?>
</ul>
    </div>
  </div>
</div>
    
          </div>
          
        </div>
        
      </div>
    </section><br>
  </div>
  <!-- /.content-wrapper -->

  <!-- /.control-sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="../js/adminlte.min.js"></script>
<script src="../js/jquery.twbsPagination.min.js"></script>

</body>
</html>
<?php
include('footer.php');
?>