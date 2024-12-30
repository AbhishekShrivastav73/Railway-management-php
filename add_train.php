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
    $stations = $_POST['stations']; // Array of stations
    $distances = $_POST['distances']; // Array of distances

    $base_fare = 25; // ₹25 base fare
    $fare_per_km = 0.50; // ₹0.50 per km

    $sql_check = "SELECT * FROM trains WHERE train_name = '$train_name' OR train_number = '$train_number'";
    $result_check = mysqli_query($con, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $_SESSION['error'] = 'Train with this name or number already exists!';
        header("Location: add_train.php");
        exit;
    }

    $sql = "INSERT INTO trains (train_name, train_number, start_station, end_station) 
            VALUES ('$train_name', '$train_number', '$start_station', '$end_station')";
    if (mysqli_query($con, $sql)) {
        $train_id = mysqli_insert_id($con);

       

        $_SESSION['success'] = 'Train added successfully!';
        header("Location: all_trains.php");
    } else {
        $_SESSION['error'] = 'Failed to add train.';
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
    <title>Add Train</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // function addStation() {
        //     const container = document.getElementById('stationsContainer');
        //     const index = container.children.length + 1;
        //     container.insertAdjacentHTML('beforeend', `
        //         <div class="flex flex-col gap-4 p-2 border rounded" id="station_${index}">
        //             <div>
        //                 <label class="block">Station ${index}:</label>
        //                 <input type="text" name="stations[]" class="p-2 border rounded w-full" required>
        //             </div>
        //             <div>
        //                 <label class="block">Distance from Start (km):</label>
        //                 <input type="number" name="distances[]" class="p-2 border rounded w-full" required>
        //             </div>
        //             <button type="button" class="text-red-500" onclick="removeStation(${index})">Remove</button>
        //         </div>
        //     `);
        // }

        // function removeStation(index) {
        //     const station = document.getElementById(`station_${index}`);
        //     station.remove();
        // }
    </script>
</head>
<body class="bg-gray-100 w-full pr-3">
    <div class="container mx-auto  flex">
        <aside class="w-1/4">
            <?php renderNavbar(); ?>
        </aside>
        <main class="w-3/4 py-6">
            <h1 class="text-2xl font-semibold mb-4">Add Train</h1>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label>Train Name</label>
                    <input type="text" name="train_name" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label>Train Number</label>
                    <input type="text" name="train_number" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label>Start Station</label>
                    <input type="text" name="start_station" class="w-full p-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label>End Station</label>
                    <input type="text" name="end_station" class="w-full p-2 border rounded" required>
                </div>
                <div id="stationsContainer" class="mb-4"></div>
                <!-- <button type="button" class="bg-green-500 text-white px-4 py-2 rounded" onclick="addStation()">Add Station</button> -->
                <button type="submit" name="add_train" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Save Train</button>
            </form>
        </main>
    </div>
</body>
</html>
