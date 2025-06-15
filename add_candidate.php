<?php
session_start();
require_once("db.php");
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_candidate.php");
    exit();
}

// Sanitize and validate inputs
$firstname = trim($_POST['fname'] ?? '');
$lastname = trim($_POST['lname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$contactno = trim($_POST['contactno'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$age = trim($_POST['age'] ?? '');

if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
    $_SESSION['registerError'] = "Please fill in all required fields.";
    header("Location: register_candidate.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['registerError'] = "Invalid email address.";
    header("Location: register_candidate.php");
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT email FROM candidates WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $_SESSION['registerError'] = "Email already registered.";
    $stmt->close();
    header("Location: register_candidate.php");
    exit();
}
$stmt->close();

// Handle resume upload
$uploadOk = true;
$folder_dir = "uploads/resume/";
$file = '';

if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $fileInfo = $_FILES['resume'];
    $fileTmpPath = $fileInfo['tmp_name'];
    $fileName = basename($fileInfo['name']);
    $fileSize = $fileInfo['size'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileType !== 'pdf') {
        $_SESSION['uploadError'] = "Wrong format. Only PDF files allowed.";
        $uploadOk = false;
    }

    if ($fileSize > 5 * 1024 * 1024) { 
        $_SESSION['uploadError'] = "File size too large. Max 5MB allowed.";
        $uploadOk = false;
    }

    if ($uploadOk) {
        $file = uniqid('resume_', true) . '.' . $fileType;
        $destination = $folder_dir . $file;

        if (!move_uploaded_file($fileTmpPath, $destination)) {
            $_SESSION['uploadError'] = "Error uploading file.";
            $uploadOk = false;
        }
    }
} else {
    $_SESSION['uploadError'] = "No file uploaded or upload error.";
    $uploadOk = false;
}

if (!$uploadOk) {
    header("Location: register_candidate.php");
    exit();
}

// Hash password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate verification hash
$hashToken = bin2hex(random_bytes(16));

// Insert new candidate into database
$stmt = $conn->prepare("INSERT INTO candidates (first_name, last_name, email, password, contact_no, date_of_birth, age, resume, hash_token)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $firstname, $lastname, $email, $hashedPassword, $contactno, $dob, $age, $file, $hashToken);

if ($stmt->execute()) {
    // Send verification email
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jobseek@gmail.com';
        $mail->Password   = 'tszeptnxdbigglhp';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('jobseek@gmail.com', 'JobSeek');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'JobSeek - Confirm Your Email Address';
        $verifyLink = "http://localhost/job-portal/verify.php?token=$hashToken&email=" . urlencode($email);
        $mail->Body    = "
            <html>
            <head><title>Confirm Your Email</title></head>
            <body>
                <p>Click the link below to verify your email address:</p>
                <a href='$verifyLink'>$verifyLink</a>
            </body>
            </html>
        ";

        $mail->send();

        $_SESSION['registerCompleted'] = true;
        header("Location: register_candidate.php");
        exit();

    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        $_SESSION['registerError'] = "Could not send verification email. Please contact support.";
        header("Location: register_candidate.php");
        exit();
    }
} else {
    error_log("Database Insert Error: " . $stmt->error);
    $_SESSION['registerError'] = "Registration failed due to server error.";
    header("Location: register_candidate.php");
    exit();
}

$stmt->close();
$conn->close();
?>
