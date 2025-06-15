<?php
session_start();

require_once("../db.php");

$pageTitle = "Mailbox | Job Portal";

// Check login session and get user ID
$userId = null;
if (!empty($_SESSION['id_candidate'])) {
    $userId = $_SESSION['id_candidate'];
} elseif (!empty($_SESSION['id_company'])) {
    $userId = $_SESSION['id_company'];
} else {
    header("Location: ../login.php");
    exit();
}

include('../shared/header_dashboard.php');

// Prepare mailbox query
$sql = "SELECT m.message_id, m.subject, m.sent_date AS mailDate, 
        COALESCE(MAX(r.reply_date), m.sent_date) AS latestDate
        FROM messages m
        LEFT JOIN message_replies r ON m.message_id = r.message_id
        WHERE m.from_user_id = ? OR m.to_user_id = ?
        GROUP BY m.message_id
        ORDER BY latestDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Grid.js for table rendering -->
<head>
  <link href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>
</head>

<div class="col-md-9 bg-white padding-2">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Mailbox</h3>
            <div class="pull-right">
              <a href="create_mail.php" class="btn btn-warning btn-flat">
                <i class="fa fa-envelope"></i> Create
              </a>
            </div>
          </div>

          <div class="box-body no-padding">
            <div class="table-responsive mailbox-messages">
              <div id="mailTable" class="table-responsive"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>
        </div>
      </div>
    </section> </br>

    

  </div>

<?php include('../footer.php'); ?>

<script>
// Wait for DOM
document.addEventListener("DOMContentLoaded", function () {
  const tableData = [
    <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $subject = htmlspecialchars($row['subject'], ENT_QUOTES);
          $link = 'read_mail.php?id_mail=' . $row['message_id'];
          $date = date("d-M-Y h:i a", strtotime($row['latestDate']));
          echo "[ gridjs.html(`<a href='$link'>$subject</a>`), '$date' ],";
        }
      }
    ?>
  ];

  new gridjs.Grid({
    columns: ["Subject", "Date"],
    data: tableData,
    search: true,
    pagination: { limit: 10 },
    sort: true
  }).render(document.getElementById("mailTable"));
});
</script>

</body>
</html>
