<?php
    require_once './config/database.php';
    require_once './includes/functions.php';
    
    $sql = "
        SELECT 
        products.* ,
        categories.name AS category_name
            FROM products
        INNER JOIN categories
            ON products.category_id = categories.id
    ";

    $result = mysqli_query($conn,$sql);

    if (!$result) {
        die("Unable to fetch records: ".mysqli_error($conn));
    }

?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Products</title>
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

    <body>
        <header>
            <!-- place navbar here -->
        </header>
        <main>
            <div class="container py-5">
                <h1 class="mb-4">Products</h1>
                <div class="row g-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <span class="badge bg-secondary">
                                    <!-- Category ID: <?= $row['category_id'] ?> -->
                                    <?= $row['category_name'] ?>
                                </span>
                                <h5 class="card-title mt-3">
                                    <?= $row['name'] ?>
                                </h5>
                                <p class="card-text">
                                    <?= $row['description'] ?>
                                </p>
                                
                                <?php
                                    $finalPrice = calculateDiscountedPrice($row['price'],$row['discount']);
                                ?>
                                <p>
                                    <del>
                                        ₹<?= number_format($row['price'],2) ?>
                                    </del>
                                </p>
                                <h5 class="text-primary">
                                    ₹<?= number_format($finalPrice,2) ?>
                                </h5>
                                <span class="badge bg-danger">
                                    <?= $row['discount'] ?>% OFF
                                </span>
                                
                                <p>Stock: <?= $row['stock'] ?></p>
                                <button class="btn btn-primary w-100">Add to Cart</button>
                            </div>
                        </div>
                    </div>                        
                    <?php endwhile ?>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Bundle (includes Popper) -->
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
