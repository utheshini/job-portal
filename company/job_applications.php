<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "Applications | JobSeek";

include('../shared/header_dashboard.php');

?>

<div class="col-md-9 bg-white padding-2">
    <h2><i>Received Applications</i></h2>

    <?php
    // Prepare the SQL query using prepared statements to avoid SQL injection
    $stmt = $conn->prepare("
        SELECT jobs.job_title, candidates.first_name, candidates.last_name, applications.applied_date, 
               applications.status, candidates.candidate_id, jobs.job_id
        FROM jobs 
        INNER JOIN applications ON jobs.job_id = applications.job_id
        INNER JOIN candidates ON candidates.candidate_id = applications.candidate_id
        WHERE applications.company_id = ?
    ");
    
    // Bind session company ID
    $stmt->bind_param("i", $_SESSION['id_company']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Convert status to lowercase for easier comparison
            $status = strtolower($row['status']);

            // Determine status class and label
            $statusClass = '';
            $statusText = '';

            if ($status === 'pending') {
                $statusClass = 'text-orange';
                $statusText = 'Pending';
            } elseif ($status === 'rejected') {
                $statusClass = 'text-red';
                $statusText = 'Rejected';
            } elseif ($status === 'selected') {
                $statusClass = 'text-success';
                $statusText = 'Selected';
            }
    ?>

    <!-- Display each application block -->
    <div class="attachment-block clearfix padding-2">
        <h4 class="attachment-heading">
            <a href="view_application.php?id=<?php echo htmlspecialchars($row['candidate_id']); ?>&id_jobpost=<?php echo htmlspecialchars($row['job_id']); ?>">
                <?php echo htmlspecialchars($row['job_title']) . ' @ (' . htmlspecialchars($row['first_name']) . ' ' . htmlspecialchars($row['last_name']) . ')'; ?>
            </a>
        </h4>
        <div class="attachment-text padding-2">
            <div class="pull-left">
                <i class="fa fa-calendar"></i> <?php echo htmlspecialchars($row['applied_date']); ?>
            </div>
            <div class="pull-right">
                <strong class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></strong>
            </div>
        </div>
    </div>

    <?php
        }
    } else {
        echo "<p>No applications received yet.</p>";
    }

    // Close the prepared statement
    $stmt->close();
    ?>

</div>
</div>
</section>

</div>

<?php include('../footer.php'); ?>

</body>
