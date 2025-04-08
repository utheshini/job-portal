<?php
session_start();
require_once("db.php");

if(isset($_GET['token']) && isset($_GET['email'])) {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Check if the provided token and email match a record in the database
    $sql = "SELECT * FROM users WHERE email='$email' AND hash='$token'";
    $result = $conn->query($sql);

    if($result->num_rows == 1) {
        // Update the user's status to verified
        $sql_update = "UPDATE users SET active = 1 WHERE email='$email'";
        if($conn->query($sql_update) === TRUE) {
            $_SESSION['verificationSuccess'] = true;
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['verificationError'] = true;
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['verificationError'] = true;
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['verificationError'] = true;
    header("Location: login.php");
    exit();
}
?>
