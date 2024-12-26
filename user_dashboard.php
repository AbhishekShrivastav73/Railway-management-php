<?php
session_start();
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

    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-semibold mb-6">Welcome, <?php echo $_SESSION['username']; ?>!</h1>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <p class="text-lg">This is your user dashboard.</p>
        </div>
    </div>

</body>
</html>
