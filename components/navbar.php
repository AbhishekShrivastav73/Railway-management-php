<?php
function renderNavbar() {
    // Start the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Navbar HTML structure
    echo '
    <nav class="bg-blue-500 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-lg font-bold">Railway Management</a>
            <div class="flex items-center space-x-4">';
    
    // Dynamic content based on session
    if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") {
        echo '<span>Welcome, Admin</span>';
    } elseif (isset($_SESSION["username"])) {
        echo '<span>Welcome, ' . htmlspecialchars($_SESSION["username"]) . '</span>';
    } else {
        echo '
        <a href="login.php" class="hover:underline">Login</a>
        <a href="signup.php" class="hover:underline">Signup</a>';
    }

    // Logout button for logged-in users
    if (isset($_SESSION["username"])) {
        echo '
        <form action="./api/logout.php" method="POST" class="inline">
            <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
                Logout
            </button>
        </form>';
    }

    echo '
            </div>
        </div>
    </nav>';
}
?>
