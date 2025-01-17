<?php
require "dbconnect.php";

header('Content-Type: application/json');

if(isset($_POST['field']) && isset($_POST['value'])) {
    $field = mysqli_real_escape_string($conn, $_POST['field']);
    $value = mysqli_real_escape_string($conn, $_POST['value']);
    
    $query = "SELECT COUNT(*) as count FROM users WHERE $field = '$value'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    echo json_encode(['duplicate' => $row['count'] > 0]);
}
?>
