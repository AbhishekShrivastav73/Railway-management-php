<?php
session_start();
include '../components/usernavbar.php';
include '../api/db.php'; // Database connection

// Get the booking ID from the URL
$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($bookingId > 0) {
    // Fetch the booking details
    $queryBooking = "
        SELECT b.booking_id, b.train_id, b.source, b.destination, b.booking_date, b.number_of_seats, b.total_fare, t.train_name
        FROM bookings b
        JOIN trains t ON b.train_id = t.id
        WHERE b.booking_id = '$bookingId'";

    $bookingResult = mysqli_query($con, $queryBooking);
    if ($bookingResult && mysqli_num_rows($bookingResult) > 0) {
        $bookingData = mysqli_fetch_assoc($bookingResult);

        // Fetch the passenger details
        $queryPassengers = "
            SELECT name, age, aadhaar_card_number
            FROM passengers
            WHERE booking_id = '$bookingId'";
        $passengersResult = mysqli_query($con, $queryPassengers);
        $passengers = mysqli_fetch_all($passengersResult, MYSQLI_ASSOC);
    } else {
        $errorMessage = "Booking not found.";
    }
} else {
    $errorMessage = "Invalid booking ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #ticket-preview, #ticket-preview * {
                visibility: visible;
            }
            #ticket-preview {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
        .ticket-container {
            width: 600px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            border: 2px solid #000;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .ticket-header img {
            width: 80px;
        }
        .ticket-header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .ticket-info p {
            font-size: 16px;
            margin: 5px 0;
        }
        .ticket-info p span {
            font-weight: bold;
        }
        .passenger-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .passenger-details th, .passenger-details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .passenger-details th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            color: #777;
        }
    </style>
    <script>
        function printTicket() {
            // Create a new window to print the ticket preview only
            var ticketContent = document.getElementById('ticket-preview').innerHTML;
            var printWindow = window.open('', '', 'height=500, width=800');
            printWindow.document.write('<html><head><title>Ticket Preview</title></head><body>');
            printWindow.document.write(ticketContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close(); // Close the document for printing
            printWindow.print(); // Trigger print dialog
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderUserNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6 h-screen overflow-auto">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Booking Successful</h2>

            <?php if (isset($errorMessage)): ?>
                <div class="mb-4 text-red-500"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php else: ?>
                <div class="bg-white shadow-lg rounded-lg p-6 space-y-6">
                    <h3 class="text-2xl font-semibold text-gray-700 mb-4">Ticket Preview</h3>

                    <!-- Ticket Preview Section -->
                    <div id="ticket-preview" class="ticket-container">
                        <!-- Header Section with Indian Railways Logo -->
                        <div class="ticket-header">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQKzBLAGZmdaNJtVEBQAn4rCtkfo-zlJfig1w&s" alt="Indian Railways Logo">
                            <h1>INDIAN RAILWAYS</h1>
                        </div>

                        <!-- Ticket Information -->
                        <div class="ticket-info">
                            <p><span>Booking ID:</span> <?php echo $bookingData['booking_id']; ?></p>
                            <p><span>Train Name:</span> <?php echo htmlspecialchars($bookingData['train_name']); ?></p>
                            <p><span>Source:</span> <?php echo htmlspecialchars($bookingData['source']); ?></p>
                            <p><span>Destination:</span> <?php echo htmlspecialchars($bookingData['destination']); ?></p>
                            <p><span>Booking Date:</span> <?php echo date('d M Y, H:i', strtotime($bookingData['booking_date'])); ?></p>
                            <p><span>Number of Seats:</span> <?php echo $bookingData['number_of_seats']; ?></p>
                            <p><span>Total Fare:</span> ₹<?php echo number_format($bookingData['total_fare'], 2); ?></p>
                        </div>

                        <!-- Passenger Details -->
                        <h3>Passenger Details</h3>
                        <div class="passenger-details">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Aadhaar Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($passengers as $passenger): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($passenger['name']); ?></td>
                                            <td><?php echo $passenger['age']; ?></td>
                                            <td><?php echo $passenger['aadhaar_card_number']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Section -->
                        <div class="footer">
                            <p>For inquiries, please contact Indian Railways at 139</p>
                        </div>
                    </div>

                    <a href="../user_dashboard.php" class="mt-6 inline-block py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Go to Homepage</a>
                </div>

                <!-- Print Button -->
                <div class="mt-6">
                    <button onclick="printTicket()" class="py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Print Ticket
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
