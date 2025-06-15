<?php
// Start the session
session_start();

// Redirect to login page if admin is not logged in
if(empty($_SESSION['id_admin'])) {
  header("Location: login.php");
  exit();
}

// Include database connection
require_once("../db.php");

// Page title
$pageTitle = "Manage Feedback | JobSeek";

// Include header
include('../shared/header_dashboard.php');

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $feedback_id = $_POST['feedback_id'];

    $stmt = $conn->prepare("DELETE FROM feedback WHERE feedback_id = ?");
    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {
        $stmt->close();
        // Redirect to avoid form resubmission
        header("Location: manage_feedback.php?deleted=1");
        exit();
    } else {
        error_log("Delete Error: " . $stmt->error);
        echo "An error occurred while deleting feedback.";
        $stmt->close();
        exit();
    }
}

// Fetch all feedback
$sql = "SELECT * FROM feedback";
$result = $conn->query($sql);
?>

<div class="col-md-9 bg-white padding-2">
    <h2>User Feedbacks</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Feedback deleted successfully.</div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User ID</th>
                <th>User Type</th>
                <th>Feedback</th>
                <th>Submitted Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['user_type']) ?></td>
                        <td><?= htmlspecialchars($row['feedback']) ?></td>
                        <td><?= htmlspecialchars($row['submitted_date']) ?></td>
                        <td>
                            <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete(<?= htmlspecialchars($row['feedback_id']) ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No feedback found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>
</div>
</section> 
</div>

<!-- Hidden form for delete -->
<form id="delete-form" method="POST" action="manage_feedback.php" style="display:none;">
    <input type="hidden" name="delete_feedback" value="true">
    <input type="hidden" name="feedback_id" id="feedback_id_field">
</form>

<?php include('../footer.php'); ?>

<script>
function confirmDelete(feedbackId) {
    if (confirm("Are you sure you want to delete this feedback?")) {
        document.getElementById('feedback_id_field').value = feedbackId;
        document.getElementById('delete-form').submit();
    }
}
</script>

</body>
</html>