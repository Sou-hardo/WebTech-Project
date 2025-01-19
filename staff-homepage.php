<?php
global $conn;
require "dbconnect.php";

// Check if user is logged in and is staff
if (!isset($_SESSION["user_id"])) {
    header("Location: customer-or-staff.php");
    exit();
}
//  else header("Location: admin.php");

// Set offer as default tab
$activeTab = 'offer';
if (isset($_GET['tab'])) {
    $activeTab = $_GET['tab'];
} elseif (isset($_GET['update'])) {
    if (isset($_GET['food_id'])) {
        $activeTab = 'item';
    } elseif (isset($_GET['restaurant_id'])) {
        $activeTab = 'restaurant';
    } elseif (isset($_GET['voucher_id']) || isset($_GET['discount_id'])) {
        $activeTab = 'offer';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .tab-container {
            margin: 20px 0;
            text-align: center;
        }

        .tab-button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            background: #f0f0f0;
            cursor: pointer;
        }

        .tab-button.active {
            background: #ff4444;
            color: white;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

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
    <?php include "staff-header.php"; ?>
    <main>
        <div class="tab-container">
            <!-- <button class="tab-button <?php echo $activeTab == 'dashboard' ? 'active' : ''; ?>" 
                    onclick="location.href='?tab=dashboard'">Dashboard</button> -->
            <button class="tab-button <?php echo $activeTab == 'offer' ? 'active' : ''; ?>"
                onclick="location.href='?tab=offer'">Add Offer</button>
            <button class="tab-button <?php echo $activeTab == 'item' ? 'active' : ''; ?>"
                onclick="location.href='?tab=item'">Add Item</button>
            <button class="tab-button <?php echo $activeTab == 'restaurant' ? 'active' : ''; ?>"
                onclick="location.href='?tab=restaurant'">Add Restaurant</button>
        </div>

        <!-- Dashboard -->
        <!-- <div class="form-container <?php echo $activeTab == 'dashboard' ? 'active' : ''; ?>">
            <h2>Welcome to Staff Dashboard</h2>
            // Add your existing dashboard content here
        </div> -->

        <!-- Offer Form -->
        <div class="form-container <?php echo $activeTab == 'offer' ? 'active' : ''; ?>">
            <?php include "staff-add-offer.php"; ?>
        </div>

        <!-- Item Form -->
        <div class="form-container <?php echo $activeTab == 'item' ? 'active' : ''; ?>">
            <?php include "staff-add-item.php"; ?>
        </div>

        <!-- Restaurant Form -->
        <div class="form-container <?php echo $activeTab == 'restaurant' ? 'active' : ''; ?>">
            <?php include "staff-add-restaurant.php"; ?>
        </div>
    </main>

    <script src="js/preview.js"></script>
</body>

</html>