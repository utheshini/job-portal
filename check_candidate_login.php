<?php
session_start();
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize email and password input
    $email = trim($_POST['email']);
    $password = $_POST['password']; 

    // Fetch candidate by email
    $stmt = $conn->prepare("SELECT candidate_id, first_name, last_name, email, password, active
                            FROM candidates WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Check account status
        if ($row['active'] === '0') {
            $_SESSION['loginActiveError'] = "Your account is not active. Check your email.";
            header("Location: login_candidate.php");
            exit();
        } elseif ($row['active'] === '2') {
            $_SESSION['loginActiveError'] = "Your account is deactivated.";
            header("Location: index.php");
            exit();
        }

        // Verify password using password_verify
        if (password_verify($password, $row['password'])) {
            // Login successful - set session
            $_SESSION['name'] = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['id_candidate'] = $row['candidate_id'];
            header("Location: candidate/index.php");
            exit();
        } else {
            $_SESSION['loginError'] = "Invalid email or password.";
            header("Location: login_candidate.php");
            exit();
        }
    } else {
        $_SESSION['loginError'] = "Invalid email or password.";
        header("Location: login_candidate.php");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // Invalid request method
    header("Location: login_candidate.php");
    exit();
}
?>