<?php
    require_once __DIR__.'/config/database.php';
    require_once __DIR__.'/includes/cart-functions.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(403);
        exit("Method not allowed.");
    }

    if (!cart_verify_csrf()) {
        http_response_code(403);
        exit("Invalid security token.");
    }

    $quantities = $_POST['quantity'] ?? [];

    if (!is_array($quantities)) {
        http_response_code(403);
        exit("Invalid cart data.");
    }

    foreach ($quantities as $productId => $quantity) {
        if (
            !ctype_digit((string)$productId)
            || (int)$productId < 1
            || !is_scalar($quantity)
            || filter_var($quantity, FILTER_VALIDATE_INT) === false
            || (int)$quantity < 1
            ) {
            continue;
        }

        $productId = (int)$productId;
        $quantity = (int)$quantity;

        // Only update products already in the session cart.
        if (!isset($_SESSION['cart'][$productId])) {
            continue;
        }

        $statement = mysqli_prepare($conn, "SELECT stock, status FROM products WHERE id = ?");
        mysqli_stmt_bind_param($statement,"i",$productId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $product = mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        if (!$product || (int)$product['status'] !== 1) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $stock = (int)$product['stock'];

        if($stock < 1){
            unset($_SESSION['cart'][$productId]);
        }else{
            $_SESSION['cart'][$productId] = min($quantity,$stock);
        }
    }

    header("Location: cart.php?success=updated");
    exit;
?>