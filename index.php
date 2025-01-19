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
                <a class="red-button" href="sign-up.php">Sign up</a>
                <a class="red-button" href="customer-or-staff.php">Sign in</a><br>
                <br><a style="font-size: 10px;" href="admin.php">Admin Login</a>
                <br><a style="font-size: 10px;" href="driver.php">Be part of our amazing service!</a>
            </div>

            <div>
                <br><br><br><br><br>
                <img class="half-size" src="images/delivery.png" alt="index-image">
            </div>

        </div>
    </main>

</body>

</html>