<?php
global $conn;
require "dbconnect.php";
if (isset($_SESSION["user_id"])) {
    $query = "SELECT admin_flag FROM users WHERE user_id = " . $_SESSION["user_id"];
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $admin_flag = $row['admin_flag'];
    if ($admin_flag == 1) {
        header("Location: admin-homepage.php");
        exit();
    } else {
        header("Location: admin.php");
        exit();
    }
}

if (isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $admin_flag = 1;
    try {
        $query = "SELECT * FROM users
                      WHERE username = '$username'
                      AND user_password = '$password'
                      AND admin_flag = '$admin_flag'";
        $result = mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $ex) {
        echo "<script>var loginError = true;</script>";
        $result = false;
    }
    if (!$result || empty($row = mysqli_fetch_assoc($result))) {
        unset($_POST);
        echo "<script>var loginError = true;</script>";
    } else {
        $_SESSION["user_id"] = $row["user_id"];
        if ($row['admin_flag'] == 1) {
            header("Location: admin-homepage.php");
        }
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign-in</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .error-message {
            color: red;
            display: none;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="simple-form-div">
            <div style="padding-left: 110px;">
                <h2>Admin Login</h2>
                <form method="post" action="admin.php">
                    <label>Username:<br>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </label><br>
                    <label>Password:<br>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </label><br>
                    <div id="errorMessage" class="error-message">
                        Incorrect Credentials!
                    </div>
                    <input type="submit" class="red-button" name="submit" value="Submit"><br>
                </form>
            </div>
        </div>
        </div>
    </main>
    <script src="js/sign-in-fail.js"></script>
</body>

</html>