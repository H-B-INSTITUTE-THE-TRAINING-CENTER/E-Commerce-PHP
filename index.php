<?php
    $productName = "Laptop";
    $productPrice = 55000;
    $productStock = 10;
    $productCategory = "Electronics";
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>E-commerce Website</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container mt-5">
            <h1 class="text-primary">E-Commerce Website</h1>
            <div class="card" style="width: 18rem;">
                <!-- <img src="..." class="card-img-top" alt="..."> -->
                <div class="card-body">
                    <h5 class="card-title"><?= $productName ?></h5>
                    <p class="card-text">Category: Rs.<?= $productCategory ?></p>
                    <p class="card-text">Price: Rs.<?= $productPrice ?></p>
                    <p class="card-text">Stock: Rs.<?= $productStock ?></p>
                    <button class="btn btn-primary">Add to Cart</button>
                </div>
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
        crossorigin="anonymous"></script>
</body>

</html>