<?php
global $conn;
require_once "dbconnect.php";
if (empty($_GET["restaurant_id"])) {
    exit();
}
$restaurant_id = $_GET["restaurant_id"];

// TODO: Fix address null error 

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Restaurant View</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <!-- <div class="container restaurant-view">
        <?php
        $query1 =
            "SELECT R.name, Ar.name AS area, Ar.city, Ar.district
                    FROM restaurant R, address A, area Ar, rating Ra
                    WHERE R.address_id = A.address_id
                    AND A.area_id = Ar.area_id
                    AND R.restaurant_id = Ra.restaurant_id
                    AND R.restaurant_id = 3;
                    ";
        $query2 =
            "SELECT ROUND(AVG(stars), 1) AS avg_rating
                    FROM rating
                    WHERE restaurant_id = 3
                    GROUP BY restaurant_id
                    ";
        try {
            $result = mysqli_query($conn, $query1);
            $row = mysqli_fetch_assoc($result);
            $r_name = $row["name"];
            $area = $row["area"];
            $city = $row["city"];
            $district = $row["district"];

            $result = mysqli_query($conn, $query2);
            $row = mysqli_fetch_array($result);
            $avg_rating = $row[0];

            echo "<div class='img-container' '><img src='images/donut.png' alt='donut'></div>
                    <div class='vertical-container half-size-vertical-container float-left restaurant-view'>
                    <div class='restaurant-view'>$r_name</div>
                    <div class='restaurant-view'>$area, $city, $district</div>";
            if (isset($avg_rating))
                echo "<div class='restaurant-view'>$avg_rating</div>";
            echo "</div>";
        } catch (mysqli_sql_exception $ex) {
            exit("Error: " . $ex->getMessage());
        }
        ?>
    </div> -->
    <h2>Items in <?php echo $r_name ?></h2>
    <div class="restaurant-view-h2">
        <form action="restaurant-view-items.php" method="get">
            <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id ?>">
            <label for="search">Search Item:
                <input type="search" name="search" placeholder="Search">
            </label>
            <label for="sort">Sort by:
                <select name="sort">
                    <option name="Newest First" value="food_id DESC" selected>Newest First</option>>
                    <option name="Oldest First" value="food_id ASC">Oldest First</option>>
                    <option name="Alphabetically" value="name ASC">Alphabetically</option>>
                    <option name="Price (Lowest to Highest)" value="final_price ASC">Price (Lowest to Highest)</option>>
                    <option name="Price (Highest to Lowest)" value="final_price DESC">Price (Highest to Lowest)</option>>
                    <option name="Rating (Lowest to Highest)" value="avg_rating ASC">Rating (Lowest to Highest)</option>>
                    <option name="Rating (Highest to Lowest)" value="avg_rating DESC">Rating (Highest to Lowest)</option>>
                </select>
            </label>
            <input type="submit" name="submit" value="Apply">
        </form>
    </div>
    <div class="container">
        <?php
        try {
            $query3 =
                "WITH
                            M1 AS (
                                SELECT M.food_id, M.name, M.price, R.name AS r_name
                                FROM menu_item M, restaurant R
                                WHERE M.restaurant_id = R.restaurant_id
                                AND R.restaurant_id = $restaurant_id
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
                            )
                        SELECT food_id, name, r_name, avg_rating, price
                        FROM M2
                        ";
            if (isset($_GET["submit"])) {
                if (isset($_GET["search"]) && $_GET["search"] != "") {
                    $query3 .= " WHERE name LIKE '%" . $_GET["search"] . "%'";
                }
                if (isset($_GET["sort"])) {
                    $query3 .= " ORDER BY " . $_GET["sort"];
                }
            }
            $result = mysqli_query($conn, $query3);
            $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
            foreach ($items as $row) {
                $food_id = $row["food_id"];
                $name = $row["name"];
                $r_name = $row["r_name"];
                $avg_rating = $row["avg_rating"];
                $price = $row["price"];

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
                echo "<div>$price</div>";
                echo "<div class='float-right'>
                            <form class='inline-div' method='get' action='rating.php'>
                                <input type='hidden' name='food_id' value='$food_id'>
                                <input type='submit' name='submit' value='rate' class='red-button'>
                            </form>
                            <form class='inline-div' method='get' action='cart.php'>
                                <input type='hidden' name='food_id' value='$food_id'>
                                <input type='submit' name='submit' value='add to cart' class='red-button'>
                            </form>
                        </div>";

                echo "</div>";
            }
        } catch (mysqli_sql_exception $ex) {
            exit("Error: " . $ex->getMessage());
        }
        ?>
    </div>
</body>

</html>