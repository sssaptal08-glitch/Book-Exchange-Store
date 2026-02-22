<?php
session_start();
require 'Model.php'; // Include your model class
error_reporting(0);
// Check for the success message in the session
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    // Clear the success message from the session
    unset($_SESSION['success_message']);
}

if (!isset($_GET['id'])) {
    echo "Book ID is missing.";
    exit;
}

// Establish database connection (assuming $conn is your PDO or mysqli connection)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Instantiate Model class with database connection
$obj = new Model($conn);
$books = $obj->getBooksByUserId($user_id);

$model = new Model($conn); // Pass $conn to Model constructor
$bookId = intval($_GET['id']);
$book = $model->getBookById($bookId);

if (!$book) {
    echo "Book not found.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>Responsive Book Page</title>
    <link rel="stylesheet" href="step.css?v=1.1">
    <!-- Bootstrap CSS -->
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

.book-details {
    display: flex;
    justify-content: space-between;
}

.book-image {
    max-width: 300px;
    margin-right: 20px;
}

.book-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.book-info {
    flex: 1;
    min-width: 300px;
}

.book-info h1 {
    font-size: 3rem; /* Increase the font size for the book title */
    margin-bottom: 20px;
    color: #333;
}

.book-attributes p {
    margin: 10px 0;
    font-size: 1.5rem; /* Increase the font size for book attributes */
    line-height: 1.5;
    color: #555;
}

.book-attributes strong {
    color: #000;
}

.price-and-buttons {
    margin-top: 20px;
}




.modal-body .book-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    margin: 10px;
    width: 150px;
    height: 300px;
}

.modal-body .book-card img {
    max-width: 80px;
    max-height: 100px;
}

.modal-body .book-card .book-details {
    text-align: center;
    flex-grow: 1;
}

.modal-body .book-card .book-details p {
    font-size: 1rem;
    margin: 5px 0;
}

.modal-body .book-card .send-request-button {
    font-size: 1rem;
    padding: 5px 10px;
    margin-top: 10px;
}

.books-section {
    display: flex;
    justify-content: space-around;
}

        /* Professional Button Styles */
.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.75rem 1.5rem; /* Adjusted padding */
    font-size: 1rem; /* Adjusted font size */
    line-height: 1.5;
    border-radius: 0.3rem;
    transition: all 0.2s ease-in-out;
    width: auto; /* Ensure width is flexible */
    min-width: 120px; /* Set a minimum width */
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0056b3;
    border-color: #004085;
}

.btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    color: #fff;
    background-color: #218838;
    border-color: #1e7e34;
}

.btn:focus, .btn:active:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
}

.btn:active {
    box-shadow: inset 0 0.2rem 0.4rem rgba(0, 0, 0, 0.15);
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Specific styles for Buy Now button */
.btn-buy-now {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-buy-now:hover {
    color: #fff;
    background-color: #0056b3;
    border-color: #004085;
}

.btn-buy-now:focus, .btn-buy-now:active:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
}

.btn-buy-now:active {
    box-shadow: inset 0 0.2rem 0.4rem rgba(0, 0, 0, 0.15);
}

.btn-buy-now:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Ensure other buttons retain their original styles */
.btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    color: #fff;
    background-color: #218838;
    border-color: #1e7e34;
}

.btn:focus, .btn:active:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
}

.btn:active {
    box-shadow: inset 0 0.2rem 0.4rem rgba(0, 0, 0, 0.15);
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}


/* Optional: Add any custom classes or additional styling */

    </style>
</head>


