<?php
// Start the session
session_start();

// Redirect to login page if admin is not logged in
if(empty($_SESSION['id_admin'])) {
  header("Location: login.php");
  exit();
}

// Include database connection
require_once("../db.php");

// Page title
$pageTitle = "Generate Reports | JobSeek";

// Include header
include('../shared/header_dashboard.php');


// Helper function to fetch distinct values from a table column
function fetchDistinctValues($conn, $table, $column) {
    $stmt = $conn->prepare("SELECT DISTINCT $column FROM $table ORDER BY $column ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row[$column];
    }
    return $values;
}

// Fetch dropdown data
$locations = fetchDistinctValues($conn, "jobs", "location");
$jobTypes = fetchDistinctValues($conn, "jobs", "job_type");
$categories = fetchDistinctValues($conn, "jobs", "category");
$cities = fetchDistinctValues($conn, "companies", "city");
?>
          
<div class="col-md-9 bg-white padding-2">
  <!-- Job Reporting Section -->
  <div class="row margin-top-20">
    <div class="col-md-12">
      <h3>Job Reporting</h3><br>
      <form id="report-form" method="post" action="job_report.php" target="_blank">
        <div class="col-md-3">
          <div class="form-group">
            <label for="report-type">Select Report Type:</label>
            <select class="form-control" id="report-type" name="report-type">
              <option value="">Select...</option>
              <option value="monthly">Monthly Report</option>
              <option value="yearly">Yearly Report</option>
            </select>
          </div>

          <div class="form-group hidden" id="month-group">
            <label for="month">Select Month:</label>
            <select class="form-control" id="month" name="month">
              <option value="">Select...</option>
              <?php foreach (range(1, 12) as $m): ?>
                <option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group hidden" id="year-group">
            <label for="year">Select Year:</label>
            <select class="form-control" id="year" name="year">
              <option value="">Select...</option>
              <?php for ($i = date("Y"); $i >= 2000; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- Location Dropdown -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="location">Select Location:</label>
            <select class="form-control" id="location" name="location">
              <option value="">Select...</option>
              <?php foreach ($locations as $location): ?>
                <option value="<?= htmlspecialchars($location) ?>"><?= htmlspecialchars($location) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Job Type Dropdown -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="job-type">Select Job Type:</label>
            <select class="form-control" id="job-type" name="job-type">
              <option value="">Select...</option>
              <?php foreach ($jobTypes as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Category Dropdown -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="category">Select Category:</label>
            <select class="form-control" id="category" name="category">
              <option value="">Select...</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
        </div>
      </form>
    </div>
  </div>

  <!-- User Reporting Section -->
  <div class="row margin-top-20">
    <div class="col-md-12">
      <h3>User Reporting</h3>
      <form id="user-report-form" method="post" action="user_report.php" target="_blank">
        <div class="col-md-3">
          <div class="form-group">
            <label for="user-report-type">Select Report Type:</label>
            <select class="form-control" id="user-report-type" name="user-report-type">
              <option value="">Select...</option>
              <option value="monthly">Monthly Report</option>
              <option value="yearly">Yearly Report</option>
            </select>
          </div>

          <div class="form-group hidden" id="user-month-group">
            <label for="user-month">Select Month:</label>
            <select class="form-control" id="user-month" name="user-month">
              <option value="">Select...</option>
              <?php foreach (range(1, 12) as $m): ?>
                <option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group hidden" id="user-year-group">
            <label for="user-year">Select Year:</label>
            <select class="form-control" id="user-year" name="user-year">
              <option value="">Select...</option>
              <?php for ($i = date("Y"); $i >= 2000; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label for="user-role">Select User:</label>
            <select class="form-control" id="user-role" name="user-role">
              <option value="">Select...</option>
              <option value="candidate">Candidate</option>
              <option value="company">Company</option>
            </select>
          </div>

          <div class="form-group hidden" id="age">
            <label for="age">Select Age:</label>
            <select class="form-control" id="age" name="age">
              <option value="">Select...</option>
              <option value="&lt; 40">&lt; 40 Years</option>
              <option value="&gt;= 40">&gt;= 40 Years</option>
            </select>
          </div>

          <div class="form-group hidden" id="company-location-group">
            <label for="company-location">Select Company Location:</label>
            <select class="form-control" id="company-location" name="company-location">
              <option value="">Select...</option>
              <?php foreach ($cities as $city): ?>
                <option value="<?= htmlspecialchars($city) ?>"><?= htmlspecialchars($city) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const reportType = document.getElementById("report-type");
  const monthGroup = document.getElementById("month-group");
  const yearGroup = document.getElementById("year-group");

  reportType.addEventListener("change", function () {
    const value = reportType.value;
    if (value === "monthly") {
      monthGroup.classList.remove("hidden");
      yearGroup.classList.add("hidden");
    } else if (value === "yearly") {
      yearGroup.classList.remove("hidden");
      monthGroup.classList.add("hidden");
    } else {
      monthGroup.classList.add("hidden");
      yearGroup.classList.add("hidden");
    }
  });

  const userReportType = document.getElementById("user-report-type");
  const userMonthGroup = document.getElementById("user-month-group");
  const userYearGroup = document.getElementById("user-year-group");

  userReportType.addEventListener("change", function () {
    const value = userReportType.value;
    if (value === "monthly") {
      userMonthGroup.classList.remove("hidden");
      userYearGroup.classList.add("hidden");
    } else if (value === "yearly") {
      userMonthGroup.classList.add("hidden");
      userYearGroup.classList.remove("hidden");
    } else {
      userMonthGroup.classList.add("hidden");
      userYearGroup.classList.add("hidden");
    }
  });

  // You mentioned: "// Event listener for user role employer..."
  const userRole = document.getElementById("user-role");
  const ageGroup = document.getElementById("age");
  const companyLocationGroup = document.getElementById("company-location-group");

  userRole.addEventListener("change", function () {
    const value = userRole.value;
    if (value === "candidate") {
      ageGroup.classList.remove("hidden");
      companyLocationGroup.classList.add("hidden");
    } else if (value === "company") {
      ageGroup.classList.add("hidden");
      companyLocationGroup.classList.remove("hidden");
    } else {
      ageGroup.classList.add("hidden");
      companyLocationGroup.classList.add("hidden");
    }
  });
});
</script>

</body>
</html>

