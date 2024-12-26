<?php
session_start();
include './components/navbar.php';
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
</head>
<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto ">
        <?php renderNavbar()?>
        
    </div>

</body>
</html>
