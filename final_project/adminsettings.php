<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <aside class="bg-white w-64 p-6 shadow-xl">
        <h1 class="text-3xl font-bold mb-8">NextGen Reality</h1>
        <nav class="space-y-4">
            <a href="#" class="flex items-center space-x-2 text-blue-500 font-medium hover:text-blue-600">
                <span>🏠</span><span>Users Details</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>📄</span><span>Content</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>📊</span><span>Splash Screen</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>👍</span><span>Settings</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>💬</span><span>Coins</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>🔗</span><span>Share</span>
            </a>
        </nav>
        <div class="mt-10 border-t pt-6">
            <button class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">Logout</button>
            <div class="flex items-center space-x-2 mt-4">
                <span>🌙</span><span>Dark Mode</span>
                <input type="checkbox" class="ml-auto">
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10">
        <?php
        // Database connection
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

        // Fetch total feedback count
        $totalFeedbackResult = $conn->query("SELECT COUNT(*) as total FROM feedback");
        $totalFeedback = $totalFeedbackResult->fetch_assoc()['total'];

        // Fetch feedback data for the graph
        $monthlyFeedback = array_fill(0, 6, 0); // Initialize for 6 months
        $currentMonth = date('n'); // Get current month (1-12)

        // Fetch monthly feedback counts
        $sql = "SELECT MONTH(created_at) as month, COUNT(*) as count FROM feedback WHERE MONTH(created_at) >= MONTH(CURRENT_DATE) - 6 GROUP BY month";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $monthlyFeedback[(int)$row['month'] - 1] = $row['count'];
        }
        ?>
        
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-blue-500 text-white p-6 rounded-xl shadow-md text-center">
                <p class="text-lg">Total Feedback</p>
                <h2 class="text-4xl font-bold"><?php echo $totalFeedback; ?></h2> <!-- Dynamic total count -->
            </div>
            <div class="bg-yellow-400 text-white p-6 rounded-xl shadow-md text-center">
                <p class="text-lg">Responded</p>
                <h2 class="text-4xl font-bold">500</h2> <!-- Static for now -->
            </div>
        </div>

        <canvas id="modelsChart" class="mt-6 w-full max-h-80"></canvas>

        <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <h2 class="text-xl font-bold p-4">Recent Activity</h2>
            <table class="w-full text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Name of User</th>
                        <th class="p-2">Message</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch feedback data
                    $sql = "SELECT user_name, message, status FROM feedback ORDER BY id DESC LIMIT 5"; // Fetch last 5 feedbacks
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='border-t'>";
                            echo "<td class='p-2'>" . htmlspecialchars($row["user_name"]) . "</td>";
                            echo "<td class='p-2'>" . htmlspecialchars($row["message"]) . "</td>";
                            echo "<td class='p-2'><button class='bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600'>Respond</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='p-2'>No feedback available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('modelsChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Number of Responses',
                    data: [<?php echo implode(',', $monthlyFeedback); ?>], // Use dynamic data
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>

