<?php
include "Model.php";
include "db.php";
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$books = [];
$requestedUser = null;

// Fetch user ID from the URL, if provided, otherwise use the logged-in user's ID
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
} else {
    // Fetch the logged-in user's ID
    $username = $_SESSION['username'];
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];
}

// Fetch the requested user's details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requestedUser = $result->fetch_assoc();

// If the user does not exist, handle the error
if (!$requestedUser) {
    echo "User not found";
    exit;
}

// Instantiate Model class with database connection
$obj = new Model($conn);

// Fetch books for the requested user
$books = $obj->getBooksByUserId($user_id);

// Fetch notifications for the logged-in user
$logged_in_username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $logged_in_username);
$stmt->execute();
$result = $stmt->get_result();
$logged_in_user = $result->fetch_assoc();
$logged_in_user_id = $logged_in_user['id'];

$notificationsQuery = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($notificationsQuery);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$notificationsResult = $stmt->get_result();
$notifications = $notificationsResult->fetch_all(MYSQLI_ASSOC);


// Fetch the requests related to the logged-in user, either as sender or receiver
$query = "SELECT r.id, r.book_id, r.requested_book_id, b1.title AS requested_book_title, b1.photo AS requested_book_photo, 
                 b2.title AS offered_book_title, b2.photo AS offered_book_photo, 
                 u.username AS sender_username, u.id AS sender_id,
                 ur.username AS receiver_username, ur.id AS receiver_id
          FROM requests r
          JOIN books b1 ON r.book_id = b1.book_ID
          JOIN books b2 ON r.requested_book_id = b2.book_ID
          JOIN users u ON r.user_id = u.id
          JOIN users ur ON b1.user_id = ur.id
          WHERE (b1.user_id = ? OR r.user_id = ?) AND r.status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>User Profile Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
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

        .profile-container {
            width: 90%;
            max-width: 1400px;
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
            margin-right: 20px;
        }

        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover;
        }

        .profile-header .user-details {
            flex: 1;
            padding-right: 20px;
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
            margin-left: 20px;
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
            width: 150px;
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
            object-fit: cover;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .notification-icon, .request-icon {
                margin-left: 10px;
            }

            .book-card {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
    <?php include 'nav.php' ?>
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="d-flex align-items-center">
                <img src="<?php echo htmlspecialchars($requestedUser['profile_picture']); ?>" alt="Profile Picture">
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($requestedUser['username']); ?></h2>
                    <p>User ID: <?php echo htmlspecialchars($requestedUser['id']); ?></p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <?php if ($user_id == $logged_in_user_id): ?>
                    <a href="edit_profile.php" class="edit-button">Edit Profile</a>
                <?php endif; ?>
                <a href="requests.php" class="request-icon" data-toggle="tooltip" title="View Requests">
                    <i class="fas fa-envelope"></i>
                    <span class="icon-name">Requests</span>
                </a>
                <a href="notifications.php" class="notification-icon" data-toggle="tooltip" title="View Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if (!empty($notifications)): ?>
                        <span class="notification-count"><?php echo count($notifications); ?></span>
                    <?php endif; ?>
                    <span class="icon-name">Notifications</span>
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

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
            const walk = (x - startX) * 3;
            slider.scrollLeft = scrollLeft - walk;
        });

        $('.send-request-button').click(function() {
            var bookId = $(this).data('book-id');
            var bookTitle = $(this).data('book-title');
            var bookPhoto = $(this).data('book-photo');

            $.ajax({
                url: 'send_request.php',
                method: 'POST',
                data: {
                    book_id: bookId,
                    title: bookTitle,
                    photo: bookPhoto
                },
                success: function(response) {
                    alert('Request sent successfully');
                },
                error: function(xhr, status, error) {
                    alert('Error sending request');
                }
            });
        });
    </script>
</body>
</html>
