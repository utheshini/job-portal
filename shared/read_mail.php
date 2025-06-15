<?php
session_start();
require_once("../db.php");

// Detect user type and ID
if (!empty($_SESSION['id_candidate'])) {
    $userType = 'user';
    $currentUserId = intval($_SESSION['id_candidate']);
    $pageTitle = "Read Mail | JobSeek";
} elseif (!empty($_SESSION['id_company'])) {
    $userType = 'company';
    $currentUserId = intval($_SESSION['id_company']);
    $pageTitle = "Read Mail | JobSeek";
} else {
    header("Location: ../login.php");
    exit();
}

include('../shared/header_dashboard.php');

// Validate and sanitize message ID
if (!isset($_GET['id_mail']) || !ctype_digit($_GET['id_mail'])) {
    header("Location: mailbox.php");
    exit();
}

$id_mail = intval($_GET['id_mail']);

// Fetch main message
$stmt = $conn->prepare("SELECT * FROM messages WHERE message_id= ? AND (from_user_id = ? OR to_user_id = ?)");
$stmt->bind_param("iii", $id_mail, $currentUserId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Message not found or access denied.";
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

// Determine other user (recipient/sender) and names
$senderType = $row['from_user_type'];
$receiverType = $senderType === "candidate" ? "company" : "candidate";
$senderId = $row['from_user_id'];
$receiverId = $row['to_user_id'];

$senderName = $receiverName = 'Unknown';

if ($senderType === 'candidate') {
    $stmtUser = $conn->prepare("SELECT first_name FROM candidates WHERE candidate_id = ?");
    $stmtUser->bind_param("i", $senderId);
    $stmtUser->execute();
    $res = $stmtUser->get_result();
    if ($res->num_rows > 0) {
        $senderName = $res->fetch_assoc()['first_name'];
    }
    $stmtUser->close();

    $stmtCompany = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $stmtCompany->bind_param("i", $receiverId);
    $stmtCompany->execute();
    $res = $stmtCompany->get_result();
    if ($res->num_rows > 0) {
        $receiverName = $res->fetch_assoc()['company_name'];
    }
    $stmtCompany->close();
} else {
    $stmtCompany = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $stmtCompany->bind_param("i", $senderId);
    $stmtCompany->execute();
    $res = $stmtCompany->get_result();
    if ($res->num_rows > 0) {
        $senderName = $res->fetch_assoc()['company_name'];
    }
    $stmtCompany->close();

    $stmtUser = $conn->prepare("SELECT first_name FROM candidates WHERE candidate_id = ?");
    $stmtUser->bind_param("i", $receiverId);
    $stmtUser->execute();
    $res = $stmtUser->get_result();
    if ($res->num_rows > 0) {
        $receiverName = $res->fetch_assoc()['first_name'];
    }
    $stmtUser->close();
}

// Determine recipient for reply (not current user)
$replyToId = $senderId === $currentUserId ? $receiverId : $senderId;
?>

<div class="col-md-9 bg-white padding-2">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <a href="mailbox.php" class="btn btn-default"><i class="fa fa-arrow-circle-left"></i> Back</a>

        <!-- Main Message -->
        <div class="box box-primary">
          <div class="box-body no-padding">
            <div class="mailbox-read-info">
              <h3><?php echo htmlspecialchars($row['subject']); ?></h3>
              <h5>
                From: <?php echo htmlspecialchars($senderName); ?>
                <span class="mailbox-read-time pull-right">
                  <?php echo date("d-M-Y h:i a", strtotime($row['sent_date'])); ?>
                </span>
              </h5>
            </div>
            <div class="mailbox-read-message">
              <?php echo nl2br(htmlspecialchars(stripcslashes($row['message']))); ?>
            </div>
          </div>
        </div>

        <!-- Replies -->
        <?php
        $stmtReply = $conn->prepare("SELECT * FROM message_replies WHERE message_id = ? ORDER BY reply_date ASC");
        $stmtReply->bind_param("i", $id_mail);
        $stmtReply->execute();
        $resultReply = $stmtReply->get_result();

        if ($resultReply->num_rows > 0) {
            while ($reply = $resultReply->fetch_assoc()) {
                $replySenderId = $reply['from_user_id'];
$replySenderType = $reply['from_user_type'];
$replyName = 'Unknown';

if ($replySenderType === 'candidate') {
    $stmt = $conn->prepare("SELECT first_name FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $replySenderId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $replyName = $res->fetch_assoc()['first_name'];
    }
    $stmt->close();
} elseif ($replySenderType === 'company') {
    $stmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
    $stmt->bind_param("i", $replySenderId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $replyName = $res->fetch_assoc()['company_name'];
    }
    $stmt->close();
}

                ?>
                <div class="box box-primary">
                  <div class="box-body no-padding">
                    <div class="mailbox-read-info">
                      <h3>Reply</h3>
                      <h5>
                        From: <?php echo htmlspecialchars($replyName); ?>
                        <span class="mailbox-read-time pull-right">
                          <?php echo date("d-M-Y h:i a", strtotime($reply['reply_date'])); ?>
                        </span>
                      </h5>
                    </div>
                    <div class="mailbox-read-message">
                      <?php echo nl2br(htmlspecialchars(stripcslashes($reply['reply_message']))); ?>
                    </div>
                  </div>
                </div>
                <?php
            }
        }
        $stmtReply->close();
        ?>

        <!-- Reply Form -->
        <div class="box box-primary">
          <div class="box-body no-padding">
            <div class="mailbox-read-info">
              <h3>Send Reply</h3>
            </div>
            <div class="mailbox-read-message">
              <form action="reply_mailbox.php" method="post" onsubmit="return validateForm()">
                <div class="form-group">
                  <textarea class="form-control input-lg" id="description" name="description" placeholder="Type your reply here..." required></textarea>
                  <input type="hidden" name="id_mail" value="<?php echo $id_mail; ?>">
                  <input type="hidden" name="to" value="<?php echo $replyToId; ?>">
                </div>
                <div class="form-group">
                  <div class="pull-right">
                    <button type="submit" class="btn btn-flat btn-success">Reply</button>
                  </div>
                </div>
                <a href="mailbox.php" class="btn btn-default">
                  <i class="fa fa-times"></i> Discard
                </a>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<script>
function validateForm() {
    const content = document.getElementById('description').value.trim();
    if (content === '') {
        alert('Please enter a reply message before submitting.');
        return false;
    }
    return true;
}
</script>
</body>
</html>
