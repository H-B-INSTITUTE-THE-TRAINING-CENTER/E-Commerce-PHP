<?php 
    function calculateTotal(float $price, float $quantity): float {
        return $price * $quantity;
    }

    function calculateDiscount(float $price, float $discountPercent): float{
        $discount = ($price * $discountPercent) / 100;
        return $price - $discount;
    }

    function isProductAvailable(int $stock): bool{
        return $stock > 0;
    }

    function formatPrice(float $price){
        return "₹" . number_format($price, 2);
    }
?>