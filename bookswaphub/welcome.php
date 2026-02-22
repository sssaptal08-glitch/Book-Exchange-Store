<?php
include "Model.php";
include "db.php";
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user ID from the URL parameter
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Fetch user details from the database based on user_id
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
    // Example: Fetch username
    $username = $userDetails['username'];
} else {
    echo "User not found."; // Handle case where user is not found in database
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .profile-container {
            width: 90%;
            max-width: 1200px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            padding: 20px;
            margin: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover; /* Ensure profile picture doesn't stretch */
        }

        .profile-header .user-details {
            flex: 1;
            padding-right: 20px; /* Added padding for better spacing */
        }

        .profile-header .user-details h2 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }

        .profile-header .user-details p {
            margin: 5px 0;
            color: #666;
        }

        .profile-header .edit-button {
            background-color: #27ae60;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .profile-header .edit-button:hover {
            background-color: #219150;
        }

        .notification-icon,
        .request-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
            transition: transform 0.3s, color 0.3s;
            margin-left: 20px; /* Increased margin for better spacing */
        }

        .notification-icon:hover,
        .request-icon:hover {
            transform: translateY(-3px);
        }

        .notification-icon .fa-bell,
        .request-icon .fa-envelope {
            font-size: 1.5em;
            color: #333;
        }

        .notification-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: #007bff;
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.8em;
        }

        .icon-name {
            display: none;
            position: absolute;
            background-color: #333;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            white-space: nowrap;
            z-index: 1;
            top: -30px;
            left: -25px;
        }

        /* Show icon name on hover */
        .notification-icon:hover .icon-name,
        .request-icon:hover .icon-name {
            display: block;
        }

        .books-section {
            margin-bottom: 20px;
        }

        .books-section .section-title {
            font-size: 1.2em;
            margin-bottom: 10px;
            color: #333;
        }

        .books-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-padding: 10px;
            margin-bottom: 20px;
        }

        .book-card {
            flex: 0 0 auto;
            width: 150px; /* Reduced width for smaller cards */
            margin-right: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .book-card img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
            object-fit: cover; /* Ensure book images maintain aspect ratio */
        }

        .book-details {
            padding: 10px;
        }

        .book-details p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #666;
        }

        .send-request-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
            font-size: 0.8em;
        }

        .send-request-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div>
                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture">
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                    <p>User ID: <?php echo htmlspecialchars($user_id); ?></p>
                </div>
            </div>
            <div>
                <a href="edit_profile.php" class="edit-button">Edit Profile</a>
                <!-- Notifications and Requests Icons -->
                <a href="requests.php" class="request-icon" data-toggle="tooltip" title="View Requests">
                    <i class="fas fa-envelope"></i>
                    <span class="icon-name">Requests</span>
                </a>
                <a href="notifications.php" class="notification-icon" data-toggle="tooltip" title="View Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="icon-name">Notifications</span>
                    <?php if (!empty($notifications)): ?>
                        <span class="notification-count"><?php echo count($notifications); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Books Shared Section -->
        <div class="books-section">
            <div class="section-title">Books Shared</div>
            <div class="books-slider">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <img src="<?php echo htmlspecialchars($book['photo']); ?>" alt="Book Image">
                        <div class="book-details">
                            <p><strong><?php echo htmlspecialchars($book['title']); ?></strong></p>
                            <p>Price: ₹<?php echo htmlspecialchars($book['price']); ?></p>
                            <button class="send-request-button" data-toggle="modal" data-target="#exchangeModal" data-book-id="<?php echo htmlspecialchars($book['book_ID']); ?>" data-book-title="<?php echo htmlspecialchars($book['title']); ?>" data-book-photo="<?php echo htmlspecialchars($book['photo']); ?>">Send Request</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // JavaScript for sliding books section (optional)
        const slider = document.querySelector('.books-slider');

        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.classList.add('active');
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.classList.remove('active');
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 3; // Adjust scrolling speed here
            slider.scrollLeft = scrollLeft - walk;
        });

        // Example AJAX request on button click (requires jQuery)
        $('.send-request-button').click(function() {
            // Get book details
            var bookTitle = $(this).siblings('p').eq(0).find('strong').text();
            var bookPrice = $(this).siblings('p').eq(1).text().replace('Price: ₹', '');

            // Example AJAX call
            $.ajax({
                url: 'send_request.php',
                method: 'POST',
                data: {
                    title: bookTitle,
                    price: bookPrice
                },
                success: function(response) {
                    // Handle success response
                    console.log('Request sent successfully');
                    // Update UI if needed
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error('Error sending request');
                }
            });
        });
    </script>
</body>
</html>
