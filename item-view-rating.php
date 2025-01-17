<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    header("Location: sign-in.php");
if (empty($_GET["food_id"])) {
    exit();
}
$food_id = $_GET["food_id"];

if (isset($_GET["submit"]) and $_GET["submit"] == "Remove") {
    $query =
        "DELETE FROM rating WHERE user_id = $user_id
            AND food_id = $food_id";
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
                AND food_id = $food_id
                ";
        $result = mysqli_query($conn, $query);
        $rated = (mysqli_fetch_array($result)[0] != 0);
        if ($rated) {
            $query =
                "UPDATE rating SET stars = $stars, comment = '$comment' 
                    WHERE user_id = $user_id AND food_id = $food_id";
            mysqli_query($conn, $query);
        } else {
            $query =
                "INSERT INTO rating(user_id, stars, comment, date, 
                            restaurant_flag, restaurant_id, food_id) 
                    VALUES ($user_id, $stars, '$comment', '$today', 0, NULL, $food_id)";
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
    <title>Restaurant View</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .menu-detail-image {
            max-width: 200px;
            max-height: 300px;
        }
    </style>
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
                            SELECT M.food_id, M.name, R.name AS r_name
                            FROM menu_item M, restaurant R
                            WHERE M.restaurant_id = R.restaurant_id
                            AND M.food_id = $food_id
                        ),
                        T2 AS (
                            SELECT food_id, ROUND(AVG(stars), 1) AS avg_rating
                            FROM rating
                            WHERE food_id IS NOT NULL
                            GROUP BY food_id
                        )
                    SELECT T1.name, T1.r_name, T2.avg_rating
                    FROM T1
                    LEFT JOIN T2
                    ON T1.food_id = T2.food_id
                    ";
        try {
            $result = mysqli_query($conn, $query1);
            $row = mysqli_fetch_assoc($result);
            $name = $row["name"];
            $r_name = $row["r_name"];
            $avg_rating = $row["avg_rating"];

            echo "<div class='img-container'>";
            // Read and display menu item image from JSON file
            $jsonFile = 'uploads/menu/' . $food_id . '.json';
            if (file_exists($jsonFile)) {
                $jsonData = json_decode(file_get_contents($jsonFile), true);
                $imageData = $jsonData['image_data'];
                $mimeType = $jsonData['mime_type'];
                echo "<img class='menu-detail-image' src='data:$mimeType;base64,$imageData' alt='$name'>";
            } else {
                echo "<img class='menu-detail-image' src='images/donut.png' alt='food'>";
            }
            echo "</div>
                    <div class='vertical-container half-size-vertical-container float-left restaurant-view'>";
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
                AND food_id = $food_id
                ";
    try {
        $result = mysqli_query($conn, $query);
        $rows = mysqli_fetch_all($result);
        if (count($rows) > 0) {
            $my_stars = $rows[0][0];
            echo "You have rated this item $my_stars stars.<br>
                    Update rating? ";
            echo "<form method='get' action='rate.php'>
                        <input type='hidden' name='food_id' value=$food_id>
                        <input type='submit' name='submit' value='Update'>
                    </form>";
            echo "<form action='item-view-rating.php'>
                            <input type='hidden' name='food_id' value=$food_id>
                            <input type='submit' name='submit' value='Remove'>
                        </form>
                    ";
        } else {
            echo "Would you like to rate this item? <br>";
            echo "<form method='get' action='rate.php'>
                        <input type='hidden' name='food_id' value=$food_id>
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
                        AND food_id = $food_id
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