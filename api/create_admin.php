<?php
// Database connection
include 'db.php'; // Make sure to include your DB connection here

// Define admin credentials (username, email, password)
$username = 'admin';  // Change to whatever you want your admin username to be
$email = 'admin@example.com';  // Admin's email address
$password = 'admin123';  // Admin's password (plain-text for now)

// Hash the password (IMPORTANT: Never store plain-text passwords in the database)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);  // Hash the password securely

// SQL query to insert admin into the 'users' table
$sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')";

// Prepare and execute the query using prepared statements
$stmt = $con->prepare($sql); // Prepare the SQL statement
$stmt->bind_param("sss", $username, $email, $hashedPassword);  // Bind parameters to the query

// Check if the insertion was successful
if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and the database connection
$stmt->close();
$con->close();
