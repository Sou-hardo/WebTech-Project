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
    <style>
        .offer-image {
            max-width: 100px;
            max-height: 200px;
        }
    </style>
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <h2>Offers Available Right Now</h2>
        <div class="row-1">
            <?php
            try {
                $offers_query = "SELECT voucher_id AS id, title, description
                            FROM voucher
                            ORDER BY start_date DESC
                            LIMIT 2";
                $offers_result = mysqli_query($conn, $offers_query);
                $recent_offers = mysqli_fetch_all($offers_result, MYSQLI_ASSOC);

                foreach ($recent_offers as $offer) {
                    $id = $offer['id'];
                    $title = $offer['title'];

                    echo "<div class='column-1'>";
                    echo "<a class='image-link' href='offers.php'>";

                    // Read and display image from JSON file
                    $jsonFile = 'uploads/offers/' . $id . '.json';
                    if (file_exists($jsonFile)) {
                        $jsonData = json_decode(file_get_contents($jsonFile), true);
                        $imageData = $jsonData['image_data'];
                        $mimeType = $jsonData['mime_type'];
                        echo "<img class='offer-image' src='data:$mimeType;base64,$imageData' alt='$title'>";
                    } else {
                        echo "<img class='offer-image' src='images/blank-image.jpg' alt='offer'>";
                    }
                    echo "</a>";
                    echo "<a href='offers.php'>$title</a>";
                    echo "</div>";
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>
        </div>
        <h2>Recently Ordered Items</h2>
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
                                )
                            SELECT food_id, name, r_name, avg_rating, price,
                                   price AS final_price
                            FROM T3";
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
                    // Read and display menu item image from JSON file
                    $jsonFile = 'uploads/menu/' . $food_id . '.json';
                    if (file_exists($jsonFile)) {
                        $jsonData = json_decode(file_get_contents($jsonFile), true);
                        $imageData = $jsonData['image_data'];
                        $mimeType = $jsonData['mime_type'];
                        echo "<img src='data:$mimeType;base64,$imageData' alt='$name'>";
                    } else {
                        echo "<img src='images/donut.png' alt='food'>";
                    }
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
                    echo "$price</div>";
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

                    echo "</div>";
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>
        </div>
    </main>
</body>

</html>