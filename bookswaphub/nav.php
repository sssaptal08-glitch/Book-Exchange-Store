<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>Books Exchange System & Buy Books</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .navbar-nav .nav-link {
            font-size: 1rem;
        }
        .navbar-nav .dropdown-menu {
            font-size: 0.875rem;
        }

        .badge.badge-sm {
            min-width: 1.5rem;
            font-size: 1.25rem;
        }

        .badge.badge-circle {
            border-radius: 50%;
            padding: 0;
            min-width: unset;
            width: 1.75rem;
        }

        .badge.badge-circle,
        .badge.badge-square {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.75rem;
            min-width: 2.75rem;
            padding: 0 .1rem;
            margin-left: -5px;
        }

        .badge-primary {
            color: #fff;
            background-color: #27ae60;
        }

        .translate-middle {
            transform: translate(-50%, -50%) !important;
        }

        .hidden {
            display: none;
        }

        /* Bottom Navbar */
        .bottom-navbar {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #27ae60;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 0;
            z-index: 1000;
            display: none; /* Hidden by default */
        }

        .bottom-navbar a {
            color: #fff;
            font-size: 1.5rem;
            padding: 1.4rem;
            position: relative;
        }

        .bottom-navbar .dropdown {
            position: relative;
            display: inline-block;
        }

        .bottom-navbar .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            bottom: 50px;
        }

        .bottom-navbar .dropdown-content a {
            color: #444;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 1rem;
        }

        .bottom-navbar .dropdown-content a:hover {
            background-color: #219150;
        }

        .bottom-navbar .dropdown:hover .dropdown-content {
            display: block;
        }

        .bottom-navbar .dropdown:hover .dropbtn {
            color: #27ae60;
        }

        /* Show bottom navbar only on mobile */
        @media (max-width: 768px) {
            .bottom-navbar {
                display: flex;
            }

            .badge.badge-circle,
            .badge.badge-square {
                height: 2rem;
                min-width: 2rem;
                width: 1.25rem;
                font-size: 0.875rem;
            }

            .badge.badge-sm {
                min-width: 1rem;
                font-size: 1rem;
            }

            .translate-middle {
                transform: translate(-50%, -50%) !important;
            }

            .bottom-navbar a {
                font-size: 1.2rem;
            }

            .bottom-navbar .dropdown-content {
                min-width: 120px;
                margin-left: -67px;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function fetchCartCount() {
                $.ajax({
                    url: 'get_cart_count.php',
                    type: 'GET',
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            $('#cart-count').text(data.cart_count);
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch cart count.');
                    }
                });
            }

            // Fetch cart count on page load
            fetchCartCount();
        });
    </script>
</head>

<body>
    <!-- Header Section -->
    <header class="header">
        <div class="header-1">
            <a href="#" class="logo"><i class="fas fa-book"></i>BookSwapHub</a>
            <form action="search_results.php" method="GET" class="search-form">
                <input type="search" name="query" placeholder="Search for books..." id="search-box" required>
                <label for="search-box" class="fas fa-search"></label>
            </form>
            <div class="icon">
                <div id="search-btn" class="fas fa-search" style="font-size: 2.4rem; margin-bottom: 32.5px; margin-left: 0px;   margin-top: 22px;"></div>
                <a href="display_whislist.php" class="fas fa-heart">  
                    <span id="wishlistCounter" class="position-absolute top-0 start-100 translate-middle badge badge-sm badge-circle badge-primary">0</span>
                    <p class="icon-text" style="font-size: 1.4rem;margin-top: 1px;margin-left: -12px;">Whislist</p>
                </a>
                <a href="display_cart_items.php" class="cart-icon" id="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge badge-sm badge-circle badge-primary">0</span>
                    <p class="icon-text" style="font-size: 1.4rem; margin-top: 0px; margin-left: 0px;"><b>cart</b></p>
                </a>
                <?php if (isset($_SESSION['username'])) { ?>
                <a id="logout-btn" class="fas fa-sign-out-alt" href="logout.php" onclick="return confirmLogout();">
                    <p class="icon-text" style="font-size: 1.4rem; margin-top: 10px;margin-left: -6px;">logout</p>
                </a>
                <?php } else { ?>
                <a id="login-btn" class="fas fa-sign-in-alt" href="login.php">
                    <p class="icon-text" style="font-size: 1.4rem;margin-top: 10px;margin-left: -6px;">login</p>
                </a>
                <?php } ?>
            </div>
        </div>
        <div class="header-2">
            <nav class="navbar">
                <a href="index.php">home</a>
                <a href="#featured">featured</a>
                <a href="#arrivals">arrivals</a>
                <a href="#reviews">reviews</a>
                <a href="#blogs">blogs</a>
                <div class="dropdown">
                    <button class="dropbtn">More</button>
                    <div class="dropdown-content">
                        <a href="#about">About</a>
                        <a href="users_dasboard.php">Profile</a>
                        <a href="#contact">Contact</a>
                        <a href="orders.php">Your Orders</a>
                    </div>
                </div>
                <?php if (isset($_SESSION['username'])) { ?>
                <div class="username" style="font-size: 1.8rem; margin-left: 68%; margin-top: -50px; padding: 11px; color: chartreuse;">
                    welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <?php if (isset($_SESSION['profile_picture'])) { ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture"
                        style="width: 35px; height: 37px; border-radius: 50%; margin-left: 10px; cursor: pointer; margin-top: -8px;margin-bottom: -13px;" onclick="toggleEditBox(event)">
                    <?php } ?>
                </div>
                <?php } ?>
            </nav>
        </div>
    </header>

    <!-- Bottom Navbar (Visible on Mobile Only) -->
    <nav class="bottom-navbar">
        <a href="index.php" class="fas fa-home"></a>
        <a href="#featured" class="fas fa-list"></a>
        <a href="#arrivals" class="fas fa-tags"></a>
        <a href="#reviews" class="fas fa-comments"></a>
        <a href="#blogs" class="fas fa-blog"></a>
        <div class="dropdown">
            <a href="javascript:void(0)" class="fas fa-ellipsis-v dropbtn"></a>
            <div class="dropdown-content">
                <a href="#about">About</a>
                <a href="users_dasboard.php">Profile</a>
                <a href="#contact">Contact</a>
                <a href="orders.php">Your Orders</a>
            </div>
        </div>
    </nav>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
    </script>
    <script src="js/wishlist.js"></script>
</body>
</html>
