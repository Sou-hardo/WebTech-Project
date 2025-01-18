<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    $user_id = 1;
$voucher_id = (isset($_GET["voucher_id"])) ? $_GET["voucher_id"] : NULL;
//    if (empty($_GET["restaurant_id"]) and empty($_GET["food_id"])) {
//        exit();
//    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <div class="simple-div">
            <h2>Confirm Order?</h2>
            <div class="simple-flexbox">
                <div>
                    <div class="simple-flexbox">
                        <form action="order-confirmed.php">
                            <input type="hidden" name="voucher_id" value="<?php echo $voucher_id ?>">
                            <input class="red-button medium-sized-button" type="submit" name="submit" value="Yes">
                        </form>
                        <form action="cart.php">
                            <input type="hidden" name="voucher_id" value="<?php echo $voucher_id ?>">
                            <input class="red-button medium-sized-button" type="submit" name="submit" value="No">
                        </form>

                    </div>

                </div>


            </div>
        </div>
    </main>
</body>

</html>