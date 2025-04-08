<?php
// Include the database connection file
require_once("db.php");

// Check if session ID is provided
if(isset($_POST['session_id'])) {
    $session_id = $_POST['session_id'];

    // Query to count the number of unread messages for the given session ID
    $sql = "SELECT COUNT(*) AS new_records FROM mailbox m 
            LEFT JOIN message_status ms ON m.id_mailbox = ms.id_message AND ms.id_user = '$session_id'
            WHERE m.id_fromuser = '$session_id' AND (ms.status IS NULL OR ms.status = 'unread')";
    $result = $conn->query($sql);

    if($result) {
        $row = $result->fetch_assoc();
        echo $row['new_records'];
    } else {
        echo 0; // No new records found
    }
} else {
    echo 0; // Session ID not provided
}
?>
