<?php
include "Model.php";
error_reporting(E_ALL);

$obj = new Model();

// Check if form is submitted for update
if (isset($_POST['update'])) {
    $obj->updateRecord($_POST, $_FILES);
}

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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Book Exchange Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h4>Book Exchange Form</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $msg = isset($_GET['msg']) ? $_GET['msg'] : null;
                        if ($msg === 'ups') {
                            echo '<div class="alert alert-primary" role="alert">
                            Record updated successfully ..
                            </div>';
                        }  
                        if (isset($_GET["editid"])) {
                            $editid = $_GET["editid"];
                            $myRecord = $obj->displayRecordById($editid);
                        }
                        ?>
                        <form action="display.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="book-condition">Book Condition</label>
                                <select class="form-control" id="book-condition" name="book_condition" required>
                                    <option value="">Select Condition</option>
                                    <option value="new" <?php if (isset($myRecord['book_condition']) && $myRecord['book_condition'] == 'new') echo 'selected'; ?>>New</option>
                                    <option value="like_new" <?php if (isset($myRecord['book_condition']) && $myRecord['book_condition'] == 'like_new') echo 'selected'; ?>>Like New</option>
                                    <option value="good" <?php if (isset($myRecord['book_condition']) && $myRecord['book_condition'] == 'good') echo 'selected'; ?>>Good</option>
                                    <option value="acceptable" <?php if (isset($myRecord['book_condition']) && $myRecord['book_condition'] == 'acceptable') echo 'selected'; ?>>Acceptable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="book-detail">ISBN</label>
                                <input type="number" class="form-control" id="book-detail" name="isbn" placeholder="Enter the 13 digit ISBN number" required value="<?php if (isset($myRecord['isbn'])) echo $myRecord['isbn']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter Book Title" required value="<?php if (isset($myRecord['title'])) echo $myRecord['title']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="school_books" <?php if (isset($myRecord['type']) && $myRecord['type'] == 'school_books') echo 'selected'; ?>>School Books (up to 12th)</option>
                                    <option value="college_books" <?php if (isset($myRecord['type']) && $myRecord['type'] == 'college_books') echo 'selected'; ?>>College Books</option>
                                    <option value="novels" <?php if (isset($myRecord['type']) && $myRecord['type'] == 'novels') echo 'selected'; ?>>Novels</option>
                                    <option value="magazines" <?php if (isset($myRecord['type']) && $myRecord['type'] == 'magazines') echo 'selected'; ?>>Magazines</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" name="category" placeholder="Enter Book Category" required value="<?php if (isset($myRecord['category'])) echo $myRecord['category']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author" placeholder="Enter Author Name" required value="<?php if (isset($myRecord['author'])) echo $myRecord['author']; ?>">
                            </div> 
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="date" class="form-control" id="year" name="year" placeholder="Enter Year of Publication" required value="<?php if (isset($myRecord['year'])) echo $myRecord['year']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="photo">Upload new photo (leave blank to keep existing)</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept=".jpg, .png, .svg">
                                <?php if (!empty($myRecord['photo'])) { ?>
                                    <img src="<?php echo $myRecord['photo']; ?>" width="150" height="150" />
                                <?php } ?>
                            </div>
                    </div>
                </div>
            </div>
            <!-- Submit Your Details Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h4>Submit Your Details</h4>
                    </div>
                    <!-- <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="user_name" required value="<?php if (isset($myRecord['user_name'])) echo $myRecord['user_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <textarea class="form-control" id="address" name="user_address" rows="3" required><?php if (isset($myRecord['user_address'])) echo $myRecord['user_address']; ?></textarea>
                        </div> -->
                        <div class="form-group">
                            <label for="book-description">Book Description:</label>
                            <textarea class="form-control" id="book-description" name="book_description" rows="4" required><?php if (isset($myRecord['book_description'])) echo $myRecord['book_description']; ?></textarea>
                        </div>
                        <input type="hidden" name="hid" value="<?php if (isset($myRecord['book_ID'])) echo $myRecord['book_ID']; ?>">
                        <button type="submit" name="update" class="btn btn-success">Update</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
