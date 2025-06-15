<?php
session_start();
require_once("../db.php");

$type = null;
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} elseif (isset($_POST['type'])) {
    $type = $_POST['type'];
}

if (!$type || !in_array($type, ['candidate', 'company'])) {
    echo "Invalid request.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/', $password)) {
        echo "<script>alert('Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.'); window.history.back();</script>";
        exit();
    }

    // Choose table based on type
    $table = $type === 'candidate' ? 'candidates' : 'companies';

    // Verify the token
    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ? AND hash_token = ?");
    $hash = hash('sha256', $token);
    $stmt->bind_param('ss', $email, $hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the password
        $hashed_password = password_hash($password);
        $stmt = $conn->prepare("UPDATE $table SET password = ?, hash_token = NULL WHERE email = ?");
        $stmt->bind_param('ss', $hashed_password, $email);
        $stmt->execute();

        echo "Your password has been reset successfully.";
    } else {
        echo "Invalid token or email.";
    }

    $stmt->close();
    $conn->close();
} elseif (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Reset Password</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">

  <link rel="stylesheet" href="../css/custom.css">
</head>
<body class="hold-transition skin-green sidebar-mini">

<div class="login-box">
  <div class="login-box-body">
    <h3 style="text-align:center;">Reset Password</h3><br>
    <form action="reset-password.php" method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
      <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
      
      <label for="password">New Password:</label><br>
      <input type="password" id="password" name="password" minlength="8" required><br><br>

      <label for="confirm_password">Confirm Password:</label><br>
      <input type="password" id="confirm_password" name="confirm_password" minlength="8" required><br><br>

      <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password<br><br>

      <div class="row">
        <div class="col-xs-6">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Reset Password</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function togglePasswordVisibility() {
  var passwordField = document.getElementById("password");
  var confirmPasswordField = document.getElementById("confirm_password");
  if (passwordField.type === "password") {
    passwordField.type = "text";
    confirmPasswordField.type = "text";
  } else {
    passwordField.type = "password";
    confirmPasswordField.type = "password";
  }
}
</script>
</body>
</html>
