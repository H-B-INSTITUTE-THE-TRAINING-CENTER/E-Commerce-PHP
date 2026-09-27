<?php
// $productName = "Laptop";
// $productPrice = 55000;
// $productStock = 10;
// $productCategory = "Electronics";

$storeName = "E-commerce Website";
$products = [
    [
        "name" => "Laptop",
        "price" => 55000,
        "category" => "Electronics",
        "image" => "https://placehold.co/600x400?text=Laptop"
    ],
    [
        "name" => "Smartphone",
        "price" => 25000,
        "category" => "Electronics",
        "image" => "https://placehold.co/600x400?text=Smartphone"
    ],
    [
        "name" => "Headphones",
        "price" => 2999,
        "category" => "Accessories",
        "image" => "https://placehold.co/600x400?text=Headphones"
    ],
    [
        "name" => "Smart Watch",
        "price" => 4999,
        "category" => "Accessories",
        "image" => "https://placehold.co/600x400?text=Smart+Watch"
    ]
];

$categories = [
    "Electronics",
    "Accessories",
    "Clothing",
    "Books"
];
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title><?= $storeName ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous" />

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><?= $storeName ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"> 🛒 Cart</a>
                        </li>
                    </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li>
                    </ul>
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>

    </header>
    <main>
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h1>
                            Shop Smart. Shop Better.
                        </h1>
                        <p class="lead mt-3">
                            Discover quality product at great prices.
                        </p>
                        <a href="#products" class="btn btn-light btn-lg mt-3">Shop Now</a>

                    </div>
                    <div class="col-md-5 text-center">
                        <div class="display-1">
                            🛒
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2>Shop by Category</h2>
                    <p class="text-muted">
                        Explore our popular categories
                    </p>
                </div>
                <div class="row g-4">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-3">
                            <div class="card category-card shadow-sm">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        🛍️
                                    </div>
                                    <h5 class="mt-3">
                                        <?= $category ?>
                                    </h5>
                                    <a href="#" class="btn btn-outline-primary">View Products</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <section class="py-5 bg-light" id="products">
            <div class="container">
                <div class="text-center mb-5">
                    <h2>Featured Products</h2>
                    <p class="text-muted">
                        Check our popular products
                    </p>
                </div>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card product-card h-100 shadow-sm">
                                <img
                                    src="<?= $product['image'] ?>"
                                    class="card-img-top product-image"
                                    alt="<?= $product['name'] ?>">
                                <div class="card-body">
                                    <span class="badge bg-secondary">
                                        <?= $product['category'] ?>
                                    </span>
                                    <h5 class="card-title mt-2">
                                        <?= $product['name'] ?>
                                    </h5>
                                    <h5 class="text-primary">
                                        ₹<?= number_format($product['price']) ?>
                                    </h5>
                                    <a href="#" class="btn btn-primary w-100">
                                        Add to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </section>
        <section class="offer-section">
            <div class="container text-center">
                <h2>
                    Special Offer 🎉
                </h2>
                <p class="lead">
                    Get up to 30% off on selected products.
                </p>
                <a href="#products" class="btn btn-warning btn-lg">
                    Explore Offers
                </a>
            </div>
        </section>

    </main>
    <footer>

        <div class="container">

            <div class="row">

                <div class="col-md-4">

                    <h5>
                        <?= $storeName ?>
                    </h5>

                    <p>
                        Your trusted online shopping destination.
                    </p>

                </div>

                <div class="col-md-4">

                    <h5>
                        Quick Links
                    </h5>

                    <ul class="list-unstyled">

                        <li>
                            <a
                                href="index.php"
                                class="text-white">
                                Home
                            </a>
                        </li>

                        <li>
                            <a
                                href="#products"
                                class="text-white">
                                Products
                            </a>
                        </li>

                        <li>
                            <a
                                href="#"
                                class="text-white">
                                Contact
                            </a>
                        </li>

                    </ul>

                </div>

                <div class="col-md-4">

                    <h5>
                        Contact
                    </h5>

                    <p>
                        Email: support@hystore.com
                    </p>

                    <p>
                        Phone: +91 98765 43210
                    </p>

                </div>

            </div>

            <hr>

            <div class="text-center">

                <p class="mb-0">
                    &copy; <?= date("Y") ?> <?= $storeName ?> 
                    All Rights Reserved.
                </p>

            </div>

        </div>

    </footer>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>