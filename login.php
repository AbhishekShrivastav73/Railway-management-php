<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-sm p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Login</h2>
        <form action="login_action.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 mt-1 border rounded-lg focus:ring focus:ring-indigo-200">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 mt-1 border rounded-lg focus:ring focus:ring-indigo-200">
            </div>
            <button type="submit"
                    class="w-full px-3 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Login
            </button>
        </form>
        <p class="mt-4 text-sm text-center text-gray-600">
            Don't have an account? 
            <a href="signup.php" class="text-indigo-600 hover:underline">Create an account</a>
        </p>
    </div>
</body>
</html>
