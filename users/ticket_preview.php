<?php
session_start();
include '../components/usernavbar.php';
include '../api/db.php'; // Database connection

// Fetch booking details based on the booking ID
$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($bookingId > 0) {
    $queryBooking = "
        SELECT b.*, t.train_name
        FROM bookings b
        JOIN trains t ON b.train_id = t.id
        WHERE b.booking_id = '$bookingId'";

    $resultBooking = mysqli_query($con, $queryBooking);
    $booking = mysqli_fetch_assoc($resultBooking);

    if ($booking) {
        $queryPassengers = "
            SELECT * 
            FROM passengers 
            WHERE booking_id = '$bookingId'";
        $resultPassengers = mysqli_query($con, $queryPassengers);
        $passengers = mysqli_fetch_all($resultPassengers, MYSQLI_ASSOC);
    } else {
        die("Invalid booking ID.");
    }
} else {
    die("No booking ID provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderUserNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Ticket Preview</h2>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-2xl font-bold mb-4 text-blue-600">Train: <?php echo htmlspecialchars($booking['train_name']); ?></h3>
                
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Booking ID:</span> <?php echo $booking['booking_id']; ?>
                </p>
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Source Station:</span> <?php echo htmlspecialchars($booking['source']); ?>
                </p>
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Destination Station:</span> <?php echo htmlspecialchars($booking['destination']); ?>
                </p>
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Number of Seats:</span> <?php echo $booking['number_of_seats']; ?>
                </p>
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Total Fare:</span> ₹<?php echo $booking['total_fare']; ?>
                </p>
                <p class="text-gray-700 mb-6">
                    <span class="font-semibold">Booking Date:</span> <?php echo date('d-m-Y H:i:s', strtotime($booking['booking_date'])); ?>
                </p>

                <h4 class="text-xl font-bold mb-4 text-gray-800">Passenger Details</h4>
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 px-4 py-2 text-left">Name</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Age</th>
                            <th class="border border-gray-200 px-4 py-2 text-left">Aadhaar Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passengers as $passenger): ?>
                            <tr>
                                <td class="border border-gray-200 px-4 py-2"><?php echo htmlspecialchars($passenger['name']); ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?php echo $passenger['age']; ?></td>
                                <td class="border border-gray-200 px-4 py-2"><?php echo $passenger['aadhaar_card_number']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="mt-6">
                    <a href="print-ticket.php?booking_id=<?php echo $bookingId; ?>" class="inline-block px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700">Print Ticket</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
