<?php
include "db.php";
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];
$book_id = $_GET['book_id'];

$query = "SELECT m.*, u1.username AS sender_name, u1.profile_picture AS sender_picture, 
                 u2.username AS receiver_name, u2.profile_picture AS receiver_picture 
          FROM messages m
          JOIN users u1 ON m.sender_id = u1.id
          JOIN users u2 ON m.receiver_id = u2.id
          WHERE m.book_id = ? AND ((m.sender_id = ? AND m.receiver_id = ?) 
                                    OR (m.sender_id = ? AND m.receiver_id = ?))
          ORDER BY m.created_at ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $book_id, $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['status' => 'success', 'messages' => $messages]);

$stmt->close();
$conn->close();
?>
