<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if (!isset($_POST['book_id']) || !isset($_POST['requested_book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Book ID is missing.']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$user_id = $_SESSION['user_id'];
$book_id = intval($_POST['book_id']);
$requested_book_id = intval($_POST['requested_book_id']);

$query = "INSERT INTO requests (user_id, book_id, requested_book_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param('iii', $user_id, $book_id, $requested_book_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Exchange request sent successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error executing statement: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
