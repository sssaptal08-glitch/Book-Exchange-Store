<?php
session_start();

// Include the necessary database connection file
include('db.php');

// Include the Model class
include "Model.php";

// Redirect to login if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch user ID from the users table
$username = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

// Instantiate the Model class with the database connection
$obj = new Model($conn);

if (isset($_POST['submit'])) {
    $_POST['user_id'] = $user_id; // Add user_id to the POST data
    $obj->insertRecord($_POST, $_FILES);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Exchange Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-header {
            color: #fff;
        }
        h4 {
            text-align: center;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success">
                <h4>Book Exchange Form</h4>
            </div>
            <div class="card-body">
                <?php
                $msg = isset($_GET['msg']) ? $_GET['msg'] : null;
                if ($msg === 'ins') {
                    echo '<div class="alert alert-primary" role="alert">
                    Record inserted successfully ..
                    </div>';
                }
                ?>
                <form action="insert.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="book-condition">Book Condition</label>
                                <select class="form-control" id="book-condition" name="book_condition" required>
                                    <option value="">Select Condition</option>
                                    <option value="new">New</option>
                                    <option value="like_new">Like New</option>
                                    <option value="good">Good</option>
                                    <option value="acceptable">Acceptable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="book-detail">ISBN</label>
                                <input type="number" class="form-control" id="book-detail" name="isbn" placeholder="Enter the 13 digit ISBN number" required>
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter Book Title" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" placeholder="Enter the price" required>
                            </div>
                            <div class="form-group">
                                <label for="final_price">Final Price</label>
                                <input type="number" class="form-control" id="final_Price" name="final_price" placeholder="Enter the fixed price" required>
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="school_books">School Books (up to 12th)</option>
                                    <option value="college_books">College Books</option>
                                    <option value="novels">Novels</option>
                                    <option value="magazines">Magazines</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="Enter Book Category" required>
                            </div>
                            <div class="form-group">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author" placeholder="Enter Author Name" required>
                            </div>
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="date" class="form-control" id="year" name="year" placeholder="Enter Year of Publication" required>
                            </div>
                            <div class="form-group">
                                <label for="photo">Upload new photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept=".jpg, .png, .svg">
                            </div>
                            <div class="form-group">
                                <label for="book-description">Book Description:</label>
                                <textarea class="form-control" id="book-description" name="book_description" rows="4"></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
