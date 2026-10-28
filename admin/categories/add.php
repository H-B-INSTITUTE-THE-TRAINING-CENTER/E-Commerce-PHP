<?php
    require_once __DIR__.'/../../config/database.php';

    function e(mixed $value){
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $name = '';
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        if($name === '' || strlen($name) > 100){
            $errors[] = "Category name is required and must not exceed 100 characters.";
        }
        if(!$errors){
            $stmt = mysqli_prepare($conn,"SELECT id FROM categories WHERE name = ?");
            mysqli_stmt_bind_param($stmt,"s",$name);
            mysqli_stmt_execute($stmt);

            $existing = mysqli_stmt_get_result($stmt);
            $duplicate = mysqli_num_rows($existing) > 0;

            mysqli_stmt_close($stmt);

            if($duplicate){
                $errors[] = "This category name already exists.";
            }
        }
        if(!$errors){
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (name,status) VALUES (?,1)");
            mysqli_stmt_bind_param($stmt,"s",$name);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_close($stmt);
                header("Location: index.php?success=created");
                exit;
            }else{
                $errors[] = "Unable to create category.";
                mysqli_stmt_close($stmt);
            }
        }
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Add Category</title>
        
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
            <div class="card shadow-sm mx-auto" style="max-width: 600px;">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Add Category</h1>
                    <?php if($errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($errors as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" id="name" name="name" maxlength="100" value="<?= e($name) ?>"  class="form-control" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Category</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
       
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
