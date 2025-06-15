<?php
session_start();
require_once("db.php");

// Set dynamic page title
$pageTitle = "Job Details | JobSeek";

// Include header
include('header.php');

// Validate and sanitize GET parameter
$jobId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 0;

if ($jobId === 0) {
    // Invalid or missing job ID
    header("Location: jobs.php");
    exit;
}

// Check if candidate is logged in and set the 'dob' session variable
if (isset($_SESSION["id_candidate"])) {
    $userID = $_SESSION["id_candidate"];
    $query = "SELECT date_of_birth FROM candidates WHERE candidate_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['dob'] = $row['date_of_birth'];
    } else {
        $_SESSION['dob'] = '';
    }
}
?>

<div class="content-wrapper" style="margin-left: 0px;">
<?php
$sql = "SELECT * FROM jobs INNER JOIN companies ON jobs.company_id = companies.company_id WHERE job_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $jobId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
?>
<section id="candidates" class="content-header">
  <div class="container">
    <div class="row">          
      <div class="col-md-9 bg-white padding-2">
        <div class="pull-left">
          <h2><b><i><?php echo htmlspecialchars($row['job_title']); ?></i></b></h2>
        </div>
        <div class="pull-right">
          <a href="#" onclick="goBack()" class="btn btn-default btn-lg btn-flat margin-top-20"><i class="fa fa-arrow-circle-left"></i> Back</a>
        </div>
        <div class="clearfix"></div>
        <hr>
        <div>
          <p>
          <?php 
              if (!empty($row['experience'])) {
                echo '<span class="margin-right-10"><i class="fa fa-location-arrow text-green"></i> ' . htmlspecialchars($row['experience']) . ' Years Experience</span>';
              }
          ?> 
          <span class="margin-right-10"><i class="fa fa-clock-o text-green"></i> <?php echo htmlspecialchars($row['max_age']); ?> Max Age</span>
          <span class="margin-right-10"><i class="fa fa-suitcase text-green"></i> <?php echo htmlspecialchars($row['job_type']); ?></span>
          <span class="margin-right-10"><i class="fa fa-map-marker text-green"></i> <?php echo htmlspecialchars($row['location']); ?></span>
          <span class="margin-right-10"><i class="fa fa-drivers-license-o text-green"></i> <?php echo htmlspecialchars($row['category']); ?></span> 
          <span class="margin-right-10"><i class="fa fa-money text-green"></i> Salary: <?php echo htmlspecialchars($row['min_salary']) . ' - ' . htmlspecialchars($row['max_salary']); ?></span>
          <i class="fa fa-calendar-times-o text-green"></i> <?php echo date("d-M-Y", strtotime($row['deadline'])); ?></p>              
        </div>
        <div>
          <?php echo stripcslashes($row['job_description']); ?>
        </div>
        <div id="apply-button-container">
<?php
  if (isset($_SESSION["id_candidate"])) {
      $currentDate = date('Y-m-d');
      $maxAge = $row['max_age'];
      $userDOB = $_SESSION['dob'];
      $diff = date_diff(date_create($userDOB), date_create($currentDate));
      $userAge = $diff->format('%y');

      // Check if candidate has already applied for this job
      $applied = false;
      $checkApplySql = "SELECT * FROM applications WHERE candidate_id=? AND job_id=?";
      $checkApplyStmt = $conn->prepare($checkApplySql);
      $checkApplyStmt->bind_param("ii", $_SESSION["id_candidate"], $jobId);
      $checkApplyStmt->execute(); 
      $applyResult = $checkApplyStmt->get_result();
      if ($applyResult->num_rows > 0) {
          $applied = true;
      }

      if ($userAge <= $maxAge && !$applied) {
?>
        <a href="candidate/apply.php?id=<?php echo $row['job_id']; ?>" class="btn btn-success btn-flat margin-top-50">Apply</a>
<?php
      } elseif ($applied) {
?>
        <div class="alert alert-info">You have already applied for this job.</div>
<?php
      } else {
?>
        <div class="alert alert-danger">You are over-aged for this job.</div>
<?php
      }
  } elseif (!isset($_SESSION['id_company']) && !isset($_SESSION['id_admin'])) {
      // Not logged in or invalid session role
?>
    <button id="apply-button" class="btn btn-success btn-flat margin-top-50">Apply</button>
<?php
  }
?>

</div>
</div>

      <div class="col-md-3">
        <div class="thumbnail">
          <img src="uploads/logo/<?php echo htmlspecialchars($row['logo']); ?>" alt="companylogo">
          <div class="caption text-center">
            <h3><a href="view_company.php?id=<?php echo htmlspecialchars($row['company_id']); ?>" target="_blank">
                <?php echo htmlspecialchars($row['company_name']); ?></a></h3>
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php 
    }
}
?>
</div>

<?php include('footer.php'); ?>

<!-- JavaScript for handling Apply button click -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var applyButton = document.getElementById("apply-button");

    if (applyButton) {
        applyButton.addEventListener("click", function() {
            var isLoggedIn = <?php echo isset($_SESSION["id_candidate"]) ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                alert("Please log in to apply for the job.");
            } else {
                window.location.href = "apply.php?id=<?php echo htmlspecialchars($jobId); ?>";
            }
        });
    }
});

function goBack() {
    if (document.referrer) {
        window.location.href = document.referrer;
    } else {
        window.location.href = 'jobs.php';
    }
}
</script>

</body>
</html>
