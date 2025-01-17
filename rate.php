<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    $user_id = 1;
if (empty($_GET["restaurant_id"]) and empty($_GET["food_id"])) {
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rate</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="container centered">
            <div>
                <?php
                if (isset($_GET["restaurant_id"])) {
                    $restaurant_id = $_GET["restaurant_id"];
                    $query =
                        "SELECT name FROM restaurant 
                                WHERE restaurant_id = $restaurant_id
                                ";
                    $result = mysqli_query($conn, $query);
                    $r_name = mysqli_fetch_array($result)[0];
                    echo "<p class='large-message'>Rate Restaurant:
                                    $r_name</p>";
                } else {
                    $food_id = $_GET["food_id"];
                    $query =
                        "SELECT name FROM menu_item 
                                WHERE food_id = $food_id
                                ";
                    $result = mysqli_query($conn, $query);
                    $f_name = mysqli_fetch_array($result)[0];
                    echo "<p class='large-message'>Rate Item:
                                    $f_name</p>";
                }
                ?>
                <?php
                if (isset($restaurant_id)) {
                    echo "<form name='rate' method='get' action='restaurant-view-rating.php'>
                                <input type='hidden' name='restaurant_id' value='$restaurant_id'>";
                } elseif (isset($food_id)) {
                    echo "<form name='rate' method='get' action='item-view-rating.php'>
                                <input type='hidden' name='food_id' value='$food_id'>";
                } else {
                    exit();
                }
                ?>

                <label for="stars">Rate
                    <select name="stars">
                        <option value="" selected disabled>How many stars?</option>
                        <option value="5">5 stars</option>
                        <option value="4">4 stars</option>
                        <option value="3">3 stars</option>
                        <option value="2">2 stars</option>
                        <option value="1">1 star</option>
                    </select>
                </label><br>
                <label for="comment">Comment<br>
                    <textarea name="comment" placeholder="Comment here"></textarea>
                </label><br>
                <input type="submit" name="submit" value="Rate">
                </form>

            </div>
        </div>
    </main>
</body>

</html>