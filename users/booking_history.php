<?php
session_start();
include '../components/usernavbar.php';
include '../api/db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch booking history for the user
$queryBookingHistory = "
    SELECT b.booking_id, b.train_id, b.source, b.destination, b.booking_date, b.number_of_seats, b.total_fare, t.train_name
    FROM bookings b
    JOIN trains t ON b.train_id = t.id
    WHERE b.user_id = '$user_id'
    ORDER BY b.booking_date DESC"; // Fetch the latest bookings first

$bookingHistoryResult = mysqli_query($con, $queryBookingHistory);

// Check if the user has any bookings
if (mysqli_num_rows($bookingHistoryResult) > 0) {
    $bookings = mysqli_fetch_all($bookingHistoryResult, MYSQLI_ASSOC);
} else {
    $noBookingsMessage = "No bookings found. Make a booking now!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .ticket-card {
            border: 1px solid #ddd;
            padding: 16px;
            margin-bottom: 12px;
            background-color: #fff;
            border-radius: 8px;
        }
        .ticket-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .ticket-header {
            font-size: 18px;
            font-weight: bold;
        }
        .ticket-details {
            font-size: 14px;
            margin-top: 8px;
        }
        .ticket-footer {
            margin-top: 12px;
            text-align: right;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderUserNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6 h-screen overflow-auto">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Booking History</h2>

            <?php if (isset($noBookingsMessage)): ?>
                <div class="mb-4 text-red-500"><?php echo htmlspecialchars($noBookingsMessage); ?></div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <?php echo htmlspecialchars($booking['train_name']); ?>
                        </div>
                        <div class="ticket-details">
                            <p><span class="font-semibold">Source:</span> <?php echo htmlspecialchars($booking['source']); ?></p>
                            <p><span class="font-semibold">Destination:</span> <?php echo htmlspecialchars($booking['destination']); ?></p>
                            <p><span class="font-semibold">Booking Date:</span> <?php echo date('d M Y, H:i', strtotime($booking['booking_date'])); ?></p>
                            <p><span class="font-semibold">Seats:</span> <?php echo $booking['number_of_seats']; ?></p>
                            <p><span class="font-semibold">Total Fare:</span> ₹<?php echo number_format($booking['total_fare'], 2); ?></p>
                        </div>
                        <div class="ticket-footer">
                            <a href="ticket_preview.php?booking_id=<?php echo $booking['booking_id']; ?>" class="text-indigo-600 hover:text-indigo-800">
                                View Ticket
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
