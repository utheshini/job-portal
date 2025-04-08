<?php
require_once("../db.php");

$userId = $_SESSION['id_company'];

// Reset status changes notifications
$sqlResetStatusChanges = "UPDATE apply_job_post SET viewed = 1 WHERE id_company = ?";
if ($stmtResetStatus = $conn->prepare($sqlResetStatusChanges)) {
    $stmtResetStatus->bind_param("i", $userId);
    $stmtResetStatus->execute();
    $stmtResetStatus->close();
}

// Reset mail notifications
$sqlResetMails = "UPDATE mailbox SET viewed = 1 WHERE id_touser = ?";
if ($stmtResetMails = $conn->prepare($sqlResetMails)) {
    $stmtResetMails->bind_param("i", $userId);
    $stmtResetMails->execute();
    $stmtResetMails->close();
}

echo json_encode(['status' => 'success']);
?>

<script>
$(document).ready(function() {
    function updateNotifications() {
        $.ajax({
            url: 'check_new_jobs.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var totalNotifications = parseInt(data.status_changes) + parseInt(data.new_mails);
                $('.notifications-menu .label').text(totalNotifications);
                $('.notifications-menu .header').text('You have ' + totalNotifications + ' notifications');

                var menuHtml = '';
                if (data.status_changes > 0) {
                    menuHtml += '<li><a href="#"><i class="fa fa-warning text-yellow"></i> ' + data.status_changes + ' application status changes</a></li>';
                }
                if (data.new_mails > 0) {
                    menuHtml += '<li><a href="#"><i class="fa fa-envelope text-green"></i> ' + data.new_mails + ' new mails received</a></li>';
                }
                if (menuHtml === '') {
                    menuHtml = '<li><a href="#">No new notifications</a></li>';
                }
                $('.notifications-menu .menu').html(menuHtml);
            }
        });
    }

    function resetNotifications() {
        $.ajax({
            url: 'reset_notifications.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    $('.notifications-menu .label').text('0');
                    $('.notifications-menu .header').text('You have 0 notifications');
                    $('.notifications-menu .menu').html('<li><a href="#">No new notifications</a></li>');
                }
            }
        });
    }

    setInterval(updateNotifications, 30000); // update every 30 seconds

    // Reset notifications when the dropdown is opened
    $('.notifications-menu').on('show.bs.dropdown', function () {
        resetNotifications();
    });
});
</script>
