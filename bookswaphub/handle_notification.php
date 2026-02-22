<?php
session_start();
require 'db.php'; // Ensure this file has your database connection code

header('Content-Type: application/json'); // Set content type to JSON

$response = ['success' => false, 'message' => ''];

// Check if request_id and action are set in $_POST
if (isset($_POST['request_id'], $_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    // Ensure action is either 'accept' or 'reject'
    if ($action === 'accept') {
        $status = 'accepted';
        $query = "UPDATE requests SET status = ? WHERE id = ?";
    } elseif ($action === 'reject') {
        $status = 'rejected';
        $query = "UPDATE requests SET status = ? WHERE id = ?";
    } else {
        $response['message'] = 'Invalid action specified.';
        echo json_encode($response);
        exit;
    }

    // Prepare and execute SQL update query
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind parameters and execute query
        $stmt->bind_param("si", $status, $request_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            if ($action === 'accept') {
                $response['message'] = 'Request accepted successfully.';
            } elseif ($action === 'reject') {
                $response['message'] = 'Request rejected successfully.';
            }
        } else {
            $response['message'] = 'Database error: ' . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        $response['message'] = 'Prepare error: ' . $conn->error;
    }
} else {
    $response['message'] = 'Invalid request parameters.';
}

// Close database connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>
