<?php
global $conn;
require "dbconnect.php";
if (isset($_SESSION["driver_id"])) {
    header("Location: driver-homepage.php");
    exit();
}

if (isset($_POST["submit"])) {
    $phone = $_POST["phone"];
    $password = md5($_POST["password"]); 
    try {
        $query = "SELECT * FROM driver 
                 WHERE phone = '$phone' 
                 AND password = '$password'";
        $result = mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
    if (empty($row = mysqli_fetch_assoc($result))) {
        unset($_POST);
        echo "<script>var loginError = true;</script>";
    } else {
        $_SESSION["driver_id"] = $row["driver_id"];
        header("Location: driver-homepage.php");
        exit();
    }
}
?>



</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Sign in</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .error-message {
            color: red;
            display: none;
        }
    </style>
</head>

<body>
    <?php include "driver-header.php"; ?>
    <main>
        <div class="simple-form-div">
            <div style="padding-left: 110px;">
                <h2>Driver Sign In</h2>
                <form method="post" action="">
                    <label>Phone:<br>
                        <input type="text" name="phone" placeholder="Enter phone number" required>
                    </label><br>
                    <label>Password:<br>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </label><br>

                    <div id="errorMessage" class="error-message">
                        Incorrect Phone or Password!
                    </div>

                    <input type="submit" class="red-button" name="submit" value="Sign In"><br>
                </form>
                <br>New driver? <a href="sign-up-driver.php" class="inline-link">Register here</a>
            </div>
        </div>
    </main>
    <script src="js/sign-in-fail.js"></script>
</body>
</html>