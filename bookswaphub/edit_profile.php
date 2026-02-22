<?php
session_start();
include('db.php'); // Include your database connection script

// Check if user is logged in, redirect if not (optional)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user's profile information from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $profile_picture = $user['profile_picture'];
    $username = $user['username'];
    $email = $user['email'];
    $address = $user['address'];
    $mobile = $user['mobile'];
    $pincode = $user['pincode'];
    
} else {
    // Handle case where user is not found (optional)
    $profile_picture = "default_profile_picture.jpg"; // Default profile picture
    $username = "Unknown";
    $email = "unknown@example.com";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .profile-info {
            width: 90%;
            max-width: 530px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin: 0 10px;
        }
        .profile-info h2 {
            margin-top: 0;
            background-color: #27ae60;
            padding: 5px;
            border-radius: 5px;
            color: white;
        }
        .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .profile-details {
            margin: 20px 0;
        }
        .edit-icon {
            color: #007bff;
            cursor: pointer;
            margin-left: 10px;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #27ae60;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: green;
        }
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                gap: 1rem;
            }
            .container {
                width: 75%;
                height: auto;
                padding: 20px;
                flex-direction: column;
                text-align: center;
            }
            .container i {
                margin-bottom: 15px;
                font-size: 4rem;
            }
            .profile-info {
                width: 90%;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="profile-info" style="width: 83%;">
        <h2>Edit Profile</h2>
        <div class="profile-picture">
            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <a href="edit_profile.php" title="Edit"></a>
        </div>
        <form id="editProfileForm" action="save_profile.php" method="POST">
            <div class="profile-details">
                <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required>
                <input type="text" name="address" placeholder="Address"value="<?php echo $address; ?>" required>
                <input type="tel" name="mobile" placeholder="Mobile Number" value="<?php echo $mobile; ?>" required>
                <input type="number" name="pincode" placeholder="Pincode" value="<?php echo $pincode; ?>" required>
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <script>
        document.getElementById('editProfileForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            fetch('save_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert('Changes saved successfully!');
                window.location.href = 'index.php';
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
