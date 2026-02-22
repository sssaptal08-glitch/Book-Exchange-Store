<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $profile_picture = $_FILES['profile_picture'];
    $profile_picture_path = $_SESSION['profile_picture'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $pincode = $_POST['pincode'];

    // Handle profile picture upload
    if ($profile_picture && $profile_picture['tmp_name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($profile_picture["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }

        // Check file size
        if ($profile_picture["size"] > 500000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
                $profile_picture_path = $target_file;
            }
        }
    }

    // Update user details including address, mobile, pincode in the database
    $sql = "UPDATE users SET username = ?, email = ?, profile_picture = ?, address = ?, mobile = ?, pincode = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $new_username, $new_email, $profile_picture_path, $address, $mobile, $pincode, $username);

    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $_SESSION['email'] = $new_email;
        $_SESSION['profile_picture'] = $profile_picture_path;
        echo '<script>alert("Profile updated successfully!");window.location.href = "index.php";</script>';
        // header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
