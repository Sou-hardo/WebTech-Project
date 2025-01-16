<?php
global $conn;
require_once "dbconnect.php";

if (isset($_GET["delete"])) {
    if (isset($_GET["voucher_id"])) {
        $voucher_id = $_GET["voucher_id"];
        $query = "DELETE FROM voucher WHERE voucher_id = $voucher_id";
        mysqli_query($conn, $query);
    } elseif (isset($_GET["discount_id"])) {
        $discount_id = $_GET["discount_id"];
        $query = "DELETE FROM discount WHERE discount_id = $discount_id";
        mysqli_query($conn, $query);
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "staff-header.php";
    ?>
    <main>
        <h2>Here are our latest deals</h2>
        <div class="container">
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

                    echo "<div class='container-item'>";
                    
                    // Read and display image from JSON file
                    $jsonFile = 'uploads/offers/' . $id . '.json';
                    if (file_exists($jsonFile)) {
                        $jsonData = json_decode(file_get_contents($jsonFile), true);
                        $imageData = $jsonData['image_data'];
                        $mimeType = $jsonData['mime_type'];
                        echo "<img src='data:$mimeType;base64,$imageData' alt='$title'>";
                    } else {
                        echo "<img src='images/donut.png' alt='offer'>";
                    }

                    echo "<div style='margin: 10px;' class='title'>$title</div>";
                    echo "<div style='margin: 10px;'>Expiry Date: $expiry_date</div>";
                    echo (is_null($promo_code)) ?  "<br>" : "<div>PROMO_CODE: $promo_code</div>";
                    echo "<div style='margin: 10px;'>$description</div>";
                    echo "<div class='float-right'>
                                <form class='inline-div' method='get' action='staff-add-offer.php'>";
                    if (is_null($promo_code))
                        echo "<input type='hidden' name='discount_id' value='$id'>";
                    else
                        echo "<input type='hidden' name='voucher_id' value='$id'>";
                    echo "<input type='submit' name='update' value='update' class='red-button'>
                                </form>
                                <form class='inline-div' method='get' action='staff-offers.php'>";
                    if (is_null($promo_code))
                        echo "<input type='hidden' name='discount_id' value='$id'>";
                    else
                        echo "<input type='hidden' name='voucher_id' value='$id'>";
                    echo "<input type='submit' name='delete' value='delete' class='red-button'>
                                </form>
                            </div>";
                    echo "</div>";
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>

        </div>
    </main>
</body>

</html>