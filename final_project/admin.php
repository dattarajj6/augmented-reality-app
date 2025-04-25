
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F7F0EB;
            padding: 0;
            margin: 0;
        }
        .chart-container {
            position: relative;
            margin: 20px 0;
            height: 60vh; /* Increased height */
            width: 100vw; /* Set width to full viewport width */
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <aside class="bg-white w-64 p-6 shadow-xl">
        <h1 class="text-3xl font-bold mb-8">NextGen Reality</h1>
        <nav class="space-y-4">
            <a href="#" class="flex items-center space-x-2 text-blue-500 font-medium hover:text-blue-600">
                <span>🏠</span> <span>Users Details</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>📄</span> <span>Content</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>📊</span> <span>Splash Screen</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>👍</span> <span>Settings</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>💬</span> <span>Coins</span>
            </a>
            <a href="#" class="flex items-center space-x-2 hover:text-blue-500">
                <span>🔗</span> <span>Share</span>
            </a>
        </nav>
        <div class="mt-10 border-t pt-6">
            <button class="w-full bg-blue-500 text-white p-2 rounded-lg hover:bg-blue-600">Logout</button>
            <div class="flex items-center space-x-2 mt-4">
                <span>🌙</span> <span>Dark Mode</span>
                <input type="checkbox" class="ml-auto">
            </div>
        </div>
    </aside>

    <main class="flex-1 p-10">
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-500 text-white p-6 rounded-xl shadow-md text-center">
                <p class="text-lg">Total Users</p>
                <?php
                $host = "localhost"; // Database host
                $db_user = "root"; // Database username
                $db_pass = ""; // Database password
                $db_name = "user_management"; // Database name

                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                $conn = new mysqli($host, $db_user, $db_pass, $db_name);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $totalUsersResult = $conn->query("SELECT COUNT(*) as total FROM users");
                $totalUsers = $totalUsersResult->fetch_assoc()['total'];
                ?>
                <h2 class="text-4xl font-bold"><?php echo $totalUsers; ?></h2>
            </div>
            <div class="bg-yellow-400 text-white p-6 rounded-xl shadow-md text-center">
                <p class="text-lg">Active Users</p>
                <h2 class="text-4xl font-bold">314</h2> <!-- Placeholder for active users -->
            </div>
            <div class="bg-purple-400 text-white p-6 rounded-xl shadow-md text-center">
                <p class="text-lg">Total Share</p>
                <h2 class="text-4xl font-bold">106</h2> <!-- Placeholder for total share -->
            </div>
        </div>

        <div class="chart-container">
            <canvas id="userChart"></canvas>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Recent Activity</h2>
            <button class="flex items-center bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                ⬇ <span class="ml-2">Download Details</span>
            </button>
        </div>

        <div class="bg-white shadow-md rounded-xl overflow-hidden">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Joined</th>
                        <th class="p-3 text-left">Type</th>
                        <th class="p-3 text-left">Password (Encrypted)</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch user data for the table
                    $result = $conn->query("SELECT id, name, email, created_at, password FROM users");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='border-t hover:bg-blue-50'>
                                <td class='p-3'>{$row['id']}</td>
                                <td class='p-3'>{$row['name']}</td>
                                <td class='p-3'>{$row['email']}</td>
                                <td class='p-3'>{$row['created_at']}</td>
                                <td class='p-3'>New</td>
                                <td class='p-3'>{$row['password']}</td>
                                <td class='p-3'><button class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600'>Delete</button></td>
                              </tr>";
                    }

                    // Prepare data for the graph (count users per month)
                    $userCounts = array_fill(0, 12, 0); // Initialize counts for each month (January to December)
                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

                    // Get user registration counts grouped by month
                    $query = "SELECT MONTH(created_at) as reg_month, COUNT(*) as user_count 
                              FROM users 
                              GROUP BY reg_month 
                              ORDER BY reg_month";

                    $result = $conn->query($query);

                    while ($row = $result->fetch_assoc()) {
                        $userCounts[(int)$row['reg_month'] - 1] = $row['user_count']; // Adjust index for 0-based array
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const months = <?php echo json_encode($months); ?>; // Month names
        const userCounts = <?php echo json_encode($userCounts); ?>; // User counts

        const data = {
            labels: months, // Using month names as labels
            datasets: [{
                label: 'Number of Users',
                data: userCounts, // User counts as data points
                backgroundColor: 'rgba(138, 77, 255, 0.5)',
                borderColor: 'rgba(138, 77, 255, 1)',
                borderWidth: 2,
                fill: false, // Disable filling under the line
                tension: 0.1 // Smooth curve
            }]
        };

        const config = {
            type: 'line', // Set chart type to line
            data: data,
            options: {
                responsive: true,
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Number of Users'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'User Registrations by Month'
                    }
                }
            }
        };

        const userChart = new Chart(document.getElementById('userChart'), config);
    </script>

</body>
</html>
