<?php
function renderNavbar() {
    // Start the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Side navigation HTML structure with icons
    echo '
    <div class="flex">
        <!-- Sidebar (Side Navigation) -->
        <div class="w-full min-h-screen bg-green-400 text-white flex flex-col">
            <div class="p-6 bg-green-600 text-center text-lg font-bold">
                Railway Management
            </div>

            <div class="flex-grow">
                <ul class="space-y-4 p-6">
                    <li><a href="admin_dashboard.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a></li>
                    <li><a href="view_users.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-users mr-3"></i> View Users</a></li>
                    <li><a href="add_seats.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-users mr-3"></i> Add Seats</a></li>
                    <li><a href="add_stations.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-users mr-3"></i> Add Stations</a></li>
                    <li><a href="add_train.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-train mr-3"></i> Add Trains</a></li>
                    <li><a href="all_trains.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-railroad-track mr-3"></i> All Trains</a></li>
                    <li><a href="settings.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded flex items-center"><i class="fas fa-cogs mr-3"></i> Settings</a></li>
                </ul>
            </div>';

    // Dynamic content based on session
    echo '<div class="p-6">
            <div class="text-center">
                <span class="block">';

    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        echo 'Welcome, Admin';
    } elseif (isset($_SESSION["username"])) {
        echo 'Welcome, ' . htmlspecialchars($_SESSION["username"]);
    }

    echo '</span>';

    // Logout button for logged-in users
    if (isset($_SESSION["username"])) {
        echo '
        <form action="./api/logout.php" method="POST" class="inline">
            <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
                Logout
            </button>
        </form>';
    } else {
        // If user is not logged in, show login and signup links
        echo '
        <a href="login.php" class="hover:underline">Login</a>
        <a href="signup.php" class="hover:underline">Signup</a>';
    }

    echo '</div></div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6">
            <!-- Your main content goes here -->
        </div>
    </div>';
}
?>

<!-- 
 <li><a href="manage_trains.php" class="block text-white hover:bg-green-600 py-2 px-4 rounded">Manage Trains</a></li> -->