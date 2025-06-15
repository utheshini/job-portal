<?php
session_start();
require_once("../db.php");

if (empty($_SESSION['id_candidate'])) {
    header("Location: ../login.php");
    exit();
}

// Check if job ID is set via POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // First verify the saved job belongs to the current user
    $verifySql = "SELECT saved_job_id FROM saved_jobs WHERE saved_job_id = ? AND candidate_id = ?";
    $verifyStmt = $conn->prepare($verifySql);
    $verifyStmt->bind_param("ii", $id, $_SESSION['id_candidate']);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();

    if ($verifyResult->num_rows === 1) {
        // Proceed to delete the saved job
        $deleteSql = "DELETE FROM saved_jobs WHERE saved_job_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            header("Location: saved_jobs.php?status=success");
            exit();
        } else {
            // Optional: Log the error in real-world apps
            header("Location: saved_jobs.php?status=error");
            exit();
        }
    } else {
        // Unauthorized delete attempt or invalid ID
        header("Location: saved_jobs.php?status=unauthorized");
        exit();
    }
} else {
    // No ID provided
    header("Location: saved_jobs.php");
    exit();
}
?>
