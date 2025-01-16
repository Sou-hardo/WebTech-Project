<?php
require "dbconnect.php";

// Fetch all admins
$admins = mysqli_query($conn, "SELECT * FROM users WHERE admin_flag = 1");

// Add or remove admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_admin'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $name = $_POST['name'];
        $email = $_POST['email'];
        
        // Add a default address for the admin
        $default_flat = '000';
        $default_house = 000;
        $default_road = '000';
        $default_zip_code = '0000';
        $default_area_id = 1; // Assuming 1 is a valid area_id
        $sql_address = "INSERT INTO address (flat_no, house_no, road_no, zip_code, area_id) 
                        VALUES ('$default_flat', $default_house, '$default_road', '$default_zip_code', $default_area_id)";
        mysqli_query($conn, $sql_address);
        
        // Get the address_id of the newly inserted address
        $address_id = mysqli_insert_id($conn);
        
        $sql = "INSERT INTO users (username, user_password, name, email, admin_flag, address_id) 
                VALUES ('$username', '$password', '$name', '$email', 1, $address_id)";
        mysqli_query($conn, $sql);
        header("Location: add-admin.php");
    } elseif (isset($_POST['remove_admin'])) {
        $username = $_POST['username'];
        
        // Get the address_id of the admin to be removed
        $query = "SELECT address_id FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $address_id = $row['address_id'];
        
        // Remove the admin
        $sql = "DELETE FROM users WHERE username = '$username'";
        mysqli_query($conn, $sql);
        
        // Remove the associated address
        $sql_address = "DELETE FROM address WHERE address_id = $address_id";
        mysqli_query($conn, $sql_address);
        
        header("Location: add-admin.php");
    }
    header("Location: add-admin.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins</title>
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
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid black;
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
            <h3>All Admins</h3>
            <table cellpadding="10" cellspacing="0">
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($admins)) { ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        
        <div class="form-container">
            <div class="input-border">
                <h3>Add Admin</h3>
                <form method="post" class="form-style">
                    <div class="form-group">
                        <label for="username_add">Username:</label>
                        <input type="text" id="username_add" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="password_add">Password:</label>
                        <input type="password" id="password_add" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="name_add">Name:</label>
                        <input type="text" id="name_add" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email_add">Email:</label>
                        <input type="email" id="email_add" name="email" placeholder="Email" required>
                    </div>
                    <button type="submit" name="add_admin">Add Admin</button>
                </form>
            </div>
            <div class="input-border">
                <h3>Remove Admin</h3>
                <form method="post" class="form-style">
                    <div class="form-group">
                        <label for="username_remove">Username:</label>
                        <input type="text" id="username_remove" name="username" placeholder="Username" required>
                    </div>
                    <button type="submit" name="remove_admin">Remove Admin</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>