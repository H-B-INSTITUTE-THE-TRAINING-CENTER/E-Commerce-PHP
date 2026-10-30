<?php
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__.'/../../includes/functions.php';

    function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $categoriesResult = mysqli_query(
        $conn,
        "SELECT id, name FROM categories
        WHERE status = 1 ORDER BY name"
    );

    if (!$categoriesResult) {
        die("Unable to load categories");
    }

    $errors = [];

    $name = '';
    $categoryId = '';
    $description = '';
    $price = '';
    $discount = '0';
    $stock = '0';
    $image = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['name'] ?? '');
        $categoryId = $_POST['category_id'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';
        $discount = $_POST['discount'] ?? '0';
        $stock = $_POST['stock'] ?? '0';
        

        if ($name === '' || strlen($name) > 150) {
            $errors[] = "Enter a product name (maximum 150 characters).";
        }

        if (filter_var($categoryId, FILTER_VALIDATE_INT) === false || (int)$categoryId < 1) {
            $errors[] = "Select a valid category.";
        } else {
            $categoryStmt = mysqli_prepare(
                $conn,
                "SELECT id FROM categories
                WHERE id = ? AND status = 1"

            );
            mysqli_stmt_bind_param(
                $categoryStmt,
                "i",
                $categoryId
            );
            mysqli_stmt_execute($categoryStmt);
            $categoryResult = mysqli_stmt_get_result($categoryStmt);
            
            // if (mysqli_num_rows($categoriesResult) !== 1) {
            //     $errors[] = "The selected category is unavilable.";
            // }

            if (mysqli_num_rows($categoryResult) !== 1) {
                $errors[] = "The selected category is unavailable.";
            }
            
            mysqli_stmt_close($categoryStmt);
        }
        if ($price === '' || !is_numeric($price) || (float)$price < 0 || (float)$price > 99999999.99) {
            $errors[] = "Enter a valid product price.";
        }
        if (!is_numeric($discount) || (float) $discount < 0 || (float)$discount > 100) {
            $errors[] = "Discount must be between 0 and 100.";
        }
        if (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
            $errors[] = "Stock must be a non-negative whole number.";
        }
        if (strlen($description) > 5000) {
            $errors[] = "Description is too long.";
        }
        
        

        if (!$errors) {
            $price = (float)$price;
            $discount = (float)$discount;
            $stock = (int)$stock;
            $categoryId = (int)$categoryId;
            
            $upload = uploadProductImage($_FILES['image'] ?? []);
            if($upload['error'] !== null){
                $errors[] = $upload['error'];
            }
                
            $image = $upload['filename'] ?? '';



            $insertProduct = "
                    INSERT INTO products (category_id,name,description,price,discount,stock,image)
                    VALUES (?,?,?,?,?,?,?)
                ";

            $insert_stmt = mysqli_prepare($conn, $insertProduct);
            if (!$insert_stmt) {
                die("Unable to prepare product insert.");
            }
            mysqli_stmt_bind_param(
                $insert_stmt,
                "issddis",
                $categoryId,
                $name,
                $description,
                $price,
                $discount,
                $stock,
                $image
            );

            if (mysqli_stmt_execute($insert_stmt)) {
                mysqli_stmt_close($insert_stmt);

                header("Location: index.php?success=created");
                exit;
            }else{
                if($image !== ''){
                    $path = __DIR__.'/../../uploads/products/'.$image;

                    if (is_file($path)) {
                        unlink($path);
                    }
                }
                $errors[] = "Unable to save the product.";
                mysqli_stmt_close($insert_stmt);
            }
           
        }
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Add Product</title>
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

<body class="bg-light">
    <main>
        <div class="container py-5">
            <div class="card shadow-sm mx-auto" style="max-width: 800px;">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Add Product</h1>
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
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
                            <input type="text" name="name" class="form-control" maxlength="150" value="<?= e($name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php while ($category = mysqli_fetch_assoc($categoriesResult)):  ?>
                                    <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= (string) $categoryId === (string) $category['id'] ? 'selected' : '' ?>>

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
                                <input type="number" name="price" class="form-control" min="0" max="99999999.99" step="0.01" value="<?= e($price) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" name="discount" class="form-control" min="0" max="100" step="0.01" value="<?= e($discount) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" min="0" step="1" value="<?= e($stock) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/jpeg, image/png, image/webp">
                            <div class="form-text">
                                Accepted Formats: JPG, PNG, WebP. Maximum Size : 2 MB.
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Product</button>
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