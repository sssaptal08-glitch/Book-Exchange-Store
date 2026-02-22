<?php
session_start();
include 'db.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $response = array(
        'success' => false,
        'message' => 'You must be logged in to manage your wishlist.'
    );
    echo json_encode($response);
    exit;
}

$username = $_SESSION['username'];

// Function to get wishlist count
function getWishlistCount($conn, $username) {
    $count_sql = "SELECT COUNT(*) AS count FROM wishlist WHERE username = ?";
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

// Check if the action is set and valid
if (isset($_POST['action']) && in_array($_POST['action'], ['addToWishlist', 'deleteFromWishlist'])) {
    $action = $_POST['action'];
    $book_id = $_POST['book_id'];

    if ($action == 'addToWishlist') {
        // Check if the book is already in the wishlist for this user
        $check_sql = "SELECT * FROM wishlist WHERE username = ? AND book_ID = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("si", $username, $book_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Book already exists in wishlist
            $response = array(
                'success' => false,
                'message' => 'This book is already in your wishlist.'
            );
        } else {
            // Insert into wishlist table
            $insert_sql = "INSERT INTO wishlist (username, book_ID) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("si", $username, $book_id);

            if ($stmt_insert->execute()) {
                $response = array(
                    'success' => true,
                    'wishlist_count' => getWishlistCount($conn, $username) // Update wishlist count
                );
            } else {
                $response = array('success' => false, 'message' => $conn->error);
            }
        }
    } elseif ($action == 'deleteFromWishlist') {
        // Delete item from wishlist table
        $delete_sql = "DELETE FROM wishlist WHERE username = ? AND book_ID = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("si", $username, $book_id);

        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'wishlist_count' => getWishlistCount($conn, $username), // Update wishlist count
                'message' => 'Item removed from wishlist.'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => $conn->error
            );
        }
    }

    echo json_encode($response);
    exit;
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request.'
    );
    echo json_encode($response);
    exit;
}
?>
