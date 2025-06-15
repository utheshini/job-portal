<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

// Proceed only if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input fields
    $companyname = trim($_POST['companyname']);
    $website     = trim($_POST['website']);
    $city        = trim($_POST['city']);
    $contactno   = trim($_POST['contactno']);
    $aboutme     = trim($_POST['aboutme']);
    $address     = trim($_POST['address']);

    $uploadOk = false;
    $file = "";

    // Handle image upload
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
        $folder_dir = "../uploads/logo/";
        $base = basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($base, PATHINFO_EXTENSION));
        $file = uniqid() . "." . $imageFileType;
        $filename = $folder_dir . $file;

        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($imageFileType, $allowedTypes)) {
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $filename)) {
                    $uploadOk = true;
                } else {
                    $_SESSION['uploadError'] = "Failed to upload image.";
                    header("Location: edit_company_profile.php");
                    exit();
                }
            } else {
                $_SESSION['uploadError'] = "Wrong Size. Max Size Allowed: 5MB";
                header("Location: edit_company_profile.php");
                exit();
            }
        } else {
            $_SESSION['uploadError'] = "Wrong Format. Only jpg, jpeg & png allowed.";
            header("Location: edit_company_profile.php");
            exit();
        }
    }

    // Build query with or without logo
    if ($uploadOk) {
        $sql = "UPDATE companies 
                SET company_name = ?, website = ?, city = ?, contact_no = ?, about_company = ?, address = ?, logo = ?
                WHERE company_id = ?";
    } else {
        $sql = "UPDATE companies 
                SET company_name = ?, website = ?, city = ?, contact_no = ?, about_company = ?, address = ?
                WHERE company_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($uploadOk) {
            $stmt->bind_param("sssssssi", $companyname, $website, $city, $contactno, $aboutme, $address, $file, $_SESSION['id_company']);
        } else {
            $stmt->bind_param("ssssssi", $companyname, $website, $city, $contactno, $aboutme, $address, $_SESSION['id_company']);
        }

        if ($stmt->execute()) {
            $_SESSION['updateSuccess'] = true;
            header("Location: edit_company_profile.php");
            exit();
        } else {
            // Log error (in production) or echo during development
            error_log("MySQL Execute Error: " . $stmt->error);
            echo "Error updating profile. Please try again.";
        }

        $stmt->close();
    } else {
        error_log("MySQL Prepare Error: " . $conn->error);
        echo "Error preparing statement.";
    }

    $conn->close();

} else {
    // Redirect if the form wasn't submitted via POST
    header("Location: edit_company_profile.php");
    exit();
}
?>
