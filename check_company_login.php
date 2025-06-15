<?php
session_start();
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch company by email
    $stmt = $conn->prepare("SELECT company_id, company_name, email, password, active
                            FROM companies WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Check account status
        if ($row['active'] === "pending") {
            $_SESSION['companyLoginError'] = "Your account is still pending approval.";
            header("Location: login_company.php");
            exit();
        } elseif ($row['active'] === "rejected") {
            $_SESSION['companyLoginError'] = "Your account was rejected. Please contact support.";
            header("Location: login_company.php");
            exit();
        } elseif ($row['active'] === "deactivated") {
            $_SESSION['companyLoginError'] = "Your account is deactivated. Contact admin for reactivation.";
            header("Location: login_company.php");
            exit();
        } elseif ($row['active'] === "approved") {
            // Verify password
            if (password_verify($password, $row['password'])) {
                $_SESSION['name'] = $row['company_name'];
                $_SESSION['id_company'] = $row['company_id'];
                header("Location: company/index.php");
                exit();
            } else {
                $_SESSION['companyLoginError'] = "Invalid email or password.";
                header("Location: login_company.php");
                exit();
            }
        }
    } else {
        $_SESSION['companyLoginError'] = "Invalid email or password.";
        header("Location: login_company.php");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: login_company.php");
    exit();
}
?>