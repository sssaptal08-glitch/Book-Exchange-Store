<?php
session_start();
include('db.php'); // Ensure this path is correct and the file exists

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch exchange requests from the database
$sql = "SELECT * FROM requests WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$user_id = $_SESSION['user_id'];
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exchange Page</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .exchange-container {
            width: 90%;
            max-width: 1200px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .exchange-header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .exchange-header h2 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }
        .requests-list {
            margin: 20px 0;
        }
        .request-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .request-item img {
            width: 50px;
            height: auto;
            border-radius: 5px;
            margin-right: 10px;
        }
        .request-item .request-details {
            display: flex;
            align-items: center;
        }
        .request-item .request-details p {
            margin: 0;
        }
        .approve-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
        .deny-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="exchange-container">
    <div class="exchange-header">
        <h2>Exchange Requests</h2>
    </div>
    <div class="requests-list">
        <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $request): ?>
                <div class="request-item">
                    <div class="request-details">
                        <img src="<?php echo htmlspecialchars($request['book_photo']); ?>" alt="Book Image">
                        <p><?php echo htmlspecialchars($request['book_title']); ?></p>
                        <form action="approve_request.php" method="post">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" class="approve-button">Approve</button>
                        </form>
                        <form action="deny_request.php" method="post">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" class="deny-button">Deny</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No exchange requests found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
