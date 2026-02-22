<?php
include 'db.php';
session_start();
$response = array('success' => false, 'cart_count' => 0, 'message' => '');

if (isset($_POST['book_ID'])) {
    $bookID = $_POST['book_ID'];
    $userID = $_SESSION['user_id']; // Adjust this according to your session variable

    // SQL query to remove the item from the cart
    $sql = "DELETE FROM cart_items WHERE book_ID = '$bookID' AND id = '$userID'";
    if ($conn->query($sql) === TRUE) {
        // SQL query to get the updated cart count
        $sql = "SELECT COUNT(*) AS cart_count FROM cart_items WHERE id = '$userID'";
        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $response['cart_count'] = $row['cart_count'];
            $response['success'] = true;
        }
    } else {
        $response['message'] = 'Failed to remove book from cart.';
    }
} else {
    $response['message'] = 'Invalid book ID.';
}

echo json_encode($response);
$conn->close();
?>