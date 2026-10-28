<?php
require_once __DIR__ . '/../../config/database.php';

$sql = "
        SELECT 
            p.id,
            p.name,
            p.price,
            p.discount,
            p.stock,
            p.status,
            c.name as category_name
        FROM products AS p
        INNER JOIN categories AS c 
            ON p.category_id = c.id
        ORDER BY p.id DESC
    ";

try {
    $result = mysqli_query($conn, $sql);
} catch (Exception $e) {
    die("Unable to fetch products: " . $e->getMessage());
}
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Manage Products</title>
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
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="h2">Product Management</h1>
                <p class="text-muted mb-0">
                    Manage your store inventory.
                </p>

            </div>
            <a href="add.php" class="btn btn-primary">
                + Add Product
            </a>

        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= e($row['id']) ?></td>
                                        <td><?= e($row['name']) ?></td>
                                        <td><?= e($row['category_name']) ?></td>
                                        <td>
                                            ₹<?= number_format((float)$row['price'], 2) ?>
                                        </td>
                                        <td><?= e($row['discount']) ?>%</td>
                                        <td><?= e($row['stock']) ?></td>
                                        <td>
                                            <?php if ($row['status'] == 1): ?>
                                                <span class="badge text-bg-success">
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="badge text-bg-secondary">
                                                    Inactive
                                                </span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="edit.php?id=<?= (int) $row['id'] ?>"
                                                    class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                                <form
                                                    action="delete.php"
                                                    method="post"
                                                    onsubmit="return confirm('Delete this product?');">

                                                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        No products found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a href="../../index.php" class="btn btn-outline-secondary mt-3">
            Back to Store
        </a>
        <?php if (($_GET['success'] ?? '') === 'created'): ?>
            <div class="alert alert-success my-4">
                Product created successfully!
            </div>
        <?php elseif (($_GET['success'] ?? '') === 'updated'): ?>
            <div class="alert alert-success my-4">
                Product updated successfully!
            </div>
        <?php elseif (($_GET['success'] ?? '') === 'deleted'): ?>
            <div class="alert alert-success my-4">
                Product deleted successfully!
            </div>
        <?php elseif (($_GET['error'] ?? '') === 'not_found'): ?>
            <div class="alert alert-warning my-4">
                Product not found or already deleted!
            </div>
        <?php endif; ?>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>