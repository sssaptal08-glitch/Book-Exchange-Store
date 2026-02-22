<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }

    $userID = $_SESSION['user_id'];
    $bookID = $_POST['book_id'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND book_ID = ?");
    $stmt->bind_param("ii", $userID, $bookID);

    if ($stmt->execute()) {
        echo "Item deleted successfully";
    } else {
        echo "Error deleting item: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
