<?php
require "dbconnect.php";

// Check if user is logged in and is admin
if (!isset($_SESSION["user_id"])) {
    header("Location: admin.php");
    exit();
}

$query = "SELECT admin_flag FROM users WHERE user_id = " . $_SESSION["user_id"];
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);
if (!$row || $row['admin_flag'] != 1) {
    header("Location: admin.php");
    exit();
}

// Add search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
if (isset($search) && !empty($search)) {
    $query = "SELECT * FROM users 
              WHERE admin_flag = 0 
              AND (name LIKE '%$search%' OR username LIKE '%$search%' 
                   OR email LIKE '%$search%') 
              ORDER BY user_id DESC LIMIT 12";
} else {
    $query = "SELECT * FROM users 
              WHERE admin_flag = 0 
              ORDER BY user_id DESC LIMIT 12";
}
$users = mysqli_query($conn, $query);

// Get total count for displaying info
$total_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE admin_flag = 0"))['count'];

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_user'])) {
    $username = $_POST['username'];

    // Get the address_id of the user to be removed
    $query = "SELECT address_id FROM users WHERE username = '$username' AND admin_flag = 0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $address_id = $row['address_id'];

        // Remove the user
        mysqli_query($conn, "DELETE FROM users WHERE username = '$username' AND admin_flag = 0");

        // Remove the associated address
        mysqli_query($conn, "DELETE FROM address WHERE address_id = $address_id");

        header("Location: admin-manage-users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira%20Code">
    <style>
        .main-container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            margin-left: 250px;
        }

        .table-container {
            width: 80%;
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

        th,
        td {
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
    ?>
    <div class="main-container">
        <div class="table-container">
            <h3>All Users</h3>
            <!-- Add search form -->
            <form method="GET" class="search-form" style="margin-bottom: 20px;">
                <div class="form-group" style="justify-content: flex-start; gap: 10px;">
                    <input type="text" name="search" placeholder="Search by name, username or email"
                        value="<?php echo htmlspecialchars($search); ?>" style="width: 900px;">
                    <button type="submit">Search</button>
                    <?php if ($search): ?>
                        <a href="admin-manage-users.php" class="button" style="padding: 5px 10px; text-decoration: none; background: #666;">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <p style="margin-bottom: 10px; color: #666;">
                Showing <?php echo mysqli_num_rows($users); ?> most recent users
                (Total users: <?php echo $total_count; ?>)
            </p>

            <table cellpadding="10" cellspacing="0">
                <tr>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($users)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['customer_flag'] ? 'Customer' : 'Staff'; ?></td>
                        <td>
                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                                <button type="submit" name="remove_user" style="background-color: #ff4444;">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>

</html>