<?php
// Start the session
session_start();

// Redirect to login page if user is not logged in
if (empty($_SESSION['id_candidate'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once("../db.php");

// Page title
$pageTitle = "My Applications | JobSeek";

// Include dashboard header
include('../shared/header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2" id="printableArea">
    <h2><i>My Applications</i></h2>
    <p>Below you will find Job Posts you have applied for</p>

    <table id="myApplicationsTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Job Role</th>
                <th>Company</th>
                <th>Applied Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Prepare the SQL query securely
            $stmt = $conn->prepare("
                SELECT jobs.job_title, companies.company_name, applications.applied_date, applications.status, applications.job_id
                FROM jobs
                INNER JOIN applications ON jobs.job_id = applications.job_id
                INNER JOIN companies ON jobs.company_id = companies.company_id 
                WHERE applications.candidate_id = ?
                ORDER BY applications.applied_date DESC
            ");

            // Bind session user ID to query
            $stmt->bind_param("i", $_SESSION['id_candidate']);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display applications
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <a href="../view_job.php?id=<?php echo htmlspecialchars($row['job_id']); ?>">
                                <?php echo htmlspecialchars($row['job_title']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['applied_date']); ?></td>
                        <td>
                            <?php
                            // Status styling
                            switch ($row['status']) {
                                case "pending":
                                    echo '<strong class="text-orange">Pending</strong>';
                                    break;
                                case "rejected":
                                    echo '<strong class="text-red">Rejected</strong>';
                                    break;
                                case "selected":
                                    echo '<strong class="text-olive">Selected</strong>';
                                    break;
                                default:
                                    echo htmlspecialchars($row['status']);
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                // No applications found
                echo '<tr><td colspan="4">No recent applications found.</td></tr>';
            }

            // Close statement
            $stmt->close();
            ?>
        </tbody>
    </table>

    <!-- Print button (hidden when printing) -->
    <div class="no-print">
        <button onclick="printTable()" class="btn btn-primary btn-lg btn-flat margin-top-20">
            <i class="fa fa-print"></i> Print
        </button>
    </div>
</div>
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<!-- JavaScript for sorting and printing -->
<script>
// Print function
function printTable() {
    const printFrame = document.createElement('iframe');
    printFrame.style.position = 'absolute';
    printFrame.style.width = '0';
    printFrame.style.height = '0';
    printFrame.style.border = 'none';
    document.body.appendChild(printFrame);

    const now = new Date();
    const dateTime = now.toLocaleString();

    const printDoc = printFrame.contentWindow.document;
    printDoc.open();
    printDoc.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @media print {
                    @page { margin: 0; }
                    body { margin: 0; }
                    .print-date-time {
                        position: absolute;
                        top: 0;
                        left: 0;
                        margin: 10px;
                        font-size: 12px;
                        color: #000;
                    }
                    .no-print { display: none; }
                    a { text-decoration: none; color: inherit; }
                }
                table {
                    width: calc(100% - 20px);
                    border-collapse: collapse;
                    margin: 10px;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                    border: 1px solid #ddd;
                }
                h3 {
                    text-align: center;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="print-date-time">Date and Time: ${dateTime}</div>
            <h3>My Applications</h3>
            ${document.getElementById('myApplicationsTable').outerHTML}
        </body>
        </html>
    `);
    printDoc.close();
    printFrame.contentWindow.focus();
    printFrame.contentWindow.print();
    document.body.removeChild(printFrame);
}
</script>

</body>
</html>
