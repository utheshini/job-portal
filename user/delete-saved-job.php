<?php
session_start();
require_once("../db.php");

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM saved_jobs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        // Redirect back to saved jobs page with a success message
        header("Location: saved-jobs.php?status=success");
    } else {
        // Redirect back with an error message
        header("Location: saved-jobs.php?status=error");
    }
} else {
    header("Location: saved-jobs.php");
}
?>
