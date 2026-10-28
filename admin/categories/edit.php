<?php 
    require_once __DIR__.'/../../config/database.php';

    function e(mixed $value){
        return htmlspecialchars(
            (string)$value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Invalid Category ID.");
    }

    $stmt = mysqli_prepare($conn,"SELECT id, name, status FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $category = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if(!$category){
        http_response_code(404);
        exit("Category not found.");
    }

    $name = $category['name'];
    $status = (string)$category['status'];
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $status = $_POST['status'] ?? '';

        if($name === '' || strlen($name) > 100){
            $errors[] = "Category name is required and must not exceed 100 characters.";
        }

        if(!in_array($status,['0','1'], true)){
            $errors[] = "Select a valid category status.";
        }

        if(!$errors){
            $statement = mysqli_prepare($conn, "SELECT id FROM categories WHERE name = ? AND id <> ?");
            mysqli_stmt_bind_param($statement,"si",$name,$id);
            mysqli_stmt_execute($statement);

            $duplicateResult = mysqli_stmt_get_result($statement);
            $duplicate = mysqli_num_rows($duplicateResult) > 0;
            mysqli_stmt_close($statement);

            if($duplicate){
                $errors[] = "Another category already uses this name.";
            }
        }
        if(!$errors){
            $status = (int)$status;
            $statement = mysqli_prepare($conn,"UPDATE categories SET name = ?, status = ? WHERE id = ?");
            mysqli_stmt_bind_param($statement,"sii",$name,$status,$id);
            
            if (mysqli_stmt_execute($statement)) {
                mysqli_stmt_close($statement);
                header("Location: index.php?success=updated");
                exit;
            }
            else{
                $errors[] = "Unable to update category.";
                mysqli_stmt_close($statement);
            }
        }
    }
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
    <head>
        <title>Edit Category</title>
        <!-- Required meta tags -->
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
                    <h1 class="h3 mb-4">
                        Edit Category #<?= (int)$id ?>
                    </h1>
                    <?php if($errors):?>
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
                            <input type="text" id="name" name="name" maxlength="100" value="<?= e($name) ?>" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Update Category</button>
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
