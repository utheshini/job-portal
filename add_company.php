<?php
session_start();
require_once("db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_company.php");
    exit();
}

// Validate and sanitize input
$name         = trim($_POST['name']);
$companyname  = trim($_POST['companyname']);
$city         = trim($_POST['city']);
$contactno    = trim($_POST['contactno']);
$website      = trim($_POST['website']);
$email        = trim($_POST['email']);
$password     = $_POST['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['uploadError'] = "Invalid email format.";
    header("Location: register_company.php");
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT email FROM companies WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['registerError'] = "Email already registered.";
    $stmt->close();
    header("Location: register_company.php");
    exit();
}
$stmt->close();

// Handle file upload
$uploadOk = true;
$folder_dir = "uploads/logo/";
$file = "";

if (!empty($_FILES['image']['tmp_name'])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['uploadError'] = "Uploaded file is not an image.";
        $uploadOk = false;
    }

    $base = basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($base, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png"];

    if (!in_array($imageFileType, $allowedTypes)) {
        $_SESSION['uploadError'] = "Only JPG, JPEG, PNG files are allowed.";
        $uploadOk = false;
    }

    if ($_FILES['image']['size'] > 500000) { 
        $_SESSION['uploadError'] = "Image too large. Max 500KB allowed.";
        $uploadOk = false;
    }

    if ($uploadOk) {
        $file = uniqid() . "." . $imageFileType;
        $filename = $folder_dir . $file;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $filename)) {
            $_SESSION['uploadError'] = "Failed to upload file.";
            $uploadOk = false;
        }
    }

    if (!$uploadOk) {
        header("Location: register_company.php");
        exit();
    }
}

// Hash password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new company into database
$stmt = $conn->prepare("INSERT INTO companies (account_holder_name, company_name, city, contact_no, website, email, password, logo)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssss", $name, $companyname, $city, $contactno, $website, $email, $hashedPassword, $file);

if ($stmt->execute()) {
    $_SESSION['registerCompleted'] = true;
    header("Location: login_company.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>