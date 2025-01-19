<?php
global $conn;
require "dbconnect.php";

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $date_of_birth = $_POST["date-of-birth"];
    $nid_no = $_POST["nid-no"];
    $license_no = $_POST["license-no"];
    $flat = $_POST["flat"];
    $house = $_POST["house"];
    $road = $_POST["road"];
    $zip_code = $_POST["zip-code"];
    $area_id = $_POST["area"];
    $password = md5($_POST["password"]); // Add password handling
    unset($_POST);

    try {
        $query1 = "INSERT INTO address(flat_no, house_no, road_no, zip_code, area_id) 
                    VALUES ('$flat', $house, '$road', '$zip_code', $area_id)";
        mysqli_query($conn, $query1);
        $query2 = "SELECT MAX(address_id) FROM address";
        $result = mysqli_query($conn, $query2);
        $row = mysqli_fetch_array($result);
        $address_id = $row[0];
        $query3 = "INSERT INTO driver (name, phone, email, date_of_birth, nid_no, license_no, address_id, password) 
                    VALUES ('$name', '$phone', '$email', '$date_of_birth', '$nid_no', '$license_no', $address_id, '$password')";
        $result = mysqli_query($conn, $query3);
        if ($result) {
            header("Location: sign-in-driver.php");
            exit();
        }
    } catch (mysqli_sql_exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">

    <style>
        input[type="text"],
        input[type="password"],
        input[type="date"],
        input[type="email"],
        input[type="number"],
        select {
            width: 300px;
        }

        input[type="submit"] {
            padding: 10px;
            width: 100px;
            float: right;
        }

        .error-message {
            color: yellow;
            font-size: 12px;
            margin-top: 5px;
            margin-bottom: 5px;
            display: none;
        }
    </style>

    
</head>

<body>
    <?php
    include "driver-header.php";
    ?>
    <main>
        <div class="container centered">
            <div>
                <h2>Driver Registration</h2>
                <form name="sign-up" method="post" action="">
                    <label>Name<br>
                        <input type="text" name="name" required><br>
                    </label>
                    <label>Phone<br>
                        <input type="text" name="phone" required><br>
                        <span id="phone-error" class="error-message"></span>
                    </label>
                    <label>Email<br>
                        <input type="email" name="email" required><br>
                        <span id="email-error" class="error-message"></span>
                    </label>
                    <label>Date of Birth<br>
                        <input type="date" name="date-of-birth" required><br>
                    </label>
                    <label>NID Number<br>
                        <input type="text" name="nid-no" required><br>
                        <span id="nid-error" class="error-message"></span>
                    </label>
                    <label>License Number<br>
                        <input type="text" name="license-no" required><br>
                        <span id="license-error" class="error-message"></span>
                    </label>
                    <label>Password<br>
                        <input type="password" name="password" required minlength="6" maxlength="20"><br>
                        <span id="password-error" class="error-message"></span>
                    </label>
                    <h3>Address</h3>
                    <label>Flat<br>
                        <input type="text" name="flat" required><br>
                    </label>
                    <label>House<br>
                        <input type="number" name="house" required><br>
                    </label>
                    <label>Road<br>
                        <input type="text" name="road" required><br>
                    </label>
                    <label>Zip Code<br>
                        <input type="text" name="zip-code" required minlength="4" maxlength="4"><br>
                    </label>
                    <h3>Area</h3>
                    <label>
                        <?php
                        try {
                            $query = "SELECT * FROM area ORDER BY name, city, district";
                            $result = mysqli_query($conn, $query);
                            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        } catch (mysqli_sql_exception $ex) {
                            exit("Error: " . $ex->getMessage());
                        }
                        echo "<select name='area'>";
                        echo "<option value='' selected disabled>Please choose an area</option>";

                        foreach ($rows as $row) {
                            $area_id = $row["area_id"];
                            $a_name = $row["name"];
                            $city = $row["city"];
                            $district = $row["district"];
                            echo "<option value=$area_id>$a_name, $city, $district</option>";
                        }
                        echo "</select>" . "<br>";
                        ?>
                    </label>
                    <input class="red-button" type="submit" name="submit" value="Register"><br>
                </form>
                <br><br><br><br>
                Already have an account? <a href="sign-in-driver.php" class="inline-link">Sign in</a>

            </div>
        </div>
    </main>
    
    <script src="js/sign-up-validations.js"></script>

</body>

</html>