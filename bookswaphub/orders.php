<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

// Fetch orders for the user, ordering by status and then by ID
$sql = "
SELECT * FROM orders 
WHERE user_id = ? 
ORDER BY 
  CASE 
    WHEN order_status = 'Canceled' THEN 1
    ELSE 0
  END, id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
  <title>Your Orders</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .order-container {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
      font-size: 30px;
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .order {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .order:hover {
      transform: translateY(-5px);
    }

    .order p {
      margin: 5px 0;
      color: #333;
      font-size: 16px;
    }

    .order .order-status {
      font-weight: bold;
      color: #f44336;
    }

    .order .order-status.completed {
      color: #4CAF50;
    }

    .order .order-actions {
      margin-top: 10px;
      display: flex;
      justify-content: flex-end;
    }

    .order .order-actions button {
      background-color: #f44336;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .order .order-actions button:hover {
      background-color: #d32f2f;
    }

    .book-item {
      display: flex;
      align-items: center;
      margin-top: 10px;
      border-top: 1px solid #ddd;
      padding-top: 10px;
    }

    .book-item img {
      max-width: 60px;
      margin-right: 15px;
      border-radius: 3px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .book-item p {
      margin: 0;
      color: #555;
    }

    .book-item .book-title {
      font-weight: bold;
    }

    @media (max-width: 600px) {
      .order-container {
        padding: 15px;
      }

      .order {
        padding: 10px;
      }

      .order p {
        font-size: 14px;
      }

      .order .order-actions button {
        padding: 6px 12px;
      }

      .book-item img {
        max-width: 50px;
        margin-right: 10px;
      }
    }
  </style>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="order-container">
    <h2>Your Orders</h2>
    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $orderID = $row['id'];
        $orderStatusClass = strtolower($row['order_status']);
        
        echo '<div class="order">';
        echo '<p>Order ID: ' . $orderID . '</p>';
        echo '<p>Payment ID: ' . $row['payment_id'] . '</p>';
        echo '<p>Name: ' . $row['name'] . '</p>';
        echo '<p>Address: ' . $row['address'] . '</p>';
        echo '<p>City: ' . $row['city'] . '</p>';
        echo '<p>State: ' . $row['state'] . '</p>';
        echo '<p>Zipcode: ' . $row['zipcode'] . '</p>';
        echo '<p>Total Amount: ₹' . $row['total_amount'] . '</p>';
        echo '<p class="order-status ' . $orderStatusClass . '">Status: ' . $row['order_status'] . '</p>';

        // Fetch book details for the order
        $bookSql = "
        SELECT b.title AS book_title, b.author AS book_author, b.photo AS book_image
        FROM cart_items ci
        JOIN books b ON ci.book_id = b.book_ID
        JOIN orders o ON ci.id = o.user_id
        WHERE o.id = ?"; // Order ID as the parameter

        $bookStmt = $conn->prepare($bookSql);
        if ($bookStmt === false) {
            die("Error preparing the book statement: " . $conn->error);
        }

        $bookStmt->bind_param('i', $orderID);
        if (!$bookStmt->execute()) {
            die("Error executing the book statement: " . $bookStmt->error);
        }

        $bookResult = $bookStmt->get_result();
        $bookDetails = $bookResult->fetch_all(MYSQLI_ASSOC);

        foreach ($bookDetails as $book) {
          echo '<div class="book-item">';
          echo '<img src="' . htmlspecialchars($book['book_image']) . '" alt="Book Image">';
          echo '<div>';
          echo '<p class="book-title">' . htmlspecialchars($book['book_title']) . '</p>';
          echo '<p>Author: ' . htmlspecialchars($book['book_author']) . '</p>';
          echo '</div>';
          echo '</div>';
        }

        $bookStmt->close();

        if ($row['order_status'] === 'Pending') {
          echo '<div class="order-actions">';
          echo '<button class="cancel-order-btn" data-order-id="' . $orderID . '">Cancel Order</button>';
          echo '</div>';
        }

        echo '</div>';
      }
    } else {
      echo "<p>No orders found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(document).ready(function() {
    $('.cancel-order-btn').on('click', function() {
      var orderID = $(this).data('order-id');

      $.ajax({
        url: 'cancel_order.php',
        type: 'POST',
        data: { order_id: orderID },
        success: function(response) {
          alert(response);
          location.reload();
        },
        error: function() {
          alert('There was an error cancelling the order.');
        }
      });
    });
  });
  </script>
</body>
</html>
