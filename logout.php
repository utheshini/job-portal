<?php
// Start session
session_start();

// Remove all session variables
session_unset();

// Destroy the session entirely
session_destroy();

// Redirect the user to the homepage (index.php)
header("Location: index.php");
exit();
