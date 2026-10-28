<?php
    require_once __DIR__.'/../../config/database.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed');
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if(!$id || $id < 1){
        http_response_code(400);
        exit("Invalid Product ID.");
    }

    $stmt = mysqli_prepare($conn,'DELETE FROM products WHERE id = ?');
    if(!$stmt){
        die("Unable to prepare product deleteion.");
    }

    mysqli_stmt_bind_param($stmt,"i",$id);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        die("Unable to delete the product.");
    }

    $deleted = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);

    if($deleted === 0){
        header("Location: index.php?error=not_found");
        exit;
    }

    header("Location: index.php?success=deleted");
    exit;



?>