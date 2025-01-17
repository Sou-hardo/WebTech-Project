<?php
global $conn;
require "dbconnect.php";
if (isset($_SESSION['user_id'])) {
    $current_id = $_SESSION['user_id'];
} else {
    exit("Please login first");
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="container centered">
            <div>
                <?php
                $query = "SELECT * FROM users WHERE user_id = $current_id;";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                $name = $row["name"];
                $phone = $row["phone"];
                $email = $row["email"];
                $date_of_birth = $row["date_of_birth"];
                $username = $row["username"];
                $password = $row["user_password"];
                $address_id = $row["address_id"];

                $query = "SELECT * FROM address WHERE address_id = $address_id";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                $flat_no = $row["flat_no"];
                $house_no = $row["house_no"];
                $road_no = $row["road_no"];
                $zip_code = $row["zip_code"];
                $area_id = $row["area_id"];

                ?>
                <h2>Change Profile</h2>
                <form name="sign-up" method="post" action="">
                    <label>Name<br>
                        <input type="text" name="name" value="<?php echo $name ?>" required><br>
                    </label>
                    <label>Phone<br>
                        <input type="text" name="phone" value="<?php echo $phone ?>" required><br>
                    </label>
                    <label>Email<br>
                        <input type="email" name="email" value="<?php echo $email ?>" required><br>
                    </label>
                    <label>Date of Birth<br>
                        <input type="date" name="date-of-birth" value="<?php echo $date_of_birth ?>" required><br>
                    </label>
                    <label>Username<br>
                        <input type="text" name="username" value="<?php echo $username ?>" required maxlength="20"><br>
                    </label>
                    <label>Password<br>
                        <input type="password" name="password" required minlength="6" maxlength="20" placeholder="Enter New Password"><br>
                    </label>
                    <h3>Address</h3>
                    <label>Flat<br>
                        <input type="text" name="flat" value="<?php echo $flat_no ?>" required><br>
                    </label>
                    <label>House<br>
                        <input type="number" name="house" value="<?php echo $house_no ?>" required><br>
                    </label>
                    <label>Road<br>
                        <input type="text" name="road" value="<?php echo $road_no ?>" required><br>
                    </label>
                    <label>Zip Code<br>
                        <input type="text" name="zip-code" value="<?php echo $zip_code ?>" required minlength="4" maxlength="4"><br>
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
                    <input class="red-button" type="submit" name="submit" value="Update"><br>
                </form>
            </div>
        </div>
    </main>
</body>

</html>

<?php
if (isset($_POST["submit"]) and $_POST["submit"] == "Update") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $date_of_birth = $_POST["date-of-birth"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $flat = $_POST["flat"];
    $house = $_POST["house"];
    $road = $_POST["road"];
    $zip_code = $_POST["zip-code"];
    $area_id = $_POST["area"];
    unset($_POST);

    try {
        $query1 = "UPDATE users SET 
                 name = '$name',
                 phone = '$phone',
                 email = '$email',
                 date_of_birth = '$date_of_birth',
                 username = '$username',
                 user_password = '$password'
                 WHERE user_id = $user_id;
                 ";

        mysqli_query($conn, $query1);
        if (isset($_POST["area_id"]) and $_POST["area_id"] != '') {
            $query2 = "UPDATE address SET 
                     flat_no = '$flat_no',
                     house_no = $house_no,
                     road_no = '$road_no',
                     zip_code = '$zip_code',
                     area_id = $area_id,
                     WHERE address_id = $address_id;
                     ";
            $result = mysqli_query($conn, $query2);
            mysqli_fetch_array($result);
        }

    } catch (mysqli_sql_exception $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
