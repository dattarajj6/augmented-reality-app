
<?php
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "feedback_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO feedback (user_name, message) VALUES (?, ?)");
$user_name = "Anonymous"; // Placeholder for username; you can modify this as needed
$message = $_POST['message']; // Get the feedback message from the form
$stmt->bind_param("ss", $user_name, $message);

// Execute the statement
if ($stmt->execute()) {
    // Redirect back to the settings page with a success message
    header("Location: settings.html?status=success");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>



