<?php
session_start();

// Redirect to login if candidates is not authenticated
if (empty($_SESSION['id_candidate'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../db.php");

// Redirect to jobs page if 'id' is not set or is not a valid number.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../jobs.php");
    exit();
}

$jobId = intval($_GET['id']);
$userId = $_SESSION['id_candidate'];

// Get job post to find associated company
$stmt = $conn->prepare("SELECT company_id FROM jobs WHERE job_id = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: jobs.php");
    exit();
}

$row = $result->fetch_assoc();
$id_company = $row['company_id'];

// Check if already applied
$stmt = $conn->prepare("SELECT 1 FROM applications WHERE candidate_id = ? AND job_id = ?");
$stmt->bind_param("ii", $userId, $jobId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Apply to job
    $stmt = $conn->prepare("INSERT INTO applications (job_id, company_id, candidate_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $jobId, $id_company, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['jobApplySuccess'] = true;
        header("Location: index.php");
        exit();
    } else {
        echo "Database error: " . $stmt->error;
    }
} else {
    header("Location: ../jobs.php");
    exit();
}
?>
