<?php
    require_once __DIR__.'/../../config/database.php';

    function e(mixed $value){
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $sql = '
        SELECT 
            c.id,
            c.name,
            c.status,
            c.created_at,
            COUNT(p.id) AS product_count
        FROM categories AS c
        LEFT JOIN products AS p
            ON p.category_id = c.id
        GROUP BY
            c.id,
            c.name,
            c.status,
            c.created_at
        ORDER BY c.id DESC
    ';

    $result = mysqli_query($conn,$sql);
    if(!$result){
        die("Unable to load categories");
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Manage Categories</title>
       
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
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="h2">Category Management</h1>
                    <p class="text-muted mb-0">
                        Manage your product categories.
                    </p>
                </div>
                <a href="add.php" class="btn btn-primary">+ Add Category</a>
            </div>
            <?php if (($_GET['success'] ?? '') === 'created'): ?>
                <div class="alert alert-success my-4">
                    Category created successfully!
                </div>
            <?php elseif (($_GET['success'] ?? '') === 'updated'): ?>
                <div class="alert alert-success my-4">
                    Category updated successfully!
                </div>
            <?php elseif (($_GET['success'] ?? '') === 'deleted'): ?>
                <div class="alert alert-success my-4">
                    Category deleted successfully!
                </div>
            <?php elseif (($_GET['error'] ?? '') === 'not_found'): ?>
                <div class="alert alert-warning my-4">
                    Category not found or already deleted!
                </div>
            <?php elseif (($_GET['error'] ?? '') === 'has_products'): ?>
                <div class="alert alert-warning">
                This category still has products assigned.
                Move those products to another category before deleting it.
            </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= (int)$row['id'] ?></td>
                                            <td><?= e($row['name']) ?></td>
                                            <td>
                                                <span class="badge text-bg-info">
                                                    <?= (int) $row['product_count'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if((int)$row['status'] === 1): ?>
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
                                                <?= e($row['created_at']) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <form action="delete.php" method="post" onsubmit="return confirm('Delete this category?');">
                                                        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>

                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No categories found.</td>
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
        </div>
        
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
