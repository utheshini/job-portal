<?php
session_start();
require_once("../db.php");

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Don't hash here — need to verify with DB hash

    // Prepare a secure SQL statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password using password_verify
        if (password_verify($password, $row['password'])) {
            // Set session variable for authenticated admin
            $_SESSION['id_admin'] = $row['admin_id'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['loginError'] = true;
            header("Location: ../index.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['loginError'] = true;
        header("Location: ../index.php");
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

} else {
    // If accessed without form submission, redirect to login page
    header("Location: ../index.php");
    exit();
}
?>