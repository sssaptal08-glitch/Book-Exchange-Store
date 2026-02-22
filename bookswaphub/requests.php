<?php
session_start();
require 'db.php';

// Redirect to login if user not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Function to send a request
function sendRequest($conn, $book_id, $requested_book_id, $user_id) {
    $query = "INSERT INTO requests (book_id, requested_book_id, user_id, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $book_id, $requested_book_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Function to delete a request
function deleteRequest($conn, $request_id, $user_id) {
    $query = "DELETE FROM requests WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $request_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Handle sending a request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $book_id = $_POST['book_id'];
    $requested_book_id = $_POST['requested_book_id'];
    $user_id = $_SESSION['user_id'];

    if (sendRequest($conn, $book_id, $requested_book_id, $user_id)) {
        header("Location: requests.php?msg=sent");
        exit;
    } else {
        echo "Failed to send request.";
    }
}

// Handle deleting a request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    $request_id = $_POST['request_id'];
    $user_id = $_SESSION['user_id'];

    if (deleteRequest($conn, $request_id, $user_id)) {
        header("Location: requests.php?msg=deleted");
        exit;
    } else {
        echo "Failed to delete request.";
    }
}

// Fetch the logged-in user's ID
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Fetch the sent and received requests
$query = "SELECT r.id, r.book_id, r.requested_book_id, 
                 b1.title AS requested_book_title, b1.photo AS requested_book_photo, 
                 b2.title AS offered_book_title, b2.photo AS offered_book_photo, 
                 u.username AS sender_username, u.id AS sender_id,
                 ur.username AS receiver_username, ur.id AS receiver_id,
                 r.status
          FROM requests r
          JOIN books b1 ON r.book_id = b1.book_id
          JOIN books b2 ON r.requested_book_id = b2.book_id
          JOIN users u ON r.user_id = u.id
          JOIN users ur ON b1.user_id = ur.id
          WHERE (b1.user_id = ? OR r.user_id = ?) AND (r.status = 'pending' OR r.status = 'accepted' OR r.status = 'rejected')";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$sent_requests = [];
