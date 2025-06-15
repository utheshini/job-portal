<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../db.php");

$pageTitle = "My Posted Jobs | JobSeek";

include('../shared/header_dashboard.php');
?>

<div class="col-md-9 bg-white padding-2">
  <h2><i>My Posted Jobs</i></h2>
  <p>In this section you can view all job postings created by you.</p>
  <div class="row margin-top-20">
    <div class="col-md-12">
      <div id="jobsTable"></div>
      <div class="no-print">
          <button onclick="printTable()" class="btn btn-primary btn-lg btn-flat margin-top-20">
              <i class="fa fa-print"></i> Print
          </button>
      </div>
    </div>
  </div>
</div>

</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>

<script>
// Prepare job posts data in JavaScript
const jobData = [
  <?php
    $sql = "SELECT * FROM jobs WHERE company_id = '$_SESSION[id_company]' ORDER BY posted_date DESC";
    $result = $conn->query($sql);
    $currentDate = date('Y-m-d');

    if($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $jobTitle = htmlspecialchars($row['job_title']);
        $viewLink = "../view_job.php?id=" . $row['job_id'];
        $editLink = "edit_job.php?id=" . $row['job_id'];
        $createdAt = htmlspecialchars($row['posted_date']);
        $status = (strtotime($row['deadline']) < strtotime($currentDate)) ? 'Expired' : 'Active';
        $statusClass = ($status === 'Expired') ? 'text-red' : 'text-olive';

        echo "[
          '{$jobTitle}',
          gridjs.html(`<a href='{$viewLink}'><i class='fa fa-address-card-o'></i></a>`),
          gridjs.html(`<a href='{$editLink}'><i class='fa fa-pencil'></i></a>`),
          '{$createdAt}',
          gridjs.html(`<strong class='{$statusClass}'>{$status}</strong>`)
        ],";
      }
    }
  ?>
];

// Initialize Grid.js
const grid = new gridjs.Grid({
  columns: ['Job Title', 'View', 'Edit', 'Posted Date', 'Status'],
  data: jobData,
  pagination: { limit: 5 },
  search: true,
  sort: true,
  className: {
    table: 'table table-hover'
  }
});

grid.render(document.getElementById("jobsTable"));

setTimeout(() => {
  const table = document.querySelector('.gridjs-table');
  if (table) table.setAttribute('id', 'gridPrintTable');
}, 500);

// Function to print the companies table
function printTable() {
  const tableEl = document.getElementById('gridPrintTable');
  if (!tableEl) {
    alert("Table is still loading. Please wait and try again.");
    return;
  }

  const tableClone = tableEl.cloneNode(true);
 
  const headerRow = tableClone.querySelector('thead tr');
  if (headerRow) {
    headerRow.removeChild(headerRow.children[headerRow.children.length - 3]);
    headerRow.removeChild(headerRow.children[headerRow.children.length - 3]);   
  }

  const bodyRows = tableClone.querySelectorAll('tbody tr');
  bodyRows.forEach(row => {
    row.removeChild(row.children[row.children.length - 3]);
    row.removeChild(row.children[row.children.length - 3]); 
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
      <title>Print My Job Postings</title>
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
      <h3>JobSeek - My Job Postings</h3>
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
