<?php
global $conn;
require_once "dbconnect.php";

if (isset($_GET["delete"])) {
    if (isset($_GET["food_id"])) {
        $food_id = $_GET["food_id"];
        $query = "DELETE FROM menu_item WHERE food_id = $food_id";
        mysqli_query($conn, $query);
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Items</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "staff-header.php";
    ?>
    <main>
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
                                M1 AS (
                                    SELECT M.food_id, M.name, M.price, R.name AS r_name
                                    FROM menu_item M, restaurant R
                                    WHERE M.restaurant_id = R.restaurant_id
                                ),
                                A1 AS (
                                    SELECT M.food_id, ROUND(AVG(Ra.stars), 1) AS avg_rating
                                    FROM menu_item M, rating Ra
                                    WHERE M.food_id = Ra.food_id
                                    GROUP BY M.food_id
                                ),
                                M2 AS (
                                    SELECT M1.food_id, M1.name, M1.price, M1.r_name, A1.avg_rating
                                    FROM M1 LEFT JOIN A1
                                    ON M1.food_id = A1.food_id
                                ),
                                D1 AS (
                                    SELECT DI.food_id, MAX(D.percentage) AS percentage FROM discount D
                                    JOIN discounted_items DI
                                    ON D.discount_id = DI.discount_id
                                    WHERE expiry_date > NOW()
                                    GROUP BY DI.food_id
                                ),
                                M3 AS (
                                    SELECT M2.food_id, M2.name, M2.r_name, 
                                        M2.avg_rating, M2.price, D1.percentage
                                    FROM M2 LEFT JOIN D1
                                    ON M2.food_id = D1.food_id
                                )
                            SELECT food_id, name, r_name, avg_rating, price, percentage, 
                            CASE
                                WHEN percentage IS NULL THEN price
                                ELSE price - price * percentage / 100
                            END AS final_price
                            FROM M3
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
                    
                    // Read and display image from JSON file
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
                    if ($price != $final_price) {
                        echo "<s>$price</s>";
                    }
                    echo " $final_price</div>";
                    echo "<div class='float-right'>
                                <form class='inline-div' method='get' action='staff-add-item.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>
                                    <input type='submit' name='update' value='update' class='red-button'>
                                </form>
                                <form class='inline-div' method='get' action='staff-items.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>
                                    <input type='submit' name='delete' value='delete' class='red-button'>
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