<?php
require "dbconnect.php";

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"])) {
    header("Location: admin.php");
    exit();
} 
//else header("Location: admin.php");

$query = "SELECT admin_flag FROM users WHERE user_id = " . $_SESSION["user_id"];
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);
if (!$row || $row['admin_flag'] != 1) {
    header("Location: admin.php");
    exit();
}

// Fetch all areas
$areas = mysqli_query($conn, "SELECT * FROM area");

// Add, modify, or remove area
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_area'])) {
        $name = $_POST['name'];
        $city = $_POST['city'];
        $district = $_POST['district'];
        $sql = "INSERT INTO area (name, city, district) VALUES ('$name', '$city', '$district')";
        mysqli_query($conn, $sql);
    } elseif (isset($_POST['modify_area'])) {
        $area_id = $_POST['area_id'];
        $name = $_POST['name'];
        $city = $_POST['city'];
        $district = $_POST['district'];
        $sql = "UPDATE area SET name='$name', city='$city', district='$district' WHERE area_id='$area_id'";
        mysqli_query($conn, $sql);
    } elseif (isset($_POST['remove_area'])) {
        $area_id = $_POST['area_id'];
        $sql = "DELETE FROM area WHERE area_id='$area_id'";
        mysqli_query($conn, $sql);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open New Location of Operation</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .main-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .table-container {
            width: 60%;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 35%;
        }
        .form-style {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .input-border {
            border: 2px solid black;
            padding: 20px;
        }
        label {
            font-weight: bold;
            width: 40%;
        }
        input {
            padding: 2px;
            font-size: 16px;
            width: 55%;
        }
        button {
            padding: 5px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        h3 {
            text-align: center;
            padding: 10px;
            color: black;
        }
    </style>
</head>

<body>
<?php
    include "admin-header.php";
    //include "footer.php";
    ?>    
    <div class="main-container">
        <div class="table-container">
            <h3>All Areas</h3>
            <table border="1" cellpadding="10" cellspacing="0">
                <tr>
                    <th>Area ID</th>
                    <th>Area Name</th>
                    <th>City</th>
                    <th>District</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($areas)) { ?>
                <tr>
                    <td><?php echo $row['area_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['city']; ?></td>
                    <td><?php echo $row['district']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        
        <div class="form-container">
            <div class="input-border">
                <h3>Modify Existing Area</h3>
                <form method="post" class="form-style">
                    <div class="form-group">
                        <label for="area_id_modify">Area ID:</label>
                        <input type="number" id="area_id_modify" name="area_id" placeholder="Area ID" required>
                    </div>
                    <div class="form-group">
                        <label for="name_modify">Area Name:</label>
                        <input type="text" id="name_modify" name="name" placeholder="Area Name" required>
                    </div>
                    <div class="form-group">
                        <label for="city_modify">City:</label>
                        <input type="text" id="city_modify" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group">
                        <label for="district_modify">District:</label>
                        <input type="text" id="district_modify" name="district" placeholder="District" required>
                    </div>
                    <button type="submit" name="modify_area">Modify Area</button>
                </form>
            </div>
            <div class="input-border">
                <h3>Add New Area</h3>
                <form method="post" class="form-style">
                    <div class="form-group">
                        <label for="name">Area Name:</label>
                        <input type="text" id="name" name="name" placeholder="Area Name" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" placeholder="City" required>
                    </div>
                    <div class="form-group">
                        <label for="district">District:</label>
                        <input type="text" id="district" name="district" placeholder="District" required>
                    </div>
                    <button type="submit" name="add_area">Add Area</button>
                </form>
                
                <h3>Remove Area</h3>
                <form method="post" class="form-style">
                    <div class="form-group">
                        <label for="area_id_remove">Area ID:</label>
                        <input type="number" id="area_id_remove" name="area_id" placeholder="Area ID" required>
                    </div>
                    <button type="submit" name="remove_area">Remove Area</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>