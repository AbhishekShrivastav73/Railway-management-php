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

    $base_fare = 25;  // ₹25 base fare
    $fare_per_km = 0.50;  // ₹0.50 per km

    // Calculate the fare based on distance
    $fare = $base_fare + ($distance * $fare_per_km);

    // Check if the train name or number already exists
    $sql_check = "SELECT * FROM trains WHERE train_name = '$train_name' OR train_number = '$train_number'";
    $result_check = mysqli_query($con, $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        $_SESSION['error'] = 'Train with this name or number already exists!';
        header("Location: add_train.php");
        exit;
    }

    // Insert new train data into the database
    $sql = "INSERT INTO trains (train_name, train_number, start_station, end_station, distance, fare) 
            VALUES ('$train_name', '$train_number', '$start_station', '$end_station', '$distance', '$fare')";

    if (mysqli_query($con, $sql)) {
        $_SESSION['success'] = 'Train added successfully';
        header("Location: all_trains.php"); // Redirect to the All Trains page
    } else {
        $_SESSION['error'] = 'Failed to add Train';
        header("Location: add_train.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Train</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function calculateFare() {
            var distance = document.getElementById('distance').value;
            var fare = 25 + (distance * 0.50);
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
            <h2 class="text-xl font-semibold mb-4">Add New Train</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error:</span> <?php echo $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="add_train.php" method="POST">
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
</body>
</html>
