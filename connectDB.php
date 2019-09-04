<?php
    $con = mysqli_connect("YOUR_SERVER","YOUR_USERNAME","YUOR_PASSWORD","YOUR_SCHEMA");
    if (!$con) {
        echo "Connection error: " . mysqli_connect_errno() . " - " . mysqli_connect_error();
        exit();
    }

    mysqli_set_charset($con, "utf8");
?>