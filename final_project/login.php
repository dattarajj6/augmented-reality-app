<?php
session_start();
$host = "localhost"; // Database host
$db_user = "root"; // Database username
$db_pass = ""; // Database password
$db_name = "user_management"; // Database name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: showcase.html"); // Redirect to index.html
        exit();
    } else {
        echo "Invalid email or password";
    }
}

$conn->close();
?>