<body>
<?php include 'nav.php' ?>
<div class="container">
        <div class="book-details">
            <div class="book-image">
                <img src="<?php echo htmlspecialchars($book['photo']); ?>" alt="Book Cover">
            </div>
            <div class="book-info">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <div class="book-attributes">
                    <p><strong>Condition:</strong> <span><?php echo htmlspecialchars($book['book_condition']); ?></span></p>
                    <p><strong>Subject/Title:</strong> <span><?php echo htmlspecialchars($book['title']); ?></span></p>
                    <p><strong>ISBN:</strong> <span><?php echo htmlspecialchars($book['isbn']); ?></span></p>
                    <p><strong>Type:</strong> <span><?php echo htmlspecialchars($book['type']); ?></span></p>
                    <p><strong>Category:</strong> <span><?php echo htmlspecialchars($book['category']); ?></span></p>
                    <p><strong>Author:</strong> <span><?php echo htmlspecialchars($book['author']); ?></span></p>
                </div>
                <div class="price-and-buttons">
                    <p class="price" style="font-size:xx-large !important; color:blue">₹<?php echo htmlspecialchars($book['final_price']); ?><span class="free-shipping">Free Shipping</span></p>
                    <button class="buy-now btn btn-primary btn-buy-now" 
        data-book-id="<?php echo $book['book_ID']; ?>" 
        data-title="<?php echo htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?>" 
        data-price="<?php echo $book['price']; ?>" 
        data-final-price="<?php echo $book['final_price']; ?>" 
        data-photo="<?php echo htmlspecialchars($book['photo'], ENT_QUOTES, 'UTF-8'); ?>">
    Buy
</button>
<button class="exchange btn btn-success" data-toggle="modal" data-target="#exchangeModal">Exchange</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
    $('.buy-now').on('click', function() {
        var bookID = $(this).data('book-id');
        var title = $(this).data('title');
        var price = $(this).data('price');
        var finalPrice = $(this).data('final-price');
        var photo = $(this).data('photo');

        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: {
                book_ID: bookID,
                title: title,
                price: price,
                final_price: finalPrice,
                photo: photo
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert('Book added to cart!');
                        // Update the cart count in the UI
                        $('#cart-count').text(data.cart_count);
                    } else {
                        alert('Failed to add book to cart: ' + data.message);
                    }
                } catch (e) {
                    alert('Error parsing response: ' + response);
                }
            },
            error: function() {
                alert('There was an error adding the book to the cart.');
            }
        });
    });
});


        document.querySelectorAll('.send-request-button').forEach(function(button) {
    button.addEventListener('click', function() {
        var offeredBookId = this.getAttribute('data-book-id');
        var requestedBookId = document.querySelector('input[name="book_offered_id"]').value;

        fetch('send_exchange_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'user_id=<?php echo $user_id; ?>&book_id=' + offeredBookId + '&requested_book_id=' + requestedBookId
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Add this line for debugging
            if (data.success) {
                alert('Exchange request sent successfully!');
                location.reload();
            } else {
                alert('Failed to send exchange request: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the request.');
        });
    });
});

    </script>

    <!-- Exchange Modal -->
    <div class="modal fade" id="exchangeModal" tabindex="-1" role="dialog" aria-labelledby="exchangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exchangeModalLabel">Exchange Book: <?php echo htmlspecialchars($book['title']); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exchangeForm">
                        <div class="form-group">
                            <label for="bookOfferedId" style="font-size:1.4rem;">Book Offered for Exchange:</label>
                            <!-- Hidden input for book ID -->
                            <input type="hidden" name="book_offered_id" value="<?php echo htmlspecialchars($book['book_ID']); ?>" style="font-size:1.4rem;">
                            <div class="book-details">
                                <!-- Book image -->
                                <img src="<?php echo htmlspecialchars($book['photo']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image" style="max-height: 150px;">
                                <input type="text" class="form-control book-title" style="font-size: 1.5rem; margin-top: 50px;margin-left:30px;text-align: center;" value="<?php echo htmlspecialchars($book['title']); ?>" readonly>
                            </div>
                        </div>

                        <div class="books-section">
                            <div class="section-title">Books Shared</div>
                            <?php foreach ($books as $book): ?>
                                <div class="book-card">
                                    <img src="<?php echo htmlspecialchars($book['photo']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <div class="book-details">
                                        <p><?php echo htmlspecialchars($book['title']); ?></p>
                                    </div>
                                    <button type="button" class="send-request-button btn btn-primary" data-book-id="<?php echo htmlspecialchars($book['book_ID']); ?>">Send Request</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
       document.querySelectorAll('.send-request-button').forEach(function(button) {
    button.addEventListener('click', function() {
        var offeredBookId = this.getAttribute('data-book-id');
        var requestedBookId = document.querySelector('input[name="book_offered_id"]').value;

        fetch('send_exchange_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'user_id=<?php echo $user_id; ?>&book_id=' + offeredBookId + '&requested_book_id=' + requestedBookId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Exchange request sent successfully!');
                location.reload();
            } else {
                alert('Failed to send exchange request: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the request.');
        });
    });
});

    </script>

    <div class="description-section">
        <div class="description">
            <h3><i class="fas fa-book"></i> Description:</h3>
            <p><?php echo htmlspecialchars($book['book_description']); ?></p>
        </div>

        <div class="info">
            <div class="info-item1">
                <i class="fas fa-user"></i>
                <div>Name: <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?></div>
            </div>
            <div class="info-item2">
                <i class="fas fa-map-marker-alt"></i>
            <div>Address: </div><br>
            
            </div>
            <div class="seller-profile">
            <a href="seller_profile.php">seller profile</a>
        </div>
        </div>
        <p><b style="text-decoration:underline;">Our Community</b> :-
        We're not just another shopping website where you buy from professional sellers 
        - we are a vibrant community of students, book lovers across India who deliver happiness to each other!
