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
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react/solid" rel="stylesheet">

</head>

<body class="bg-gray-100 w-full flex">

    <!-- Sidebar (Side Navigation) -->
    <div class="w-1/4 h-screen">


        <?php renderNavbar() ?>
    </div>
    <!-- Main Content Area -->
    <div class="  p-6">
        <h1 class="text-3xl font-semibold mb-6">Admin Dashboard</h1>

        <!-- Main Content Here -->
        <p>Welcome to the admin dashboard. Use the sidebar to navigate through different options.</p>
    </div>

</body>

</html>