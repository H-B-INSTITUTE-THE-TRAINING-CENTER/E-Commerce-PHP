<?php
    include "functions.php";

    $total = calculateTotal(55000,2);
    echo "Total: ₹" . $total."<br>";

    echo formatPrice(55000);
?>