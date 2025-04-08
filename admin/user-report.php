<?php
session_start();

if (empty($_SESSION['id_admin'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

$reportType = $_POST['user-report-type'] ?? '';
$month = $_POST['user-month'] ?? '';
$year = $_POST['user-year'] ?? '';
$userRole = $_POST['user-role'] ?? '';
$location = $_POST['employer-location'] ?? '';
$age = $_POST['age'] ?? '';

// Check if any filters have been applied
$hasFilters = $reportType || $month || $year || $location || $userRole || $age;

if (!$hasFilters) {
  echo '<script>alert("No options selected. Please go back and select at least one option."); window.location.href = "report.php";</script>';
  exit();
}

// Initialize the SQL query
$query = "";
$params = [];
$paramTypes = ""; // Will be used to specify the types for bind_param
$currentYear = date('Y'); // Get the current year

if ($userRole === 'job-seeker') {
  $query = "SELECT u.firstname, u.lastname, u.age, u.email, u.address, u.contactno,  u.education, u.skills, u.date
            FROM users u WHERE 1=1";
} elseif ($userRole === 'employer') {
  $query = "SELECT c.companyname, c.name, c.email, c.city, c.contactno, c.website, c.createdAt
            FROM company c WHERE 1=1";
} else {
  echo '<script>alert("Invalid user role selected."); window.location.href = "report.php";</script>';
  exit();
}

if ($reportType == 'monthly' && $month) {
    if ($userRole === 'job-seeker') {
      $query .= " AND MONTH(u.date) = ? AND YEAR(u.date) = ?";
      $params[] = $month;
      $params[] = $currentYear;
      $paramTypes .= "ii";
    } elseif ($userRole === 'employer') {
      $query .= " AND MONTH(c.createdAt) = ? AND YEAR(c.createdAt) = ?";
      $params[] = $month;
      $params[] = $currentYear;
      $paramTypes .= "ii";
    }
} elseif ($reportType == 'yearly' && $year) {
    if ($userRole === 'job-seeker') {
      $query .= " AND YEAR(u.date) = ?";
      $params[] = $year;
      $paramTypes .= "i";
    } elseif ($userRole === 'employer') {
      $query .= " AND YEAR(c.createdAt) = ?";
      $params[] = $year;
      $paramTypes .= "i";
    }
}

if ($userRole === 'employer' && $location) {
    $query .= " AND c.city = ?";
    $params[] = $location;
    $paramTypes .= "s"; // assuming city is a string
}

if ($userRole === 'job-seeker' && $age) {
    if ($age === '< 40') {
        $query .= " AND u.age < ?";
        $params[] = 40;
        $paramTypes .= "i";
    } elseif ($age === '>= 40') {
        $query .= " AND u.age >= ?";
        $params[] = 40;
        $paramTypes .= "i";
    }
}

// Prepare and execute the query to count total results
$countQuery = "SELECT COUNT(*) AS total FROM (" . $query . ") AS sub";
$countStmt = $conn->prepare($countQuery);
if ($paramTypes) {
  $countStmt->bind_param($paramTypes, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult->fetch_assoc()['total'];

// Prepare and execute the query
$stmt = $conn->prepare($query);
if ($paramTypes) {
  $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get the current date and time
$reportDateTime = date('Y-m-d H:i:s');

// Determine the report heading based on the report type
if ($reportType == 'monthly') {
  $reportHeading = 'Monthly ' . ucfirst(str_replace('-', ' ', $userRole)) . ' Report for ' . date('F', mktime(0, 0, 0, $month, 10)) . ' ' . $currentYear;
} elseif ($reportType == 'yearly') {
  $reportHeading = 'Annual ' . ucfirst(str_replace('-', ' ', $userRole)) . ' Report for ' . htmlspecialchars($year);
} else {
  $reportHeading = ucfirst(str_replace('-', ' ', $userRole)) . ' Report';
}

// Generate the printable report
echo '<html><head><title>' . htmlspecialchars($reportHeading) . '</title><style>
  table { width: 100%; border-collapse: collapse; }
  table, th, td { border: 1px solid black; }
  th, td { padding: 8px; text-align: left; }
  h3 { text-align: center; margin-top: 20px; }
  #report-date-time { position: absolute; top: 10px; left: 10px; padding-left: 10px; font-size: 12px; }
  @media print { @page { margin: 0; } }
</style></head><body>';
echo '<div id="report-date-time">Date and Time: ' . htmlspecialchars($reportDateTime) . '</div>';
echo '<h3>' . htmlspecialchars($reportHeading) . '</h3>';
echo '<p>Total Count: ' . htmlspecialchars($totalCount) . '</p>';

if ($result->num_rows > 0) {
  echo '<table><thead><tr>';
  if ($userRole === 'job-seeker') {
    echo '<th>First Name</th><th>Last Name</th><th>Email</th><th>Address</th><th>Phone No</th><th>Education</th><th>skills</th><th>Registered Date</th>';
  } elseif ($userRole === 'employer') {
    echo '<th>Company Name</th><th>Account Creator Name</th><th>Email</th><th>Phone No</th><th>Location</th><th>Website</th><th>Registered Date</th>';
  }
  echo '</tr></thead><tbody>';

  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    if ($userRole === 'job-seeker') {
      echo '<td>' . htmlspecialchars($row['firstname']) . '</td>';
      echo '<td>' . htmlspecialchars($row['lastname']) . '</td>';
      echo '<td>' . htmlspecialchars($row['age']) . '</td>';
      echo '<td>' . htmlspecialchars($row['email']) . '</td>';
      echo '<td>' . htmlspecialchars($row['address']) . '</td>';
      echo '<td>' . htmlspecialchars($row['contactno']) . '</td>';
      echo '<td>' . htmlspecialchars($row['education']) . '</td>';
      echo '<td>' . htmlspecialchars($row['skills']) . '</td>';
      echo '<td>' . htmlspecialchars($row['date']) . '</td>';
    } elseif ($userRole === 'employer') {
      echo '<td>' . htmlspecialchars($row['companyname']) . '</td>';
      echo '<td>' . htmlspecialchars($row['name']) . '</td>';
      echo '<td>' . htmlspecialchars($row['email']) . '</td>';
      echo '<td>' . htmlspecialchars($row['contactno']) . '</td>';
      echo '<td>' . htmlspecialchars($row['city']) . '</td>';
      echo '<td>' . (!empty($row['website']) ? htmlspecialchars($row['website']) : '-') . '</td>';
      echo '<td>' . htmlspecialchars($row['createdAt']) . '</td>';
    }
    echo '</tr>';
  }

  echo '</tbody></table>';
} else {
  echo '<p>No results found.</p>';
}

echo '<script>window.print();</script>';
echo '</body></html>';
?>
