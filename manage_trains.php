<?php
session_start();
include './api/db.php';
include './components/navbar.php';

// Check if admin is logged in
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_train'])) {
    $train_name = $_POST['train_name'];
    $train_number = $_POST['train_number'];
    $start_station = $_POST['start_station'];
    $end_station = $_POST['end_station'];
    $distance = $_POST['distance'];

    $base_fare = 25;  // ₹100 as base fare
    $fare_per_km = 0.50;  // ₹10 per km for regular trains

    // Calculate the fare based on distance
    $fare = $base_fare + ($distance * $fare_per_km);
    // Fare Calculation: ₹10 per km
    // $fare = $distance * 0.25; // You can change this multiplier based on the fare policy

    // Insert new train data into the database
    $sql = "INSERT INTO trains (train_name, train_number, start_station, end_station, distance, fare) 
            VALUES ('$train_name', '$train_number', '$start_station', '$end_station', '$distance', '$fare')";

    if (mysqli_query($con, $sql)) {
        $_SESSION['success'] = 'Train added successfully';
    } else {
        $_SESSION['error'] = 'Failed to add Train';
    }

    // Redirect to avoid resubmission
    header("Location: manage_trains.php");
    exit;
}

// Fetch all trains from the database
$sql = "SELECT * FROM trains";
$result = mysqli_query($con, $sql);
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
        // JavaScript to automatically calculate the fare based on distance input
        function calculateFare() {
            var distance = document.getElementById('distance').value;
            var fare = 25 + (distance * 0.50); // ₹10 per km, change this multiplier if needed
            document.getElementById('fare').value = fare;
        }
    </script>
</head>

<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderNavbar() ?>
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
                        <th class="py-2 px-4 border-b">Distance</th>
                        <th class="py-2 px-4 border-b">Fare</th>
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
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['distance']); ?> km</td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($train['fare']); ?> ₹</td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_train.php?id=<?php echo $train['id']; ?>" class="text-blue-500">Edit</a> |
                                <a href="delete_train.php?id=<?php echo $train['id']; ?>" class="text-red-500">Delete</a>
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

            <!-- Form to Add a New Train -->
            <div class="bg-white p-8 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-4">Add New Train</h2>
                <form action="manage_trains.php" method="POST">
                    <div class="mb-4">
                        <label for="train_name" class="block text-gray-700">Train Name</label>
                        <input type="text" name="train_name" id="train_name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="train_number" class="block text-gray-700">Train Number</label>
                        <input type="text" name="train_number" id="train_number" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="start_station" class="block text-gray-700">Start Station</label>
                        <input type="text" name="start_station" id="start_station" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="end_station" class="block text-gray-700">End Station</label>
                        <input type="text" name="end_station" id="end_station" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <div class="mb-4">
                        <label for="distance" class="block text-gray-700">Distance (in km)</label>
                        <input type="number" name="distance" id="distance" class="w-full p-2 border border-gray-300 rounded mt-1" oninput="calculateFare()" required>
                    </div>
                    <div class="mb-4">
                        <label for="fare" class="block text-gray-700">Fare</label>
                        <input type="number" name="fare" id="fare" class="w-full p-2 border border-gray-300 rounded mt-1" readonly>
                    </div>
                    <button type="submit" name="add_train" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 hover:bg-blue-600">Add Train</button>
                </form>
            </div>

        </div>
    </div>

</body>

</html>
