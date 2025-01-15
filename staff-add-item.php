<?php
global $conn;
require "dbconnect.php";

if (isset($_GET["submit"])) {
    if (isset($_GET["restaurant_id"]) and $_GET["restaurant_id"] != '') {
        $restaurant_id = $_GET["restaurant_id"];
        $name = $_GET["name"];
        if (isset($_GET["category"]) and $_GET["category"] != '')
            $category = $_GET["category"];
        else
            $category = NULL;
        $price = $_GET["price"];
        $query =
            "INSERT INTO menu_item(name, category, price, restaurant_id)
                VALUES ('$name', '$category', $price, $restaurant_id)";
        $result = mysqli_query($conn, $query);
        if ($result) {
            header("Location: staff-homepage.php");
            exit();
        }
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
        <div class="basic-form">
            <form>
                <label>Name:<br>
                    <input type="text" name="name" required>
                </label><br>
                <label>Category:<br>
                    <input type="text" name="category">
                </label><br>
                <label>Price:<br>
                    <input type="number" name="price" required>
                </label><br>
                <label>Restaurant:<br>
                    <select name="restaurant_id">
                        <option value="" disabled selected>Please choose an option</option>
                        <?php
                        $query2 = "SELECT restaurant_id, name FROM restaurant";
                        $result = mysqli_query($conn, $query2);
                        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        foreach ($rows as $row) {
                            $restaurant_id = $row["restaurant_id"];
                            $r_name = $row["name"];
                            echo "<option value=$restaurant_id>$r_name</option>";
                        }
                        ?>
                    </select>
                </label><br>
                <input type="submit" name="submit" value="Add Item">
            </form>
        </div>


    </main>
</body>

</html>