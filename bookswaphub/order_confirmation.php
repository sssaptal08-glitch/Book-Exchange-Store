<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

// Fetch the latest order for the user without joins
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";

// Prepare the statement and check for errors
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param('i', $userID);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "No recent order found.";
    exit();
}

$orderID = $order['id'];

$stmt->close();

// Fetch the book details related to the order
$sql = "
SELECT b.title AS book_title, b.author AS book_author, b.photo AS book_image
FROM cart_items ci
JOIN books b ON ci.book_id = b.book_ID
WHERE ci.id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param('i', $userID);
$stmt->execute();
$result = $stmt->get_result();
$bookDetails = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
    }
    .confirmation-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      overflow: hidden;
    }
    .confirmation-container h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #4CAF50;
    }
    .confirmation-container p {
      margin: 10px 0;
      color: #555;
      font-size: 16px;
    }
    .order-details {
      margin-top: 20px;
      text-align: left;
    }
    .order-details p {
      font-weight: bold;
      color: #333;
      margin: 5px 0;
    }
    .book-item {
      display: flex;
      align-items: center;
      margin-top: 20px;
      border-top: 1px solid #ddd;
      padding-top: 20px;
    }
    .book-item img {
      max-width: 100px;
      margin-right: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .book-item p {
      margin: 0;
      color: #555;
    }
    .back-to-orders {
      display: inline-block;
      margin-top: 30px;
      padding: 12px 25px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }
    .back-to-orders:hover {
      background-color: #45a049;
    }
    @media (max-width: 768px) {
      .confirmation-container {
        padding: 20px;
      }
      .book-item {
        flex-direction: column;
        align-items: flex-start;
      }
      .book-item img {
        margin: 0 0 10px 0;
      }
    }
  </style>
</head>
<body>
  <div class="confirmation-container">
    <h2>Order Confirmation</h2>
    <p>Thank you for your purchase! Your order has been placed successfully.</p>
    <div class="order-details">
      <p>Order ID: <?php echo $order['id']; ?></p>
      <p>Payment ID: <?php echo $order['payment_id']; ?></p>
      <p>Name: <?php echo $order['name']; ?></p>
      <p>Address: <?php echo $order['address']; ?></p>
      <p>City: <?php echo $order['city']; ?></p>
      <p>State: <?php echo $order['state']; ?></p>
      <p>Zipcode: <?php echo $order['zipcode']; ?></p>
      <p>Total Amount: ₹<?php echo $order['total_amount']; ?></p>
      <p>Status: <?php echo $order['order_status']; ?></p>
      <?php foreach ($bookDetails as $book): ?>
      <div class="book-item">
        <img src="<?php echo $book['book_image']; ?>" alt="Book Image">
        <div>
          <p><strong>Book Title:</strong> <?php echo $book['book_title']; ?></p>
          <p><strong>Author:</strong> <?php echo $book['book_author']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <a href="orders.php" class="back-to-orders">Back to Orders</a>
  </div>
</body>
</html>
