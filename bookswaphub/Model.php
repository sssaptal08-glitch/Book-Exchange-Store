<?php
if (!class_exists('Model')) {
class Model{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "book";
    private $conn;

    function __construct(){
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if($this->conn->connect_error){
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function insertRecord($post, $files){
        $bookcondition = $post["book_condition"];
        $isbn = $post["isbn"];
        $title = $post["title"];
        $price = $post["price"];
        $final_price = $post["final_price"];
        $type = $post["type"];
        $category = $post["category"];
        $author = $post["author"];
        $year = isset($post["year"]) ? $post["year"] : '';
        $photo = $this->uploadFile($files['photo']);
        $des = $post["book_description"];
        $user_id = $_SESSION['user_id']; 
        if ($photo) {
            $sql = "INSERT INTO books (book_condition, isbn, title, price, final_price, type, category, author, year, photo, book_description, user_id) 
                    VALUES ('$bookcondition', '$isbn', '$title', '$price', '$final_price', '$type', '$category', '$author', '$year', '$photo', '$des', '$user_id')";

            if ($this->conn->query($sql) === TRUE) {
                header('Location: insert.php?msg=ins');
            } else {
                echo "Error: " . $sql . "<br>" . $this->conn->error;
            }
        }
    }

    private function uploadFile($file) {
        if ($file["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (getimagesize($file["tmp_name"]) === false) {
            echo "File is not an image.";
            return false;
        }

        if ($file["size"] > 5000000 || !in_array($imageFileType, ["jpg", "jpeg", "png", "svg"])) {
            echo "Invalid file size or format.";
            return false;
        }

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            return false;
        }
    }

    function updateRecord($post, $files) {
        $bookcondition = $post["book_condition"];
        $isbn = $post["isbn"];
        $title = $post["title"];
        $price = $post["price"];
        $final_price = $post["final_price"];
        $type = $post["type"];
        $category = $post["category"];
        $author = $post["author"];
        $year = isset($post["year"]) ? $post["year"] : '';
        $editid = $post["hid"];
        $des = $post["book_description"];

        $currentRecord = $this->displayRecordById($editid);
        $currentPhoto = $currentRecord['photo'];

        if ($files['photo']['error'] === UPLOAD_ERR_OK) {
            $photo = $this->uploadFile($files['photo']);
            if (!$photo) {
                echo "Failed to upload new photo.";
                return;
            }
        } else {
            $photo = $currentPhoto;
        }

        $sql = "UPDATE `books` 
                SET 
                    `book_condition` = '$bookcondition',
                    `isbn` = '$isbn',
                    `title` = '$title',
                    `price` = '$price',
                    `final_price` = '$final_price',
                    `type` = '$type',
                    `category` = '$category',
                    `author` = '$author',
                    `year` = '$year',
                    `photo` = '$photo',
                    `book_description` = '$des'
                WHERE `book_ID` = '$editid'";

        if ($this->conn->query($sql) === TRUE) {
            header('Location: display.php?msg=ups');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }

    public function deletRecord($delid){
        $sql="DELETE FROM books WHERE book_ID= '$delid' ";
        if ($this->conn->query($sql) === TRUE) {
            header('Location: display.php?msg=del');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
    }

    public function displayRecord() {
        $sql = "SELECT * FROM books";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }   
        return [];
    }

    public function getBooksByUserId($user_id) {
        $sql = "SELECT * FROM books WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        }
        return [];
    }

    public function displayRecordById($editid) {
        $sql = "SELECT * FROM books WHERE book_ID='$editid'";
        $result = $this->conn->query($sql);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            return $row;
        }
        return null;
    }

    public function getBookById($id) {
        $query = "SELECT * FROM books WHERE book_ID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getRequestsByUserId($userId) {
        $query = "SELECT r.*, b.title AS book_title, b.photo AS book_photo, u.username AS sender_username 
                  FROM requests r 
                  JOIN books b ON r.book_id = b.book_ID 
                  JOIN users u ON r.user_id = u.id 
                  WHERE b.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSentRequestsByUserId($userId) {
        $query = "SELECT r.*, b.title AS book_title, b.photo AS book_photo, u.username AS receiver_username 
                  FROM requests r 
                  JOIN books b ON r.book_id = b.book_ID 
                  JOIN users u ON b.user_id = u.id 
                  WHERE r.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getNotificationsByUserId($userId) {
        $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
}
?>
