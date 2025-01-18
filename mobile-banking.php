<?php
global $conn;
require_once "dbconnect.php";

if (isset($_SESSION["user_id"]))
    $user_id = $_SESSION["user_id"];
else
    header("Location: login.php");
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

            if (isset($_POST["submit"]) and $_POST["submit"] == "Make Payment") {
                if (isset($_POST["payment_method"]) and $_POST["payment_method"] != '') {
                    $payment_method = $_POST["payment_method"];
                    $transaction_src = $_POST["mobile-number"];
                    $query = "INSERT INTO payment(user_id, payment_method, transaction_source, amount) 
                                VALUES ($user_id, '$payment_method', '$transaction_src', $due_amount)";
                    mysqli_query($conn, $query);
                    $query = "UPDATE users SET due_amount = 0 WHERE user_id = $user_id";
                    mysqli_query($conn, $query);
                    
                    // Redirect after successful payment
                    header("Location: mobile-banking.php");
                    exit();
                }
            }

            echo '
                <div class="container centered">
                    <div>
                        <form action="mobile-banking.php" method="POST" onsubmit="return validateForm()">
                            <label>Type:
                                <select name="payment_method">
                                    <option value="" disabled selected>Please choose an option</option>
                                    <option value="bkash">Bkash</option>
                                    <option value="rocket">Rocket</option>
                                    <option value="nagad">Nagad</option>
                                </select>
                            </label><br><br>
                            <label>Mobile Number:<br>
                                <input type="text" name="mobile-number" id="mobile-number" placeholder="Enter Mobile Number" required>
                            </label>
                            <span id="mobile-error" style="color: red; display: none;"></span><br>
                            <label>PIN:<br>
                                <input type="password" name="pin" id="pin" placeholder="Enter PIN" required>
                            </label>
                            <span id="pin-error" style="color: red; display: none;"></span><br>
                            <input class="red-button" type="submit" name="submit" value="Make Payment">
                        </form>
                    </div>
                </div>';
        }
        ?>
    </main>

    <script src="js/payment-validation.js"></script>
</body>
</html>