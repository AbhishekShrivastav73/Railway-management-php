<?php
include('./api/db.php');
include './components/navbar.php';

if (isset($_GET['id'])) {
    $train_id = $_GET['id'];

    // Fetch train data
    $train_query = "SELECT * FROM trains WHERE id = '$train_id'";
    $train_result = mysqli_query($con, $train_query);
    $train = mysqli_fetch_assoc($train_result);

    // Fetch all train stations for this train
    $station_query = "SELECT * FROM train_stations WHERE train_id = '$train_id'";
    $stations_result = mysqli_query($con, $station_query);
    $stations = mysqli_fetch_all($stations_result, MYSQLI_ASSOC);

    if (!$train) {
        header('Location: all_trains.php');
        exit;
    }
}

// Update train details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['train_name'])) {
    $train_name = $_POST['train_name'];
    $train_number = $_POST['train_number'];
    $start_station = $_POST['start_station'];
    $end_station = $_POST['end_station'];

    // Update train query
    $update_train_query = "UPDATE trains SET 
        train_name = '$train_name', 
        train_number = '$train_number', 
        start_station = '$start_station', 
        end_station = '$end_station' 
        WHERE id = '$train_id'";

    if (mysqli_query($con, $update_train_query)) {
        $_SESSION['success'] = 'Train updated successfully!';
        header('Location: all_trains.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update the train. Please try again.';
    }
}

// Update station details
if (isset($_POST['edit_station_id'])) {
    $station_id = $_POST['edit_station_id'];
    $station_name = $_POST['station_name'];
    $station_location = $_POST['station_location'];
    $distance_from_start = $_POST['distance_from_start']; // Add distance field

    $update_station_query = "UPDATE train_stations SET 
        station_name = '$station_name',
        distance_from_start = '$distance_from_start'
        WHERE id = '$station_id' AND train_id = '$train_id'";

    if (mysqli_query($con, $update_station_query)) {
        $_SESSION['success'] = 'Station updated successfully!';
        header("Location: edit_train.php?id=$train_id");
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update station. Please try again.';
    }
}

// Delete station
if (isset($_POST['delete_station_id'])) {
    $delete_station_id = $_POST['delete_station_id'];

    $delete_station_query = "DELETE FROM train_stations WHERE id = '$delete_station_id' AND train_id = '$train_id'";

    if (mysqli_query($con, $delete_station_query)) {
        $_SESSION['success'] = 'Station deleted successfully!';
        header("Location: edit_train.php?id=$train_id");
        exit;
    } else {
        $_SESSION['error'] = 'Failed to delete station. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Train</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderNavbar() ?>
        </div>
        <div class="w-3/4 py-6 h-screen overflow-auto">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Edit Train</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Error:</span> <?php echo $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="edit_train.php?id=<?php echo $train_id; ?>" method="POST" class="space-y-6 mb-8">
                <div>
                    <label for="train_name" class="block text-sm font-medium text-gray-700">Train Name</label>
                    <input type="text" name="train_name" id="train_name" value="<?php echo htmlspecialchars($train['train_name']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                </div>
                <div>
                    <label for="train_number" class="block text-sm font-medium text-gray-700">Train Number</label>
                    <input type="text" name="train_number" id="train_number" value="<?php echo htmlspecialchars($train['train_number']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                </div>
                <div>
                    <label for="start_station" class="block text-sm font-medium text-gray-700">Start Station</label>
                    <input type="text" name="start_station" id="start_station" value="<?php echo htmlspecialchars($train['start_station']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                </div>
                <div>
                    <label for="end_station" class="block text-sm font-medium text-gray-700">End Station</label>
                    <input type="text" name="end_station" id="end_station" value="<?php echo htmlspecialchars($train['end_station']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Train</button>
            </form>

            <h2 class="text-xl font-semibold text-gray-800 mb-4">Train Stations</h2>

            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-3">Station Name</th>
                            <th class="px-6 py-3">Distance From Start</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stations as $station): ?>
                            <tr class="bg-white border-b">
                                <td class="px-6 py-4"><?php echo htmlspecialchars($station['station_name']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($station['distance_from_start']); ?></td>
                                <td class="px-6 py-4">
                                    <!-- Edit button -->
                                    <button onclick="document.getElementById('editStationModal<?php echo $station['id']; ?>').classList.remove('hidden');" class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <!-- Delete button -->
                                    <form action="edit_train.php?id=<?php echo $train_id; ?>" method="POST" class="inline-block ml-4">
                                        <input type="hidden" name="delete_station_id" value="<?php echo $station['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php foreach ($stations as $station): ?>
                <!-- Edit Modal for each Station -->
                <div id="editStationModal<?php echo $station['id']; ?>" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-800 bg-opacity-50">
                    <div class="bg-white rounded-lg p-6 w-1/3">
                        <form action="edit_train.php?id=<?php echo $train_id; ?>" method="POST">
                            <h3 class="text-lg font-semibold mb-4">Edit Station</h3>
                            <div>
                                <label for="station_name" class="block text-sm font-medium text-gray-700">Station Name</label>
                                <input type="text" name="station_name" id="station_name" value="<?php echo htmlspecialchars($station['station_name']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                            </div>
                           
                            <div>
                                <label for="distance_from_start" class="block text-sm font-medium text-gray-700">Distance From Start</label>
                                <input type="text" name="distance_from_start" id="distance_from_start" value="<?php echo htmlspecialchars($station['distance_from_start']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" required>
                            </div>
                            <input type="hidden" name="edit_station_id" value="<?php echo $station['id']; ?>">
                            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4">Update Station</button>
                        </form>
                        <button onclick="document.getElementById('editStationModal<?php echo $station['id']; ?>').classList.add('hidden');" class="mt-4 text-red-500">Close</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
