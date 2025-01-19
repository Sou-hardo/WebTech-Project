<?php
require "dbconnect.php";

if (!isset($_SESSION["driver_id"]) || !isset($_POST['order_id'])) {
    header("Location: driver.php");
    exit();
}

$order_id = $_POST['order_id'];

$query = "UPDATE orders SET delivery_status = 'complete' WHERE order_id = $order_id";

mysqli_query($conn, $query);
header("Location: driver-homepage.php");
?>
