<?php
// Include database connection and other necessary files
include('./api/db.php'); // Include your database connection file
include './components/navbar.php';

// Get the train ID from the URL
if (isset($_GET['id'])) {
    $train_id = $_GET['id'];

    // Fetch the train details from the database
    $query = "SELECT * FROM trains WHERE id = '$train_id'";
    $result = mysqli_query($con, $query);
    $train = mysqli_fetch_assoc($result);

    if (!$train) {
        // Redirect if the train doesn't exist
        header('Location: all_trains.php');
        exit;
    }
} else {
    // Redirect if no train ID is provided
    header('Location: all_trains.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $train_name = $_POST['train_name'];
    $train_number = $_POST['train_number'];
    $start_station = $_POST['start_station'];
    $end_station = $_POST['end_station'];
    $distance = $_POST['distance'];
    $base_fare = 25;  // ₹25 base fare
    $fare_per_km = 0.50;  // ₹0.50 per km

    // Calculate the fare based on distance
    $fare = $base_fare + ($distance * $fare_per_km);

    // Update the train details in the database
    $update_query = "UPDATE trains SET 
        train_name = '$train_name', 
        train_number = '$train_number', 
        start_station = '$start_station', 
        end_station = '$end_station', 
        distance = '$distance', 
        fare = '$fare' 
        WHERE id = '$train_id'";

    if (mysqli_query($con, $update_query)) {
        $_SESSION['success'] = 'Train updated successfully!';
        header('Location: all_trains.php');
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update the train. Please try again.';
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
    <script>
        // Fare calculation based on distance (you can adjust the rate)
        function calculateFare() {
            const distance = document.getElementById('distance').value;
            var fare = 25 + (distance * 0.50);
            // const farePerKm = 5; // Assume fare per kilometer is 5
            // const fare = distance * farePerKm;
            document.getElementById('fare').value = fare;
        }
    </script>
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

            <form action="edit_train.php?id=<?php echo $train_id; ?>" method="POST" class="space-y-6">
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
                
                <div>
                    <label for="distance" class="block text-sm font-medium text-gray-700">Distance (in km)</label>
                    <input type="number" name="distance" id="distance" value="<?php echo htmlspecialchars($train['distance']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" oninput="calculateFare()" required>
                </div>
                
                <div>
                    <label for="fare" class="block text-sm font-medium text-gray-700">Fare (₹)</label>
                    <input type="number" name="fare" id="fare" value="<?php echo htmlspecialchars($train['fare']); ?>" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2" readonly required>
                </div>
                
                <div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Train</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
