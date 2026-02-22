<?php
include 'db.php';
session_start();

$userID = $_SESSION['user_id']; // Adjust this according to your session variable

$sql = "SELECT COUNT(*) as count FROM cart_items WHERE id = '$userID'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$response = array('success' => true, 'cart_count' => $row['count']);
echo json_encode($response);

$conn->close();
?>
