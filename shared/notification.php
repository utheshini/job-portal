<?php
// Include DB connection
require_once("../db.php");

// Initialize default notification counts
$notificationCounts = [
    'new_jobs' => 0,
    'status_changes' => 0,
    'new_mails' => 0,
    'new_replies' => 0
];

/**
 * Fetch notification counts for a specific user.
 * Supports both 'candidate' and 'company' roles.
 */
function getNotificationCounts($conn, $userId, $userType) {
    $counts = [
        'new_jobs' => 0,
        'status_changes' => 0,
        'new_mails' => 0,
        'new_replies' => 0
    ];

    if ($userType === 'candidate') {
        // Count newly posted jobs in the last 24 hours
        $sqlNewJobs = "SELECT COUNT(*) AS new_jobs FROM jobs WHERE posted_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        if ($result = $conn->query($sqlNewJobs)) {
            $row = $result->fetch_assoc();
            $counts['new_jobs'] = $row['new_jobs'] ?? 0;
        }

        // Count status updates on user's applications
        $sql = "SELECT COUNT(*) AS status_changes FROM applications WHERE candidate_id = ? AND status IN ('selected', 'rejected')";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['status_changes'] = ($result) ? ($result->fetch_assoc()['status_changes'] ?? 0) : 0;
            $stmt->close();
        }

        // Count new mails received from companies
        $sql = "SELECT COUNT(*) AS new_mails FROM messages WHERE to_user_id = ? AND sent_date >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND from_user_type = 'company'";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['new_mails'] = ($result) ? ($result->fetch_assoc()['new_mails'] ?? 0) : 0;
            $stmt->close();
        }

        // Count new replies from companies
        $sql = "SELECT COUNT(*) AS new_replies FROM message_replies WHERE to_user_id = ? AND from_user_type = 'company' AND reply_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['new_replies'] = ($result) ? ($result->fetch_assoc()['new_replies'] ?? 0) : 0;
            $stmt->close();
        }
    } elseif ($userType === 'company') {
        // Count pending applications
        $sql = "SELECT COUNT(*) AS status_changes FROM applications WHERE company_id = ? AND status = 'pending'";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['status_changes'] = ($result) ? ($result->fetch_assoc()['status_changes'] ?? 0) : 0;
            $stmt->close();
        }

        // Count new mails received from job seekers
        $sql = "SELECT COUNT(*) AS new_mails FROM messages WHERE to_user_id = ? AND sent_date >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND from_user_type = 'candidate'";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['new_mails'] = ($result) ? ($result->fetch_assoc()['new_mails'] ?? 0) : 0;
            $stmt->close();
        }

        // Count new replies from job seekers
        $sql = "SELECT COUNT(*) AS new_replies FROM message_replies WHERE to_user_id = ? AND from_user_type = 'candidate' AND reply_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['new_replies'] = ($result) ? ($result->fetch_assoc()['new_replies'] ?? 0) : 0;
            $stmt->close();
        }
    }

    return $counts;
}

// Determine user identity and type from session
if (isset($_SESSION['id_candidate'])) {
    $userId = $_SESSION['id_candidate'];
    $userType = 'candidate';
} elseif (isset($_SESSION['id_company'])) {
    $userId = $_SESSION['id_company'];
    $userType = 'company';
} else {
    die("Unauthorized access.");
}

// Fetch notifications
$notificationCounts = getNotificationCounts($conn, $userId, $userType);
$total = array_sum($notificationCounts);
?>

<!-- Notification Dropdown HTML -->
<li class="notifications-menu dropdown">
    <a href="#" class="dropdown-toggle">
        <i class="fa fa-bell"></i>
        <span class="label label-warning"><?= $total ?></span>
    </a>
    <ul class="dropdown-menu" style="display: none;">
        <li class="header">You have <?= $total ?> notifications</li>
        <li>
            <ul class="menu">
                <?php if ($userType === 'candidate' && $notificationCounts['new_jobs'] > 0): ?>
                    <li><i class="fa fa-users text-aqua"></i> <?= $notificationCounts['new_jobs'] ?> new jobs added</li>
                <?php endif; ?>
                <?php if ($notificationCounts['status_changes'] > 0): ?>
                    <li><i class="fa fa-warning text-yellow"></i> <?= $notificationCounts['status_changes'] ?> application status changes</li>
                <?php endif; ?>
                <?php if ($notificationCounts['new_mails'] > 0): ?>
                    <li><i class="fa fa-envelope text-green"></i> <?= $notificationCounts['new_mails'] ?> new mails received</li>
                <?php endif; ?>
                <?php if ($notificationCounts['new_replies'] > 0): ?>
                    <li><i class="fa fa-reply text-blue"></i> <?= $notificationCounts['new_replies'] ?> new replies</li>
                <?php endif; ?>
                <?php if ($total == 0): ?>
                    <li>No new notifications</li>
                <?php endif; ?>
            </ul>
        </li>
    </ul>
</li>

<!-- JavaScript for dropdown and auto-refresh -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Toggle dropdown visibility
    dropdownToggle.addEventListener('click', function (e) {
        e.preventDefault();
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Hide dropdown if clicked outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            dropdownMenu.style.display = 'none';
        }
    });
});
</script>
