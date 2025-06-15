<?php
session_start(); // Start session
require_once("db.php"); // Include DB connection

// Set dynamic page title
$pageTitle = "Browse Jobs | JobSeek";

$jobsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $jobsPerPage;

// Fetch distinct values for filters
$locationsResult = $conn->query("SELECT DISTINCT location FROM jobs");
$jobTypesResult = $conn->query("SELECT DISTINCT job_type FROM jobs");
$categoriesResult = $conn->query("SELECT DISTINCT category FROM jobs");

// Check if a job is already saved by the candidate
function isJobSaved($conn, $userId, $jobId) {
  $sql = "SELECT * FROM saved_jobs WHERE candidate_id = ? AND job_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $userId, $jobId);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->num_rows > 0;
}

// Prepare WHERE conditions for search and filters
$whereClauses = ["j.deadline >= CURDATE()"];

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if (!empty($search)) {
    $whereClauses[] = "(j.job_title LIKE '%$search%' OR j.job_description LIKE '%$search%' OR c.company_name LIKE '%$search%')";
}

if (isset($_GET['location']) && is_array($_GET['location'])) {
    $locations = array_map([$conn, 'real_escape_string'], $_GET['location']);
    $whereClauses[] = "j.location IN ('" . implode("','", $locations) . "')";
}

if (isset($_GET['job_type']) && is_array($_GET['job_type'])) {
    $jobTypes = array_map([$conn, 'real_escape_string'], $_GET['job_type']);
    $whereClauses[] = "j.job_type IN ('" . implode("','", $jobTypes) . "')";
}

if (isset($_GET['category']) && is_array($_GET['category'])) {
    $categories = array_map([$conn, 'real_escape_string'], $_GET['category']);
    $whereClauses[] = "j.category IN ('" . implode("','", $categories) . "')";
}

if (isset($_GET['experience'])) {
  $experience = (int)$_GET['experience'];
  $whereClauses[] = "j.experience >= $experience";
}

$whereSql = implode(' AND ', $whereClauses);

// Fetch jobs matching filters
$sql = "SELECT j.job_title, j.location, j.job_type, j.max_salary, c.logo, c.company_name, j.job_id
        FROM jobs j 
        JOIN companies c ON j.company_id = c.company_id 
        WHERE $whereSql
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $jobsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of matching jobs for pagination
$totalJobsSql = "SELECT COUNT(*) as total FROM jobs j 
                 JOIN companies c ON j.company_id = c.company_id 
                 WHERE $whereSql";
$totalJobsStmt = $conn->prepare($totalJobsSql);
$totalJobsStmt->execute();
$totalJobsResult = $totalJobsStmt->get_result();
$totalJobs = $totalJobsResult->fetch_assoc()['total'];
$totalPages = ceil($totalJobs / $jobsPerPage);

