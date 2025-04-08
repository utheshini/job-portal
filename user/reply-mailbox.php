<?php


session_start();

if(empty($_SESSION['id_user'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");

if(isset($_POST)) {
	$to  = $_POST['to'];

	$message = mysqli_real_escape_string($conn, $_POST['description']);

	$sql = "INSERT INTO reply_mailbox (id_mailbox, id_user, usertype, id_touser, message) VALUES ('$_POST[id_mail]', '$_SESSION[id_user]', 'user', '$to', '$message')";

	if($conn->query($sql) == TRUE) {
		header("Location: read-mail.php?id_mail=".$_POST['id_mail']);
		exit();
	} else {
		echo $conn->error;
	}
} else {
	header("Location: mailbox.php");
	exit();
}