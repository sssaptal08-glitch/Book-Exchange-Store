<?php
include 'db.php';
session_start();

$orderID = $_POST['order_id'];
$userID = $_SESSION['user_id'];

$sql = "UPDATE orders SET order_status = 'Cancelled' WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $orderID, $userID);

if ($stmt->execute()) {
    echo 'Order cancelled successfully';
} else {
    echo 'Error: ' . $conn->error;
}

$stmt->close();
$conn->close();
?>
