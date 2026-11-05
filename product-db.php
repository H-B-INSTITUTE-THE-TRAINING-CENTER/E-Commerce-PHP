<?php

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    require_once './config/database.php';
    require_once './includes/functions.php';

    // Fetch products with their category names
    $sql = "
            SELECT
                p.*,
                c.name AS category_name
            FROM products AS p
            LEFT JOIN categories AS c
                ON p.category_id = c.id
            ORDER BY p.id DESC
        ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Product query failed: " . mysqli_error($conn));
    }

    $totalProducts = mysqli_num_rows($result);

    // Helper for safe HTML output
    function e(mixed $value)
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Our Products | Online Store</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .product-image {
            height: 220px;
            width: 100%;
            object-fit: contain;
            padding: 12px;
            background: #ffffff;
        }

        .product-card {
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-description {
            min-height: 48px;
        }
    </style>
</head>
<body>
    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Our Products</h1>
            <span class="badge bg-primary fs-6">
                <?= $totalProducts ?> Products
            </span>
        </div>
        <?php if ($totalProducts > 0): ?>
            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $imageFilename = basename($row['image'] ?? '');

                        $imageFile = __DIR__ . '/uploads/products/' . $imageFilename;

                        $imageUrl = !empty($imageFilename) && is_file($imageFile)
                            ? 'uploads/products/' . rawurlencode($imageFilename)
                            : null;

                        $finalPrice = calculateDiscountedPrice(
                            (float)$row['price'],
                            (float)$row['discount']
                        );
                    ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card product-card h-100 shadow-sm">
                            <?php if ($imageUrl): ?>
                                <img
                                    src="<?= e($imageUrl) ?>"
                                    class="card-img-top product-image"
                                    alt="<?= e($row['name']) ?>">
                            <?php else: ?>
                                <div class="product-image d-flex justify-content-center align-items-center">
                                    <span class="text-muted">
                                        No Image Available
                                    </span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <span class="badge bg-secondary align-self-start">
                                    <?= e($row['category_name'] ?? 'Uncategorized') ?>
                                </span>
                                <h5 class="card-title mt-3">
                                    <?= e($row['name']) ?>
                                </h5>
                                <p class="card-text product-description">
                                    <?= e($row['description']) ?>
                                </p>
                                <p class="mb-1">
                                    <del class="text-muted">
                                        ₹<?= number_format((float)$row['price'], 2) ?>
                                    </del>
                                </p>
                                <h5 class="text-primary">
                                    ₹<?= number_format((float)$finalPrice, 2) ?>
                                </h5>
                                <span class="badge bg-danger align-self-start mb-3">
                                    <?= (int)$row['discount'] ?>% OFF
                                </span>
                                <p>
                                    Stock: <?= (int)$row['stock'] ?>
                                </p>
                                <a href="product-details.php?id=<?= (int)$row['id'] ?>"
                                    class="btn btn-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>

            <div class="alert alert-warning text-center">
                <h4>No Products Found</h4>
                <p class="mb-0">
                    Please add products through the admin panel.
                </p>
            </div>

        <?php endif; ?>

    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>