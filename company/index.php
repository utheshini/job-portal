<?php
// Start session to access session variables
session_start();

// Redirect user to login page if not logged in as a company
if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection file
require_once("../db.php");

// Set page title
$pageTitle = "Dashboard | JobSeek";

// Include shared dashboard header
include('../shared/header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2">
    <h3>Overview</h3>

    <!-- Informational alert about dashboard features -->
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <i class="icon fa fa-info"></i> In this dashboard you are able to change your account settings, post and manage your jobs. Got a question? Do not hesitate to drop us a mail.
    </div>

    <div class="row">

        <!-- Total Jobs Posted by Employer -->
        <div class="col-md-6">
            <div class="info-box bg-c-yellow">
                <span class="info-box-icon bg-red"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Job Posted</span>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE company_id = ?");
                    $stmt->bind_param("i", $_SESSION['id_company']);
                    $stmt->execute();
                    $stmt->bind_result($totalJobs);
                    $stmt->fetch();
                    $stmt->close();
                    ?>
                    <span class="info-box-number"><?php echo $totalJobs; ?></span>
                </div>
            </div>
        </div>

        <!-- Total Job Applications Received -->
        <div class="col-md-6">
            <div class="info-box bg-c-yellow">
                <span class="info-box-icon bg-green"><i class="ion ion-ios-browsers"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Received Applications</span>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE company_id = ?");
                    $stmt->bind_param("i", $_SESSION['id_company']);
                    $stmt->execute();
                    $stmt->bind_result($totalApplications);
                    $stmt->fetch();
                    $stmt->close();
                    ?>
                    <span class="info-box-number"><?php echo $totalApplications; ?></span>
                </div>
            </div>
        </div>

        <!-- Total Active Jobs -->
        <div class="col-md-6">
            <div class="info-box bg-c-yellow">
                <span class="info-box-icon bg-blue"><i class="ion ion-ios-checkmark-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Jobs</span>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE company_id = ? AND deadline >= CURDATE()");
                    $stmt->bind_param("i", $_SESSION['id_company']);
                    $stmt->execute();
                    $stmt->bind_result($activeJobs);
                    $stmt->fetch();
                    $stmt->close();
                    ?>
                    <span class="info-box-number"><?php echo $activeJobs; ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

</div> <!-- .row -->
</div> <!-- .container -->
</section>
</div> <!-- .content-wrapper -->

<?php include('../footer.php'); ?>

</body>
</html>
