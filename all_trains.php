<?php 
session_start();
include './components/navbar.php';
include './api/db.php'; // Database connection

// Fetch all trains from the database
$query = "SELECT * FROM trains";
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
    <script>
        // Function to confirm the deletion of a train
        function confirmDelete() {
            return confirm("Are you sure you want to delete this train?");
        }
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
                                <a href="./api/delete_train.php?id=<?php echo $train['id']; ?>" class="text-red-500" onclick="return confirmDelete()">Delete</a>
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
