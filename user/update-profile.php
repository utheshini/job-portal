<?php

// To Handle Session Variables on This Page
session_start();

if(empty($_SESSION['id_user'])) {
  header("Location: ../index.php");
  exit();
}

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

// If user actually clicked update profile button
if(isset($_POST)) {

    // Escape Special Characters
    $firstname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lname']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contactno = mysqli_real_escape_string($conn, $_POST['contactno']);
    $aboutme = mysqli_real_escape_string($conn, $_POST['aboutme']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);

    // Fetch the current resume filename from the database
    $stmt = $conn->prepare("SELECT resume FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $_SESSION['id_user']);
    $stmt->execute();
    $stmt->bind_result($currentResume);
    $stmt->fetch();
    $stmt->close();

    // Initialize upload flag to true
    $uploadOk = false;

    // Check if a file was uploaded
    if(isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {

        // Set the directory for uploads
        $folder_dir = "../uploads/resume/";
        // Get the base name of the uploaded file
        $base = basename($_FILES['resume']['name']); 
        // Extract the file extension
        $resumeFileType = pathinfo($base, PATHINFO_EXTENSION); 
        // Generate a unique file name to avoid conflicts
        $file = uniqid() . "." . $resumeFileType;  
        // Full path for the uploaded file 
        $filename = $folder_dir . $file;  

        // Validate the file type (only PDFs allowed)
        if($resumeFileType == "pdf")  {
            // Validate the file size (less than 5MB)	
            if($_FILES['resume']['size'] < 5000000) { // File size is less than 5MB
                // Move the file to the target directory
                if (move_uploaded_file($_FILES["resume"]["tmp_name"], $filename)) {
                    $uploadOk = true;
                } else {
                    $_SESSION['uploadError'] = "Error uploading the file.";
                    header("Location: edit-profile.php");
                    exit();
                }
            } else {
                $_SESSION['uploadError'] = "Wrong Size. Max Size Allowed: 5MB";
                header("Location: edit-profile.php");
                exit();
            }
        } else {
            $_SESSION['uploadError'] = "Wrong Format. Only PDF Allowed";
            header("Location: edit-profile.php");
            exit();
        }
    } else {
        // No file was uploaded, use the current resume
        $file = $currentResume;
    }

    // Update User Details Query
    $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', address='$address', contactno='$contactno', aboutme='$aboutme', education='$education', experience='$experience', skills='$skills'";

    // If a new file was successfully uploaded, include it in the update query
    if($uploadOk == true) {
        $sql .= ", resume='$file'";
    }

    // Add condition to update the specific user
    $sql .= " WHERE id_user='$_SESSION[id_user]'";

    // Execute the update query
    if($conn->query($sql) === TRUE) {
        $_SESSION['updateSuccess'] = true;
        header("Location: edit-profile.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close database connection. Not compulsory but good practice.
    $conn->close();

} else {
    // Redirect them back to the edit profile page if they didn't click the update button
    header("Location: edit-profile.php");
    exit();
}
