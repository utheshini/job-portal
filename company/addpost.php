<?php

// To Handle Session Variables on This Page
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../index.php");
    exit();
}

// Including Database Connection From db.php file to avoid rewriting in all files
require_once("../db.php");

// If user Actually clicked Add Post Button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Initialize an array to hold error messages
    $errors = [];

    // Assign values to variables
    $jobtitle = $_POST['jobtitle'];
    $description = trim($_POST['description']);
    $minimumsalary = (float)$_POST['minimumsalary'];
    $maximumsalary = (float)$_POST['maximumsalary'];
    $experience = $_POST['experience'];
    $job_type = $_POST['job_type'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $deadline = $_POST['deadline'];
    $maxage = (int)$_POST['maxage'];

    // Server-side validation
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if ($maximumsalary <= $minimumsalary) {
        $errors[] = "Maximum salary must be greater than minimum salary.";
    }

    if ($maxage < 18) {
        $errors[] = "Age should be above 18.";
    }

    $today = date("Y-m-d");
    if ($deadline <= $today) {
        $errors[] = "The deadline must be a future date.";
    }

    // If there are errors, display them and stop execution
    if (!empty($errors)) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul></div>';
    } else {
        // Proceed with database insertion if no errors

        // New way using prepared statements. This is safe from SQL INJECTION.
        $stmt = $conn->prepare("INSERT INTO job_post(id_company, jobtitle, description, minimumsalary, maximumsalary, experience, job_type, category, deadline, location, maxage) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if prepare statement was successful
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind parameters: 'i' for integer, 's' for string
        $stmt->bind_param(
            "issssssssss", 
            $_SESSION['id_company'], 
            $jobtitle, 
            $description, 
            $minimumsalary, 
            $maximumsalary, 
            $experience, 
            $job_type, 
            $category, 
            $deadline, 
            $location, 
            $maxage
        );

        // Execute the statement
        if ($stmt->execute()) {
            // If data Inserted successfully then redirect to dashboard
            $_SESSION['jobPostSuccess'] = true;
            header("Location: my-job-post.php");
            exit();
        } else {
            // If data failed to insert then show that error.
            echo "Error: " . htmlspecialchars($stmt->error);
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }

} else {
    // Redirect them back to dashboard page if they didn't click Add Post button
    header("Location: create-job-post.php");
    exit();
}
