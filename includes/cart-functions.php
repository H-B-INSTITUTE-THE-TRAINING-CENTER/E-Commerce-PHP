<?php
    // Start session before any HTML output
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

   // Generate or retrieve CSRF token.
   function cart_csrf_token(){
        if(empty($_SESSION['cart_csrf_token'])){
            $_SESSION['cart_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['cart_csrf_token'];
    }

    // Verify submitted CSRF token.
    function cart_verify_csrf() {
        $submittedToken = $_POST['csrf_token'] ?? '';

        return isset($_SESSION['cart_csrf_token']) && is_string($submittedToken) && hash_equals($_SESSION['cart_csrf_token'], $submittedToken);
    }

    // Count total cart items (sum of quantities).
    function cart_item_count() {
        $count = 0;
        foreach($_SESSION['cart'] ?? [] as $quantity){
            $count += (int)$quantity;
        }
        return $count;
    }

    



?> 