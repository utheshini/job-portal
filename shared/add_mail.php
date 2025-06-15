<?php
session_start();
require_once("../db.php");

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Common inputs
    $to = intval($_POST['to']); // Ensure it's an integer
    $subject = trim($_POST['subject']);
    $message = trim($_POST['description']);

    // Input validation
    if (empty($to) || empty($subject) || empty($message)) {
        echo "All fields are required.";
        exit();
    }

    // Determine sender identity (candidate or company)
    if (!empty($_SESSION['id_candidate'])) {
        // Candidate sending message
        $idFromUser = $_SESSION['id_candidate'];
        $fromUser = 'user';
    } elseif (!empty($_SESSION['id_company'])) {
        // Company sending message
        $idFromUser = $_SESSION['id_company'];
        $fromUser = 'company';
    } else {
        // No valid session
        header("Location: ../login.php");
        exit();
    }

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO messages (from_user_id, from_user_type, to_user_id, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $idFromUser, $fromUser, $to, $subject, $message);

    if ($stmt->execute()) {
        header("Location: mailbox.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Redirect if accessed without POST
    header("Location: mailbox.php");
    exit();
}
?>
