<?php
session_start();

if (empty($_SESSION['id_admin'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

$reportType = $_POST['report-type'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';
$location = $_POST['location'] ?? '';
$jobType = $_POST['job-type'] ?? '';
$category = $_POST['category'] ?? '';

// Check if any filters have been applied
$hasFilters = $reportType || $month || $year || $location || $jobType || $category;

if (!$hasFilters) {
  echo '<script>alert("No options selected. Please go back and select at least one option."); window.location.href = "report.php";</script>';
  exit();
}

// Initialize the SQL query
$query = "SELECT jp.jobtitle, jp.createdat, jp.location, jp.job_type, jp.category, c.companyname 
          FROM job_post jp 
          JOIN company c ON jp.id_company = c.id_company 
          WHERE 1=1";

// Apply filters based on selected options
$params = [];
$currentYear = date('Y'); // Get the current year

if ($reportType == 'monthly' && $month) {
  // Append conditions to the query for monthly report with the current year
  $query .= " AND MONTH(jp.createdat) = ? AND YEAR(jp.createdat) = ?";
  // Add month and current year to the params array
  $params[] = $month;
  $params[] = $currentYear;
} elseif ($reportType == 'yearly' && $year) {
  // Append condition to the query for yearly report
  $query .= " AND YEAR(jp.createdat) = ?";
  // Add year to the params array
  $params[] = $year;
}

if ($location) {
  $query .= " AND location = ?";
  $params[] = $location;
}

if ($jobType) {
  $query .= " AND job_type = ?";
  $params[] = $jobType;
}

if ($category) {
  $query .= " AND category = ?";
  $params[] = $category;
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($params) {
  $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$totalCount = $result->num_rows; // Get the total count of results
//if ($result->num_rows == 0) {
  // Redirect to report.php with a no_results query parameter if no results are found
  //header("Location: report.php?no_results=true");
  //exit();
//}

// Get the current date and time
$reportDateTime = date('Y-m-d H:i:s');


// Determine the report heading based on the report type
if ($reportType == 'monthly') {
  $reportHeading = 'Monthly Jobs Report for ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $currentYear; //date('F', mktime(0, 0, 0, $month, 10)) returns the name of the month for the given month number
} elseif ($reportType == 'yearly') {
  $reportHeading = 'Annual Jobs Report for ' . htmlspecialchars($year);
} else {
  $reportHeading = 'Jobs Report';
}

// Generate the printable report
echo '<html><head><title>Jobs Report</title><style>
  table { width: 100%; border-collapse: collapse; }
  table, th, td { border: 1px solid black; }
  th, td { padding: 8px; text-align: left; }
  h3 { text-align: center; margin-top: 20px; }
  #report-date-time { position: absolute; top: 10px; left: 10px; padding-left: 10px; font-size: 12px; }
    @media print {
    @page { margin: 0; }

</style></head><body>';
echo '<div id="report-date-time"> Date and Time: '. htmlspecialchars($reportDateTime) . '</div>';
echo '<h3>'. htmlspecialchars($reportHeading) . '</h3>';
echo '<div id="total-count">Total Jobs: ' . $totalCount . '</div>';

if ($result->num_rows > 0) {
  echo '<table><thead><tr><th>Job Title</th><th>Company Name</th><th>Posted Date</th><th>Location</th><th>Job Type</th><th>Category</th></tr></thead><tbody>';

  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['jobtitle']) . '</td>';
    echo '<td>' . htmlspecialchars($row['companyname']) . '</td>';
    echo '<td>' . htmlspecialchars($row['createdat']) . '</td>';
    echo '<td>' . htmlspecialchars($row['location']) . '</td>';
    echo '<td>' . htmlspecialchars($row['job_type']) . '</td>';
    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
    echo '</tr>';
  }

  echo '</tbody></table>';
} else {
  echo '<p>No results found.</p>';
}

echo '<script>window.print();</script>';
echo '</body></html>';
?>
