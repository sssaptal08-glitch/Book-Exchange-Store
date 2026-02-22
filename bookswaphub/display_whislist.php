<?php
session_start();
include 'db.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "You must be logged in to view your wishlist.";
    exit;
}

$username = $_SESSION['username'];

// Fetch wishlist items for the logged-in user from the database
$query = "
SELECT w.book_ID, b.title, b.price, b.photo 
FROM wishlist w 
JOIN books b ON w.book_ID = b.book_ID
WHERE w.username = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

// Fetch all wishlist items
$wishlistItems = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>Manage Wishlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">
    <style>
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
        .sidebar {
            width: 200px;
            background-color: #f8f9fa;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            margin-top:74px;
        }
        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }
        .sidebar nav ul li {
            margin-bottom: 20px;
        }
        .sidebar nav ul li a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        .sidebar nav ul li a i {
            margin-right: 10px;
        }
        .wishlist-container {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
        }
        .wishlist-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .wishlist-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .wishlist-item img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            margin-right: 20px;
        }
        .wishlist-item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .wishlist-item-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .wishlist-item-actions {
            display: flex;
            gap: 10px;
        }
        .wishlist-item-actions button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button {
            background-color: #ff4c4c;
            color: white;
        }
        .add-to-cart-button {
            background-color: #007bff;
            color: white;
        }
        @media (max-width: 768px) {
            .wishlist-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .wishlist-item img {
                width: 100%;
                height: auto;
                margin-bottom: 10px;
            }
            .wishlist-item-actions {
                flex-direction: column;
                width: 100%;
                gap: 5px;
            }
            .sidebar {
                padding: 45px;
            }
            .sidebar nav ul {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
            }
            .sidebar nav ul li {
                flex: 1 1 45%;
                text-align: center;
                margin-bottom: 10px;
            }
            .wishlist-container {
                margin-left: 0;
                padding: 10px;
            }
        }
    </style>
     
</head>
<body>
<?php include 'nav.php'?>
    <div class="wishlist-container">
        <h1>Manage your wishlist</h1>
        <?php if (empty($wishlistItems)) { ?>
            <p>No items in your wishlist.</p>
        <?php } else { ?>
            <?php foreach ($wishlistItems as $item) { ?>
                <div class="wishlist-item" data-book-id="<?php echo htmlspecialchars($item['book_ID']); ?>">
                    <img src="<?php echo $item['photo']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <div class="wishlist-item-details">
                        <span class="wishlist-item-title"><?php echo htmlspecialchars($item['title']); ?></span>
                        <span class="wishlist-item-price" style="font-size:x-large;">₹<?php echo htmlspecialchars($item['price']); ?></span>
                        <div class="wishlist-item-actions">
                            <form method="post" action="delete_whislist.php">
                                <input type="hidden" name="book_ID" value="<?php echo htmlspecialchars($item['book_ID']); ?>">
                                <button type="button" class="delete-button" data-book-id="<?php echo htmlspecialchars($item['book_ID']); ?>">Remove</button>
                                </form>
                            <button class="add-to-cart-button">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function() {
    $('.delete-button').click(function() {
        var bookId = $(this).data('book-id');
        var wishlistItem = $(this).closest('.wishlist-item');

        $.ajax({
            url: 'delete_whislist.php',
            method: 'POST',
            data: {
                action: 'deleteFromWishlist',
                book_ID: bookId // Ensure this matches the key used in PHP ($_POST['book_ID'])
            },
            dataType: 'json',
            success: function(response) {
                console.log(response); // Log the server response for debugging
                if (response.success) {
                    // Remove the wishlist item from UI
                    wishlistItem.remove();
                    alert(response.message);

                    // Update the wishlist counter
                    updateWishlistCounter(response.wishlist_count);

                    // Find the like icon associated with the book and reset its color
                    var icon = document.querySelector('.custom-icon[data-book-id="' + bookId + '"]');
                    if (icon) {
                        icon.classList.remove('liked');
                        icon.style.color = ''; // Reset to default color
                        saveWishlistState(bookId, false); // Update local storage
                    }
                } else {
                    alert('Failed to delete item from wishlist: ' + response.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
                alert('Error: Unable to delete item from wishlist.');
            }
        });
    });
});

</script>

</body>
</html>