</p>
    </div>
    <div class="container1">
        <div class="how-it-works">
            <h2>How Does It Work?</h2>
            <div class="steps">
                <div class="step">
                    <img src="image/pngegg.png" alt="Step 1">
                    <h3>Step 1</h3>
                    <p>Seller Posts An Ad</p>
                    <p>Seller posts an ad on Bookswaphub to sell their used books.</p>
                </div>
                <div class="step">
                    <img src="image/pngegg (1).png" alt="Step 2">
                    <h3>Step 2</h3>
                    <p>Buyer Pays Online</p>
                    <p>Buyer makes an online payment to Bookswaphub to buy those books.</p>
                </div>
                <div class="step">
                    <img src="image/pngegg (2).png" alt="Step 3">
                    <h3>Step 3</h3>
                    <p>Seller Ships The Books</p>
                    <p>Seller then ships the books to the buyer.</p>
                </div>
            </div>
        </div>
    </div>
   
    <div class="container-faq">
        <div class="faq-video">
            <div class="video-responsive">
            </div>
        </div>
        <div class="faq-content">
            <h2>Frequently Asked Questions</h2>
            <div class="question">
                <h3>As A Buyer, Why Should I Use Bookswaphub Teleport?</h3>
                <p>You should use Bookswaphub Teleport because after you place the order, seller will receive the payment only after you receive the product. With which you will have a confidence that your payment will only be released to seller after you receives the exact product. You can purchase the used books from any seller across India with confidence.</p>
            </div>
            <div class="question">
                <h3>What If The Seller Doesn't Ships The Item To Buyer?</h3>
                <p>If the items are not shipped within 4 days of order placement, then that order will be auto cancelled and the full money will be refunded back into the account.</p>
            </div>
        </div>
    </div>

        <div class="trust-section">
            <div class="trust-card">
                <h3>Bookswaphub Trust</h3>
                <p>Safe and secure online payment process</p>
                <img src="image/pngegg (3).png" alt="Step 3">
            </div>
            <div class="trust-card" style="background-color: #e8f5e9;">
                <h3 >Bookswaphub Intermediary</h3>
                <p>Payment is not directly made to seller</p>
                <img src="image/pngegg (4).png" alt="Step 3">
            </div>
            <div class="trust-card" style="background-color: #f3e5f5;">
                <h3 >Bookswaphub Chats</h3>
                <p>Easy communication with sellers for any queries</p>
                <img src="image/pngegg (5).png" alt="Step 3">
            </div>
        </div>

<?php include 'footer.php' ?>


    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle exchange button click
            $('.send-request-button').click(function() {
                var bookId = $(this).data('book-id');
                window.location.href = 'exchange.php?book_id=' + bookId;
            });
        });
    </script>
</body>
</html>