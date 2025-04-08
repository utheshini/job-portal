<?php
// check_new_jobs.php
require_once("../db.php");
session_start();

$userId = $_SESSION['id_user'];
$notificationCounts = getNotificationCounts($conn, $userId);

echo json_encode($notificationCounts);
?>
