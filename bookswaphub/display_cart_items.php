<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
  <title>Your Cart</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* Reset default styles */
    * {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      outline: none;
      border: none;
      text-decoration: none;
      text-transform: capitalize;
      transition: all .2s linear;
    }

    .cart-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .cart-item {
      display: flex;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 20px;
      overflow: hidden; /* Ensure items don't overflow */
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .cart-item-img {
      flex: 0 0 150px; /* Fixed width for image */
    }

    .cart-item-img img {
      width: 100%;
      height: auto;
      border-radius: 8px 0 0 8px; /* Rounded corners on left side */
    }

    .cart-item-details {
      flex: 1;
      padding: 15px;
      display: flex;
      flex-direction: column;
    }

    .cart-item-details h3 {
      font-size: 18px;
      margin-bottom: 10px;
      color: #333;
    }

    .cart-item-details p {
      margin: 5px 0;
      color: #666;
    }

    .cart-item-price {
      flex: 0 0 150px; /* Fixed width for price */
      text-align: center;
      padding: 15px;
      border-left: 1px solid #ddd;
    }

    .cart-item-price p {
      font-size:1.4rem;;
      font-weight: bold;
    }

    .cart-item-actions {
      padding: 15px;
      flex: 0 0 100px; /* Fixed width for actions */
      display: flex;
      align-items: center;
      justify-content: center;
      border-left: 1px solid #ddd;
    }

    .cart-item-actions button {
      background-color: #f44336;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 3px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .cart-item-actions button:hover {
      background-color: #d32f2f;
    }

    .cart-summary {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      margin-top: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .cart-summary h2 {
      font-size: 20px;
      margin-bottom: 10px;
    }

    .cart-summary p {
      font-size: 16px;
      margin-bottom: 10px;
    }

    .cart-summary button {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .cart-summary button:hover {
      background-color: #45a049;
    }

    .modal-content {
      border-radius: 10px;
    }

    .modal-header {
      border-bottom: 2px solid #f1f1f1;
    }

    .modal-header .close {
      font-size: 1.5rem;
    }

    .modal-body {
      padding: 2rem;
    }

    .modal-body .form-group {
      margin-bottom: 1.5rem;
    }

    .modal-body input, .modal-body textarea {
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .modal-footer {
      border-top: 2px solid #f1f1f1;
      padding: 1rem 2rem;
    }

    .modal-footer .btn {
      border-radius: 5px;
    }

    .btn-success {
      background-color: #27ae60;
      border: none;
    }

    .btn-success:hover {
      background-color: #219150;
    }
    p{
      font-size:1.8rem;
    }
  </style>
</head>
<body>
<?php
session_start(); // Start session at the very beginning of the file

include 'nav.php';
?>
  <div class="cart-container">
  <?php
    include 'db.php';

    // Check if user_id is set in the session
    if (isset($_SESSION['user_id'])) {
        $userID = $_SESSION['user_id'];

        $sql = "SELECT * FROM cart_items WHERE id = '$userID'";
        $result = $conn->query($sql);

        $cartItemCount = $result->num_rows;
        $totalAmount = 0;

        if ($cartItemCount > 0) {
            echo "<h2>Cart Items:</h2>";
            while ($row = $result->fetch_assoc()) {
                $totalAmount += $row['final_price'];
                echo '<div class="cart-item" data-id="' . $row['id'] . '">';
                echo '<div class="cart-item-img">';
                echo '<img src="' . $row['photo'] . '" alt="' . $row['title'] . '">';
                echo '</div>';
                echo '<div class="cart-item-details">';
                echo '<h3>' . $row['title'] . '</h3>';
                echo '<p><strong>Price:</strong> ₹' . $row['price'] . '</p>';
                echo '<p><strong>Final Price:</strong> ₹' . $row['final_price'] . '</p>';
                echo '</div>';
                echo '<div class="cart-item-price">';
                echo '<p>₹' . $row['final_price'] . '</p>';
                echo '</div>';
                echo '<div class="cart-item-actions">';
                echo '<button class="remove-btn" data-book-id="' . $row['book_ID'] . '">Remove</button>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>No cart items found.</p>";
        }
    } else {
        echo "<p>Please log in to view your cart.</p>";
    }

    $conn->close();
    ?>
    <div class="cart-summary">
      <h2>Cart Summary</h2>
      <p>Number of Items: <span id="item-count"><?php echo $cartItemCount; ?></span></p>
      <p>Total Amount: ₹<span id="total-amount"><?php echo $totalAmount; ?></span></p>
      <button id="buy-now">Buy Now</button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="deliveryModal" tabindex="-1" aria-labelledby="deliveryModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deliveryModalLabel">Delivery Address</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="delivery-form">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" placeholder="Name">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" placeholder="Address"></textarea>
              </div>
              <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" placeholder="City">
              </div>
              <div class="form-group">
                <label for="state">State</label>
                <input type="text" class="form-control" id="state" placeholder="State">
              </div>
              <div class="form-group">
                <label for="zipcode">Zip Code</label>
                <input type="text" class="form-control" id="zipcode" placeholder="Zip Code">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="submit-order">Submit Order</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>


$(document).ready(function() {
    $('.remove-btn').on('click', function() {
        var bookID = $(this).data('book-id');
        var button = $(this); // Store the clicked button

        $.ajax({
            url: 'delete_cart_item.php',
            type: 'POST',
            data: {
                book_ID: bookID
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    // Remove the item from the UI
                    button.closest('.cart-item').remove();
                    // Update the item count in the summary
                    $('#item-count').text(data.cart_count);
                } else {
                    alert('Failed to remove book from cart: ' + data.message);
                }
            },
            error: function() {
                alert('There was an error removing the book from the cart.');
            }
        });
    });
});



    $(document).ready(function() {
      $('#buy-now').on('click', function() {
        $('#deliveryModal').modal('show');
      });

      $('#submit-order').on('click', function() {
        var name = $('#name').val();
        var address = $('#address').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var zipcode = $('#zipcode').val();

        if (!name || !address || !city || !state || !zipcode) {
          alert('Please fill out all fields.');
          return;
        }

        var options = {
  "key": "rzp_test_FMHsZgbtpcuXFD",
  "amount": <?php echo $totalAmount * 100; ?>,
  "currency": "INR",
  "name": "BookSwapHub",
  "description": "Test Transaction",
  "handler": function (response) {
    $.ajax({
      url: 'create_order.php',
      type: 'POST',
      data: {
        razorpay_payment_id: response.razorpay_payment_id,
        name: name,
        address: address,
        city: city,
        state: state,
        zipcode: zipcode,
        total_amount: <?php echo $totalAmount; ?>
      },
      success: function(orderResponse) {
        alert('Order placed successfully!');
        $('#deliveryModal').modal('hide');
        window.location.href = 'order_confirmation.php';
      },
      error: function() {
        alert('There was an error placing the order.');
      }
    });
  },
  "prefill": {
    "name": name
  },
  "theme": {
    "color": "#3399cc"
  },
  "method": {
    "netbanking": true,
    "card": true,
    "wallet": true,
    "upi": true,
    "qr_code": true // Hypothetical QR code support
  }
};
        var rzp1 = new Razorpay(options);
        rzp1.open();
      });
    });
  </script>
</body>
</html>
