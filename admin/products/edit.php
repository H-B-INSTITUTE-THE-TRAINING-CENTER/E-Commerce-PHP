<?php

    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../includes/functions.php';


    function e(mixed $value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Invalid product ID.");
    }

    $findStmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($findStmt, "i", $id);
    mysqli_stmt_execute($findStmt);

    $productResult = mysqli_stmt_get_result($findStmt);

    $product = mysqli_fetch_assoc($productResult);

    mysqli_stmt_close($findStmt);

    if (!$product) {
        http_response_code(400);
        exit("Product not found.");
    }

    // CATEGORIES
    $categoriesResult = mysqli_query($conn, "SELECT id, name FROM categories WHERE status = 1 ORDER BY name");
    if (!$categoriesResult) {
        die("Unable to load categories.");
    }

    $errors = [];

    $name = $product['name'];
    $categoryId = (string)$product['category_id'];
    $description = $product['description'] ?? '';
    $price = (string)$product['price'];
    $discount = (string)$product['discount'];
    $stock = (string)$product['stock'];
    $image = $product['image'] ?? '';
    $status = (string)$product['status'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $categoryId = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';
        $discount = $_POST['discount'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $image = $product['image'] ?? '';
        $status = $_POST['status'] ?? '';


        if ($name === '' || strlen($name) > 150) {
            $errors[] = "Enter a product name (maximum 150 characters).";
        }
        if (filter_var($categoryId, FILTER_VALIDATE_INT) === false || (int)$categoryId < 1) {
            $errors[] = "Select a valid category.";
        } else {
            $checkCategory = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ? AND status = 1");
            mysqli_stmt_bind_param($checkCategory, "i", $categoryId);
            mysqli_stmt_execute($checkCategory);

            $checkResult = mysqli_stmt_get_result($checkCategory);

            if (mysqli_num_rows($checkResult) !== 1) {
                $errors[] = "The selected category is unavailable.";
            }

            mysqli_stmt_close($checkCategory);
        }
        if ($price === '' || !is_numeric($price) || (float)$price < 0 || (float)$price > 99999999.99) {
            $errors[] = "Enter a valid product price.";
        }
        if ($discount === '' || !is_numeric($discount) || (float)$discount < 0 || (float)$discount > 100) {
            $errors[] = "Discount must be between 0 to 100.";
        }
        if (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
            $errors[] = "Stock must be a non-negative whole number.";
        }
        if (strlen($description) > 5000) {
            $errors[] = "Description is too long.";
        }
        if (!in_array($status, ['0', '1'], true)) {
            $errors[] = "Select a valid product status.";
        }

        $oldImage  = $product['image'] ?? '';
        $newImage = null;

        if (!$errors) {
            $price = (float)$price;
            $discount = (float)$discount;
            $stock = (int)$stock;
            $categoryId = (int)$categoryId;
            $status = (int)$status;
            $id = (int)$id;

            $upload = uploadProductImage($_FILES['image'] ?? []);
            if($upload['error'] !== null){
                $errors[] = $upload['error'];
            }else if($upload['filename'] !== null){
                $newImage = $upload['filename'];
                $image = $newImage;
            }

            $sql = "UPDATE products
            SET category_id = ?, name = ?, description = ?,
                price = ?, discount = ?, stock = ?, image = ?, status = ?
            WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "issddisii",
                $categoryId,
                $name,
                $description,
                $price,
                $discount,
                $stock,
                $image,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                if ($newImage !== null && $oldImage !== '' && $oldImage !== $newImage) {
                    $oldPath = __DIR__.'/../../uploads/products'.$oldImage;
                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
                header("Location: index.php?success=updated");
                exit;
            } else {
                $errors[] = "Update failed: " . mysqli_stmt_error($stmt);
            }
        }
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Edit Product</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous" />
</head>

<body class="bg-light">
    <main>
        <div class="container py-5">
            <div class="card shadow-sm mx-auto" style="max-width: 800px;">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Edit Product #<?= (int)$id ?></h1>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" maxlength="150" value="<?= e($name) ?>" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <?php while ($category = mysqli_fetch_assoc($categoriesResult)): ?>
                                    <option value="<?= (int)$category['id'] ?>"
                                        <?= (string)$categoryId === (string)$category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?= e($description) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" name="price" min="0" max="99999999.99" step="0.01" value="<?= e($price) ?>" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" name="discount" min="0" max="100" step="0.01" value="<?= e($discount) ?>" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" min="0" step="1" value="<?= e($stock) ?>" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                Replace Product Image
                            </label>

                            <?php if (!empty($product['image'])): ?>
                                <div class="mb-2">
                                    <img
                                        src="../../uploads/products/<?= e($product['image']) ?>"
                                        alt="Current product image"
                                        style="width: 120px; height: 120px; object-fit: contain;"
                                        class="img-thumbnail">
                                </div>
                            <?php endif; ?>

                            <input
                                type="file"
                                id="image"
                                name="image"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp">

                            <div class="form-text">
                                Leave empty to keep the existing image.
                                JPG, PNG, or WebP only; maximum 2 MB.
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <button class="btn btn-primary" type="submit">Update Product</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

    </main>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>