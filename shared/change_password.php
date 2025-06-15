<?php
session_start();
require_once("../db.php");

// Determine user type and session info
if (!empty($_SESSION['id_admin'])) {
    $userId = $_SESSION['id_admin'];
    $table = 'admin';
    $idColumn = 'admin_id';
    $redirectTo = 'shared/settings.php';
    $loginPage = '../admin/login.php';
} elseif (!empty($_SESSION['id_company'])) {
    $userId = $_SESSION['id_company'];
    $table = 'companies';
    $idColumn = 'company_id';
    $redirectTo = 'shared/settings.php';
    $loginPage = '../login_company.php';
} elseif (!empty($_SESSION['id_candidate'])) {
    $userId = $_SESSION['id_candidate'];
    $table = 'candidates';
    $idColumn = 'candidate_id';
    $redirectTo = 'shared/settings.php';
    $loginPage = '../login_candidate.php';
} else {
    // If no valid session, redirect to generic login
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];

    // Server-side password strength validation example
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        // Redirect back with error message or handle appropriately
        header("Location: $redirectTo?error=weak_password");
        exit();
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare statement to update password
    $stmt = $conn->prepare("UPDATE $table SET password=? WHERE $idColumn=?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        header("Location: $redirectTo?error=server_error");
        exit();
    }

    $stmt->bind_param("si", $hashedPassword, $userId);

    if ($stmt->execute()) {
        // On success, logout user by destroying the session
        session_unset();
        session_destroy();

        // Redirect to login page with success message
        header("Location: $loginPage?password_changed=success");
        exit();
    } else {
        error_log("Execute failed: " . $stmt->error);
        header("Location: $redirectTo?error=server_error");
        exit();
    }
} else {
    header("Location: $redirectTo");
    exit();
}
?>
