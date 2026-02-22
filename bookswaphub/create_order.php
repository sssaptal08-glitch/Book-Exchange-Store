<?php
include 'db.php';
session_start();

$userID = $_SESSION['user_id'];
$paymentID = $_POST['razorpay_payment_id'];
$name = $_POST['name'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = $_POST['zipcode'];
$totalAmount = $_POST['total_amount'];
$orderStatus = 'Pending';

// Insert the order into the database
$sql = "INSERT INTO orders (user_id, payment_id, name, address, city, state, zipcode, total_amount, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isssssiss', $userID, $paymentID, $name, $address, $city, $state, $zipcode, $totalAmount, $orderStatus);

if ($stmt->execute()) {
    // Clear the cart after successful order
    $sql = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    
    // Redirect to a confirmation page
    header('Location: order_confirmation.php'); // Adjust this path as needed
    exit();
} else {
    // Handle the error in a more subtle way or log it
    // Optionally redirect to an error page or display a friendly error message
    header('Location: error.php'); // Adjust this path as needed
    exit();
}

$stmt->close();
$conn->close();
?>
