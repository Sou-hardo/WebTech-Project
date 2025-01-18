<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    $user_id = 1;
//    if (empty($_GET["restaurant_id"]) and empty($_GET["food_id"])) {
//        exit();
//    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card-Payment</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
</head>

<body>
    <?php
    include "header.php";
    ?>
    <main>
        <?php
        $query = "SELECT due_amount FROM users WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $due_amount = mysqli_fetch_array($result)[0];
        if ($due_amount == 0) {
            echo "<h2 style='margin-left: 20px;'>Your payment is complete</h2>";
        } else {
            echo "<h2>You have to pay BDT $due_amount</h2>";

            if ($_GET["submit"] == "Make Payment") {
                if (isset($_GET["payment_method"]) and $_GET["payment_method"] != '') {
                    $payment_method = $_GET["payment_method"];
                    $transaction_src = $_GET["card-number"];
                    $query = "INSERT INTO payment(user_id, payment_method, transaction_source, amount) 
                                VALUES ($user_id, '$payment_method', '$transaction_src', $due_amount)";
                    mysqli_query($conn, $query);
                    $query = "UPDATE users SET due_amount = 0 WHERE user_id = $user_id";
                    mysqli_query($conn, $query);
                }
            }
        }
            ?>

                <div class="container centered">
                    <div>
                        <form action="card-payment.php">
                            <label>Card Type:
                                <select name="payment_method">
                                    <option value="" disabled selected>Please choose an option</option>
                                    <option value="visa">VISA</option>
                                    <option value="mastercard">MasterCard</option>
                                    <option value="american express">American Express</option>
                                </select>
                            </label><br><br>
                            <label>Card Number:<br>
                                <input type="text" name="card-number" placeholder="Enter Card Number" required>
                            </label><br>
                            <label>Expiry Date:<br>
                                <input type="text" name="expiry-date" placeholder="MM/YY" required>
                            </label><br>
                            <label>CVC/CVV:<br>
                                <input type="password" name="cvc-cvv" placeholder="CVC/CVV" required>
                            </label><br>
                            <label>Card Holder Name:<br>
                                <input type="password" name="holder-name" placeholder="Card Holder Name" required>
                            </label><br>
                            <input class="red-button" type="submit" name="submit" value="Make Payment">
                        </form>
                    </div>
                </div>';
    </main>
</body>

</html>