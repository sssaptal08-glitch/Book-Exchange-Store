<?php
session_start();
include('db.php'); // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $book_title = $_POST['book_title'];
    $book_photo = $_POST['book_photo'];
    $user_id = $_SESSION['user_id'];

    // Insert the request into your database
    $sql = "INSERT INTO requests (user_id, book_id, book_title, book_photo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $book_id, $book_title, $book_photo);

    if ($stmt->execute()) {
        echo "Request sent successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
