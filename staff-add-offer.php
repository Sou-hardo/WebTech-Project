<?php
global $conn;
require_once "dbconnect.php";

// Fetch existing offer data if update was clicked
$isUpdate = false;
$existingData = null;
if (isset($_GET["update"])) {
    $isUpdate = true;
    if (isset($_GET["voucher_id"])) {
        $voucher_id = $_GET["voucher_id"];
        $query = "SELECT *, voucher_id as id FROM voucher WHERE voucher_id = $voucher_id";
    } elseif (isset($_GET["discount_id"])) {
        $discount_id = $_GET["discount_id"];
        $query = "SELECT *, discount_id as id FROM discount WHERE discount_id = $discount_id";
    }
    $result = mysqli_query($conn, $query);
    $existingData = mysqli_fetch_assoc($result);
}

if (isset($_POST["submit"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $percentage = $_POST["percentage"];
    $start_date = $_POST["start_date"];
    $expiry_date = $_POST["expiry_date"];

    try {
        if (isset($_POST["voucher_id"])) { // Update existing voucher
            $voucher_id = $_POST["voucher_id"];
            $promo_code = $_POST["promo_code"];
            $query = "UPDATE voucher SET 
                     title='$title', description='$description', 
                     promo_code='$promo_code', percentage=$percentage,
                     start_date='$start_date', expiry_date='$expiry_date'
                     WHERE voucher_id=$voucher_id";
        } elseif (isset($_POST["discount_id"])) { // Update existing discount
            $discount_id = $_POST["discount_id"];
            $query = "UPDATE discount SET 
                     title='$title', description='$description',
                     percentage=$percentage, start_date='$start_date',
                     expiry_date='$expiry_date'
                     WHERE discount_id=$discount_id";
        } else { // Insert new voucher
            $promo_code = $_POST["promo_code"];
            $query = "INSERT INTO voucher (title, description, promo_code, percentage, start_date, expiry_date) 
                      VALUES ('$title', '$description', '$promo_code', $percentage, '$start_date', '$expiry_date')";
        }

        $result = mysqli_query($conn, $query);

        if ($result) {
            // Handle image upload
            if (isset($_FILES['offer_image']) && $_FILES['offer_image']['size'] > 0) {
                $file = $_FILES['offer_image'];
                $id = isset($_POST["voucher_id"]) ? $_POST["voucher_id"] : (isset($_POST["discount_id"]) ? $_POST["discount_id"] : mysqli_insert_id($conn));
                $fileName = $id . '.json';
                $uploadPath = 'uploads/offers/';

                // Process image and create JSON
                $imageData = [
                    'offer_id' => $id,
                    'title' => $title,
                    'image_data' => base64_encode(file_get_contents($file['tmp_name'])),
                    'mime_type' => $file['type']
                ];

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
    <title>Offers</title>
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
                <h2><?php echo $isUpdate ? 'Update Offer' : 'Add New Offer'; ?></h2>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($isUpdate): ?>
                        <?php if (isset($_GET["voucher_id"])): ?>
                            <input type="hidden" name="voucher_id" value="<?php echo $existingData['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="discount_id" value="<?php echo $existingData['id']; ?>">
                        <?php endif; ?>
                    <?php endif; ?>

                    <label>Title:<br>
                        <input type="text" name="title" required maxlength="30"
                            value="<?php echo $isUpdate ? $existingData['title'] : ''; ?>">
                    </label><br>
                    <label>Description:<br>
                        <textarea name="description" maxlength="255" rows="4"><?php
                                                                                echo $isUpdate ? $existingData['description'] : '';
                                                                                ?></textarea>
                    </label><br>
                    <?php if (!isset($_GET["discount_id"])): ?>
                        <label>Promo Code:<br>
                            <input type="text" name="promo_code" required maxlength="20"
                                value="<?php echo $isUpdate ? $existingData['promo_code'] : ''; ?>">
                        </label><br>
                    <?php endif; ?>
                    <label>Discount Percentage:<br>
                        <input type="number" name="percentage" required min="0" max="100" step="0.01"
                            value="<?php echo $isUpdate ? $existingData['percentage'] : ''; ?>">
                    </label><br>
                    <label>Start Date:<br>
                        <input type="date" name="start_date" required
                            value="<?php echo $isUpdate ? $existingData['start_date'] : ''; ?>">
                    </label><br>
                    <label>Expiry Date:<br>
                        <input type="date" name="expiry_date" required
                            value="<?php echo $isUpdate ? $existingData['expiry_date'] : ''; ?>">
                    </label><br><br>
                    
                    <?php if ($isUpdate): ?>
                        <div>Current image:</div>
                        <?php
                        $jsonFile = 'uploads/offers/' . $existingData['id'] . '.json';
                        if (file_exists($jsonFile)) {
                            $jsonData = json_decode(file_get_contents($jsonFile), true);
                            echo "<img src='data:{$jsonData['mime_type']};base64,{$jsonData['image_data']}' 
                                  alt='{$existingData['title']}' style='max-width: 200px; margin: 10px 0;'>";
                        }
                        ?>
                    <?php endif; ?>
                    <img id="imagePreview" alt="Preview"><br>
                    <label>Offer Image:<br>
                        <input type="file" name="offer_image" accept="image/*"
                            <?php echo !$isUpdate ? 'required' : ''; ?>
                            onchange="previewImage(this)">
                    </label><br>
                    
                    <input type="submit" name="submit"
                        value="<?php echo $isUpdate ? 'Update Offer' : 'Add Offer'; ?>">
                </form>
            </div>
            <?php
            try {
                $query =
                    "SELECT voucher_id AS id, title, description, promo_code, percentage,
                                start_date, expiry_date
                            FROM voucher
                            UNION
                            (SELECT discount_id AS id, title, description, NULL AS promo_code, percentage,
                                start_date, expiry_date
                            FROM discount)
                            ORDER BY start_date DESC
                            ";
                $result = mysqli_query($conn, $query);
                $offers = mysqli_fetch_all($result, MYSQLI_ASSOC);
                foreach ($offers as $row) {
                    $id = $row["id"];
                    $title = $row["title"];
                    $description = $row["description"];
                    $promo_code = $row["promo_code"];
                    $percentage = $row["percentage"];
                    $start_date = $row["start_date"];
                    $expiry_date = $row["expiry_date"];
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>
        </div>
    </main>

    <script src="js/preview.js"></script>
</body>

</html>