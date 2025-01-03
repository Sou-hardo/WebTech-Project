<?php
global $conn;
require "dbconnect.php";

if (isset($_SESSION["user_id"])) {
    header("Location: homepage.php");
    exit();
}

//    if (isset($_SESSION["user_id"]))
//        $user_id = $_SESSION["user_id"];
//    else
//        $user_id = 1;
//    if (empty($_GET["restaurant_id"]) and empty($_GET["food_id"])) {
//        exit();
//    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer or Staff</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        input[type="submit"] {
            width: 150px;
        }
    </style>
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="simple-div">
            <h2>Are you a customer or staff?</h2>
            <div class="simple-flexbox">
                <div>
                    <div class="simple-flexbox">
                        <form method="post" action="sign-in.php">
                            <input class="red-button medium-sized-button" type="submit" name="user_type" value="Customer">
                        </form>
                        <form method="post" action="sign-in.php">
                            <input class="red-button medium-sized-button" type="submit" name="user_type" value="Staff">
                        </form>

                    </div>

                </div>


            </div>
        </div>
    </main>
</body>

</html>