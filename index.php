<?php
global $conn;
require "dbconnect.php";
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    include "footer.php";
    ?>
    <main>
        <div class='simple-flexbox no-style'>
            <div>
                <p><b>A <em>Web-Technologies </em>Project<br>For the Academic Year <em>Fall 2024-25</em></b></p><br>
                <a class="red-button" href="sign-up_role.php">Sign up</a>
                <a class="red-button" href="role.php">Sign in</a><br>
            </div>

            <div>
                <br><br><br><br><br>
                <img class="half-size" src="images/delivery.png" alt="index-image">
            </div>
        </div>
    </main>

    <footer><br><br><br><br><br>
        <h2 style="text-align: center;">More Info</h2>
        <div class="container">
            <div class="row-1" style="text-align: center;">
                <div class="item-1">
                    <b></b>
                </div>
                <div class="item-1">
                    <b></b>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>

<?php

?>