<?php
session_start();
include 'db.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response = array(
        'success' => false,
        'message' => 'You must be logged in to buy a book.'
    );
    echo json_encode($response);
    exit;
}

$userid = $_SESSION['user_id']; // Assuming 'user_id' is the user's identifier in the session

// Function to get cart count
function getCartCount($conn, $userid) {
    $count_sql = "SELECT COUNT(*) AS count FROM cart_items WHERE id = ?";
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param("i", $userid); // Assuming 'id' is an integer in the database
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookID = $_POST['book_ID'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $finalPrice = $_POST['final_price'];
    $photo = $_POST['photo'];

    // Validate input data
    if (empty($bookID) || empty($title) || empty($price) || empty($finalPrice) || empty($photo)) {
        $response = array(
            'success' => false,
            'message' => 'Missing required book information.'
        );
        echo json_encode($response);
        exit;
    }

    // Check if the book is already in the cart for this user
    $check_sql = "SELECT * FROM cart_items WHERE id = ? AND book_ID = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $userid, $bookID); // Updated to use "ii" for two integers
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Book already exists in cart
        $response = array(
            'success' => false,
            'message' => 'This book is already in your cart.'
        );
    } else {
        // Insert into cart_items table
        $insert_sql = "INSERT INTO cart_items (id, book_ID, title, price, final_price, photo, added_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iissds", $userid, $bookID, $title, $price, $finalPrice, $photo);

        if ($stmt_insert->execute()) {
            $response = array(
                'success' => true,
                'cart_count' => getCartCount($conn, $userid) // Update cart count
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to add book to cart.',
                'error' => $stmt_insert->error // Include the error message for debugging
            );
        }
    }

    echo json_encode($response);
    exit;
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method.'
    );
    echo json_encode($response);
    exit;
}
?>
