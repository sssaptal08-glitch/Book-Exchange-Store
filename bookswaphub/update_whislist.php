<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo 'User not logged in';
    exit;
}

$username = $_SESSION['username'];
$book_id = $_POST['book_ID'];
$action = $_POST['action'];

if ($action === 'add') {
    $sql = "INSERT INTO wishlist (username, book_ID) VALUES ($username, $book_id)";
} else if ($action === 'remove') {
    $sql = "DELETE FROM wishlist WHERE user_id = $user_id AND book_ID = $book_id";
}

if ($conn->query($sql) === TRUE) {
    echo 'Success';
} else {
    http_response_code(500);
    echo 'Error: ' . $conn->error;
}

$conn->close();
?>
