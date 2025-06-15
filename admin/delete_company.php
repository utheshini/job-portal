<?php
session_start();

if(empty($_SESSION['id_admin'])) {
	header("Location:/login.php");
	exit();
}

require_once("../db.php");

// Check if 'id' is set and is a valid positive integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
	$companyId = $_GET['id'];

	// Use prepared statement to update
	$stmt = $conn->prepare("UPDATE companies SET active = 'deactivated' WHERE company_id = ?");
	$stmt->bind_param("i", $companyId);

	if ($stmt->execute()) {
		header("Location: manage_companies.php");
		exit();
	} else {
		echo "Error deactivating company: " . $conn->error;
	}

	$stmt->close();
} else {
	echo "Invalid or missing company ID.";
}
?>