<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    $user_id = 1;
//    if (empty($_GET["restaurant_id"]) and empty($_GET["food_id"])) {
//        exit();
//    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="simple-div">
            <h2>Payment Type?</h2>
            <div class="simple-flexbox">
                <div>
                    <div class="simple-flexbox">
                        <form action="card-payment.php">
                            <input class="red-button medium-sized-button" type="submit" name="submit" value="Card Payment">
                        </form>
                        <form action="mobile-banking.php">
                            <input class="red-button medium-sized-button" type="submit" name="submit" value="Mobile Banking">
                        </form>

                    </div>

                </div>


            </div>
        </div>
    </main>
</body>

</html>