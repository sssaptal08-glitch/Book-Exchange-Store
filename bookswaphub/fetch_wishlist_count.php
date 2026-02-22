<?php
session_start();
include 'db.php';

$response = ['success' => false, 'wishlist_count' => 0];

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Adjust the query based on your table structure
    $query = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $response['success'] = true;
        $response['wishlist_count'] = $row['wishlist_count'];
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
