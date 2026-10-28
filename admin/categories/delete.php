<?php
    require_once __DIR__.'/../../config/database.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        header('Allow: POST');
        exit("Method not allowed");
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        exit("Invalid Category ID.");
    }

    mysqli_begin_transaction($conn);
    try {
        $statement = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($statement,"i",$id);
        mysqli_stmt_execute($statement);

        $categoryResult = mysqli_stmt_get_result($statement);
        $category = mysqli_fetch_assoc($categoryResult);
        mysqli_stmt_close($statement);

        if(!$category){
            mysqli_rollback($conn);
            header("Location: index.php?error=not_found");
            exit;
        }

        $statement = mysqli_prepare($conn, "SELECT id FROM products WHERE category_id = ? LIMIT 1");
        mysqli_stmt_bind_param($statement,"i",$id);
        mysqli_stmt_execute($statement);

        $productResult = mysqli_stmt_get_result($statement);
        $hasProducts = mysqli_num_rows($productResult) > 0;
        mysqli_stmt_close($statement);

        if($hasProducts){
            mysqli_rollback($conn);
            header("Location: index.php?error=has_products");
            exit;
        }

        $statement = mysqli_prepare($conn,"DELETE FROM categories WHERE id = ?");
        mysqli_stmt_bind_param($statement,"i",$id);
        mysqli_stmt_execute($statement);

        $deleted = mysqli_stmt_affected_rows($statement);

        mysqli_stmt_close($statement);

        if($deleted !== 1){
            throw new RuntimeException("Category was not deleted");
        }
        mysqli_commit($conn);
        header("Location: index.php?success=deleted");
        exit;


    } catch (Throwable $e) {
        mysqli_rollback($conn);
        error_log("Category deleteion failed: ".$e->getMessage());
        http_response_code(500);
        exit("Unable to delete the category. Check the server error log.");
    }
?>