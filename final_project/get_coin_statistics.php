
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coin_redeem";
try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get total coins created
    $stmtCreated = $pdo->query("SELECT SUM(coins) AS totalCreated FROM codes"); // Adjust table name and column as needed
    $totalCreatedResult = $stmtCreated->fetch(PDO::FETCH_ASSOC);
    $totalCreated = $totalCreatedResult['totalCreated'];

    // Query to get total coins redeemed
    $stmtRedeemed = $pdo->query("SELECT SUM(coins) AS totalRedeemed FROM codes WHERE redeemed = 1"); // Adjust table name and column as needed
    $totalRedeemedResult = $stmtRedeemed->fetch(PDO::FETCH_ASSOC);
    $totalRedeemed = $totalRedeemedResult['totalRedeemed'];

    // Prepare response
    $response = [
        'totalCreated' => $totalCreated ? $totalCreated : 0,
        'totalRedeemed' => $totalRedeemed ? $totalRedeemed : 0
    ];

    // Set response header to JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Handle connection error
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>

