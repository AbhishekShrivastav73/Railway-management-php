<?php
session_start();
include './components/navbar.php';
include './api/db.php'; // Database connection

// Backend Logic for saving the seats
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $train_id = $_POST['train_id'];
    $total_seats = $_POST['total_seats'];
    $available_seats = $_POST['available_seats'];

    // Validation
    if (empty($train_id) || empty($total_seats) || empty($available_seats)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        // Insert the total seats and available seats into the database
        $query = "INSERT INTO seats (train_id, total_seats, available_seats) VALUES ('$train_id', '$total_seats', '$available_seats')";
        if (mysqli_query($con, $query)) {
            $_SESSION['success'] = "Seats added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add seat data: " . mysqli_error($con);
        }
    }
}

// Fetch trains for the dropdown
$queryTrains = "SELECT id, train_name FROM trains ORDER BY train_name ASC";
$resultTrains = mysqli_query($con, $queryTrains);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Seats to Train</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6 h-screen overflow-auto">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Add Seats to Train</h2>

            <!-- Seat Management Form -->
            <form action="" method="POST" class="bg-white shadow-lg rounded-lg p-6 space-y-6">
                <!-- Train Dropdown -->
                <div>
                    <label for="train_id" class="block text-sm font-medium text-gray-700 mb-1">Select Train</label>
                    <select name="train_id" id="train_id" 
                            class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            required>
                        <option value="" disabled selected>Select Train</option>
                        <?php while ($train = mysqli_fetch_assoc($resultTrains)): ?>
                            <option value="<?php echo $train['id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Total Seats Input -->
                <div class="mb-6">
                    <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-1">Total Seats for the Train</label>
                    <input type="number" name="total_seats" id="total_seats" 
                           class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           placeholder="Enter total seats" required>
                </div>

                <!-- Available Seats Input -->
                <div class="mb-6">
                    <label for="available_seats" class="block text-sm font-medium text-gray-700 mb-1">Available Seats for the Train</label>
                    <input type="number" name="available_seats" id="available_seats" 
                           class="bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                           placeholder="Enter available seats" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="block w-full bg-indigo-600 text-white py-3 px-6 rounded shadow hover:bg-indigo-700 transition duration-150 text-lg font-medium">
                    Save Seats
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
