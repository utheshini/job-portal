<?php
require_once("../db.php");

function getNotificationCounts($conn, $userId) {
    $response = [
        'new_jobs' => 0,
        'status_changes' => 0,
        'new_mails' => 0,
        'new_replies' => 0,
        'new_job_titles' => [],
        'new_mail_companies' => []
    ];

    // Count new job posts within the last day and get job titles
    $sqlNewJobs = "SELECT jobtitle FROM job_post WHERE createdat >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $resultNewJobs = $conn->query($sqlNewJobs);
    if ($resultNewJobs) {
        $response['new_jobs'] = $resultNewJobs->num_rows;
        while ($row = $resultNewJobs->fetch_assoc()) {
            $response['new_job_titles'][] = $row['jobtitle'];
        }
    }

    // Count unread status changes
    $sqlStatusChanges = "SELECT COUNT(*) AS status_changes FROM apply_job_post WHERE id_user = ? AND status IN ('selected', 'rejected')";
    if ($stmt = $conn->prepare($sqlStatusChanges)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $resultStatusChanges = $stmt->get_result();
        if ($resultStatusChanges) {
            $rowStatusChanges = $resultStatusChanges->fetch_assoc();
            $response['status_changes'] = $rowStatusChanges['status_changes'];
        }
    }

    // Count new unread mails from companies and get company names
    $sqlNewMails = "SELECT DISTINCT company.name AS company_name 
                    FROM mailbox 
                    JOIN company ON mailbox.id_fromuser = company.id_company
                    WHERE mailbox.id_touser = ? 
                    AND mailbox.createdAt >= DATE_SUB(NOW(), INTERVAL 1 DAY) 
                    AND mailbox.fromuser = 'company'";
    if ($stmtMails = $conn->prepare($sqlNewMails)) {
        $stmtMails->bind_param("i", $userId);
        $stmtMails->execute();
        $resultMails = $stmtMails->get_result();
        if ($resultMails) {
            $response['new_mails'] = $resultMails->num_rows;
            while ($row = $resultMails->fetch_assoc()) {
                $response['new_mail_companies'][] = $row['company_name'];
            }
        }
    }

    // Count new unread replies
    $sqlNewReplies = "SELECT COUNT(*) AS new_replies FROM reply_mailbox WHERE id_touser = ? AND createdAt >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND usertype = 'company'";
    if ($stmtReplies = $conn->prepare($sqlNewReplies)) {
        $stmtReplies->bind_param("i", $userId);
        $stmtReplies->execute();
        $resultReplies = $stmtReplies->get_result();
        if ($resultReplies) {
            $rowReplies = $resultReplies->fetch_assoc();
            $response['new_replies'] = $rowReplies['new_replies'];
        }
    }

    return $response;
}

$userId = $_SESSION['id_user'];
$notificationCounts = getNotificationCounts($conn, $userId);
?>

<!-- Notification Menu HTML -->
<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell"></i>
        <span class="label label-warning"><?= array_sum($notificationCounts) ?></span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">You have <?= array_sum($notificationCounts) ?> notifications</li>
        <li>
            <ul class="menu">
                <?php if ($notificationCounts['new_jobs'] > 0): ?>
                <?php foreach ($notificationCounts['new_job_titles'] as $title): ?>
                <li><a href="#"><i class="fa fa-users text-aqua"></i> New job: <?= htmlspecialchars($title) ?></a></li>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($notificationCounts['status_changes'] > 0): ?>
                <li><a href="#"><i class="fa fa-warning text-yellow"></i> <?= $notificationCounts['status_changes'] ?> application status changes</a></li>
                <?php endif; ?>
                <?php if ($notificationCounts['new_mails'] > 0): ?>
                <?php foreach ($notificationCounts['new_mail_companies'] as $company): ?>
                <li><a href="#"><i class="fa fa-envelope text-green"></i> New mail from: <?= htmlspecialchars($company) ?></a></li>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($notificationCounts['new_replies'] > 0): ?>
                <li><a href="#"><i class="fa fa-reply text-blue"></i> <?= $notificationCounts['new_replies'] ?> new replies</a></li>
                <?php endif; ?>
            </ul>
        </li>
    </ul>
</li>

<script>
$(document).ready(function() {
    function updateNotifications() {
        $.ajax({
            url: 'check_new_jobs.php',  // Make sure this script returns the updated counts
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var totalNotifications = parseInt(data.new_jobs) + parseInt(data.status_changes) + parseInt(data.new_mails) + parseInt(data.new_replies);
                $('.notifications-menu .label').text(totalNotifications);
                $('.notifications-menu .header').text('You have ' + totalNotifications + ' notifications');

                var menuHtml = '';
                if (data.new_jobs > 0) {
                    $.each(data.new_job_titles, function(index, title) {
                        menuHtml += '<li><a href="#"><i class="fa fa-users text-aqua"></i> New job: ' + title + '</a></li>';
                    });
                }
                if (data.status_changes > 0) {
                    menuHtml += '<li><a href="#"><i class="fa fa-warning text-yellow"></i> ' + data.status_changes + ' application status changes</a></li>';
                }
                if (data.new_mails > 0) {
                    $.each(data.new_mail_companies, function(index, company) {
                        menuHtml += '<li><a href="#"><i class="fa fa-envelope text-green"></i> New mail from: ' + company + '</a></li>';
                    });
                }
                if (data.new_replies > 0) {
                    menuHtml += '<li><a href="#"><i class="fa fa-reply text-blue"></i> ' + data.new_replies + ' new replies</a></li>';
                }
                if (menuHtml === '') {
                    menuHtml = '<li><a href="#">No new notifications</a></li>';
                }
                $('.notifications-menu .menu').html(menuHtml);
            }
        });
    }

    setInterval(updateNotifications, 30000); // update every 30 seconds
});
</script>




<!--check_new_jobs.php-->

<?php
require_once("../db.php");

// Assuming you have the same function as above in this file or included
$userId = $_SESSION['id_user'];
$notificationCounts = getNotificationCounts($conn, $userId);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($notificationCounts);
?>
