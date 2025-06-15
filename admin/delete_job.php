<?php
session_start();

if (empty($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

require_once("../db.php");

if (isset($_GET['id'])) {
    $jobpostId = $_GET['id'];

    // Validate that id is a positive integer
    if (!filter_var($jobpostId, FILTER_VALIDATE_INT)) {
        die("Invalid job ID.");
    }

    // Prepare statements for deletion
    $conn->begin_transaction();

    try {
        // Delete from saved_jobs
        $stmt1 = $conn->prepare("DELETE FROM saved_jobs WHERE job_id = ?");
        $stmt1->bind_param("i", $jobpostId);
        $stmt1->execute();

        // Delete from applications
        $stmt2 = $conn->prepare("DELETE FROM applications WHERE job_id = ?");
        $stmt2->bind_param("i", $jobpostId);
        $stmt2->execute();

        // Delete from jobs
        $stmt3 = $conn->prepare("DELETE FROM jobs WHERE job_id = ?");
        $stmt3->bind_param("i", $jobpostId);
        $stmt3->execute();

        // Commit transaction if all queries succeed
        $conn->commit();

        // Redirect on success
        header("Location: manage_jobs.php");
        exit();

    } catch (Exception $e) {
        // Rollback on failure
        $conn->rollback();
        echo "Error deleting job post: " . $e->getMessage();
    }

} else {
    // id param not set
    echo "No job ID specified.";
}
?>
