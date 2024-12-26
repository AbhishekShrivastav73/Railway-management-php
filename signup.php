<?php
// Include database connection
include './api/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collecting form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert user into the database
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashedPassword', 'user')";

    // Execute the query
    if (mysqli_query($con, $sql)) {
        echo "<p class='text-green-500'>Signup successful!</p>";
    } else {
        echo "<p class='text-red-500'>Error: " . mysqli_error($con) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
        
        <form action="signup.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded mt-1" required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded mt-1" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded mt-1" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded mt-4 hover:bg-blue-600">Sign Up</button>
        </form>
    </div>

</body>
</html>
