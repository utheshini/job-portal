<?php
session_start();
require_once("../db.php");

// Ensure user is logged in and job_id is posted
if (isset($_SESSION['id_candidate']) && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['id_candidate'];

    // Use prepared statements to avoid SQL injection
    $check_sql = "SELECT saved_job_id FROM saved_jobs WHERE candidate_id = ? AND job_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $job_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = 'Job is already saved.';
    } else {
        $insert_sql = "INSERT INTO saved_jobs (candidate_id, job_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $job_id);

        if ($insert_stmt->execute()) {
            $_SESSION['message'] = 'Job saved successfully.';
        } else {
            // Optional: log error details securely
            $_SESSION['message'] = 'Error: Unable to save job.';
        }
    }
} else {
    $_SESSION['message'] = 'Error: Unable to save job.';
}

// Redirect to the referring page or fallback
header("Location: saved_jobs.php");
exit();

?>