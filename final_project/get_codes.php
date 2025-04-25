
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coin_redeem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT code, coins, redeemed FROM coin_codes";
$result = $conn->query($sql);

$codes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
}

echo json_encode($codes);
$conn->close();
?>

