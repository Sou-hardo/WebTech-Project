<?php
require "dbconnect.php";

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"])) {
    header("Location: admin.php");
    exit();
}

$query = "SELECT admin_flag FROM users WHERE user_id = " . $_SESSION["user_id"];
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);
if (!$row || $row['admin_flag'] != 1) {
    header("Location: admin.php");
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get total number of customers
function getTotalCustomers($conn)
{
    $sql = "SELECT COUNT(*) as total_customers FROM users WHERE customer_flag = 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_customers'];
}

// Function to get total number of staffs
function getTotalStaffs($conn)
{
    $sql = "SELECT COUNT(*) as total_staffs FROM users WHERE customer_flag = 0";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_staffs'];
}

// Function to get total number of placed orders
function getTotalOrders($conn)
{
    $sql = "SELECT COUNT(DISTINCT order_id) as total_orders FROM ordered_items";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_orders'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">

    <style>
        .text-align {
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <?php
    include "admin-header.php";
    include "footer.php";
    ?>
    <div class="text-align">
        <h2>Statistics</h2>
        <p>Total Customers: <?php echo getTotalCustomers($conn); ?></p>
        <p>Total Staffs: <?php echo getTotalStaffs($conn); ?></p>
        <p>Total Orders: <?php echo getTotalOrders($conn); ?></p>
    </div>
</body>

</html>