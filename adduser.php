<?php
session_start();
require_once("db.php");
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST)) {
    $firstname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $contactno = mysqli_real_escape_string($conn, $_POST['contactno']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);


    $password = base64_encode(strrev(md5($password)));
    
    $sql = "SELECT email FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows == 0) {
        $uploadOk = true;
        $folder_dir = "uploads/resume/";
        $base = basename($_FILES['resume']['name']);
        $resumeFileType = pathinfo($base, PATHINFO_EXTENSION);
        $file = uniqid() . "." . $resumeFileType;
        $filename = $folder_dir .$file;

        if(file_exists($_FILES['resume']['tmp_name'])) {
            if($resumeFileType == "pdf")  {
                if($_FILES['resume']['size'] < 500000) { 
                    move_uploaded_file($_FILES["resume"]["tmp_name"], $filename);
                } else {
                    $_SESSION['uploadError'] = "Wrong Size. Max Size Allowed : 5MB";
                    $uploadOk = false;
                }
            } else {
                $_SESSION['uploadError'] = "Wrong Format. Only PDF Allowed";
                $uploadOk = false;
            }
        } else {
            $_SESSION['uploadError'] = "Something Went Wrong. File Not Uploaded. Try Again.";
            $uploadOk = false;
        }

        if($uploadOk == false) {
            header("Location: register-candidates.php");
            exit();
        }

        $hash = md5(uniqid());

        $sql = "INSERT INTO users(firstname, lastname, email, password,  contactno,  dob, age, resume, hash) 
                VALUES ('$firstname', '$lastname', '$email', '$password',  '$contactno',  '$dob', '$age',  '$file', '$hash')";

        if($conn->query($sql) === TRUE) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'jobpal29@gmail.com';
                $mail->Password = 'tszeptnxdbigglhp';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('jobpal29@gmail.com', 'JobSeek');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'JobSeek - Confirm Your Email Address';
                $mail->Body    = '
                    <html>
                    <head>
                        <title>Confirm Your Email</title>
                    </head>
                    <body>
                        <p>Click Link To Confirm</p>
                        <a href="http://localhost/job/verify.php?token='.$hash.'&email='.$email.'">Verify Email</a>
                    </body>
                    </html>
                ';

                $mail->send();

                $_SESSION['registerCompleted'] = true;
                header("Location: register-candidates.php");
                exit();

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error " . $sql . "<br>" . $conn->error;
        }
    } else {
        $_SESSION['registerError'] = true;
        header("Location: register-candidates.php");
        exit();
    }

    $conn->close();

} else {
    header("Location: register-candidates.php");
    exit();
}
?>
