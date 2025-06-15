<?php
session_start();

if(empty($_SESSION['id_admin'])) {
  header("Location: login.php");
  exit();
}

require_once("../db.php");

// Check if 'id' is set and is a valid positive integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $candidateId = $_GET['id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE candidates SET active = 2 WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidateId);

    if ($stmt->execute()) {
        header("Location: manage_candidates.php");
        exit();
    } else {
        echo "Error deleting candidate: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid or missing candidate ID.";
}
?>
