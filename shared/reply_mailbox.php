<?php
session_start(); 
require_once("../db.php"); 

// Initialize user variables
$userId = null;
$userType = null;

// Determine if the user is logged in as a candidate or company
if (!empty($_SESSION['id_candidate'])) {
    $userId = $_SESSION['id_candidate'];
    $userType = 'candidate';
} elseif (!empty($_SESSION['id_company'])) {
    $userId = $_SESSION['id_company'];
    $userType = 'company';
} else {
    // No valid session found; redirect to login page
    header("Location: ../login.php");
    exit();
}

// Process the form only if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if all required POST fields are set
    if (isset($_POST['to'], $_POST['id_mail'], $_POST['description'])) {

        // Sanitize and validate input values
        $to = intval($_POST['to']); // Ensure receiver ID is an integer
        $id_mail = intval($_POST['id_mail']); // Ensure mail ID is an integer
        $message = trim($_POST['description']); // Remove leading/trailing whitespace

        if (!empty($message)) {
            // Escape message string for safety
            $message = mysqli_real_escape_string($conn, $message);

            // Use prepared statement to safely insert reply into the database
            $stmt = $conn->prepare("
                INSERT INTO message_replies (message_id, from_user_id, from_user_type, to_user_id, reply_message)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iisis", $id_mail, $userId, $userType, $to, $message);

            // Execute query and handle response
            if ($stmt->execute()) {
                // Redirect back to the mail read page after successful reply
                header("Location: read_mail.php?id_mail=" . $id_mail);
                exit();
            } else {
                // Display error if insertion fails
                echo "Database Error: " . htmlspecialchars($stmt->error);
            }

            // Clean up statement
            $stmt->close();
        } else {
            // Message is empty
            echo "Reply message cannot be empty.";
        }
    } else {
        // Missing required form fields
        echo "Invalid form submission.";
    }
} else {
    // Redirect if accessed without POST method
    header("Location: mailbox.php");
    exit();
}
?>
