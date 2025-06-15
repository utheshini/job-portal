<?php
session_start();

if(empty($_SESSION['id_admin'])) {
	header("Location:/login.php");
	exit();
}

require_once("../db.php");

// Validate GET request and ID
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
	$companyId = $_GET['id'];

	// Use prepared statement to avoid SQL injection
	$stmt = $conn->prepare("UPDATE companies SET active = 'approved' WHERE company_id = ?");
	$stmt->bind_param("i", $companyId);

	if ($stmt->execute()) {
		header("Location: manage_companies.php");
		exit();
	} else {
		echo "Error approving company: " . $conn->error;
	}

	$stmt->close();
} else {
	echo "Invalid or missing company ID.";
}
?>