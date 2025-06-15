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
$pageTitle = "Manage Companies | JobSeek";

// Include header
include('../shared/header_dashboard.php');
?>

<!-- Main Content: Manage Jobs -->
<div class="col-md-9 bg-white padding-2">
  <h3>Manage Companies</h3>
  <div class="row margin-top-20">
    <div class="col-md-12">
      <div class="box-body table-responsive no-padding">
        <div id="companiesTableGrid"></div>

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
  const companyData = [
    <?php
    $stmt = $conn->prepare("SELECT * FROM companies ORDER BY created_date DESC");
    if (!$stmt) {
      die("Prepare failed: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // Status with action links for pending companies, else just text
        $status = '';
        if ($row['active'] === "approved") {
          $status = "<span class='text-success'>Activated</span>";
        } else if ($row['active'] === "pending") {
          $status = "<a href='reject_company.php?id=" . urlencode($row['company_id']) . "' onclick='return confirm(\"Are you sure you want to reject this employer?\");'><strong style='color:red;'>Reject</strong></a> | 
                     <a href='approve_company.php?id=" . urlencode($row['company_id']) . "' onclick='return confirm(\"Are you sure you want to approve this employer?\");'><strong style='color:green;'>Approve</strong></a>";
        } else if ($row['active'] === "deactivated") {
          $status = "<span class='text-primary'>Deactivated</span>";
        } else if ($row['active'] === "rejected") {
          $status = "<span class='text-danger'>Rejected</span>";
        } else {
          $status = "<span>Unknown</span>";
        }

        $delete = "<a href='delete_company.php?id=" . urlencode($row['company_id']) . "' onclick='return confirm(\"Are you sure you want to deactivate this employer?\");'><i class='fa fa-trash'></i></a>";

        echo '[' .
          '"' . htmlspecialchars($row['company_name']) . '",' .
          '"' . htmlspecialchars($row['account_holder_name']) . '",' .
          '"' . htmlspecialchars($row['email']) . '",' .
          '"' . htmlspecialchars($row['contact_no']) . '",' .
          '"' . htmlspecialchars($row['city']) . '",' .
          '"' . date("d-M-Y", strtotime($row['created_date'])) . '",' .
          '`' . $status . '`,' .
          '`' . $delete . '`' .
        '],';
      }
    } else {
      echo '["No companies found.", "", "", "", "", "", "", ""]';
    }
    $stmt->close();
    ?>
  ];

  // Initialize Grid.js table with columns and the company data
  new gridjs.Grid({
    columns: [
      "Company Name",
      "Account Holder Name",
      "Email",
      "Phone No",
      "City",
      "Created Date",
      { name: "Status", formatter: cell => gridjs.html(cell) },
      { name: "Delete", formatter: cell => gridjs.html(cell) }
    ],
    data: companyData,
    search: true,
    pagination: { limit: 10 },
    sort: true,
    resizable: true,
    className: {
      table: 'gridjs-table table table-bordered table-hover'
    }
  }).render(document.getElementById("companiesTableGrid"));

  setTimeout(() => {
    const table = document.querySelector('.gridjs-table');
    if (table) table.setAttribute('id', 'gridPrintTable');
  }, 500);
});

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
    headerRow.removeChild(headerRow.children[headerRow.children.length - 1]);  
  }

  const bodyRows = tableClone.querySelectorAll('tbody tr');
  bodyRows.forEach(row => {
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
      <title>Print Companies</title>
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
      <h3>JobSeek - Companies</h3>
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
