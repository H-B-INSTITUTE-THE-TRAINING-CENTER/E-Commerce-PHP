<?php
    require_once __DIR__.'/config/database.php';
    require_once __DIR__.'/includes/functions.php';
    require_once __DIR__.'/includes/cart-functions.php';

    $cart = $_SESSION['cart'] ?? [];

    $cartProducts = [];
    $subtotal = 0;

    foreach ($cart as $productId => $quantity) {
        $productId = (int)$productId;
        $quantity = (int)$quantity;

        if ($productId < 1 || $quantity < 1) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $statement = mysqli_prepare(
            $conn, 
            "SELECT id, name, price, discount, stock, image, status
            FROM products
            WHERE id = ? AND status = 1"
        );

        mysqli_stmt_bind_param($statement,"i",$productId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $product = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($statement);

        // Remove products that are deleted or inactive.
        if(!$product){
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        // Keep cart quantity within current stock.
        if ($quantity > (int)$product['stock']) {
            $quantity = (int)$product['stock'];
            $_SESSION['cart'][$productId] = $quantity;
        }

        if ($quantity < 1) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $product['quantity'] = $quantity;
        $product['final_price'] = calculateDiscountedPrice($product['price'],$product['discount']);
        $product['line_total'] = $product['final_price'] * $quantity;
        $subtotal += $product['line_total'];

        $cartProducts[] = $product;
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Shopping Cart</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body class="bg-light">
        <div class="container py-5">
            <h1 class="mb-4">My Shopping Cart</h1>
            <?php if(($_GET['success'] ?? '') === 'added'): ?>
                <div class="alert alert-success">
                    Product added to your cart successfully!
                </div>
            <?php endif; ?>
            <?php if(($_GET['success']?? '') === 'updated'): ?>
                <div class="alert alert-succeess">
                    Cart updated successfully!
                </div>
            <?php endif; ?>
            <?php if(($_GET['success']?? '') === 'removed'): ?>
                <div class="alert alert-succeess">
                    Product removed from cart.
                </div>
            <?php endif; ?>
            <?php if (empty($cartProducts)): ?>
                <div class="alert alert-info">
                    Your shopping cart is empty
                </div>
                <a href="product-db.php" class="btn btn-primary">Continue Shopping</a>
            <?php else: ?>
                <form action="update-cart.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(cart_csrf_token(),ENT_QUOTES,'UTF-8') ?>">
                    <div class="card shadow-sm">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartProducts as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php
                                                        $filename = basename($item['image'] ?? '');
                                                        $imageFile = __DIR__ . '/uploads/products/' . $filename;

                                                        $imageUrl = $filename !== '' && is_file($imageFile)
                                                            ? 'uploads/products/' . rawurlencode($filename)
                                                            : 'assets/images/no-image.png';
                                                    ?>
                                                    <img
                                                        src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                        alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>"
                                                        width="65"
                                                        height="65"
                                                        style="object-fit:contain;"
                                                    >

                                                    <strong>
                                                        <?= htmlspecialchars($item['name'],ENT_QUOTES,'UTF-8') ?>
                                                    </strong>
                                                </div>
                                            </td>
                                            <td>
                                                ₹<?= number_format($item['final_price'], 2) ?>
                                            </td>
                                            <td>
                                                <input 
                                                    type="number" 
                                                    name="quantity[<?= (int)$item['id'] ?>]"
                                                    value="<?= (int)$item['quantity'] ?>"
                                                    min="1"
                                                    max="<?= (int)$item['stock'] ?>"
                                                    class="form-control"
                                                    style="width:90px;"
                                                    required
                                                >
                                            </td>
                                            <td>
                                                ₹<?= number_format($item['line_total'], 2) ?>
                                            </td>
                                            <td>
                                                <button 
                                                    type="submit" 
                                                    form="remove-<?= (int)$item['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                >
                                                Remove
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                        <a href="product-db.php" class="btn btn-outline-secondary">Continue Shopping</a>
                        <div class="text-end">
                            <h4>Subtotal:  ₹<?= number_format($subtotal, 2) ?></h4>
                            <button type="submit" class="btn btn-primary">Update Cart</button>
                        </div>
                    </div>
                </form>

                <!-- Individual remove forms -->
                <?php foreach($cartProducts as $item): ?>
                    <form action="remove-from-cart.php" id="remove-<?= (int)$item['id'] ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(cart_csrf_token(),ENT_QUOTES,'UTF-8') ?>">
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                    </form>
                <?php endforeach; ?>
            <?php endif ?>

        </div>
        
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
