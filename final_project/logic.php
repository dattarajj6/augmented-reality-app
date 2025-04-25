<?php

$host = 'localhost'; // Database host
$db = 'splash_screen'; // Database name
$user = 'root'; // Database username
$password = ''; // Database password

// Create connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getSplashData':
        $result = $conn->query("SELECT * FROM splash_data ORDER BY id DESC LIMIT 1");
        $data = $result->fetch_assoc();
        echo json_encode($data);
        break;

    case 'updateSplash':
        $title = $_POST['title'];
        $bgcolor = $_POST['bgcolor'];
        $fontName = $_POST['fontName'];
        $image = '';

        if ($_POST['imageOption'] == 'upload' && isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name'];
            $uploadPath = "uploads/" . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        } elseif ($_POST['imageOption'] == 'link') {
            $image = $_POST['imageLink'];
        }

        $stmt = $conn->prepare("INSERT INTO splash_data (title, bgcolor, font_name, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $title, $bgcolor, $fontName, $image);
        $stmt->execute();

        // Increment view count when a new splash is added
        $conn->query("UPDATE view_count SET count = count + 1 WHERE id = 1");

        echo json_encode(['message' => 'Splash screen updated successfully!']);
        break;

    case 'incrementViewCount':
        $conn->query("UPDATE view_count SET count = count + 1 WHERE id = 1");
        break;

    case 'getViewCount':
        $result = $conn->query("SELECT count FROM view_count WHERE id = 1");
        $data = $result->fetch_assoc();

        // Get monthly views for the graph
        $monthlyViews = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthResult = $conn->query("SELECT COUNT(*) as count FROM splash_data WHERE MONTH(created_at) = $i");
            $monthData = $monthResult->fetch_assoc();
            $monthlyViews[] = $monthData['count'];
        }

        echo json_encode(['count' => (int)$data['count'], 'monthlyViews' => $monthlyViews]);
        break;

    case 'getRecentSplashData':
        $result = $conn->query("SELECT * FROM splash_data ORDER BY created_at DESC LIMIT 5");
        $recentData = [];
        while ($row = $result->fetch_assoc()) {
            $recentData[] = $row;
        }
        echo json_encode($recentData);
        break;

    case 'deleteSplash':
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM splash_data WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['message' => 'Splash screen deleted successfully!']);
        break;

    default:
        echo json_encode(['message' => 'Invalid action']);
}

$conn->close();
?>
