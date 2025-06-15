<?php
session_start();

if (empty($_SESSION['id_admin'])) {
	header("Location: login.php");
	exit();
}

require_once("../db.php");

// Check if 'id' is provided and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
	$companyId = $_GET['id'];

	// Use prepared statement to prevent SQL injection
	$stmt = $conn->prepare("UPDATE companies SET active = 'rejected' WHERE company_id = ?");
	$stmt->bind_param("i", $companyId);

	if ($stmt->execute()) {
		header("Location: manage_companies.php");
		exit();
	} else {
		echo "Error rejecting company: " . $conn->error;
	}

	$stmt->close();
} else {
	echo "Invalid or missing company ID.";
}
?>