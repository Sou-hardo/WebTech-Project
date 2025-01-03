<?php
global $conn;
require "dbconnect.php";
if (isset($_SESSION["user_id"])) {
    $query = "SELECT customer_flag FROM users WHERE user_id = " . $_SESSION["user_id"];
    $result = mysqli_query($conn, $query);
    $customer_flag = mysqli_fetch_array($result)[0];
    if ($customer_flag == 1) {
        header("Location: homepage.php");
        exit();
    } else {
        header("Location: staff\staff-homepage.php");
        exit();
    }
}
if (isset($_POST["user_type"])) {
    $customer_flag = ($_POST["user_type"] == "Customer") ? 1 : 0;
    //        echo $customer_flag;
} else {
    header("Location: customer-or-staff.php");
    exit();
}

if (isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    try {
        $query = "SELECT * FROM users
                      WHERE username = '$username'
                      AND user_password = '$password'
                      AND customer_flag = $customer_flag";
        echo $query;
        $result = mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
    if (empty($row = mysqli_fetch_assoc($result))) {
        unset($_POST);
        echo "Incorrect username or password!<br>";
    } else {
        $_SESSION["user_id"] = $row["user_id"];
        if ($customer_flag == 1)
            header("Location: homepage.php");
        else
            header("Location: staff\staff-homepage.php");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>

    </style>
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="simple-form-div">
            <div style="padding-left: 110px;">
                <h2>Welcome Back!</h2>
                <form method="post" action="sign-in.php">
                    <?php
                    if ($customer_flag == 1)
                        echo '<input type="hidden" name="user_type" value="Customer">';
                    else
                        echo '<input type="hidden" name="user_type" value="Staff">';
                    ?>
                    <label>Username:<br>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </label><br>
                    <label>Password:<br>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </label><br>
                    <input type="submit" class="red-button" name="submit" value="Submit"><br>
                </form>
                <br>Don't have an account? <a class="inline-link"><br>Sign up</a>
            </div>
        </div>
        </div>
    </main>
</body>

</html>