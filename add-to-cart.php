<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
    require_once __DIR__.'/config/database.php';
    require_once __DIR__.'/includes/cart-functions.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit("Method not allowed.");
    }

    if (!cart_verify_csrf()) {
        http_response_code(403);
        exit("Invalid security token. Refresh and try again.");
    }

    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity']?? null, FILTER_VALIDATE_INT);

    if (!$productId || $productId < 1 || !$quantity || $quantity < 1) {
        header("Location: product-db.php?error=invalid");
        exit;
    }

    // Check product in database.
    $stmt = mysqli_prepare($conn, "SELECT id, stock, status FROM products WHERE id = ? LIMIT 1");

    mysqli_stmt_bind_param($stmt,"i",$productId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$product || (int)$product['status'] !== 1) {
        header("Location: product-db.php?error=unavialabel");
        exit;
    }

    $stock = (int)$product['stock'];
    
    if($stock < 1){
        header("Location: product-details.php?id=".$productId."&error=out_of_stock");
        exit;
    }

    $_SESSION['cart'] = $_SESSION['cart'] ?? [];

    $currentQuantity = (int)($_SESSION['cart'][$productId] ?? 0);
    $newQuantity = $currentQuantity + $quantity;

    // Prevent cart quantity from exceeding stock.
    if($newQuantity > $stock){
        header("Location: product-details.php?id=".$productId."&error=stock_limit");
        exit;
    }

    $_SESSION['cart'][$productId] = $newQuantity;

    header("Location:cart.php?success=added");
    exit;

?>