// Helper function to retain filters while paginating
function getQueryString() {
  $query = [];
  foreach ($_GET as $key => $value) {
      if ($key !== 'page') {
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

include('header.php');
?>
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
                <!-- Filter Sidebar -->
                <div class="col-md-3">
                    <div class="box box-solid">
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
                                <!-- Location Filter -->
                                <div class="form-group">
                                    <label><i class="fa fa-map text-red"></i> City </label><br>
                                    <?php while ($row = $locationsResult->fetch_assoc()) { ?>
                                        <label class="checkbox-inline" style="color: #a8a2a2;">
                                            <input type="checkbox" name="location[]" value="<?php echo htmlspecialchars($row['location']); ?>"
                                                <?php if (isset($_GET['location']) && in_array($row['location'], $_GET['location'])) echo 'checked'; ?>>
                                            <?php echo htmlspecialchars($row['location']); ?>
                                        </label><br>
                                    <?php } ?>
                                </div>

                                <!-- Experience Filter -->
                                <div class="form-group">
                                    <label><i class="fa fa-filter text-red"></i> Experience </label><br>
                                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                                        <label class="checkbox-inline" style="color: #a8a2a2;">
                                            <input type="radio" name="experience" value="<?php echo $i; ?>"
                                                <?php if (isset($_GET['experience']) && $_GET['experience'] == $i) echo 'checked'; ?>>
                                            >= <?php echo $i; ?> years
                                        </label><br>
                                    <?php } ?>
                                </div>

                                <!-- Job Type Filter -->
                                <div class="form-group">
                                    <label><i class="fa fa-briefcase text-red"></i> Job Type </label><br>
                                    <?php while ($row = $jobTypesResult->fetch_assoc()) { ?>
                                        <label class="checkbox-inline" style="color: #a8a2a2;">
                                            <input type="checkbox" name="job_type[]" value="<?php echo htmlspecialchars($row['job_type']); ?>"
                                                <?php if (isset($_GET['job_type']) && in_array($row['job_type'], $_GET['job_type'])) echo 'checked'; ?>>
                                            <?php echo htmlspecialchars($row['job_type']); ?>
                                        </label><br>
                                    <?php } ?>
                                </div>

                                <!-- Category Filter -->
                                <div class="form-group">
                                    <label><i class="fa fa-folder text-red"></i> Category </label><br>
                                    <?php while ($row = $categoriesResult->fetch_assoc()) { ?>
                                        <label class="checkbox-inline" style="color: #a8a2a2;">
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

                <!-- Job Listings -->
                <div class="col-md-9">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $jobSaved = isset($_SESSION['id_candidate']) ? isJobSaved($conn, $_SESSION['id_candidate'], $row['job_id']) : false;
                            ?>
                            <div class="attachment-block clearfix">
                                <img class="attachment-img" src="uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="Logo">
                                <div class="attachment-pushed">
                                    <h4 class="attachment-heading">
                                        <a href="view_job.php?id=<?php echo htmlspecialchars($row['job_id']); ?>">
                                            <?php echo htmlspecialchars($row['job_title']); ?>
                                        </a>
                                        <span class="attachment-heading pull-right">
                                            LKR<?php echo htmlspecialchars($row['max_salary']); ?>/Month
                                        </span>
                                    </h4>
                                    <div class="attachment-text">
                                        <strong><?php echo htmlspecialchars($row['company_name']); ?> | 
                                                <?php echo htmlspecialchars($row['location']); ?> | 
                                                <?php echo htmlspecialchars($row['job_type']); ?>
                                        </strong>
                                        <?php
                                        $isGuest = empty($_SESSION['id_candidate']) && empty($_SESSION['id_company']) && empty($_SESSION['id_admin']);
                                        ?>

                                        <?php if ($isGuest): ?>
                                            <button type="button" onclick="showLoginMessage()" class="btn btn-primary">Save Job</button>
                                        <?php elseif (!empty($_SESSION['id_candidate'])): ?>
                                            <?php if ($jobSaved): ?>
                                                <button type="button" class="btn btn-secondary" disabled>Saved</button>
                                            <?php else: ?>
                                                <form action="./candidate/save_job.php" method="POST">
                                                    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($row['job_id']); ?>">
                                                    <button type="submit" class="btn btn-primary">Save Job</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>

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
                                        <li class="<?php if ($i == $page) echo 'active'; ?>">
                                            <a href="?page=<?php echo $i; ?><?php echo getQueryString(); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($page < $totalPages): ?>
                                        <li><a href="?page=<?php echo ($page + 1); ?><?php echo getQueryString(); ?>">Next &raquo;</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div> <!-- End of col-md-9 -->
            </div> <!-- End of row -->
        </div> <!-- End of container -->
    </section>
</div> <!-- End of content-wrapper -->

<?php include('footer.php'); ?>

<script>
    function showLoginMessage() {
        alert("Please login to save this job.");
    }
</script>

</body>
</html>