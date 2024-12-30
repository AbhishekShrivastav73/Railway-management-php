<?php 
session_start();
include './components/navbar.php';
include './api/db.php'; // Database connection

// Fetch all trains with their stations using a JOIN query
$query = "
    SELECT 
        trains.id AS train_id,
        trains.train_name,
        trains.train_number,
        trains.start_station,
        trains.end_station,
        GROUP_CONCAT(train_stations.station_name ORDER BY train_stations.id SEPARATOR ', ') AS station_list
    FROM 
        trains
    LEFT JOIN 
        train_stations 
    ON 
        train_stations.train_id = trains.id
    GROUP BY 
        trains.id";
$result = mysqli_query($con, $query);

// Check for errors in fetching data
if (!$result) {
    $_SESSION['error'] = "Failed to fetch train data.";
    header('Location: all_trains.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trains</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react/solid" rel="stylesheet">

    <script>
        // Function to toggle dropdown visibility
        function toggleDropdown(button) {
            const dropdown = button.nextElementSibling; // Select the dropdown menu
            const isHidden = dropdown.classList.contains("hidden");
            document.querySelectorAll(".relative .absolute").forEach(el => el.classList.add("hidden")); // Close all other dropdowns
            if (isHidden) {
                dropdown.classList.remove("hidden");
            } else {
                dropdown.classList.add("hidden");
            }
        }

        // Close dropdown if clicked outside
        window.addEventListener("click", function (event) {
            if (!event.target.closest(".relative")) {
                document.querySelectorAll(".relative .absolute").forEach(el => el.classList.add("hidden"));
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderNavbar(); ?>
        </div>
        <div class="w-3/4 py-6 h-screen overflow-auto flex flex-col gap-2">
            <h2 class="text-xl font-semibold mb-4">All Trains</h2>
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Train Name</th>
                        <th class="py-2 px-4 border-b">Train Number</th>
                        <th class="py-2 px-4 border-b">Start Station</th>
                        <th class="py-2 px-4 border-b">End Station</th>
                        <th class="py-2 px-4 border-b">Stops at Stations</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($train = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['train_name']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['train_number']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['start_station']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['end_station']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <!-- Dropdown for stations -->
                                <div class="relative">
                                    <button onclick="toggleDropdown(this)" class="px-4 py-2 bg-blue-500 text-white rounded shadow">
                                        View Stations
                                    </button>
                                    <div class="absolute hidden bg-white border shadow-lg mt-2 rounded w-48 z-10">
                                        <?php 
                                        $stations = explode(', ', $train['station_list']);
                                        foreach ($stations as $station): 
                                        ?>
                                            <div class="px-4 py-2 hover:bg-gray-100"><?php echo htmlspecialchars($station); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_train.php?id=<?php echo $train['train_id']; ?>" class="text-blue-500">Edit</a> |
                                <a href="./api/delete_train.php?id=<?php echo $train['train_id']; ?>" class="text-red-500" onclick="return confirm('Are you sure you want to delete this train?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Display Success or Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">Success:</span> <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); // Clear the message after it's displayed ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error:</span> <?php echo $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); // Clear the message after it's displayed ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
