<?php
session_start();
include('db.php'); 
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email']; 
            $_SESSION['profile_picture'] = $user['profile_picture'];// Set the email in the session
            header("Location: index.php"); // Redirect to welcome page or any other page after successful login
            exit;
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No user found with that email!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-form-container {
            width: 400px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 30px;
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
            margin-bottom: 15px;
            font-size: 16px;
        }
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
            margin-top: 15px;
            transition: background-color 0.2s ease-in-out;
        }
        .btn:hover {
            background-color: #219150;
        }
        a {
            color: #27ae60;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        a:hover {
            color: #219150;
            text-decoration: underline;
        }
        #close-login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #ccc;
            transition: color 0.2s ease-in-out;
        }
        #close-login-btn:hover {
            color: #999;
        }
        .checkbox {
            margin-bottom: 15px;
        }
        .checkbox label {
            cursor: pointer;
            font-weight: normal;
        }
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-form-container">
    <div id="close-login-btn" class="fas fa-times"></div>
    <form action="" method="post">
        <h3>sign in</h3>
        <span>email</span>
        <input type="email" name="email" class="box" placeholder="enter your email" required>
        <span>password</span>
        <input type="password" name="password" class="box" placeholder="enter your password" required>
        <div class="checkbox">
            <input type="checkbox" name="checkbox" id="remember-me">
            <label for="remember-me">remember me</label>
        </div>
        <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <input type="submit" value="sign in" class="btn">
        <p>forget password? <a href="#">click here</a></p>
        <p>don't have an account? <a href="register.php" id="open-register-form"> Create Account</a></p>
    </form>
</div>
</body>
</html>
