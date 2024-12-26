<?php
session_start();
include './api/db.php';
include './components/navbar.php';

// Check if admin is logged in
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto p-6">
        <?php renderNavbar() ?>
        <h1 class="text-3xl font-semibold mb-6">Admin Dashboard</h1>

        <!-- Add Train Form -->
        <!-- <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-semibold mb-4">Add New Train</h2>
            <form action="admin_dashboard.php" method="POST">
                <div class="mb-4">
                    <label for="train_name" class="block text-gray-700">Train Name</label>
                    <input type="text" name="train_name" id="train_name" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                
                <div class="mb-4">
                    <label for="departure_time" class="block text-gray-700">Departure Time</label>
                    <input type="time" name="departure_time" id="departure_time" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                
                <div class="mb-4">
                    <label for="arrival_time" class="block text-gray-700">Arrival Time</label>
                    <input type="time" name="arrival_time" id="arrival_time" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>
                
                <div class="mb-4">
                    <label for="distance_km" class="block text-gray-700">Distance (km)</label>
                    <input type="number" name="distance_km" id="distance_km" class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>

                <div class="mb-4">
                    <label for="amount_per_km" class="block text-gray-700">Amount per Kilometer</label>
                    <input type="number" name="amount_per_km" id="amount_per_km" class="w-full p-2 border border-gray-300 rounded mt-1" step="0.01" required>
                </div>
                
                <button type="submit" name="add_train" class="w-full bg-blue-500 text-white p-2 rounded mt-4 hover:bg-blue-600">Add Train</button>
            </form>
        </div>

        <!-- Train List Table -->
        <!-- <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Train List</h2>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left">Train Name</th>
                        <th class="py-2 px-4 text-left">Departure Time</th>
                        <th class="py-2 px-4 text-left">Arrival Time</th>
                        <th class="py-2 px-4 text-left">Distance (km)</th>
                        <th class="py-2 px-4 text-left">Amount per km</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="border-b">
                            <td class="py-2 px-4"><?php echo $row['train_name']; ?></td>
                            <td class="py-2 px-4"><?php echo $row['departure_time']; ?></td>
                            <td class="py-2 px-4"><?php echo $row['arrival_time']; ?></td>
                            <td class="py-2 px-4"><?php echo $row['distance_km']; ?></td>
                            <td class="py-2 px-4"><?php echo $row['amount_per_km']; ?></td>
                            <td class="py-2 px-4">
                                <a href="edit_train.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a> |
                                <a href="delete_train.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> -->
    </div>

</body>
</html>
