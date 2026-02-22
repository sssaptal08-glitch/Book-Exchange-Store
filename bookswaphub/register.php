<?php
session_start();
include('db.php'); 
$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        $error_message = 'Passwords do not match!';
    } else {
        // Handle file upload
        $profile_picture = $_FILES['profile_picture'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($profile_picture["tmp_name"]);
        if($check === false) {
            $error_message = "File is not an image.";
        } elseif (file_exists($target_file)) {
            $error_message = "Sorry, file already exists.";
        } elseif ($profile_picture["size"] > 500000) {
            $error_message = "Sorry, your file is too large.";
        } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password, profile_picture) VALUES ('$username', '$email', '$hashed_password', '$target_file')";

                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
                } else {
                    $error_message = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600&display=swap');
    
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
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f0f0;
        overflow: hidden;
        height: 300rem;
    }

    .register-form-container {
        width: 90%;
        max-width: 400px;
        margin: 20px auto;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 30px;
        position: relative;
    }

    h3 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }

    span {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    /* Input field styles */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
        margin-bottom: 8px;
        font-size: 16px;
        display: flex;
        align-items: center;
        padding-left: 11px;
    }

    /* Input field icons using Font Awesome */
    .box::before {
        content: attr(data-icon);
        font-family: FontAwesome;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        display: inline-block;
        width: 30px;
        color: #ccc;
        position: absolute;
        left: 10px;
    }

    /* Input field focus styles */
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="file"]:focus {
        border-color: #27ae60;
    }

    /* Submit button styles */
    .btn {
        display: block;
        width: 100%;
        padding: 10px 20px;
        background-color: #27ae60;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 4px;
        transition: background-color 0.2s ease-in-out;
    }

    .btn:hover {
        background-color: #219150;
    }

    /* Link styles */
    a {
        color: #27ae60;
        text-decoration: none;
        transition: color 0.2s ease-in-out;
    }

    a:hover {
        color: #219150;
        text-decoration: underline;
    }

    /* Error message styles */
    .error-message {
        color: red;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
        font-size:1rem;
    }

    /* Close button styles (using Font Awesome) */
    #close-register-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        color: #27ae60;
        transition: color 0.2s ease-in-out;
    }

    #close-register-btn:hover {
        color: #219150;
    }

    /* Media queries for responsiveness */
    @media (max-width: 768px) {
        .register-form-container {
            width: 90%;
            padding: 20px;
        }

        h3 {
            font-size: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            font-size: 14px;
            padding-left: 30px;
        }

        .btn {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .register-form-container {
            width: 95%;
            padding: 15px;
        }

        h3 {
            font-size: 18px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            font-size: 12px;
            padding-left: 25px;
        }

        .btn {
            font-size: 12px;
        }
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="register-form-container">
    <div id="close-register-btn" class="fas fa-times"></div>
    <form action="register.php" method="post" enctype="multipart/form-data">
    <h3>Register</h3>
    <span>Username</span>
    <input type="text" name="username" class="box" data-icon="&#xf007;" placeholder="Enter your username" required>
    <span>Email</span>
    <input type="email" name="email" class="box" data-icon="&#xf0e0;" placeholder="Enter your email" required>
    <span>Password</span>
    <input type="password" name="password" class="box" data-icon="&#xf023;" placeholder="Enter your password" required id="password">
    <span>Confirm Password</span>
    <input type="password" name="confirm_password" class="box" data-icon="&#xf023;" placeholder="Confirm your password" required id="confirm_password">
    <span>Profile Picture</span>
    <input type="file" name="profile_picture" class="box" accept="image/*">
    <input type="checkbox" onclick="togglePassword()"> Show Password
    <?php if ($error_message): ?>
    <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <input type="submit" value="Register" class="btn">
    <p>Already have an account? <a href="login.php" id="open-login-form">Sign in</a></p>
</form>

</div>
<script>
    function togglePassword() {
        var password = document.getElementById("password");
        var confirm_password = document.getElementById("confirm_password");
        if (password.type === "password" || confirm_password.type === "password") {
            password.type = "text";
            confirm_password.type = "text";
        } else {
            password.type = "password";
            confirm_password.type = "password";
        }
    }
</script>
</body>
</html>
