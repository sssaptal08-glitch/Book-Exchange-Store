<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $bookId = intval($_POST['bookId']);
    if (!in_array($bookId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $bookId;
    }

    $cartCount = count($_SESSION['cart']);
    echo json_encode(['cartCount' => $cartCount]);
}
?>
