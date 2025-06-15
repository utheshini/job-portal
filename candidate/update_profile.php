<?php
session_start();

if (empty($_SESSION['id_candidate'])) {
    header("Location: ../index.php");
    exit();
}

// Include database connection
require_once("../db.php");

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize user inputs to prevent SQL injection
    $firstname   = mysqli_real_escape_string($conn, $_POST['fname']);
    $lastname    = mysqli_real_escape_string($conn, $_POST['lname']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $contactno   = mysqli_real_escape_string($conn, $_POST['contactno']);
    $aboutme     = mysqli_real_escape_string($conn, $_POST['aboutme']);
    $education   = mysqli_real_escape_string($conn, $_POST['education']);
    $experience  = mysqli_real_escape_string($conn, $_POST['experience']);
    $skills      = mysqli_real_escape_string($conn, $_POST['skills']);

    // Get current resume file name from database
    $stmt = $conn->prepare("SELECT resume FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $_SESSION['id_candidate']);
    $stmt->execute();
    $stmt->bind_result($currentResume);
    $stmt->fetch();
    $stmt->close();

    $uploadOk = false; // Flag to track successful upload
    $file = $currentResume; // Default to current resume

    // Check if a new resume was uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {

        $folder_dir = "../uploads/resume/";
        $base = basename($_FILES['resume']['name']); 
        $resumeFileType = strtolower(pathinfo($base, PATHINFO_EXTENSION)); 
        $file = uniqid() . "." . $resumeFileType;  
        $filename = $folder_dir . $file;

        // Allow only PDF files under 5MB
        if ($resumeFileType === "pdf") {
            if ($_FILES['resume']['size'] < 5000000) {
                if (move_uploaded_file($_FILES["resume"]["tmp_name"], $filename)) {
                    $uploadOk = true;
                } else {
                    $_SESSION['uploadError'] = "Error uploading the file.";
                    header("Location: edit_profile.php");
                    exit();
                }
            } else {
                $_SESSION['uploadError'] = "File too large. Max size: 5MB.";
                header("Location: edit_profile.php");
                exit();
            }
        } else {
            $_SESSION['uploadError'] = "Invalid file format. Only PDF is allowed.";
            header("Location: edit_profile.php");
            exit();
        }
    }

    // Prepare update statement using prepared statements for security
    $query = "UPDATE candidates SET first_name=?, last_name=?, address=?, contact_no=?, about_me=?, education=?, experience=?, skills=?";
    if ($uploadOk) {
        $query .= ", resume=?";
    }
    $query .= " WHERE candidate_id=?";

    $stmt = $conn->prepare($query);

    // Bind parameters based on whether a new resume was uploaded
    if ($uploadOk) {
        $stmt->bind_param(
            "sssssssssi",
            $firstname,
            $lastname,
            $address,
            $contactno,
            $aboutme,
            $education,
            $experience,
            $skills,
            $file,
            $_SESSION['id_candidate']
        );
    } else {
        $stmt->bind_param(
            "ssssssssi",
            $firstname,
            $lastname,
            $address,
            $contactno,
            $aboutme,
            $education,
            $experience,
            $skills,
            $_SESSION['id_candidate']
        );
    }

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['updateSuccess'] = true;
        header("Location: edit_profile.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Redirect to edit profile page if accessed without POST
    header("Location: edit_profile.php");
    exit();
}
?>