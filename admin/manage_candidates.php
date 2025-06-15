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
$pageTitle = "Manage Candidates | JobSeek";

// Include header
include('../shared/header_dashboard.php');
?>

<!-- Main Content: Manage Jobs -->
<div class="col-md-9 bg-white padding-2">
  <h3>Manage Candidates</h3>
  <div class="row margin-top-20">
    <div class="col-md-12">
      <div class="box-body table-responsive no-padding">
        <div id="candidatesTableGrid"></div>

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
  const candidateData = [
    <?php
    $sql = "SELECT * FROM candidates ORDER BY registered_date DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $experience = $row['experience'] ? htmlspecialchars($row['experience']) : 'No Experience';
        
        if ($row['active'] == '1') {
          $activeStatus = '<span style="color: green;">Yes</span>';
        } elseif ($row['active'] == '2') {
          $activeStatus = '<span style="color: orange;">Deactivated</span>';
        } else {
          $activeStatus = '<span style="color: red;">No</span>';
        }

        $resumeLink = ($row['resume'] != '') 
          ? '<a href="../uploads/resume/' . rawurlencode($row['resume']) . '" download="' . 
            htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . ' Resume"><i class="fa fa-file-pdf-o"></i></a>'
          : 'No Resume Uploaded';

        echo '[
          "' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '",
          "' . htmlspecialchars($row['address']) . '",
          "' . htmlspecialchars($row['email']) . '",
          "' . htmlspecialchars($row['contact_no']) . '",
          "' . htmlspecialchars($row['education']) . '",
          "' . $experience . '",
          "' . htmlspecialchars($row['skills']) . '",
          "' . date("d-M-Y", strtotime($row['registered_date'])) . '",
          `' . $activeStatus . '`,
          `' . $resumeLink . '`,
          `<a href="delete_candidate.php?id=' . urlencode($row['candidate_id']) . '" onclick="return confirm(\'Are you sure you want to delete this candidate?\');"><i class="fa fa-trash"></i></a>`
        ],';
      }
    } else {
      echo '["No candidates found.", "", "", "", "", "", "", "", "", "", ""]';
    }
    ?>
  ];

  // Initialize Grid.js table with columns and the candidate data
  new gridjs.Grid({
    columns: [
      "Candidate",
      "Address",
      "Email Address",
      "Contact No",
      "Education",
      "Experience",
      "Skills",
      "Registered Date",
      {
        name: "Active",
        formatter: cell => gridjs.html(cell)
      },
      {
        name: "Download Resume",
        formatter: cell => gridjs.html(cell)
      },
      {
        name: "Delete",
        formatter: cell => gridjs.html(cell)
      }
    ],
    data: candidateData,
    search: true,
    pagination: { limit: 10 },
    sort: true,
    resizable: true,
    className: {
      table: 'gridjs-table table table-bordered table-hover'
    }
  }).render(document.getElementById("candidatesTableGrid"));

  setTimeout(() => {
    const table = document.querySelector('.gridjs-table');
    if (table) table.setAttribute('id', 'gridPrintTableCandidates');
  }, 500);
});

// Function to print the candidates table
function printTable() {
  const tableEl = document.getElementById('gridPrintTableCandidates');
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
      <title>Print Candidates</title>
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
      <h3>JobSeek - Candidates</h3>
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
