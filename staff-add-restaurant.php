<?php
global $conn;
require "dbconnect.php";

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $flat = $_POST["flat"];
    $house = $_POST["house"];
    $road = $_POST["road"];
    $zip_code = $_POST["zip-code"];
    $area_id = $_POST["area"];

    try {
        $query1 = "INSERT INTO address(flat_no, house_no, 
                        road_no, zip_code, area_id) VALUES 
                        ('$flat', $house, '$road', '$zip_code', $area_id)";
        mysqli_query($conn, $query1);
        $query2 = "SELECT MAX(address_id) FROM address";
        $result = mysqli_query($conn, $query2);
        $row = mysqli_fetch_array($result);
        $address_id = $row[0];
        $query3 = "INSERT INTO restaurant (name, phone_no, address_id) VALUES 
                       ('$name', '$phone', $address_id)";
        $result = mysqli_query($conn, $query3);
        if ($result) {
            header("Location: staff-homepage.php");
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
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>All Items</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "staff-header.php";
    ?>
    <main>
        <div class="container centered">
            <div>

                <form method="post" action="staff-add-restaurant.php">
                    <label>Name<br>
                        <input type="text" name="name" required><br>
                    </label>
                    <label>Phone<br>
                        <input type="text" name="phone" required><br>
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


                            //                            echo "<input type='radio' name='area' value="
                            //                                .$row["area_id"]."> ";
                            //                            echo ucfirst($row["name"]).
                            //                                 " (City: ".ucfirst($row["city"]).
                            //                                 ", District: ".ucfirst($row["district"]).") <br>";
                        }
                        echo "</select>" . "<br>";
                        ?>
                    </label>
                    <input class="red-button" type="submit" name="submit" value="Add Branch"><br>
                </form>

            </div>
        </div>
    </main>
</body>

</html>
