<?php
session_start();
include 'db.php';

$response = ['success' => false, 'message' => '', 'bookExists' => false];

if (isset($_SESSION['username']) && isset($_POST['book_ID'])) {
    $username = $_SESSION['username'];
    $bookId = $_POST['book_ID'];

    // Check if book_ID exists in the wishlist
    $checkQuery = "SELECT * FROM wishlist WHERE username = ? AND book_ID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("si", $username, $bookId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Delete the item from the wishlist
        $deleteQuery = "DELETE FROM wishlist WHERE username = ? AND book_ID = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("si", $username, $bookId);

        if ($deleteStmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Item removed from wishlist.';
        } else {
            $response['message'] = 'Failed to remove item from wishlist.';
        }

        $deleteStmt->close();
    } else {
        $response['message'] = 'Item not found in wishlist.';
    }

    $checkStmt->close();
    $response['bookExists'] = ($checkResult->num_rows > 0); // Set bookExists based on query result
}

$conn->close();
echo json_encode($response);
?>
