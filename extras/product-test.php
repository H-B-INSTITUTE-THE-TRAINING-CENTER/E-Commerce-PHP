<?php
    require_once './config/database.php';
    $sql = "SELECT * FROM `products`";
    $result = mysqli_query($conn,$sql);
    if (!$result) {
        die("Unable to fetch records: ".mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($result)) {
            echo $row['name']."<br>";
    }
?>