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
            <h2>We are Hiring!</h2>
            <div class="simple-flexbox">
                <div>
                    <div class="simple-flexbox">
                        <form method="post" action="sign-up-driver.php">
                            <input class="red-button medium-sized-button" type="submit" name="user_type" value="Sign-Up">
                        </form>
                        <form method="post" action="sign-in-driver.php">
                            <input class="red-button medium-sized-button" type="submit" name="user_type" value="Sign-In">
                        </form>

                    </div>

                </div>


            </div>
        </div>
    </main>
</body>

</html>