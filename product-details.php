<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once __DIR__.'/config/database.php';
    require_once __DIR__.'/includes/functions.php';

    function e(mixed $value){
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );   
    }

    $id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);

    if(!$id || $id < 1){
        http_response_code(400);
        exit("Invalid Product ID.");
    }

    $sql = "
        SELECT 
            p.*,
            c.name AS category_name
        FROM products AS p
        LEFT JOIN categories AS c
            ON p.category_id = c.id
        WHERE p.id = ? AND p.status = 1
        LIMIT 1
    ";

    $statement = mysqli_prepare($conn,$sql);

    if(!$statement){
        die("Database Error: ".mysqli_error($conn));
    }

    mysqli_stmt_bind_param($statement,"i",$id);
    mysqli_stmt_execute($statement);

    $result = mysqli_stmt_get_result($statement);
    $product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($statement);

    if(!$product){
        http_response_code(400);
        exit("Product not found or unavilable");
    }

    $finalPrice = calculateDiscountedPrice($product['price'],$product['discount']);

    $imageFilename = basename($product['image'] ?? '');

    $imageFile = __DIR__ . '/uploads/products/' . $imageFilename;
    
    $imageURL = !empty($imageFilename) && is_file($imageFile) ? 'uploads/products/'.rawurlencode($imageFilename) : 'assets/images/no-image.png';    
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title><?= e($product['name']) ?> | Online Store</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-dark bg-dark">
            <div class="container">
                <a href="index.php" class="navbar-brand">Online Store</a>
                <a href="product-db.php" class="btn btn-outline-light">All products</a>
            </div>
        </nav>
        <main class="container py-5">
            <div class="row g-5">
                <!-- PRODUCT IMAGE -->
                 <div class="col-md-6">
                    <div class="card shadow-sm">
                        <img src="<?= e($imageURL) ?>"
                        class="img-fluid rounded" 
                        alt="<?= e($product['image']) ?>"
                        style="width:100%; height: 450px; object-fit: contain; background: white;">
                    </div>
                 </div>
                 <!-- PRODUCT INFORMATION -->
                 <div class="col-md-6">
                    <span class="badge bg-secondary mb-3">
                        <?= e($product['category_name'] ?? 'Uncategorized') ?>
                    </span>
                    <h1 class="mb-3"><?= e($product['name']) ?></h1>
                    <p class="text-muted">
                        <?= nl2br(e($product['description'])) ?>
                    </p>
                    <hr>

                    <!-- PRICING -->
                     <p class="mb-1">
                        Original Price:
                        <del>
                            ₹<?= number_format((float)$product['price'],2) ?>
                        </del>
                     </p>
                     <h2 class="text-primary">
                        ₹<?= number_format((float)$finalPrice,2) ?>
                     </h2>
                     <span class="badge bg-danger">
                        <?= (float)$product['discount'] ?>% OFF
                     </span>
                     <hr>
                     <!-- STOCK -->
                    <?php if((int)$product['stock'] > 0): ?>
                        <p class="text-success fw-bold">
                            In Stock (<?= (int)$product['stock'] ?> available)
                        </p>
                    <?php else: ?>
                        <p class="text-danger fw-bold">Out Of Stock</p>
                    <?php endif; ?>

                    <!-- QUANTITY -->
                     <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" value="1" min="1" max="<?= (int)$product['stock']?>"
                        <?= (int)$product['stock'] < 1 ? 'disabled':'' ?>
                        style="max-width: 120px;" >
                     </div>

                     <!-- ADD TO CART -->
                      <button type="submit" class="btn btn-primary btn-lg" <?= (int)$product['stock'] < 1 ? 'disabled':'' ?>>
                        Add to Cart
                    </button>
                    <a href="product-db.php" class="btn btn-outline-secondary btn-lg">Continue Shopping</a>
                        
                 </div>

            </div>
        </main>
        <footer class="bg-dark text-white text-center py-3">
            <p class="mb-0">
                &copy; <?= date('Y') ?>Online Store. All Rights Reserved.
            </p>
        </footer>
        
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
