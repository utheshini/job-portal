<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

// Proceed only if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = []; // Array to collect validation errors

    // Sanitize and assign form input values
    $jobtitle = trim($_POST['jobtitle']);
    $description = trim($_POST['description']);
    $minimumsalary = (float)$_POST['minimumsalary'];
    $maximumsalary = (float)$_POST['maximumsalary'];
    $experience = trim($_POST['experience']);
    $job_type = trim($_POST['job_type']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $deadline = $_POST['deadline'];
    $maxage = (int)$_POST['maxage'];

    // ---------- Server-Side Validations ----------

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

    // ---------- Handle Validation Errors ----------

    if (!empty($errors)) {
        // Display error messages
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul></div>';

    } else {
        // ---------- Insert into Database Safely ----------

        // Prepare SQL statement with placeholders (prevents SQL injection)
        $stmt = $conn->prepare("INSERT INTO jobs(
            company_id, job_title, job_description, min_salary, max_salary,
            experience, job_type, category, deadline, location, max_age
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the statement was prepared correctly
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        // Bind parameters (i = integer, s = string)
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

        // Execute the statement and handle the result
        if ($stmt->execute()) {
            $_SESSION['jobPostSuccess'] = true; // Set success message
            header("Location: my_job_postings.php"); // Redirect on success
            exit();
        } else {
            // Output error on failure
            echo "Error: " . htmlspecialchars($stmt->error);
        }

        // Close prepared statement and DB connection
        $stmt->close();
        $conn->close();
    }

} else {
    // If accessed without POST request, redirect to form
    header("Location: create_job.php");
    exit();
}
?>