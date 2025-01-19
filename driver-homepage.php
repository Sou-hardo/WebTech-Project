<?php
require "dbconnect.php";

// Check if user is logged in and is driver
if (!isset($_SESSION["driver_id"])) {
    header("Location: driver.php");
    exit();
}

// Fetch processing orders with customer details
$query = "SELECT o.order_id, o.total_price, u.name as customer_name, 
        u.phone, a.flat_no, a.house_no, a.road_no, ar.city, ar.district
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN address a ON o.address_id = a.address_id
        JOIN area ar ON a.area_id = ar.area_id
        WHERE o.delivery_status = 'processing'
        ORDER BY o.order_id ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Homepage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
        }
        .btn-complete {
            background-color: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-complete:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <?php include "driver-header.php"; ?>

    <div>
        <h2>Processing Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Total Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td>
                        <?php 
                        echo $row['flat_no'] ? "Flat: " . $row['flat_no'] . ", " : "";
                        echo "House: " . $row['house_no'] . ", ";
                        echo "Road: " . $row['road_no'] . ", ";
                        echo $row['city'] . ", " . $row['district'];
                        ?>
                    </td>
                    <td><?php echo $row['total_price']; ?></td>
                    <td>
                        <form action="complete_delivery.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <button type="submit" class="btn-complete">Complete Delivery</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
</body>

</html>