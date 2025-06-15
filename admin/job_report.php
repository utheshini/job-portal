<?php
session_start();

if (empty($_SESSION['id_admin'])) {
  header("Location: login.php");
  exit();
}

require_once("../db.php");

// Sanitize and validate inputs
$reportType = filter_input(INPUT_POST, 'report-type', FILTER_SANITIZE_STRING);
$month = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT);
$year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$jobType = filter_input(INPUT_POST, 'job-type', FILTER_SANITIZE_STRING);
$category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

$hasFilters = $reportType || $month || $year || $location || $jobType || $category;
if (!$hasFilters) {
  header("Location: report.php?error=no_filters");
  exit();
}

// Construct query
$query = "SELECT j.job_title, j.posted_date, j.location, j.job_type, j.category, c.company_name 
          FROM jobs j
          JOIN companies c ON j.company_id = c.company_id 
          WHERE 1=1";

$params = [];
$types = "";

$currentYear = date('Y');

if ($reportType === 'monthly' && $month) {
  $query .= " AND MONTH(j.posted_date) = ? AND YEAR(j.posted_date) = ?";
  $params[] = $month;
  $types .= "ii";
  $params[] = $currentYear;
} elseif ($reportType === 'yearly' && $year) {
  $query .= " AND YEAR(j.posted_date) = ?";
  $params[] = $year;
  $types .= "i";
}

if ($location) {
  $query .= " AND j.location = ?";
  $params[] = $location;
  $types .= "s";
}
if ($jobType) {
  $query .= " AND j.job_type = ?";
  $params[] = $jobType;
  $types .= "s";
}
if ($category) {
  $query .= " AND j.category = ?";
  $params[] = $category;
  $types .= "s";
}

// Prepare statement
$stmt = $conn->prepare($query);
if (!$stmt) {
  die("Database error: " . $conn->error);
}

// Bind params
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$totalCount = $result->num_rows;

$reportDateTime = date('Y-m-d H:i:s');

// Determine heading
if ($reportType === 'monthly') {
  $reportHeading = 'Monthly Jobs Report for ' . date('F', mktime(0, 0, 0, (int)$month, 10)) . ' ' . $currentYear;
} elseif ($reportType === 'yearly') {
  $reportHeading = 'Annual Jobs Report for ' . htmlspecialchars($year);
} else {
  $reportHeading = 'Jobs Report';
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Jobs Report</title>
  <style>
    body { font-family: Arial, sans-serif; }
    #report-date-time { font-size: 12px; position: absolute; top: 10px; left: 10px; }
    h3 { text-align: center; margin-top: 30px; }
    #total-count { margin: 20px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 8px; text-align: left; }
    @media print {
      @page { margin: 0; }
      body { margin: 1cm; }
    }
  </style>
</head>
<body>
  <div id="report-date-time">Date and Time: <?= htmlspecialchars($reportDateTime) ?></div>
  <h3><?= htmlspecialchars($reportHeading) ?></h3>
  <div id="total-count">Total Jobs: <?= $totalCount ?></div>

  <?php if ($totalCount > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Job Title</th>
          <th>Company Name</th>
          <th>Posted Date</th>
          <th>Location</th>
          <th>Job Type</th>
          <th>Category</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['job_title']) ?></td>
            <td><?= htmlspecialchars($row['company_name']) ?></td>
            <td><?= htmlspecialchars($row['posted_date']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['job_type']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="margin: 20px;">No results found.</p>
  <?php endif; ?>

  <script>
    window.print();
  </script>
  
</body>
</html>