$received_requests = [];
while ($row = $result->fetch_assoc()) {
    if ($row['sender_id'] == $user_id) {
        $sent_requests[] = $row;
    } else {
        $received_requests[] = $row;
    }
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600&display=swap');
        
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
        .status {
            margin-top: 10px;
        }
        .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        h2{
            text-align:center;
        }

        .nav-tabs .nav-link{
            font-size:1.7rem;
        }

    .progress-bar {
    display: ruby;
    justify-content: space-between;
    align-items: center;
    border: none; /* Remove the border if not needed */
}

.progress-bar .step {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 70px;
}

.progress-bar .step .circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #ddd; /* Default background color */
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.progress-bar .step .label {
    margin-top: 5px;
    text-align: center;
    font-size: 14px;
    /* gap:80px; */
}

.progress-bar .step.pending .circle {
    background: #f0ad4e; /* Color for pending status */
}

.progress-bar .step.accepted .circle {
    background: #5cb85c; /* Color for accepted status */
}

.progress-bar .step.rejected .circle {
    background: #d9534f; /* Color for rejected status */
}


.progress-bar .line.active {
    background: #5cb85c; /* Active line color */
}
.label{
    color:black;
    justify-content: space-between;
}
h5{
    font-size:2rem;
}

p{
    font-size:1.4rem;
}
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
    <h2>Book Requests</h2>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sent'): ?>
        <div class="alert alert-success" role="alert">
            Request sent successfully.
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success" role="alert">
            Request deleted successfully.
        </div>
    <?php endif; ?>
    
    <ul class="nav nav-tabs" id="requestTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="sent-requests-tab" data-toggle="tab" href="#sent-requests" role="tab" aria-controls="sent-requests" aria-selected="true">Sent Requests</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="received-requests-tab" data-toggle="tab" href="#received-requests" role="tab" aria-controls="received-requests" aria-selected="false">Received Requests</a>
        </li>
    </ul>
    
    <div class="tab-content" id="requestTabsContent">
        <div class="tab-pane fade show active" id="sent-requests" role="tabpanel" aria-labelledby="sent-requests-tab">
            <h3>Sent Requests</h3>
            <?php if (empty($sent_requests)): ?>
                <p>No sent requests at the moment.</p>
            <?php else: ?>
                <?php foreach ($sent_requests as $request): ?>
                    <div class="notification">
                        <div class="book-image">
                            <img src="<?php echo htmlspecialchars($request['requested_book_photo']); ?>" alt="Book Image">
                        </div>
                        <div class="book-image">
                            <img src="<?php echo htmlspecialchars($request['offered_book_photo']); ?>" alt="Book Image">
                        </div>
                        <div class="book-info">
                            <h5>Requested Book: <?php echo htmlspecialchars($request['requested_book_title']); ?> by <?php echo htmlspecialchars($request['receiver_username']); ?></h5>
                            <h5>Offered Book: <?php echo htmlspecialchars($request['offered_book_title']); ?> by <?php echo htmlspecialchars($request['sender_username']); ?></h5>
                            <div class="progress-bar bg-none">
                                <div class="step <?php echo ($request['status'] === 'pending') ? 'pending' : ''; ?>">
                                    <div class="circle">1</div>
                                    <div class="label">Pending</div>
                                </div>
                                <?php if ($request['status'] === 'accepted'): ?>
                                    <div class="line active"></div>
                                    <div class="step accepted">
                                        <div class="circle">2</div>
                                        <div class="label">Accepted</div>
                                    </div>
                                <?php elseif ($request['status'] === 'rejected'): ?>
                                    <div class="line"></div>
                                    <div class="step rejected">
                                        <div class="circle">&#x2716;</div>
                                        <div class="label">Rejected</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="buttons">
                            <?php if ($request['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="delete_request" class="btn btn-danger">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="tab-pane fade" id="received-requests" role="tabpanel" aria-labelledby="received-requests-tab">
            <h3>Received Requests</h3>
            <?php if (empty($received_requests)): ?>
                <p>No received requests at the moment.</p>
            <?php else: ?>
                <?php foreach ($received_requests as $request): ?>
                    <div class="notification">
                        <div class="book-image">
                            <img src="<?php echo htmlspecialchars($request['requested_book_photo']); ?>" alt="Book Image">
                        </div>
                        <div class="book-image">
                            <img src="<?php echo htmlspecialchars($request['offered_book_photo']); ?>" alt="Book Image">
                        </div>
                        <div class="book-info">
                            <h5>Requested Book: <?php echo htmlspecialchars($request['requested_book_title']); ?> by <?php echo htmlspecialchars($request['receiver_username']); ?></h5>
                            <h5>Offered Book: <?php echo htmlspecialchars($request['offered_book_title']); ?> by <?php echo htmlspecialchars($request['sender_username']); ?></h5>
                            <div class="progress-bar">
                                <div class="step <?php echo ($request['status'] === 'pending') ? 'pending' : ''; ?>">
                                    <div class="circle">1</div>
                                    <div class="label">Pending</div>
                                </div>
                                <?php if ($request['status'] === 'accepted'): ?>
                                    <div class="line active"></div>
                                    <div class="step accepted">
                                        <div class="circle">2</div>
                                        <div class="label">Accepted</div>
                                    </div>
                                <?php elseif ($request['status'] === 'rejected'): ?>
                                    <div class="line"></div>
                                    <div class="step rejected">
                                        <div class="circle">&#x2716;</div>
                                        <div class="label">Rejected</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="buttons">
                            <?php if ($request['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="delete_request" class="btn btn-danger">Cancel</button>
                                </form>
                                <form method="POST" action="accept_request.php" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="accept_request" class="btn btn-success">Accept</button>
                                </form>
                                <form method="POST" action="reject_request.php" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="reject_request" class="btn btn-warning">Reject</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
