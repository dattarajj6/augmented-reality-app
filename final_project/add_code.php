

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coin_redeem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $coins = $_POST['coins'];

    // Check for existing code
    $stmt = $conn->prepare("SELECT * FROM coin_codes WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This code already exists.']);
        $stmt->close();
        exit;
    }

    // Insert new code
    $stmt = $conn->prepare("INSERT INTO coin_codes (code, coins) VALUES (?, ?)");
    $stmt->bind_param("si", $code, $coins);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Code added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>

