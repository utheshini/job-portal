<?php

session_start();

require_once("../db.php");

if(isset($_SESSION['id_user']) && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['id_user'];

    // Check if the job is already saved by the user
    $check_query = "SELECT * FROM saved_jobs WHERE user_id = '$user_id' AND job_id = '$job_id'";
    $check_result = $conn->query($check_query);

    if($check_result->num_rows > 0) {
        // Job already saved
        $_SESSION['message'] = 'Job is already saved.';
    } else {
        // Insert into saved_jobs table
        $insert_query = "INSERT INTO saved_jobs (user_id, job_id) VALUES ('$user_id', '$job_id')";
        if($conn->query($insert_query) === TRUE) {
            $_SESSION['message'] = 'Job saved successfully.';
        } else {
            $_SESSION['message'] = 'Error: Unable to save job.';
        }
    }
} else {
    $_SESSION['message'] = 'Error: Unable to save job.';
}

// Redirect to the previous page
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'http://localhost/job/user/saved-jobs.php';
header("Location: $referrer");
exit();

?>
