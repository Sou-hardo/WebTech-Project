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
    <title>Rate</title>
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
            echo "<h2 style='margin-left: 20px;''>Your payment is complete</h2>";
        } else {
            echo "<h2>You have to pay BDT $due_amount</h2>";

            if (isset($_GET["submit"]) and $_GET["submit"] == "Make Payment") {
                if (isset($_GET["payment_method"]) and $_GET["payment_method"] != '') {
                    $payment_method = $_GET["payment_method"];
                    $transaction_src = $_GET["mobile-number"];
                    $query = "INSERT INTO payment(user_id, payment_method, transaction_source, amount) 
                                VALUES ($user_id, '$payment_method', '$transaction_src', $due_amount)";
                    mysqli_query($conn, $query);
                    $query = "UPDATE users SET due_amount = 0 WHERE user_id = $user_id";
                    mysqli_query($conn, $query);
                }
            }


            echo '
                <div class="container centered">
                    <div>
                        <form action="mobile-banking.php">
                            <label>Type:
                                <select name="payment_method">
                                    <option value="" disabled selected>Please choose an option</option>
                                    <option value="bkash">Bkash</option>
                                    <option value="rocket">Rocket</option>
                                    <option value="nagad">Nagad</option>
                                </select>
                            </label><br><br>
                            <label>Mobile Number:<br>
                                <input type="text" name="mobile-number" placeholder="Enter Mobile Number" required>
                            </label><br>
                            <label>PIN:<br>
                                <input type="password" name="pin" placeholder="Enter PIN" required>
                            </label><br>
                            <input class="red-button" type="submit" name="submit" value="Make Payment">
                        </form>
                    </div>
                </div>';
        }
        ?>
    </main>
</body>

</html>