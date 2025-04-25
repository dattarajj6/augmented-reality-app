
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coin_redeem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];

    // Check if the code exists and is not redeemed
    $stmt = $conn->prepare("SELECT coins, redeemed FROM coin_codes WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['redeemed'] == 0) {
            // Mark the code as redeemed
            $stmt = $conn->prepare("UPDATE coin_codes SET redeemed = 1 WHERE code = ?");
            $stmt->bind_param("s", $code);
            $stmt->execute();

            echo json_encode(['success' => true, 'coins' => $row['coins']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'This code has already been redeemed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid code.']);
    }

    $stmt->close();
}
$conn->close();
?>

