<?php
session_start();
require 'Model.php'; // Include your model class

// Check for the success message in the session
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$query = "SELECT r.id, r.book_id, r.requested_book_id, b1.title AS requested_book_title, b1.photo AS requested_book_photo, b2.title AS offered_book_title, b2.photo AS offered_book_photo, u.username AS sender_username, u.id AS sender_id
          FROM requests r
          JOIN books b1 ON r.book_id = b1.book_ID
          JOIN books b2 ON r.requested_book_id = b2.book_ID
          JOIN users u ON r.user_id = u.id
          WHERE r.requested_book_id IN (SELECT book_ID FROM books WHERE user_id = ?) AND r.status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            padding: 20px;
        }
        .notification {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .book-image {
            max-width: 100px;
            margin-right: 20px;
        }
        .book-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .book-info {
            flex: 1;
        }
        .buttons {
            margin-top: 10px;
        }
        .buttons button {
            margin-right: 10px;
        }
        .view-profile {
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h2>Notifications</h2>
    <?php if (empty($requests)): ?>
        <p>No notifications at the moment.</p>
    <?php else: ?>
        <?php foreach ($requests as $request): ?>
            <div class="notification">
                <div class="book-image">
                    <img src="<?php echo htmlspecialchars($request['requested_book_photo']); ?>" alt="Book Image">
                </div>
                <div class="book-info">
                    <h5><?php echo htmlspecialchars($request['requested_book_title']); ?></h5>
                    <p><strong>Requested by:</strong> <?php echo htmlspecialchars($request['sender_username']); ?></p>
                    <p><strong>Offered Book:</strong> <?php echo htmlspecialchars($request['offered_book_title']); ?></p>
                    <div class="book-image">
                        <img src="<?php echo htmlspecialchars($request['offered_book_photo']); ?>" alt="Offered Book Image">
                    </div>
                </div>
                <div class="buttons">
                    <button class="accept btn btn-success" data-request-id="<?php echo $request['id']; ?>">Accept</button>
                    <button class="reject btn btn-danger" data-request-id="<?php echo $request['id']; ?>">Reject</button>
                    <a href="users_dasboard.php?user_id=<?php echo $request['sender_id']; ?>" class="view-profile">sender Profile</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('.accept').on('click', function() {
            var requestId = $(this).data('request-id');
            $.ajax({
                url: 'handle_notification.php',
                type: 'POST',
                data: { request_id: requestId, action: 'accept' },
                success: function(response) {
                    if (response.success) {
                        alert('Request accepted!');
                        location.reload();
                    } else {
                        alert('Failed to accept request: ' + response.message);
                    }
                },
                error: function() {
                    alert('There was an error processing the request.');
                }
            });
        });

        $('.reject').on('click', function() {
            var requestId = $(this).data('request-id');
            $.ajax({
                url: 'handle_notification.php',
                type: 'POST',
                data: { request_id: requestId, action: 'reject' },
                success: function(response) {
                    if (response.success) {
                        alert('Request rejected!');
                        location.reload();
                    } else {
                        alert('Failed to reject request: ' + response.message);
                    }
                },
                error: function() {
                    alert('There was an error processing the request.');
                }
            });
        });
    });
</script>
</body>
</html>
