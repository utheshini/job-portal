<?php
session_start();
require_once("db.php");

// Check if the token and email are present in the URL
if (isset($_GET['token']) && isset($_GET['email'])) {
    // Sanitize input
    $token = htmlspecialchars(trim($_GET['token']));
    $email = filter_var(trim($_GET['email']), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['verificationError'] = true;
        header("Location: login_candidate.php");
        exit();
    }

    // Fetch candidate details securely using email and token for verification.
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE email = ? AND hash_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching candidate was found
    if ($result->num_rows === 1) {
        // Update candidate's status to active (verified)
        $update_stmt = $conn->prepare("UPDATE candidates SET active = 1 WHERE email = ?");
        $update_stmt->bind_param("s", $email);

        if ($update_stmt->execute()) {
            $_SESSION['verificationSuccess'] = true;
        } else {
            $_SESSION['verificationError'] = true;
        }

        $update_stmt->close();
        header("Location: login_candidate.php");
        exit();
    } else {
        $_SESSION['verificationError'] = true;
        header("Location: login_candidate.php");
        exit();
    }

    $stmt->close();
} else {
    // Redirect if required parameters are missing
    $_SESSION['verificationError'] = true;
    header("Location: login_candidate.php");
    exit();
}
?>
