<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input to prevent SQL injection and trim whitespace
    $name = trim($_POST['name']);

    // Check if name is not empty
    if (!empty($name)) {
        // Use prepared statement for security
        $stmt = $conn->prepare("UPDATE companies SET account_holder_name = ? WHERE company_id = ?");
        $stmt->bind_param("si", $name, $_SESSION['id_company']);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Redirect to index or dashboard on success
            header("Location: index.php");
            exit();
        } else {
            // Output database error (optional for debugging; remove in production)
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Redirect back with an error if the input was empty
        header("Location: settings.php?error=empty_name");
        exit();
    }

    // Close the database connection
    $conn->close();

} else {
    // Redirect to settings page if the form was not submitted properly
    header("Location: ../shared/settings.php");
    exit();
}
?>
