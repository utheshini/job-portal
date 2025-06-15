<?php
session_start();
require_once("../db.php");
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $userType = $_POST['user_type'] ?? '';

    // Validate user type
    if (!in_array($userType, ['candidate', 'company'])) {
        echo 'Invalid user type selected.';
        exit;
    }

    // Map user type to table name
    $table = $userType === 'candidate' ? 'candidates' : 'companies';

    // Check if email exists in the selected table
    $stmt = $conn->prepare("SELECT * FROM {$table} WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $hash = hash('sha256', $token);

        // Update the user with the generated hash
        $stmt = $conn->prepare("UPDATE {$table} SET hash_token = ? WHERE email = ?");
        $stmt->bind_param('ss', $hash, $email);
        $stmt->execute();

        // Prepare the reset link
        $resetLink = 'http://localhost/job-portal/shared/reset_password.php?email=' . urlencode($email) . 
             '&token=' . urlencode($token) . 
             '&type=' . urlencode($userType);

        // Send email with the reset password link using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jobseek@gmail.com';
            $mail->Password   = 'tszeptnxdbigglhp'; // Use environment variables or config for security!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('jobseek@gmail.com', 'JobSeek');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = 'Click the following link to reset your password: <a href="' . $resetLink . '">Reset Password</a>';

            $mail->send();
            echo "<script>alert('A password reset link has been sent to your email address.');</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'No account found with that email address.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Forgot Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/_all-skins.min.css">
  <link rel="stylesheet" href="../css/custom.css">
</head>
<body class="hold-transition skin-green sidebar-mini">

<div class="login-box">
  <div class="login-logo">
    <p><b><a href="../index.php">JobSeek</a></b></p>
  </div>
  <div class="login-box-body">
    <h3 style="text-align:center;">Forgot Password</h3>
    <br>
    <form action="forgot_password.php" method="POST">
      <div class="form-group has-feedback">
        <label for="user_type">I am a:</label>
        <select class="form-control" id="user_type" name="user_type" required>
          <option value="" disabled selected>Select user type</option>
          <option value="candidate">Candidate</option>
          <option value="company">Employer</option>
        </select>
      </div>
      <br>
      <div class="form-group has-feedback">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <br>
      <div class="row">
        <div class="col-xs-6">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Send Reset Link</button>
        </div>
      </div>
    </form>
  </div>
</div>

</body>
</html>
