<?php
$host = "localhost"; // Database host
$db_user = "root"; // Database username
$db_pass = ""; // Database password
$db_name = "user_management"; // Database name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        header("Location: showcase.html"); // Redirect to index.html
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>
