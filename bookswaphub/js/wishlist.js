const wishlistCounterElement = document.getElementById('wishlistCounter');
let wishlistCount = parseInt(localStorage.getItem('wishlistCount')) || 0;
wishlistCounterElement.textContent = wishlistCount;
// const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

function updateWishlistCounter(count) {
    wishlistCount = count;
    wishlistCounterElement.textContent = wishlistCount;
    localStorage.setItem('wishlistCount', wishlistCount);
}

function handleWishlistAction(action, bookId, title, icon) {
    $.ajax({
        url: 'add_to_wishlist.php',
        method: 'POST',
        data: { action: action, book_id: bookId },
        dataType: 'json',
        success: function(response) {
            console.log(response); // Log the server response for debugging
            if (response.success) {
                updateWishlistCounter(response.wishlist_count);
                if (action === 'addToWishlist') {
                    icon.classList.add('liked');
                    icon.style.color = 'red'; // Change to red color
                    alert('Added "' + title + '" to wishlist!');
                    saveWishlistState(bookId, true);
                } else if (action === 'deleteFromWishlist') {
                    icon.classList.remove('liked');
                    icon.style.color = ''; // Reset to default color
                    alert('Removed "' + title + '" from wishlist.');
                    saveWishlistState(bookId, false);
                }
            } else {
                alert('Failed to update wishlist: ' + response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
            alert('Error: Unable to update wishlist.');
        }
    });
}

function addToWishlist(icon, title) {
    if (!isLoggedIn) {
        alert('Please log in to add items to your wishlist.');
        window.location.href = 'login.php'; // Redirect to login page
        return;
    }

    let bookId = icon.getAttribute('data-book-id');
    let action = icon.classList.contains('liked') ? 'deleteFromWishlist' : 'addToWishlist';

    handleWishlistAction(action, bookId, title, icon);
}

function saveWishlistState(bookId, isLiked) {
    let wishlistState = JSON.parse(localStorage.getItem('wishlistState')) || {};
    wishlistState[bookId] = isLiked;
    localStorage.setItem('wishlistState', JSON.stringify(wishlistState));
}

function loadWishlistState() {
    let wishlistState = JSON.parse(localStorage.getItem('wishlistState')) || {};
    document.querySelectorAll('.custom-icon').forEach(icon => {
        let bookId = icon.getAttribute('data-book-id');
        if (wishlistState[bookId]) {
            icon.classList.add('liked');
            icon.style.color = 'red';
        }
    });
}

function hideWishlistCounter() {
    wishlistCounterElement.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    loadWishlistState();
    if (isLoggedIn) {
        fetchWishlistCount(); // Fetch wishlist count from the server if the user is logged in
    } else {
        hideWishlistCounter();
        localStorage.removeItem('wishlistCount'); // Clear the wishlist count from localStorage
    }
});

function fetchWishlistCount() {
    $.ajax({
        url: 'fetch_wishlist_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateWishlistCounter(response.wishlist_count);
            } else {
                console.log('Failed to fetch wishlist count: ' + response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
        }
    });
}



// const wishlistCounterElement = document.getElementById('wishlistCounter');
// let wishlistCount = parseInt(localStorage.getItem('wishlistCount')) || 0;
// wishlistCounterElement.textContent = wishlistCount;
// const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

function updateWishlistCounter(count) {
    wishlistCount = count;
    wishlistCounterElement.textContent = wishlistCount;
    localStorage.setItem('wishlistCount', wishlistCount);
}

function handleWishlistAction(action, bookId, title, icon) {
    $.ajax({
        url: 'add_to_wishlist.php',
        method: 'POST',
        data: { action: action, book_id: bookId },
        dataType: 'json',
        success: function(response) {
            console.log(response); // Log the server response for debugging
            if (response.success) {
                updateWishlistCounter(response.wishlist_count);
                if (action === 'addToWishlist') {
                    icon.classList.add('liked');
                    icon.style.color = 'red'; // Change to red color
                    alert('Added "' + title + '" to wishlist!');
                    saveWishlistState(bookId, true);
                } else if (action === 'deleteFromWishlist') {
                    icon.classList.remove('liked');
                    icon.style.color = ''; // Reset to default color
                    alert('Removed "' + title + '" from wishlist.');
                    saveWishlistState(bookId, false);
                }
            } else {
                alert('Failed to update wishlist: ' + response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
            alert('Error: Unable to update wishlist.');
        }
    });
}
function handleRemoveFromWishlist(bookId) {
    $.ajax({
        url: 'delete_whislist.php', // Adjust URL as necessary
        method: 'POST',
        data: { book_ID: bookId },
        dataType: 'json',
        success: function(response) {
            console.log(response); // Log server response for debugging
            if (response.success) {
                // Remove the wishlist item DOM element
                let wishlistItem = document.querySelector('.wishlist-item[data-book-id="' + bookId + '"]');
                if (wishlistItem) {
                    wishlistItem.remove();
                }

                // Reset icon color to normal state if book_ID was not found in wishlist
                let likeIcon = document.querySelector('.custom-icon[data-book-id="' + bookId + '"]');
                if (likeIcon) {
                    if (!response.bookExists) {
                        likeIcon.style.color = ''; // Reset inline color style
                    }
                    likeIcon.classList.remove('liked'); // Remove 'liked' class in any case
                }

                alert(response.message); // Show success message
            } else {
                alert('Failed to remove item from wishlist: ' + response.message); // Show error message
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
            alert('Error: Unable to update wishlist.');
        }
    });
}

function addToWishlist(icon, title) {
    if (!isLoggedIn) {
        alert('Please log in to add items to your wishlist.');
        window.location.href = 'login.php'; // Redirect to login page
        return;
    }

    let bookId = icon.getAttribute('data-book-id');
    let action = icon.classList.contains('liked') ? 'deleteFromWishlist' : 'addToWishlist';

    handleWishlistAction(action, bookId, title, icon);
}

function saveWishlistState(bookId, isLiked) {
    let wishlistState = JSON.parse(localStorage.getItem('wishlistState')) || {};
    wishlistState[bookId] = isLiked;
    localStorage.setItem('wishlistState', JSON.stringify(wishlistState));
}
function loadWishlistState() {
    let wishlistState = JSON.parse(localStorage.getItem('wishlistState')) || {};
    // let isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
    
    document.querySelectorAll('.custom-icon').forEach(icon => {
        let bookId = icon.getAttribute('data-book-id');
        if (wishlistState[bookId]) {
            icon.classList.add('liked');
            if (isLoggedIn) {
                icon.style.color = 'red'; // Show red color if logged in and item is liked
            } else {
                icon.style.color = ''; // Reset to default color when logged out
            }
        } else {
            icon.classList.remove('liked');
            icon.style.color = ''; // Reset to default color
        }
    });
}

function loadWishlistState() {
    // let isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;
    let wishlistState = JSON.parse(localStorage.getItem('wishlistState')) || {};
    document.querySelectorAll('.custom-icon').forEach(icon => {
        let bookId = icon.getAttribute('data-book-id');
        if (wishlistState[bookId]) {
            icon.classList.add('liked');
            if (isLoggedIn) {
                icon.style.color = 'red';
            } else {
                icon.style.color = ''; // Reset to default color when logged out
            }
        } else {
            icon.classList.remove('liked');
            icon.style.color = ''; // Reset to default color
        }
    });
}


function hideWishlistCounter() {
    wishlistCounterElement.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    loadWishlistState();
    if (isLoggedIn) {
        fetchWishlistCount(); // Fetch wishlist count from the server if the user is logged in
    } else {
        hideWishlistCounter();
        localStorage.removeItem('wishlistCount'); // Clear the wishlist count from localStorage
    }
});

function fetchWishlistCount() {
    $.ajax({
        url: 'fetch_wishlist_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateWishlistCounter(response.wishlist_count);
            } else {
                console.log('Failed to fetch wishlist count: ' + response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX error: ', textStatus, errorThrown); // Log AJAX errors
        }
    });
}

