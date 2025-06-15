<?php
// Start the session
session_start();

// Redirect to login page if admin is not logged in
if (empty($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once("../db.php");

// Set page title
$pageTitle = "Admin Dashboard | JobSeek";

// Include header
include('../shared/header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2">
    <h3>Job Portal Statistics</h3>
    <div class="row">

        <?php
        // Function to count rows based on query
        function getRowCount($conn, $query)
        {
            $result = $conn->query($query);
            return ($result && $result->num_rows > 0) ? $result->num_rows : 0;
        }

        // Dashboard statistics queries
        $stats = [
            [
                'label' => 'Active Company Registered',
                'icon' => 'ion-briefcase',
                'bg' => 'bg-red',
                'query' => "SELECT company_id FROM companies WHERE active = 'approved'",
            ],
            [
                'label' => 'Pending Company Approval',
                'icon' => 'ion-briefcase',
                'bg' => 'bg-red',
                'query' => "SELECT company_id FROM companies WHERE active = 'pending'",
            ],
            [
                'label' => 'Registered Candidates',
                'icon' => 'ion-person-stalker',
                'bg' => 'bg-green',
                'query' => "SELECT candidate_id FROM candidates WHERE active = '1'",
            ],
            [
                'label' => 'Pending Candidates Confirmation',
                'icon' => 'ion-person-stalker',
                'bg' => 'bg-green',
                'query' => "SELECT candidate_id FROM candidates WHERE active = '0'",
            ],
            [
                'label' => 'Total Job Posts',
                'icon' => 'ion-person-add',
                'bg' => 'bg-aqua',
                'query' => "SELECT job_id FROM jobs",
            ],
            [
                'label' => 'Total Applications',
                'icon' => 'ion-ios-browsers',
                'bg' => 'bg-yellow',
                'query' => "SELECT application_id FROM applications",
            ],
        ];

        // Generate info boxes
        foreach ($stats as $stat) {
            $count = getRowCount($conn, $stat['query']);
            ?>
            <div class="col-md-6">
                <div class="info-box bg-c-yellow">
                    <span class="info-box-icon <?php echo $stat['bg']; ?>">
                        <i class="ion <?php echo $stat['icon']; ?>"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?php echo $stat['label']; ?></span>
                        <span class="info-box-number"><?php echo $count; ?></span>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
</div>

</div> <!-- End of row -->
</div> <!-- End of container -->
</section>
</div>

<?php include('../footer.php'); ?>
</body>
</html>
