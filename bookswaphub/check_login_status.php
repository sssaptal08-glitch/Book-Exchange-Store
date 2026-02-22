<?php
session_start();
include 'db.php';

$response = array();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $response['loggedIn'] = true;
    $response['wishlist_count'] = getWishlistCount($conn, $username); // You can reuse the function from add_to_wishlist.php
} else {
    $response['loggedIn'] = false;
}

echo json_encode($response);
?>
