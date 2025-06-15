<?php
session_start();

if (empty($_SESSION['id_candidate'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Saved Jobs | JobSeek";

include('../shared/header_dashboard.php');
?>

<!-- Main content area -->
<div class="col-md-9 bg-white padding-2">
    <h2><i>Saved Jobs</i></h2>
    <p>Below you will find Job Posts you have saved</p>

    <?php
    // Prepare SQL query to fetch saved jobs for the logged-in user
    // Uses prepared statement to avoid SQL injection
    $sql = "SELECT saved_jobs.*, jobs.*, companies.company_name 
            FROM saved_jobs 
            INNER JOIN jobs ON saved_jobs.job_id = jobs.job_id
            INNER JOIN companies ON jobs.company_id = companies.company_id 
            WHERE saved_jobs.candidate_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id_candidate']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display saved jobs if available
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>
    <div class="attachment-block clearfix padding-2">
        <!-- Job Title linking to job detail page -->
        <h4 class="attachment-heading">
            <a href="../view_job.php?id=<?php echo htmlspecialchars($row['job_id']); ?>">
                <?php echo htmlspecialchars($row['job_title']); ?>
            </a>
        </h4>

        <!-- Job metadata and delete button -->
        <div class="attachment-text padding-2">
            <div class="pull-left"><i class="fa fa-calendar"></i> <?php echo htmlspecialchars($row['saved_date']); ?></div>
            <div class="pull-right"><i class="fa fa-building"></i> <?php echo htmlspecialchars($row['company_name']); ?></div>
            <div style="clear:both;"></div>

            <!-- Form to delete saved job -->
            <form action="delete_saved_job.php" method="post" onsubmit="return confirm('Are you sure you want to delete this saved job?');">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['saved_job_id']); ?>">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
    <?php
        }
    } else {
        // If no saved jobs are found
        echo '<p class="text-center">No saved jobs found.</p>';
    }

    // Close statement
    $stmt->close();
    ?>
</div>
</div>
</div>
</section><br>
</div>

<?php include('../footer.php'); ?>

</body>
</html>
