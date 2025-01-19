<?php
global $conn;
require_once "dbconnect.php";

// Fetch existing restaurant data if update was clicked
$isUpdate = false;
$existingData = null;
if (isset($_GET["update"]) && isset($_GET["restaurant_id"])) {
    $isUpdate = true;
    $restaurant_id = $_GET["restaurant_id"];
    $query = "SELECT r.*, a.* FROM restaurant r 
              JOIN address a ON r.address_id = a.address_id 
              WHERE r.restaurant_id = $restaurant_id";
    $result = mysqli_query($conn, $query);
    $existingData = mysqli_fetch_assoc($result);
}

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $flat = $_POST["flat"];
    $house = $_POST["house"];
    $road = $_POST["road"];
    $zip_code = $_POST["zip-code"];
    $area_id = $_POST["area"];

    try {
        if (isset($_POST["restaurant_id"])) { // Update existing restaurant
            $restaurant_id = $_POST["restaurant_id"];
            $address_id = $_POST["address_id"];

            // Update address
            $query1 = "UPDATE address SET flat_no='$flat', house_no=$house, 
                      road_no='$road', zip_code='$zip_code', area_id=$area_id 
                      WHERE address_id=$address_id";
            mysqli_query($conn, $query1);

            // Update restaurant
            $query2 = "UPDATE restaurant SET name='$name', phone_no='$phone' 
                      WHERE restaurant_id=$restaurant_id";
            $result = mysqli_query($conn, $query2);
        } else { // Insert new restaurant
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
        }

        if ($result) {
            // Handle image upload
            if (isset($_FILES['restaurant_image']) && $_FILES['restaurant_image']['size'] > 0) {
                $file = $_FILES['restaurant_image'];
                $restaurant_id = isset($_POST["restaurant_id"]) ? $_POST["restaurant_id"] : mysqli_insert_id($conn);
                $fileName = $restaurant_id . '.json';
                $uploadPath = 'uploads/restaurant/';

                // Process image and create JSON
                $imageData = [
                    'restaurant_id' => $restaurant_id,
                    'name' => $name,
                    'image_data' => base64_encode(file_get_contents($file['tmp_name'])),
                    'mime_type' => $file['type']
                ];

                // Save JSON file
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                file_put_contents($uploadPath . $fileName, json_encode($imageData));
            }

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
    <style>
        #imagePreview {
            max-width: 200px;
            margin: 10px 0;
            display: none;
        }
        input[type="text"], input[type="number"], input[type="date"], input[type="time"], input[type="file"], textarea {
            width: 350px;
        }
    </style>
</head>

<body>
    <?php
    //include "staff-header.php";
    ?>
    <main>
        <div class="container centered">
            <div>
                <h2><?php echo $isUpdate ? 'Update Restaurant' : 'Add New Restaurant'; ?></h2>
                <form method="post" action="staff-add-restaurant.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <?php if ($isUpdate): ?>
                        <input type="hidden" name="restaurant_id" value="<?php echo $existingData['restaurant_id']; ?>">
                        <input type="hidden" name="address_id" value="<?php echo $existingData['address_id']; ?>">
                    <?php endif; ?>

                    <label>Name<br>
                        <input type="text" name="name" required
                            value="<?php echo $isUpdate ? $existingData['name'] : ''; ?>">
                    </label>
                    <div id="nameError" class="error"></div><br>
                    
                    <label>Phone<br>
                        <input type="text" name="phone" required
                            value="<?php echo $isUpdate ? $existingData['phone_no'] : ''; ?>">
                    </label>
                    <div id="phoneError" class="error"></div><br>
                    <h3>Address</h3>
                    <label>Flat<br>
                        <input type="text" name="flat" required
                            value="<?php echo $isUpdate ? $existingData['flat_no'] : ''; ?>"><br>
                    </label>
                    <label>House<br>
                        <input type="number" name="house" required
                            value="<?php echo $isUpdate ? $existingData['house_no'] : ''; ?>"><br>
                    </label>
                    <label>Road<br>
                        <input type="text" name="road" required
                            value="<?php echo $isUpdate ? $existingData['road_no'] : ''; ?>"><br>
                    </label>
                    <label>Zip Code<br>
                        <input type="text" name="zip-code" required
                            value="<?php echo $isUpdate ? $existingData['zip_code'] : ''; ?>"><br>
                    </label>
                    <div id="zipError" class="error"></div><br>
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
                        echo "<option value='' disabled " . (!$isUpdate ? 'selected' : '') . ">Please choose an area</option>";

                        foreach ($rows as $row) {
                            $area_id = $row["area_id"];
                            $a_name = $row["name"];
                            $city = $row["city"];
                            $district = $row["district"];
                            $selected = $isUpdate && $existingData['area_id'] == $area_id ? 'selected' : '';
                            echo "<option value=$area_id $selected>$a_name, $city, $district</option>";
                        }
                        echo "</select>";
                        ?>
                    </label><br><br>

                    <?php if ($isUpdate): ?>
                        <div>Current image:</div>
                        <?php
                        $jsonFile = 'uploads/restaurant/' . $existingData['restaurant_id'] . '.json';
                        if (file_exists($jsonFile)) {
                            $jsonData = json_decode(file_get_contents($jsonFile), true);
                            echo "<img src='data:{$jsonData['mime_type']};base64,{$jsonData['image_data']}' 
                                  alt='{$existingData['name']}' style='max-width: 200px; margin: 10px 0;'>";
                        }
                        ?>
                    <?php endif; ?>
                    <img id="imagePreview" alt="Preview"><br>

                    <label>Restaurant Image:<br>
                        <input type="file" name="restaurant_image" accept="image/*"
                            <?php echo !$isUpdate ? 'required' : ''; ?>
                            onchange="previewImage(this)">
                    </label><br>
                    
                    <input class="red-button" type="submit" name="submit"
                        value="<?php echo $isUpdate ? 'Update Restaurant' : 'Add Branch'; ?>"><br>
                </form>
            </div>
        </div>
    </main>

    <script src="js/add-restaurant-validation.js"></script>
    <script src="js/preview.js"></script>
</body>

</html>