<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
// else
//     $user_id = 1;
if (empty($_GET["restaurant_id"])) {
    exit();
}
$restaurant_id = $_GET["restaurant_id"];

if (isset($_GET["submit"]) and $_GET["submit"] == "Remove") {
    $query =
        "DELETE FROM rating WHERE user_id = $user_id
            AND restaurant_id = $restaurant_id";
    mysqli_query($conn, $query);
}

if (isset($_GET["submit"]) and $_GET["submit"] == "Rate") {
    if (empty($_GET["stars"]) or $_GET["stars"] == '') {
        echo "<p class='alert'>You have to choose between 1-5 stars!</p>";
    } else {
        $stars = $_GET["stars"];
        $comment = $_GET["comment"];
        $today = date("Y-m-d");
        $query =
            "SELECT COUNT(*) FROM rating
                WHERE user_id = $user_id
                AND restaurant_id = $restaurant_id
                ";
        $result = mysqli_query($conn, $query);
        $rated = (mysqli_fetch_array($result)[0] != 0);
        if ($rated) {
            $query =
                "UPDATE rating SET stars = $stars 
                    WHERE user_id = $user_id AND restaurant_id = $restaurant_id";
            mysqli_query($conn, $query);
        } else {
            $query =
                "INSERT INTO rating(user_id, stars, comment, date, 
                            restaurant_flag, restaurant_id, food_id) 
                    VALUES ($user_id, $stars, '$comment', '$today', 1, $restaurant_id, NULL)";
            mysqli_query($conn, $query);
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Restaurant</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <div class="container restaurant-view">
        <?php
        $query1 =
            "WITH
                        T1 AS (
                            SELECT R.restaurant_id, R.name, Ar.name AS area, Ar.city, Ar.district
                            FROM restaurant R, address A, area Ar
                            WHERE R.address_id = A.address_id
                            AND A.area_id = Ar.area_id
                            AND R.restaurant_id = $restaurant_id
                        ),
                        T2 AS (
                            SELECT restaurant_id, ROUND(AVG(stars), 1) AS avg_rating
                            FROM rating
                            WHERE restaurant_id IS NOT NULL
                            GROUP BY restaurant_id
                        )
                    SELECT T1.name, T1.area, T1.city, T1.district, T2.avg_rating
                    FROM T1
                    LEFT JOIN T2
                    ON T1.restaurant_id = T2.restaurant_id
                    ";
        try {
            $result = mysqli_query($conn, $query1);
            $row = mysqli_fetch_assoc($result);
            $r_name = $row["name"];
            $area = $row["area"];
            $city = $row["city"];
            $district = $row["district"];
            $avg_rating = $row["avg_rating"];

            echo "<div class='img-container'>";
            // Read and display restaurant image from JSON file
            $jsonFile = 'uploads/restaurant/' . $restaurant_id . '.json';
            if (file_exists($jsonFile)) {
                $jsonData = json_decode(file_get_contents($jsonFile), true);
                $imageData = $jsonData['image_data'];
                $mimeType = $jsonData['mime_type'];
                echo "<img src='data:$mimeType;base64,$imageData' alt='$r_name'>";
            } else {
                echo "<img src='images/donut.png' alt='restaurant'>";
            }
            echo "</div>
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
    </div>
    <?php
    $query =
        "SELECT stars 
                FROM rating 
                WHERE user_id = $user_id 
                AND restaurant_id = $restaurant_id
                ";
    try {
        $result = mysqli_query($conn, $query);
        $rows = mysqli_fetch_all($result);
        if (count($rows) > 0) {
            $my_stars = $rows[0][0];
            echo "You have rated this restaurant $my_stars stars.<br>
                    Update rating? ";
            echo "<form method='get' action='rate.php'>
                        <input type='hidden' name='restaurant_id' value=$restaurant_id>
                        <input type='submit' name='submit' value='Update'>
                    </form>";
            echo "<form action='restaurant-view-rating.php'>
                            <input type='hidden' name='restaurant_id' value=$restaurant_id>
                            <input type='submit' name='submit' value='Remove'>
                        </form>
                    ";
        } else {
            echo "Would you like to rate this restaurant? <br>";
            echo "<form method='get' action='rate.php'>
                        <input type='hidden' name='restaurant_id' value=$restaurant_id>
                        <input type='submit' name='submit' value='Rate'>
                    </form>";
        }
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
    ?>

    <div class="vertical-container">
        <?php
        try {
            $query3 =
                "SELECT Ra.stars, Ra.comment, Ra.date, U.name
                        FROM users U, rating Ra
                        WHERE U.user_id = Ra.user_id
                        AND restaurant_id = $restaurant_id
                        ";
            $result = mysqli_query($conn, $query3);
            $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
            foreach ($items as $row) {
                $stars = $row["stars"];
                $comment = $row["comment"];
                $date = $row["date"];
                $u_name = $row["name"];
                echo "<div class='vertical-container-item restaurant-view'>
                            <div class='comment-container'>
                                <div>$u_name</div> 
                                <div class='float-right'>$stars</div>
                            </div>
                            <div>$date</div>  
                            <div class='comment'>$comment</div>
                        </div>";
            }
        } catch (mysqli_sql_exception $ex) {
            exit("Error: " . $ex->getMessage());
        }
        ?>
    </div>
</body>

</html>