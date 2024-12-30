<?php
session_start();
include './components/usernavbar.php';
if ($_SESSION['role'] != 'user') {
    header("Location: login.php");  // Redirect if not a user
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@heroicons/react/solid" rel="stylesheet">

</head>
<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto flex ">
        <div class="w-1/4">
            <?php renderUserNavbar() ?>
        </div>
        
    </div>

</body>
</html>
