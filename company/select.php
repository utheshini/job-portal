<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

// Validate GET parameters
if (!isset($_GET['id'], $_GET['id_jobpost']) || 
    !filter_var($_GET['id'], FILTER_VALIDATE_INT) || 
    !filter_var($_GET['id_jobpost'], FILTER_VALIDATE_INT)) {
    header("Location: index.php");
    exit();
}

$id_company = $_SESSION['id_company'];
$id_candidate = (int) $_GET['id'];
$id_jobpost = (int) $_GET['id_jobpost'];

// Check if application exists
$checkStmt = $conn->prepare("SELECT application_id FROM applications WHERE company_id = ? AND candidate_id = ? AND job_id = ?");
$checkStmt->bind_param("iii", $id_company, $id_candidate, $id_jobpost);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    header("Location: index.php");
    exit();
}
$checkStmt->close();

// Update status to 'selected'
$updateStmt = $conn->prepare("UPDATE applications SET status = 'selected' WHERE company_id = ? AND candidate_id = ? AND job_id = ?");
$updateStmt->bind_param("iii", $id_company, $id_candidate, $id_jobpost);

if ($updateStmt->execute()) {
    header("Location: job_applications.php");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}

$updateStmt->close();
$conn->close();
?>