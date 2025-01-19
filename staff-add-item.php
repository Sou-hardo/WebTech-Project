<?php
global $conn;
require_once "dbconnect.php";

// Fetch existing item data if update was clicked
$isUpdate = false;
$existingData = null;
if (isset($_GET["update"]) && isset($_GET["food_id"])) {
    $isUpdate = true;
    $food_id = $_GET["food_id"];
    $query = "SELECT * FROM menu_item WHERE food_id = $food_id";
    $result = mysqli_query($conn, $query);
    $existingData = mysqli_fetch_assoc($result);
}

if (isset($_POST["submit"])) {
    if (isset($_POST["restaurant_id"]) and $_POST["restaurant_id"] != '') {
        $restaurant_id = $_POST["restaurant_id"];
        $name = $_POST["name"];
        if (isset($_POST["category"]) and $_POST["category"] != '')
            $category = $_POST["category"];
        else
            $category = NULL;
        $price = $_POST["price"];

        if (isset($_POST["food_id"])) {  // Update existing item
            $food_id = $_POST["food_id"];
            $query = "UPDATE menu_item 
                     SET name='$name', category='$category', 
                         price=$price, restaurant_id=$restaurant_id 
                     WHERE food_id=$food_id";
        } else {  // Insert new item
            $query = "INSERT INTO menu_item(name, category, price, restaurant_id)
                     VALUES ('$name', '$category', $price, $restaurant_id)";
        }
        
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            // Handle image upload
            if (isset($_FILES['item_image']) && $_FILES['item_image']['size'] > 0) {
                $file = $_FILES['item_image'];
                $food_id = isset($_POST["food_id"]) ? $_POST["food_id"] : mysqli_insert_id($conn);
                $fileName = $food_id . '.json';
                $uploadPath = 'uploads/menu/';

                // Process image and create JSON
                $imageData = [
                    'food_id' => $food_id,
                    'name' => $name,
                    'category' => $category,
                    'restaurant_id' => $restaurant_id,
                    'image_data' => base64_encode(file_get_contents($file['tmp_name'])),
                    'mime_type' => $file['type']
                ];

                // Save JSON file
                file_put_contents($uploadPath . $fileName, json_encode($imageData));
            }
            
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
    <style>
        #imagePreview {
            max-width: 200px;
            margin: 10px 0;
            display: none;
        }
        input[type="text"], input[type="number"], input[type="date"], input[type="time"], input[type="file"], textarea {
            width: 350px;
        }
        .error {
            color: red;
            font-size: 0.9em;
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
            <h2><?php echo $isUpdate ? 'Update Item' : 'Add New Item'; ?></h2>
            <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <?php if ($isUpdate): ?>
                    <input type="hidden" name="food_id" value="<?php echo $existingData['food_id']; ?>">
                <?php endif; ?>
                
                <label>Name:<br>
                    <input type="text" name="name" required 
                           value="<?php echo $isUpdate ? $existingData['name'] : ''; ?>">
                </label>
                <div id="nameError" class="error"></div><br>
                <label>Category:<br>
                    <input type="text" name="category" 
                           value="<?php echo $isUpdate ? $existingData['category'] : ''; ?>">
                </label>
                <div id="categoryError" class="error"></div><br>
                <label>Price:<br>
                    <input type="number" name="price" required 
                           value="<?php echo $isUpdate ? $existingData['price'] : ''; ?>">
                </label><br>
                <label>Restaurant:<br>
                    <select name="restaurant_id">
                        <option value="" disabled <?php echo !$isUpdate ? 'selected' : ''; ?>>
                            Please choose an option
                        </option>
                        <?php
                        $query2 = "SELECT restaurant_id, name FROM restaurant";
                        $result = mysqli_query($conn, $query2);
                        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        foreach ($rows as $row) {
                            $restaurant_id = $row["restaurant_id"];
                            $r_name = $row["name"];
                            $selected = $isUpdate && $existingData['restaurant_id'] == $restaurant_id ? 'selected' : '';
                            echo "<option value=$restaurant_id $selected>$r_name</option>";
                        }
                        ?>
                    </select>
                </label><br><br>
                
                <?php if ($isUpdate): ?>
                    <div>Current image:</div>
                    <?php
                    $jsonFile = 'uploads/menu/' . $existingData['food_id'] . '.json';
                    if (file_exists($jsonFile)) {
                        $jsonData = json_decode(file_get_contents($jsonFile), true);
                        echo "<img src='data:{$jsonData['mime_type']};base64,{$jsonData['image_data']}' 
                              alt='{$existingData['name']}' style='max-width: 200px; margin: 10px 0;'>";
                    }
                    ?>
                <?php endif; ?>
                <img id="imagePreview" alt="Preview"><br>
                <label>Food Image:<br>
                    <input type="file" name="item_image" accept="image/*" 
                           <?php echo !$isUpdate ? 'required' : ''; ?> 
                           onchange="previewImage(this)">
                </label><br>
                <input type="submit" name="submit" 
                       value="<?php echo $isUpdate ? 'Update Item' : 'Add Item'; ?>">
            </form>
            </div>
        </div>
    </main>

    <script src="js/add-item-validation.js"></script>
    <script src="js/preview.js"></script>
</body>

</html>