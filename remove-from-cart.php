<?php
    require_once __DIR__.'/includes/cart-functions.php';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(403);
        exit("Method not allowed.");
    }

    if (!cart_verify_csrf()) {
        http_response_code(403);
        exit("Invalid security token.");
    }

    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);

    if (!$productId || $productId < 1) {
        header("Location: cart.php");
        exit;
    }

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    header("Location: cart.php?success=removed");

?>