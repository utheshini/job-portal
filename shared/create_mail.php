<?php
session_start();

require_once("../db.php");

$pageTitle = "Create Mail | JobSeek";
include('../shared/header_dashboard.php');

// Determine user type
$isCandidate = isset($_SESSION['id_candidate']);
$isCompany = isset($_SESSION['id_company']);

// Redirect to login if not authenticated
if (!$isCandidate && !$isCompany) {
    header("Location: ../login.php");
    exit();
}
?>

<div class="col-md-9 bg-white padding-2">
  <form action="add_mail.php" method="post" onsubmit="return validateForm()">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Compose New Message</h3>
      </div>
      <div class="box-body">

        <!-- Recipient Dropdown -->
        <div class="form-group">
          <select name="to" class="form-control" required>
            <?php
            if ($isCandidate) {
              // Candidate: message selected companies
              $sql = "
                SELECT DISTINCT companies.company_id, companies.company_name 
                FROM applications 
                INNER JOIN companies ON applications.company_id = companies.company_id 
                WHERE applications.candidate_id = ? AND applications.status = 'selected'
              ";

              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $_SESSION['id_candidate']);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($row['company_id']) . '">' . htmlspecialchars($row['company_name']) . '</option>';
                }
              } else {
                echo '<option value="">No selected companies available</option>';
              }

            } elseif ($isCompany) {
              // Company: message selected candidates
              $sql = "
                SELECT candidates.candidate_id, candidates.first_name, candidates.last_name 
                FROM applications 
                INNER JOIN candidates ON applications.candidate_id = candidates.candidate_id 
                WHERE applications.company_id = ? AND applications.status = 'selected'
              ";

              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $_SESSION['id_company']);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo '<option value="' . htmlspecialchars($row['candidate_id']) . '">' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</option>';
                }
              } else {
                echo '<option value="">No selected candidates available</option>';
              }
            }
            ?>
          </select>
        </div>

        <!-- Subject -->
        <div class="form-group">
          <input class="form-control" name="subject" placeholder="Subject:" required>
        </div>

        <!-- Message Body -->
        <div class="form-group">
          <textarea class="form-control input-lg" id="description" name="description" placeholder="Enter your message here..."></textarea>
        </div>

      </div>
      <div class="box-footer">
        <div class="pull-right">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-envelope-o"></i> Send
          </button>
        </div>
        <a href="mailbox.php" class="btn btn-default"><i class="fa fa-times"></i> Discard</a>
      </div>
    </div>
  </form>
</div>
</div>
</div>
</section>
</div>

<?php include('../footer.php'); ?>

<!-- JavaScript for Validation -->
<script>
  function validateForm() {
    var content = tinymce.get('description').getContent({ format: 'text' }).trim();
    if (content === '') {
      alert('Please enter a message before submitting.');
      return false;
    }
    return true;
  }
</script>

</body>
</html>
