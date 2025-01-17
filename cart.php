<?php
global $conn;
require_once "dbconnect.php";

if (empty($_SESSION["user_id"])) {
    header("Location: sign-in.php");
};
if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    $user_id = 1;
$user_id = $_SESSION["user_id"];

if (isset($_GET["add-item"])) {
    try {
        $food_id = $_GET["food_id"];
        $query = "SELECT COUNT(*) FROM cart 
                    WHERE user_id = $user_id
                    AND food_id = $food_id";
        $result = mysqli_query($conn, $query);
        $in_cart = mysqli_fetch_array($result)[0] != 0;
        if (!$in_cart) {
            $query = "INSERT INTO cart(user_id, food_id, quantity) 
                        VALUES ($user_id, $food_id, 1)";
            mysqli_query($conn, $query);
        }
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
}

if (isset($_GET["voucher_id"]) and $_GET["voucher_id"] == '')
    unset($_GET["voucher_id"]);

if (isset($_GET["remove"])) {
    $food_id = $_GET["food_id"];
    $query = "DELETE FROM cart 
                WHERE user_id = $user_id AND food_id = $food_id";
    try {
        mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
}
if (isset($_GET["update"])) {
    if (isset($_GET["voucher_id"])) {
        $voucher_id = $_GET["voucher_id"];
    } elseif (isset($_GET["promo_code"])) {
        $promo_code = $_GET["promo_code"];
        $query = "SELECT * FROM voucher WHERE promo_code = '$promo_code'";
        $result = mysqli_query($conn, $query);
        if (!empty($row = mysqli_fetch_assoc($result))) {
            $voucher_id = $row["voucher_id"];
        }
    }

    $food_id = $_GET["food_id"];
    $quantity = $_GET["quantity"];
    $query = "UPDATE cart SET quantity = $quantity
                WHERE user_id = $user_id AND food_id = $food_id";
    try {
        mysqli_query($conn, $query);
    } catch (mysqli_sql_exception $ex) {
        exit("Error: " . $ex->getMessage());
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <a href="menu-items-all.php" class="inline-link"><- Continue Shopping</a><br>
                <h2>Shopping cart</h2>
                <p>
                    You have
                    <?php
                    try {
                        $query = "SELECT COUNT(*) FROM cart WHERE user_id = $user_id";
                        $result = mysqli_query($conn, $query);
                        echo $number_of_items = mysqli_fetch_array($result)[0];
                    } catch (mysqli_sql_exception $ex) {
                        exit("Error: " . $ex->getMessage());
                    }
                    ?>
                    item(s) in your cart<br>
                </p>
                <div class="vertical-container">
                    <?php
                    $query1 =
                        "WITH
                            T1 AS (
                                SELECT M.food_id, M.name, M.price, R.name AS r_name, C.quantity
                                FROM menu_item M, restaurant R, cart C
                                WHERE user_id = $user_id
                                    AND M.restaurant_id = R.restaurant_id
                                    AND C.food_id = M.food_id
                            ),
                            T2 AS (
                                SELECT DI.food_id, MAX(D.percentage) AS percentage FROM discount D
                                JOIN discounted_items DI
                                ON D.discount_id = DI.discount_id
                                WHERE expiry_date > NOW()
                                GROUP BY DI.food_id
                            ),
                            T3 AS (
                                SELECT T1.food_id, T1.name, T1.r_name,
                                    T1.price, T1.quantity, T2.percentage
                                FROM T1 LEFT JOIN T2
                                ON T1.food_id = T2.food_id
                            ),
                            T4 AS (
                            SELECT food_id, name, r_name, quantity,
                            CASE
                                WHEN percentage IS NULL THEN price
                                ELSE ROUND(price - price * percentage / 100, 2)
                            END AS price
                            FROM T3
                            )
                        SELECT * FROM T4
                        ";
                    $result = mysqli_query($conn, $query1);
                    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    $total_price = 0;
                    foreach ($items as $row) {
                        $food_id = $row["food_id"];
                        $name = $row["name"];
                        $r_name = $row["r_name"];
                        $quantity = $row["quantity"];
                        $price = $row["price"];
                        $total_price += $price * $quantity;

                        echo "<div class='vertical-container-item'>";
                        
                        // Read and display image from JSON file
                        $jsonFile = 'uploads/menu/' . $food_id . '.json';
                        if (file_exists($jsonFile)) {
                            $jsonData = json_decode(file_get_contents($jsonFile), true);
                            $imageData = $jsonData['image_data'];
                            $mimeType = $jsonData['mime_type'];
                            echo "<img src='data:$mimeType;base64,$imageData' alt='$name'>";
                        } else {
                            echo "<img src='images/donut.png' alt='food'>";
                        }

                        echo "<div class='vertical-container-inner-1'>
                                <div class='title'>$name</div>
                                <div>$r_name</div>
                            </div>
                            <div class='vertical-container-inner-2'>
                                <form action='cart.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>";
                        if (isset($voucher_id)) {
                            echo "<input type='hidden' name='voucher_id' value='$voucher_id'>";
                        } else if (isset($promo_code)) {
                            echo "<input type='hidden' name='promo_code' value='$promo_code'>";
                        }
                        echo "<input type='number' name='quantity' min='1' value='$quantity'>
                                    <input type='submit' name='update' value='Update' class='red-button'>
                                </form>
                                
                            </div>
                            <div class='vertical-container-inner-3'>
                                <span>" . ($price * $quantity) . "</span>
                            </div>                            
                            <div class='vertical-container-inner-4'>
                                <form method='get' action='cart.php'>
                                    <input type='hidden' name='food_id' value='$food_id'>
                                    <input type='submit' name='remove' value='Remove' class='red-button'>
                                </form>
                            </div>                            
                        </div>";
                        //                        echo $name, $r_name, $quantity, $price, "<br>";
                    }
                    ?>
                </div>
                <div class="container" style="width: 35%; flex-wrap: wrap">
                    <form action="cart.php">
                        <?php
                        if (isset($_GET["voucher"]) and $_GET["voucher"] == "Remove") {
                            unset($_GET["promo_code"]);
                            unset($_GET["voucher_id"]);
                        }
                        if (isset($_GET["promo_code"])) {
                            $promo_code = $_GET["promo_code"];
                            $query = "SELECT * FROM voucher WHERE promo_code = '$promo_code'";
                            $result = mysqli_query($conn, $query);
                            if (!empty($row = mysqli_fetch_assoc($result))) {
                                $voucher_id = $row["voucher_id"];
                                echo "<label>Enter voucher
                                    <input type='text' name='promo_code' placeholder='PROMO CODE' value='$promo_code'>
                                </label>";
                            } else {
                                echo "<label>Enter voucher
                                    <input type='text' name='promo_code' placeholder='PROMO CODE'>
                                </label>";
                            }
                        } elseif (isset($_GET["voucher_id"])) {
                            $voucher_id = $_GET["voucher_id"];
                            $query = "SELECT promo_code FROM voucher WHERE voucher_id = $voucher_id";
                            $result = mysqli_query($conn, $query);
                            $promo_code = (empty($row = mysqli_fetch_array($result))) ? '' : $row[0];
                            echo "<label>Enter voucher
                                <input type='text' name='promo_code' placeholder='PROMO CODE' value='$promo_code'>
                            </label>";
                        } else {
                            echo "<label style='color: white;'>Enter voucher
                                <input type='text' name='promo_code' placeholder='PROMO CODE'>
                            </label>";
                        }
                        ?>
                        <?php
                        if (isset($voucher_id)) {
                            echo "<input type='submit' class='red-button' name='voucher' value='Remove'>";
                        } else {
                            echo "<input type='submit' class='red-button' name='voucher' value='Apply'>";
                        }
                        ?>
                    </form>
                    <div style="color: white;">
                    <form action="order-confirmation.php">
                        <?php
                        if (isset($voucher_id)) {
                            echo "<input type='hidden' name='voucher_id' value='$voucher_id'>";
                            $query = "SELECT percentage FROM voucher WHERE voucher_id = $voucher_id";
                            $result = mysqli_query($conn, $query);
                            $percentage = mysqli_fetch_array($result)[0];
                            $total_price -= round($total_price * $percentage / 100, 2);
                        }
                        echo "Subtotal: $total_price<br>
                                Shipping: 60<br>
                                Total: " . $total_price + 60 . "<br>";
                        if ($number_of_items > 0)
                            echo "<input type='submit' name='checkout' value='Checkout' class='red-button'>";
                        else
                            echo "<input type='submit' name='checkout' value='Checkout' class='red-button' disabled>";
                        ?>
                    </form>
                    </div>
                </div>
    </main>
</body>

</html>