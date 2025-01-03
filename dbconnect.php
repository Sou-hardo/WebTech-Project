<?php
session_start();
try {
    $conn = mysqli_connect(
        hostname: "localhost",
        username: "root",
        database: "final"
    );
} catch (mysqli_sql_exception $ex) {
    exit("Error: " . $ex->getMessage());
}