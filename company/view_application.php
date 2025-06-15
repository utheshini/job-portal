<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "View Application | JobSeek";

include('../shared/header_dashboard.php');

// Validate and sanitize input parameters from GET
if (!isset($_GET['id'], $_GET['id_jobpost']) || 
    !filter_var($_GET['id'], FILTER_VALIDATE_INT) || 
    !filter_var($_GET['id_jobpost'], FILTER_VALIDATE_INT)) {
    header("Location: job_applications.php");
    exit();
}

$id_candidate = (int) $_GET['id'];
$id_jobpost = (int) $_GET['id_jobpost'];

// Fetch the application status securely
$status = '';
$stmt = $conn->prepare("SELECT status FROM applications WHERE company_id = ? AND candidate_id = ? AND job_id = ?");
$stmt->bind_param("iii", $_SESSION['id_company'], $id_candidate, $id_jobpost);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

// Fetch user details securely
$stmt = $conn->prepare("SELECT * FROM candidates WHERE candidate_id = ?");
$stmt->bind_param("i", $id_candidate);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}
?>

<div class="col-md-9 bg-white padding-2">
    <div class="row margin-top-20">
        <div class="col-md-12">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="pull-left">
                    <h2><b><i><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></i></b></h2>
                </div>
                <div class="pull-right">
                    <a href="job_applications.php" class="btn btn-default btn-lg btn-flat margin-top-20">
                        <i class="fa fa-arrow-circle-left"></i> Back
                    </a>
                </div>
                <div class="clearfix"></div>
                <hr>

                <div>
                    <?php
                    // Display sanitized user information
                    echo 'Email: ' . htmlspecialchars($row['email']) . '<br>';
                    echo 'Date Of Birth: ' . htmlspecialchars($row['date_of_birth']) . '<br>';
                    echo 'Age: ' . htmlspecialchars($row['age']) . ' Years<br>';
                    echo 'Address: ' . htmlspecialchars($row['address']) . '<br>';
                    echo 'Phone Number: ' . htmlspecialchars($row['contact_no']) . '<br><br>';
                    echo 'Education: ' . htmlspecialchars($row['education']) . '<br><br>';
                    echo 'Experience: ' . htmlspecialchars($row['experience']) . '<br><br>';

                    // Format skills with line breaks
                    echo 'Skills:<br>' . nl2br(htmlspecialchars($row['skills'])) . '<br><br>';

                    // Resume download link (only if resume exists)
                    if (!empty($row['resume'])) {
                        $resumePath = htmlspecialchars($row['resume']);
                        $resumeName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name'] . ' Resume');
                        echo "<a href=\"../uploads/resume/$resumePath\" class=\"btn btn-info\" download=\"$resumeName\">Download Resume</a><br><br>";
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-3 pull-left">
                            <!-- Disable "Select" button if already selected or rejected -->
                            <a href="select.php?id=<?php echo $id_candidate; ?>&id_jobpost=<?php echo $id_jobpost; ?>"
                               class="btn btn-success <?php echo ($status === 'selected' || $status === 'rejected') ? 'disabled' : ''; ?>"
                               <?php echo ($status === 'selected' || $status === 'rejected') ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                Select Application
                            </a>
                        </div>
                        <div class="col-md-3 pull-right">
                            <!-- Disable "Reject" button if already selected or rejected -->
                            <a href="reject.php?id=<?php echo $id_candidate; ?>&id_jobpost=<?php echo $id_jobpost; ?>"
                               class="btn btn-danger <?php echo ($status === 'selected' || $status === 'rejected') ? 'disabled' : ''; ?>"
                               <?php echo ($status === 'selected' || $status === 'rejected') ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                Reject Application
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

</body>
</html>