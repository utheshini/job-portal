<?php
session_start();

if (empty($_SESSION['id_admin'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

// Retrieve POST parameters safely with null coalescing
$reportType = $_POST['user-report-type'] ?? '';
$month = $_POST['user-month'] ?? '';
$year = $_POST['user-year'] ?? '';
$userRole = $_POST['user-role'] ?? '';
$location = $_POST['employer-location'] ?? '';
$age = $_POST['age'] ?? '';

// Check if at least one filter is selected
if (!$reportType && !$month && !$year && !$location && !$userRole && !$age) {
    echo '<script>alert("No options selected. Please go back and select at least one option."); window.location.href = "report.php";</script>';
    exit();
}

// Base query and parameters initialization
$query = "";
$params = [];
$paramTypes = "";
$currentYear = date('Y');

// Determine base query depending on user role
if ($userRole === 'candidate') {
    $query = "SELECT ca.first_name, ca.last_name, ca.age, ca.email, ca.address, ca.contact_no, ca.education, ca.skills, ca.registered_date
              FROM candidates ca WHERE 1=1";
} elseif ($userRole === 'company') {
    $query = "SELECT c.company_name, c.account_holder_name, c.email, c.city, c.contact_no, c.website, c.created_date
              FROM companies c WHERE 1=1";
} else {
    echo '<script>alert("Invalid user role selected."); window.location.href = "report.php";</script>';
    exit();
}

// Append filters to the query based on input
if ($reportType === 'monthly' && $month) {
    if ($userRole === 'candidate') {
        $query .= " AND MONTH(ca.registered_date) = ? AND YEAR(ca.registered_date) = ?";
    } else {
        $query .= " AND MONTH(c.created_date) = ? AND YEAR(c.created_date) = ?";
    }
    $params[] = $month;
    $params[] = $currentYear;
    $paramTypes .= "ii";
} elseif ($reportType === 'yearly' && $year) {
    if ($userRole === 'candidate') {
        $query .= " AND YEAR(ca.registered_date) = ?";
    } else {
        $query .= " AND YEAR(c.created_date) = ?";
    }
    $params[] = $year;
    $paramTypes .= "i";
}

if ($userRole === 'company' && $location) {
    $query .= " AND c.city = ?";
    $params[] = $location;
    $paramTypes .= "s";
}

if ($userRole === 'candidate' && $age) {
    if ($age === '< 40') {
        $query .= " AND ca.age < ?";
        $params[] = 40;
        $paramTypes .= "i";
    } elseif ($age === '>= 40') {
        $query .= " AND ca.age >= ?";
        $params[] = 40;
        $paramTypes .= "i";
    }
}

// Prepare and execute count query
$countQuery = "SELECT COUNT(*) AS total FROM ($query) AS sub";
$countStmt = $conn->prepare($countQuery);
if ($paramTypes) {
    $countStmt->bind_param($paramTypes, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult->fetch_assoc()['total'];

// Prepare and execute main query
$stmt = $conn->prepare($query);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$reportDateTime = date('Y-m-d H:i:s');

// Set report heading
if ($reportType === 'monthly') {
    $reportHeading = 'Monthly Jobs Report for ' . date('F', mktime(0, 0, 0, (int)$month, 10)) . ' ' . $currentYear;
} elseif ($reportType === 'yearly') {
    $reportHeading = 'Annual ' . ucfirst($userRole) . ' Report for ' . htmlspecialchars($year);
} else {
    $reportHeading = ucfirst($userRole) . ' Report';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($reportHeading) ?></title>
<style>
  table { width: 100%; border-collapse: collapse; }
  table, th, td { border: 1px solid black; }
  th, td { padding: 8px; text-align: left; }
  h3 { text-align: center; margin-top: 20px; }
  #report-date-time { position: absolute; top: 10px; left: 10px; font-size: 12px; }
  @media print { @page { margin: 0; } }
</style>
</head>
<body>
<div id="report-date-time">Date and Time: <?= htmlspecialchars($reportDateTime) ?></div>
<h3><?= htmlspecialchars($reportHeading) ?></h3>
<p>Total Count: <?= htmlspecialchars($totalCount) ?></p>

<?php if ($result->num_rows > 0): ?>
<table>
  <thead>
    <tr>
      <?php if ($userRole === 'candidate'): ?>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Age</th>
        <th>Email</th>
        <th>Address</th>
        <th>Phone No</th>
        <th>Education</th>
        <th>Skills</th>
        <th>Registered Date</th>
      <?php else: /* company */ ?>
        <th>Company Name</th>
        <th>Account Creator Name</th>
        <th>Email</th>
        <th>Phone No</th>
        <th>Location</th>
        <th>Website</th>
        <th>Registered Date</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <?php if ($userRole === 'candidate'): ?>
          <td><?= htmlspecialchars($row['first_name']) ?></td>
          <td><?= htmlspecialchars($row['last_name']) ?></td>
          <td><?= htmlspecialchars($row['age']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td><?= htmlspecialchars($row['contact_no']) ?></td>
          <td><?= htmlspecialchars($row['education']) ?></td>
          <td><?= htmlspecialchars($row['skills']) ?></td>
          <td><?= htmlspecialchars($row['registered_date']) ?></td>
        <?php else: /* company */ ?>
          <td><?= htmlspecialchars($row['company_name']) ?></td>
          <td><?= htmlspecialchars($row['account_holder_name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['contact_no']) ?></td>
          <td><?= htmlspecialchars($row['city']) ?></td>
          <td><?= !empty($row['website']) ? htmlspecialchars($row['website']) : '-' ?></td>
          <td><?= htmlspecialchars($row['created_date']) ?></td>
        <?php endif; ?>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php else: ?>
  <p>No results found.</p>
<?php endif; ?>

<script>
  window.print();
</script>
</body>
</html>