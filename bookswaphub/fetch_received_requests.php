<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch received requests
$stmt = $conn->prepare("
    SELECT 
        n.notification_id,
        n.message,
        n.read_status,
        n.created_at,
        u.username as sender_username
    FROM 
        notifications n
    JOIN 
        users u ON n.sender_id = u.id
    WHERE 
        n.user_id = ?
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$received_requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
