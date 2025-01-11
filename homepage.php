<?php
global $conn;
require "dbconnect.php";
if (empty($_SESSION["user_id"])) {
    echo "You must be logged in to make an order<br>";
    header("Location: index.php");
    exit();
} else {
    $user_id = $_SESSION["user_id"];
}
try {
    $query = "SELECT * FROM ordered_items
                GROUP BY food_id";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (mysqli_sql_exception $ex) {
    exit("Error: " . $ex->getMessage());
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <h2>Offers Available Right Now</h2>
        <div class="row-1">
            <div class="column-1">
                <a class="image-link" href="">
                    <img src="images/blank-image.jpg" alt="offer 1">
                </a>
                <a href="">
                    Offer-1
                </a>
            </div>
            <div class="column-2">
                <a class="image-link" href="">
                    <img src="images/blank-image.jpg" alt="offer 2">
                </a>
                <a class="image-link" href="">
                    Offer-2
                </a>
            </div>
        </div>
        <a href="offers.php">See all offers</a>
        <h2>Best Sellers</h2>
        <h2>Here's everything we got</h2>
        <div class="restaurant-view-h2">
            <form action="" method="post">
                <label>Search Item
                    <input type="search" name="search" placeholder="Search">
                </label>
                <label>Sort by
                    <select name="sort">
                        <option value="food_id DESC" selected>Newest First</option>
                        <option value="food_id ASC">Oldest First</option>
                        <option value="name ASC">Alphabetically</option>
                        <option value="final_price ASC">Price (Lowest to Highest)</option>
                        <option value="final_price DESC">Price (Highest to Lowest)</option>
                        <option value="avg_rating ASC">Rating (Lowest to Highest)</option>
                        <option value="avg_rating DESC">Rating (Highest to Lowest)</option>
                    </select>
                </label>
                <input type="submit" name="submit" value="Apply">
            </form>
        </div>
        <div class="container">
            <?php
            try {
                $query1 =
                    "WITH
                                X AS (
                                    SELECT food_id, COUNT(*) AS total_ordered 
                                    FROM ordered_items
                                    GROUP BY food_id
                                    ORDER BY total_ordered DESC
                                    LIMIT 6
                                ),
                                T1 AS (
                                    SELECT M.food_id, M.name, M.price, R.name AS r_name
                                    FROM menu_item M, restaurant R
                                    WHERE M.restaurant_id = R.restaurant_id
                                    AND M.food_id IN (
                                        SELECT food_id FROM X
                                        )
                                ),
                                T2 AS (
                                    SELECT M.food_id, ROUND(AVG(Ra.stars), 1) AS avg_rating
                                    FROM menu_item M, rating Ra
                                    WHERE M.food_id = Ra.food_id
                                    GROUP BY M.food_id
                                ),
                                T3 AS (
                                    SELECT T1.food_id, T1.name, T1.price, T1.r_name, T2.avg_rating
                                    FROM T1 LEFT JOIN T2
                                    ON T1.food_id = T2.food_id
                                ),
                                T4 AS (
                                    SELECT DI.food_id, MAX(D.percentage) AS percentage FROM discount D
                                    JOIN discounted_items DI
                                    ON D.discount_id = DI.discount_id
                                    WHERE expiry_date > NOW()
                                    GROUP BY DI.food_id
                                ),
                                T5 AS (
                                    SELECT T3.food_id, T3.name, T3.r_name, T3.avg_rating, T3.price, T4.percentage
                                    FROM T3 LEFT JOIN T4
                                    ON T3.food_id = T4.food_id
                                )
                            SELECT food_id, name, r_name, avg_rating, price, percentage,
                            CASE
                                WHEN percentage IS NULL THEN price
                                ELSE price - price * percentage / 100
                            END AS final_price
                            FROM T5
                            ";
                if (isset($_POST["submit"])) {
                    if ($_POST["search"] != "") {
                        $query1 .= " WHERE name LIKE '%" . $_POST["search"] . "%'";
                    }
                    $query1 .= " ORDER BY " . $_POST["sort"];
                }
                $result = mysqli_query($conn, $query1);
                $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
                foreach ($items as $row) {
                    $food_id = $row["food_id"];
                    $name = $row["name"];
                    $r_name = $row["r_name"];
                    $avg_rating = $row["avg_rating"];
                    $price = $row["price"];
                    $final_price = $row["final_price"];

                    echo "<div class='container-item'>";
                    echo "<img src='images/donut.png' alt='food'>";
                    echo "<div>$name</div>";
                    if (is_null($avg_rating)) {
                        echo "<div class='row'>
                                    <div>$r_name </div>
                                    <div class='float-right'>N/A</div>
                                </div>";
                    } else {
                        echo "<div class='row'>
                                    <div>$r_name </div>
                                    <div class='float-right'>$avg_rating</div>
                                </div>";
                    }
                    echo "<div>";
                    if ($price != $final_price) {
                        echo "<s>$price</s>";
                    }
                    echo " $final_price</div>";
                    echo "<div class='float-right'>
                                <form class='inline-div' method='get' action='item-view-rating.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>
                                    <input type='submit' name='submit' value='rate' class='red-button'>
                                </form>
                                <form class='inline-div' method='get' action='cart.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>
                                    <input type='submit' name='add-item' value='add to cart' class='red-button'>
                                </form>
                            </div>";

                    //                            foreach (array_keys($row) as $key) {
                    //                                echo $key." : ".$row[$key]."<br>";
                    //                            }
                    echo "</div>";
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>
        </div>
        <a href="menu-items-all.php">See all items</a>
    </main>
</body>

</html>