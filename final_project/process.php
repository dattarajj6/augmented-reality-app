<?php
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "flashcards_db"; // Ensure the database name is correct

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'addCategory') {
        $stmt = $conn->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['icon']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'addSubcategory') {
        $stmt = $conn->prepare("INSERT INTO subcategories (category_id, name, icon) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $data['categoryId'], $data['name'], $data['icon']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'addFlashcard') {
        $stmt = $conn->prepare("INSERT INTO flashcards (name, image, link, qr_code, subcategory_id, view_count) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("ssssi", $data['name'], $data['image'], $data['link'], $data['qrCode'], $data['subcategoryId']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'incrementViewCount') {
        $stmt = $conn->prepare("UPDATE flashcards SET view_count = view_count + 1 WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    
    } elseif ($data['action'] === 'updateCategory') {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['icon'], $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'updateSubcategory') {
        $stmt = $conn->prepare("UPDATE subcategories SET name = ?, icon = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['icon'], $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'updateFlashcard') {
        $stmt = $conn->prepare("UPDATE flashcards SET name = ?, image = ?, link = ?, qr_code = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $data['name'], $data['image'], $data['link'], $data['qrCode'], $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'lockFlashcard') {
        $stmt = $conn->prepare("UPDATE flashcards SET is_locked = 1 WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();

    } elseif ($data['action'] === 'unlockFlashcard') {
        $stmt = $conn->prepare("UPDATE flashcards SET is_locked = 0 WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['type'] === 'category') {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
    } elseif ($data['type'] === 'subcategory') {
        $stmt = $conn->prepare("DELETE FROM subcategories WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
    } elseif ($data['type'] === 'flashcard') {
        $stmt = $conn->prepare("DELETE FROM flashcards WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'getCategories') {
        $result = $conn->query("SELECT * FROM categories");
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($categories);
    } elseif ($_GET['action'] === 'getSubcategories') {
        $categoryId = $_GET['categoryId'];
        $stmt = $conn->prepare("SELECT * FROM subcategories WHERE category_id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $subcategories = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($subcategories);
    } elseif ($_GET['action'] === 'getFlashcards') {
        if (isset($_GET['subcategoryId'])) {
            $subcategoryId = $_GET['subcategoryId'];
            $stmt = $conn->prepare("SELECT f.*, s.name AS subcategory_name FROM flashcards f JOIN subcategories s ON f.subcategory_id = s.id WHERE f.subcategory_id = ?");
            $stmt->bind_param("i", $subcategoryId);
            $stmt->execute();
            $result = $stmt->get_result();
            $flashcards = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($flashcards);
        } else {
            $result = $conn->query("SELECT f.*, s.name AS subcategory_name FROM flashcards f JOIN subcategories s ON f.subcategory_id = s.id");
            $flashcards = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($flashcards);
        }
    } elseif ($_GET['action'] === 'getChartData') {
        $categoryCount = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
        $subcategoryCount = $conn->query("SELECT COUNT(*) as count FROM subcategories")->fetch_assoc()['count'];
        $flashcardCount = $conn->query("SELECT COUNT(*) as count FROM flashcards")->fetch_assoc()['count'];

        $response = [
            'labels' => ['Categories', 'Subcategories', 'Flashcards'],
            'values' => [$categoryCount, $subcategoryCount, $flashcardCount]
        ];
        echo json_encode($response);
    }
}

$conn->close();
?>

