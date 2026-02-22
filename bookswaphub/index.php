<?php
session_start();
include('db.php');

if (isset($_SESSION['username'])) {
    // Fetch user details from the database
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $user['email'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>Books Exchange System & Buy Books</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .edit-box {
            display: none;
            position: absolute;
            right: 77px;
            top: 170px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            z-index: 1000;
            animation: slideIn 0.3s forwards;
        }

        .edit-icon {
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: 10px;
        }

        .edit-box img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .edit-box input[type="text"],
        .edit-box input[type="email"] {
            width: calc(100% - 30px);
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 3px;
            display: inline-block;
        }

        .edit-box input[type="file"] {
            margin: 10px 0;
        }

        .edit-box .btn {
            background-color: #27ae60;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .edit-box .btn:hover {
            background-color: #219150;
        }
        .profile-info {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.edit-icon {
    font-size: 18px;
    color: #888;
    cursor: pointer;
    transition: color 0.2s ease-in-out;
    margin-bottom: -62px;
    display: ruby-text;
    margin-top: 16px;
    margin-left: 206px;
    border: 0.2px solid;
    border-radius: 50%;
    padding: 10px;
    height: 39px;
    width: 39px;
}

.edit-icon:hover {
    color: #333; /* Change color on hover */
}


        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 600px) {
            .edit-box {
                width: 90%;
                left: 5%;
                top: 10px;
            }

            .edit-box img {
                width: 80px;
                height: 80px;
            }

            .edit-box input[type="text"],
            .edit-box input[type="email"] {
                width: calc(100% - 20px);
            }
        }
 .custom-icon {
    width: 40px;
    height: 40px;
    font-size: 24px;
    position: relative; /* Required for the pseudo-element positioning */
    color: black; /* Default color */
    transition: color 0.3s; /* Smooth color transition */
}

.custom-icon:hover {
    color: red; /* Change color on hover */
}

.custom-icon::after {
    content: "Add to whislist";
    position: absolute;
    top: 30px; /* Adjust position as needed */
    left: 50%;
    transform: translateX(-50%);
    background-color:DimGray;
    color: white;
    padding: 10px;
    border-radius: 3px;
    font-size: 8px;
    white-space: nowrap;
    word-spacing:2px;
    opacity: 0;
    /* pointer-events: none; Prevent interfering with other elements */
    transition: opacity 0.3s; /* Smooth fade-in effect */
}

.custom-icon:hover::after {
    opacity: 1; /* Show message on hover */
}

    </style>
</head>

<body>
   <?php include 'nav.php'?>
    <!-- Edit Box -->
    <div id="editBox" class="edit-box">
        <form action="save_profile.php" method="post" enctype="multipart/form-data">
        <a href="edit_profile.php"><i class="fas fa-pencil-alt edit-icon" title="Edit Profile"></i></a>
            <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" id="profilePicPreview">
            <input type="file" name="profile_picture" accept="image/*" onchange="previewProfilePic(event)">
            <br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
            <br>
            <input type="submit" value="Save Changes" class="btn">
        </form>
    </div>

    <script>
        // Toggle edit box visibility
        function toggleEditBox(event) {
            var editBox = document.getElementById('editBox');
            if (editBox.style.display === 'block') {
                editBox.style.display = 'none';
            } else {
                editBox.style.display = 'block';
                event.stopPropagation(); // Prevent immediate closure
            }
        }

        // Close edit box when clicking outside
        window.onclick = function(event) {
            var editBox = document.getElementById('editBox');
            if (event.target != editBox && !editBox.contains(event.target)) {
                editBox.style.display = 'none';
            }
        }

        // Preview profile picture
        function previewProfilePic(event) {
            var output = document.getElementById('profilePicPreview');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // Free memory
            }
        }
    </script>
    <!-- end header section -->

    <!-- bottom navbar -->
    <!-- <nav class="bottom-navbar">
        <a href="#home" class="fas fa-home"></a>
        <a href="#featured" class="fas fa-list"></a>
        <a href="#arrivals" class="fas fa-tags"></a>
        <a href="#reviews" class="fas fa-comments"></a>
        <a href="#blogs" class="fas fa-blog"></a>
    </nav> -->

    <!-- home section start -->
    <section class="home" id="home">

        <div class="row">

            <div class="content">
                <h3>Welcome to BookSwapHub!</h3>
                <p>Discover, buy, and exchange used books with ease.</p>
                <a href="insert.php" class="btn">Exchange Book</a>
            </div>

            <div class="swiper books-slider">
                <div class="swiper-wrapper">
                    <a href="#" class="swiper-slide"><img src="image/card1.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/card9.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/cart3.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/cart4.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/card8.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/card6.jpg" alt=""></a>
                    <a href="#" class="swiper-slide"><img src="image/cart3.jpg" alt=""></a>
                </div>
                <a href="#"><img src="image/stand.png" class="stand" alt=""></a>
            </div>
        </div>
    </section>
    <!-- end home section -->

    <!-- icons section starting -->

    <section class="icon-container">

        <div class="icon">
            <i class="fas fa-plane"></i>
            <div class="content">
                <h3>free shipping</h3>
                <p>order over ₹500</p>
            </div>
        </div>

        <div class="icon">
            <i class="fas fa-lock"></i>
            <div class="content">
                <h3>secure payment</h3>
                <p>100 secure payment</p>
            </div>
        </div>

        <div class="icon">
            <i class="fas fa-redo-alt"></i>
            <div class="content">
                <h3>easy returns</h3>
                <p>10 days returns</p>
            </div>
        </div>

        <div class="icon">
            <i class="fas fa-headset"></i>
            <div class="content">
                <h3>24/7 supports</h3>
                <p>call us anytime</p>
            </div>
    </section>
    <!-- end ion section -->

    <!-- start featured section -->
    <section class="featured" id="featured">
    <h1 class="heading"><span>Featured Books</span></h1>
    <div class="swiper featured-slider">
        <div class="swiper-wrapper">
            <?php 
            include 'db.php';
            $sql = "SELECT book_ID, title, price, final_price, photo FROM books";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='swiper-slide box'>";
                    echo "<div class='image'>";
                    echo "<img src='" . $row['photo'] . "' alt='Product Image' height='100px'>";
                    echo "</div>";
                    echo "<div class='content'>";
                    echo "<i class='fa-regular fa-heart custom-icon' data-book-id='" . $row['book_ID'] . "' onclick=\"addToWishlist(this, '" . $row['title'] . "')\"></i>";
                    echo "<h3>" . $row['title'] . "</h3>";
                    echo "<div class='price'>₹" . $row['final_price'] . "<span>₹" . $row['price'] . "</span></div>";
                    echo "<a href='book_details.php?id=". $row['book_ID'] ."' class='btn'>Exchange</a>"; 
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "0 results";
            }

            $conn->close();
            ?>        
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
</section>


    <!-- end featured section -->

    <!-- start newsletter -->
    <section class="newsletter">
        <form action="">
            <h3>subscribe for latest updates</h3>
            <input type="email" name="email" placeholder="enter your email" class="box">
            <input type="submit" value="subscribe" class="btn">
        </form>
    </section>
    <!-- end news letter -->

    <section class="arrivals" id="arrivals">
        <h1 class="heading"><span>new arrivals</span></h1>
        <div class="swiper arrivals-slider">
            <div class="swiper-wrapper">
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card1.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card2.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>

                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/cart3.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>

                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/cart4.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/cart5.webp" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="swiper arrivals-slider">
            <div class="swiper-wrapper">
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card1.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card6.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>

                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card7.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>

                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card8.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
                <a href="" class="swiper-slide box">
                    <div class="image">
                        <img src="image/card9.jpg" alt="">
                    </div>
                    <div class="content">
                        <h3>new arrivals</h3>
                        <div class="price">$58 <span>$89</span></div>
                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <!-- end arrivals section -->

    <!-- start review section -->
    <section class="reviews" id="reviews">
        <h1 class="heading"><span>client's reviews</span></h1>
        <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide box">
                    <img src="image/images (1).jpg" alt="">
                    <h3>aarti singh</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum necessitatibus officia reiciendis
                        deleniti totam dolor voluptas minus nobis ducimus fugit eaque laboriosam dolore, eos autem,
                        aperiam, consequuntur molestias! Deleniti, cupiditate.</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/images (2).jpg" alt="">
                    <h3>rani tiwari</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum necessitatibus officia reiciendis
                        deleniti totam dolor voluptas minus nobis ducimus fugit eaque laboriosam dolore, eos autem,
                        aperiam, consequuntur molestias! Deleniti, cupiditate.</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/images (3).jpg" alt="">
                    <h3>vikas yadav</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum necessitatibus officia reiciendis
                        deleniti totam dolor voluptas minus nobis ducimus fugit eaque laboriosam dolore, eos autem,
                        aperiam, consequuntur molestias! Deleniti, cupiditate.</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/image4.jpg" alt="">
                    <h3>avinash sharma</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum necessitatibus officia reiciendis
                        deleniti totam dolor voluptas minus nobis ducimus fugit eaque laboriosam dolore, eos autem,
                        aperiam, consequuntur molestias! Deleniti, cupiditate.</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/GGG.jpg" alt="">
                    <h3>ankit dubey</h3>
                    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Harum necessitatibus officia reiciendis
                        deleniti totam dolor voluptas minus nobis ducimus fugit eaque laboriosam dolore, eos autem,
                        aperiam, consequuntur molestias! Deleniti, cupiditate.</p>
                    <div class="star">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- end review section -->

    <!-- start blog section -->
    <?php
    include "db.php";
$sql = "SELECT title, image, content, link FROM blogs";
$result = $conn->query($sql);
?>

<section class="blogs" id="blogs">
    <h1 class="heading"><span>our blogs</span></h1>
    <div class="swiper blogs-slider">
        <div class="swiper-wrapper">

            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="swiper-slide box">';
                    echo '<div class="image">';
                    echo '<img src="' . $row["image"] . '" alt="Blog Image">';
                    echo '</div>';
                    echo '<div class="content">';
                    echo '<h3>' . $row["title"] . '</h3>';
                    echo '<p>' . substr($row["content"], 0, 150) . '...</p>';
                    echo '<a href="' . $row["link"] . '" class="btn">read more</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No blogs available.</p>";
            }
            ?>

        </div>
    </div>
</section>

<?php
$conn->close();
?>


    <!-- blogs section end -->

    <!-- start footer section -->

    <?php include 'footer.php' ?>