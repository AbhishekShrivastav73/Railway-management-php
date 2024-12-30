<?php
session_start();
include './components/navbar.php';
include './api/db.php'; // Database connection

// Backend Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_id = $_POST['train_id'];
    $station_names = $_POST['station_names'];
    $distances = $_POST['distances'];

    // Validation
    if (empty($train_id) || empty($station_names) || empty($distances)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        foreach ($station_names as $index => $station_name) {
            $distance = $distances[$index];
            if (!empty($station_name) && !empty($distance)) {
                $query = "INSERT INTO train_stations (train_id, station_name, distance_from_start) 
                          VALUES ('$train_id', '$station_name', '$distance')";
                if (!mysqli_query($con, $query)) {
                    $_SESSION['error'] = "Failed to add station: " . mysqli_error($con);
                    break;
                }
            }
        }

        if (!isset($_SESSION['error'])) {
            $_SESSION['success'] = "Stations added successfully.";
        }
    }
}

// Fetch trains for the dropdown
$query = "SELECT id, train_name FROM trains ORDER BY train_name ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function addStationRow() {
            const container = document.getElementById('station-container');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-3 gap-4 mb-4';

            row.innerHTML = `
                <input type="text" name="station_names[]" placeholder="Station Name" 
                       class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                <input type="number" name="distances[]" placeholder="Distance (km)" 
                       class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                <button type="button" onclick="removeStationRow(this)" 
                        class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition duration-150">Remove</button>
            `;

            container.appendChild(row);
        }

        function removeStationRow(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6 h-screen overflow-auto">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Add Stations to Train</h2>

            <!-- Station Addition Form -->
            <form action="" method="POST" class="bg-white shadow-lg rounded-lg p-6 space-y-6">
                <!-- Train Dropdown -->
                <div>
                    <label for="train_id" class="block text-sm font-medium text-gray-700 mb-1">Select Train</label>
                    <select name="train_id" id="train_id" class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                        <option value="" disabled selected>Select Train</option>
                        <?php while ($train = mysqli_fetch_assoc($result)): ?>
                            <option value="<?php echo $train['id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dynamic Station Inputs -->
                <div id="station-container" class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <input type="text" name="station_names[]" placeholder="Station Name" 
                               class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                        <input type="number" name="distances[]" placeholder="Distance (km)" 
                               class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                        <button type="button" onclick="removeStationRow(this)" 
                                class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition duration-150">Remove</button>
                    </div>
                </div>

                <!-- Add More Button -->
                <button type="button" onclick="addStationRow()" 
                        class="block bg-green-500 text-white px-6 py-2 rounded shadow hover:bg-green-600 transition duration-150">
                    + Add More Stations
                </button>

                <!-- Submit Button -->
                <button type="submit" 
                        class="block w-full bg-indigo-600 text-white py-3 px-6 rounded shadow hover:bg-indigo-700 transition duration-150 text-lg font-medium">
                    Save All Stations
                </button>
            </form>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mt-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-400 text-green-700">
                    <p><strong>Success:</strong> <?php echo $_SESSION['success']; ?></p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mt-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-400 text-red-700">
                    <p><strong>Error:</strong> <?php echo $_SESSION['error']; ?></p>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

