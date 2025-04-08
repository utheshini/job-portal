<?php

session_start();

if(empty($_SESSION['id_admin'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

if(isset($_GET)) {
    $jobpostId = $_GET['id'];

    // Delete related entries in saved_jobs table
    $sqlSavedJobs = "DELETE FROM saved_jobs WHERE job_id='$jobpostId'";
    $conn->query($sqlSavedJobs);

    // Delete related entries in apply_job_post table
    $sqlApplyJobPost = "DELETE FROM apply_job_post WHERE id_jobpost='$jobpostId'";
    $conn->query($sqlApplyJobPost);

    // Delete the job post itself
    $sqlJobPost = "DELETE FROM job_post WHERE id_jobpost='$jobpostId'";
    if($conn->query($sqlJobPost)) {
        header("Location: active-jobs.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
