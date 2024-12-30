<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include database connection file (Make sure to adjust this path based on your project structure)
include('../api/db.php');
include '../components/usernavbar.php';

// Fetch all train details from the database
$query = "SELECT * FROM trains"; // Assuming your trains table has train_name, source, destination, time, etc.
$result = mysqli_query($con, $query);

// Check for any errors in query
if (!$result) {
    die("Error fetching train data: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Trains</title>
    <!-- Include your CSS file or Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="flex">
        <!-- Sidebar (can be your admin or user navbar) -->
        <?php renderUserNavbar() ?>

        <!-- Main Content Area -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-bold mb-6">Available Trains</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- Train Table -->
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="px-4 py-2">Train Name</th>
                            <th class="px-4 py-2">Source</th>
                            <th class="px-4 py-2">Destination</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($train = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($train['train_name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($train['start_station']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($train['end_station']); ?></td>
                                <td class="px-4 py-2">
                                    <a href="book_ticket.php?train_id=<?php echo $train['id']; ?>" 
                                       class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                        Book Now
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-red-500">No trains available at the moment.</p>
            <?php endif; ?>

            <!-- Close the database connection -->
            <?php mysqli_close($con); ?>
        </div>
    </div>
</body>

</html>
