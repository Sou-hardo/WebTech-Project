<?php
global $conn;
require_once "dbconnect.php";
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
    include "header.php";
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

                    echo "<div class='container-item'>
                                <img src='images/offer-combo-food-based-on-the-season.jpg' alt='donut'>
                                <div style='margin: 10px;' class='title'>$title</div>";
                    echo "<div style='margin: 10px;'>Expiry Date: $expiry_date</div>";
                    echo (is_null($promo_code)) ?  "<br>" : "<div>PROMO_CODE: $promo_code</div>";
                    echo "<div style='margin: 10px;'>
                                    $description 
                                </div>
                            </div>";
                }
            } catch (mysqli_sql_exception $ex) {
                exit("Error: " . $ex->getMessage());
            }
            ?>

        </div>
    </main>
</body>

</html>