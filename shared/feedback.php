<?php
session_start();

require_once("../db.php");

$pageTitle = "Send Feedback | JobSeek";

include('header_dashboard.php');

// Determine user type and ID based on session
if (isset($_SESSION['id_company'])) {
    $user_type = 'employer';
    $user_id = $_SESSION['id_company'];
} elseif (isset($_SESSION['id_candidate'])) {
    $user_type = 'candidate';
    $user_id = $_SESSION['id_candidate'];
} else {
    header("Location: ../login.php");
    exit();
}


// Handle feedback form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);

    if (!empty($feedback)) {
        $feedback = mysqli_real_escape_string($conn, $feedback);

        // Use prepared statement to insert feedback
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, user_type, feedback) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $user_type, $feedback);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Feedback cannot be empty.";
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Feedback Form</h2>

            <!-- Alert Messages -->
            <?php if (isset($success)) : ?>
                <div class="alert alert-success">Thank you for your feedback!</div>
            <?php endif; ?>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Feedback Form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="feedback">Your Feedback:</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>
</div>

</div>
</div>

</section> </br>

</div>

<?php include('../footer.php'); ?>

</body>
</html>
