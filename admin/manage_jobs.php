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
$pageTitle = "Manage jobs | JobSeek";

// Include header
include('../shared/header_dashboard.php');
?>

<!-- Main Content: Manage Jobs -->
<div class="col-md-9 bg-white padding-2">
    <h3>Manage Jobs</h3>
    <div class="row margin-top-20">
        <div class="col-md-12">
            <div class="box-body table-responsive no-padding">
                <div id="jobsTableGrid"></div>

                <!-- Print Button -->
                <div class="no-print">
                    <button onclick="printTable()" class="btn btn-primary btn-lg btn-flat margin-top-20">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<!-- Include Grid.js CSS and JS -->
<link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const jobData = [
    <?php
    $stmt = $conn->prepare("SELECT jobs.*, companies.company_name 
                            FROM jobs 
                            INNER JOIN companies ON jobs.company_id = companies.company_id 
                            ORDER BY created_date DESC");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $currentDate = date('Y-m-d');

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $status = (strtotime($row['deadline']) < strtotime($currentDate)) ? 'Expired' : 'Active';
            $statusClass = ($status === 'Expired') ? 'text-red' : 'text-olive';

            echo '[
              "' . htmlspecialchars($row['job_title']) . '",
              "' . htmlspecialchars($row['company_name']) . '",
              "' . date("d-M-Y", strtotime($row['posted_date'])) . '",
              `<strong class=\'' . $statusClass . '\'>' . $status . '</strong>`,
              `<a href="../view_job.php?id=' . urlencode($row['job_id']) . '"><i class="fa fa-address-card-o"></i></a>`,
              `<a href="delete_job.php?id=' . urlencode($row['job_id']) . '" onclick="return confirm(\'Are you sure you want to delete this job post?\');"><i class="fa fa-trash"></i></a>`
            ],';
        }
    } else {
        echo '["No job postings found.", "", "", "", "", ""]';
    }
    $stmt->close();
    ?>
  ];

  // Initialize Grid.js table with columns and the job data
  new gridjs.Grid({
    columns: [
      "Job Title",
      "Company Name",
      "Date Created",
      {
        name: "Status",
        formatter: cell => gridjs.html(cell)
      },
      {
        name: "View",
        formatter: cell => gridjs.html(cell)
      },
      {
        name: "Delete",
        formatter: cell => gridjs.html(cell)
      }
    ],
    data: jobData,
    search: true,
    pagination: { limit: 10 },
    sort: true,
    resizable: true,
    className: {
      table: 'gridjs-table table table-bordered table-hover'
    }
  }).render(document.getElementById("jobsTableGrid"));

  setTimeout(() => {
    const table = document.querySelector('.gridjs-table');
    if (table) table.setAttribute('id', 'gridPrintTable');
  }, 500);
});

// Function to print the jobs table
function printTable() {
  const tableEl = document.getElementById('gridPrintTable');
  if (!tableEl) {
    alert("Table is still loading. Please wait and try again.");
    return;
  }

  const tableClone = tableEl.cloneNode(true);
 
  const headerRow = tableClone.querySelector('thead tr');
  if (headerRow) {
    headerRow.removeChild(headerRow.children[headerRow.children.length - 1]); 
    headerRow.removeChild(headerRow.children[headerRow.children.length - 1]); 
  }

  const bodyRows = tableClone.querySelectorAll('tbody tr');
  bodyRows.forEach(row => {
    row.removeChild(row.children[row.children.length - 1]); 
    row.removeChild(row.children[row.children.length - 1]); 
  });

  const tableHTML = tableClone.outerHTML;
  const now = new Date();
  const dateTime = now.toLocaleString();

  const printFrame = document.createElement('iframe');
  printFrame.style.position = 'absolute';
  printFrame.style.width = '0';
  printFrame.style.height = '0';
  printFrame.style.border = 'none';
  document.body.appendChild(printFrame);

  const printDoc = printFrame.contentWindow.document;
  printDoc.open();
  printDoc.write(`
    <html>
    <head>
      <title>Print Jobs</title>
      <style>
        @media print {
          .no-print, .no-print * { display: none !important; }
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
      <h3>JobSeek - Jobs</h3>
      ${tableHTML}
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
