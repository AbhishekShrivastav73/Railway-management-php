<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include '../components/usernavbar.php';
include '../api/db.php'; // Database connection
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

// Fetch Trains for Dropdown
$queryTrains = "SELECT id, train_name FROM trains ORDER BY train_name ASC";
$resultTrains = mysqli_query($con, $queryTrains);

// Handle the ticket booking form
$bookingError = '';
$calculatedFare = 0;
$availableSeats = null;

// Handle the ticket booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the number of seats and the selected source and destination stations
    $selectedSeats = isset($_POST['number_of_seats']) ? (int)$_POST['number_of_seats'] : 0;
    $sourceStation = $_POST['source_station'] ?? '';
    $destinationStation = $_POST['destination_station'] ?? '';
    $trainId = $_POST['train_id'] ?? 0;
    $passengerNames = $_POST['passenger_name'] ?? [];
    $passengerAges = $_POST['passenger_age'] ?? [];
    $passengerAdhars = $_POST['passenger_adhar'] ?? [];

    // Fetch the distance between the source and destination from the database
    if (!empty($sourceStation) && !empty($destinationStation)) {
        $queryDistance = "
        SELECT ts1.distance_from_start AS source_distance, ts2.distance_from_start AS destination_distance
        FROM train_stations ts1
        JOIN train_stations ts2 ON ts1.train_id = ts2.train_id
        WHERE ts1.station_name = '$sourceStation' AND ts2.station_name = '$destinationStation'";

        $distanceResult = mysqli_query($con, $queryDistance);
        if ($distanceResult && mysqli_num_rows($distanceResult) > 0) {
            $distanceData = mysqli_fetch_assoc($distanceResult);
            $sourceDistance = $distanceData['source_distance'];
            $destinationDistance = $distanceData['destination_distance'];

            // Validate if the destination is ahead of the source
            if ($destinationDistance > $sourceDistance) {
                $distance = $destinationDistance - $sourceDistance;
                $calculatedFare = 50 + ($distance * 0.50); // Base fare + per-km cost
            } else {
                $bookingError = 'Invalid station order: Destination cannot be same or before Source.';
            }
        } else {
            $bookingError = 'Invalid stations selected.';
        }
    }
    // Insert booking details into the bookings table
    if (empty($bookingError) && $selectedSeats > 0 && $calculatedFare > 0) {
        $bookingDate = date('Y-m-d H:i:s');
        $user_id = $_SESSION['user_id'];
        $totalfare = $calculatedFare * $selectedSeats;
        $queryBooking = "
            INSERT INTO bookings (train_id, source, destination, booking_date, number_of_seats, total_fare,user_id)
            VALUES ('$trainId', '$sourceStation', '$destinationStation', '$bookingDate', '$selectedSeats', '$totalfare','$user_id')";

        if (mysqli_query($con, $queryBooking)) {
            // Get the last inserted booking_id
            $bookingId = mysqli_insert_id($con);

            // Insert passengers into the passengers table
            foreach ($passengerNames as $index => $name) {
                $age = $passengerAges[$index];
                $aadhaar = $passengerAdhars[$index];
                $queryPassenger = "
                    INSERT INTO passengers (booking_id, name, age, aadhaar_card_number)
                    VALUES ('$bookingId', '$name', '$age', '$aadhaar')";
                mysqli_query($con, $queryPassenger);
            }

            // Update available seats in the seats table
            $querySeats = "
                SELECT available_seats
                FROM seats
                WHERE train_id = '$trainId'";

            $seatsResult = mysqli_query($con, $querySeats);
            if ($seatsResult) {
                $currentSeats = mysqli_fetch_assoc($seatsResult)['available_seats'];
                $newSeats = $currentSeats - $selectedSeats;
                $queryUpdateSeats = "
                    UPDATE seats
                    SET available_seats = '$newSeats'
                    WHERE train_id = '$trainId'";
                mysqli_query($con, $queryUpdateSeats);
            }


            try {
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'abhisheks.infoseek@gmail.com';                     //SMTP username
                $mail->Password   = 'flkmijuzgteppjrq';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $useremail = $_SESSION['email'];
                $mail->setFrom('abhisheks.infoseek@gmail.com', 'Railway Management Team ');
                $mail->addAddress($useremail, $_SESSION['username']);     //Add a recipient
                //Name is optional
                $mail->addReplyTo('abhisheks.infoseek@gmail.com', 'Railway Management Team');


                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Ticket Booking Confirmation';
                $mail->Body    = "<p>Your booking has been confirmed. Booking ID: <b>$bookingId</b></p>
                <p>Train_id: <b>$trainId</b></p>
                <p>Source Station: <b>$sourceStation</b></p>
                <p>Destination Station: <b>$destinationStation</b></p>
                <p>Number of Seats: <b>$selectedSeats</b></p>
                <p>Total Fare: <b>$totalfare </b></p>";

                $mail->AltBody = "Your booking has been confirmed. Booking ID: $bookingId";


                $mail->send();

                header("Location: booking-success.php?booking_id=$bookingId");
                exit();
                // echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }


            // Redirect to a success page or display a success message

        } else {
            $bookingError = 'There was an error processing your booking.';
        }
    }
}

// Fetch stations and available seats based on the selected train using JOIN queries
$stations = [];
if (!empty($_GET['train_id'])) {
    $train_id = $_GET['train_id'];

    // JOIN query to get all stations for the selected train
    $queryStations = "
        SELECT ts.station_name
        FROM train_stations ts
        JOIN trains t ON ts.train_id = t.id
        WHERE t.id = '$train_id'
        ORDER BY ts.distance_from_start ASC";

    $stationsResult = mysqli_query($con, $queryStations);
    if ($stationsResult) {
        while ($station = mysqli_fetch_assoc($stationsResult)) {
            $stations[] = $station['station_name'];
        }
    }

    // JOIN query to get available seats for the selected train
    $querySeats = "
        SELECT available_seats
        FROM seats
        WHERE train_id = '$train_id'";

    $seatsResult = mysqli_query($con, $querySeats);
    if ($seatsResult) {
        $availableSeats = mysqli_fetch_assoc($seatsResult)['available_seats'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto flex">
        <div class="w-1/4">
            <?php renderUserNavbar(); ?>
        </div>
        <div class="w-3/4 py-8 px-6 h-screen overflow-auto">
            <h2 class="text-3xl font-bold text-gray-700 mb-6">Book Your Train Ticket</h2>
            <!-- <p><?php echo $_SESSION['email']; ?></p> -->

            <!-- Display Error Message -->
            <?php if ($bookingError): ?>
                <div class="mb-4 text-red-500"><?php echo htmlspecialchars($bookingError); ?></div>
            <?php endif; ?>

            <!-- Ticket Booking Form -->
            <form method="POST" class="bg-white shadow-lg rounded-lg p-6 space-y-6">
                <!-- Train Dropdown -->
                <div>
                    <label for="train_id" class="block text-sm font-medium text-gray-700 mb-1">Select Train</label>
                    <select name="train_id" id="train_id"
                        class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        onchange="window.location.href='?train_id=' + this.value" required>
                        <option value="" disabled selected>Select Train</option>
                        <?php while ($train = mysqli_fetch_assoc($resultTrains)): ?>
                            <option value="<?php echo $train['id']; ?>" <?php echo (isset($_GET['train_id']) && $_GET['train_id'] == $train['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($train['train_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Display Available Seats -->
                <?php if ($availableSeats !== null): ?>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Available Seats: <?php echo $availableSeats; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Number of Seats Input -->
                <div>
                    <label for="number_of_seats" class="block text-sm font-medium text-gray-700 mb-1">Number of Seats</label>
                    <input type="number" name="number_of_seats" id="number_of_seats"
                        class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        min="1" max="<?php echo $availableSeats; ?>" value="0" required>
                </div>

                <!-- Source and Destination Stations -->
                <?php if (!empty($stations)): ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="source_station" class="block text-sm font-medium text-gray-700 mb-1">Select Source Station</label>
                            <select name="source_station" id="source_station"
                                class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                <option value="" disabled selected>Select Source Station</option>
                                <?php foreach ($stations as $station): ?>
                                    <option value="<?php echo $station; ?>"><?php echo htmlspecialchars($station); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="destination_station" class="block text-sm font-medium text-gray-700 mb-1">Select Destination Station</label>
                            <select name="destination_station" id="destination_station"
                                class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                <option value="" disabled selected>Select Destination Station</option>
                                <?php foreach ($stations as $station): ?>
                                    <option value="<?php echo $station; ?>"><?php echo htmlspecialchars($station); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Passenger Details -->
                <div id="passenger-details">
                    <h3 class="text-lg font-medium text-gray-700">Passenger Details</h3>
                    <div id="passenger-forms">
                        <div class="flex space-x-4">
                            <input type="text" name="passenger_name[]" placeholder="Passenger Name" class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm" required>
                            <input type="number" name="passenger_age[]" placeholder="Age" class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm" required>
                            <input type="text" name="passenger_adhar[]" placeholder="Aadhaar Number" class="block w-full bg-gray-50 border border-gray-300 rounded-md p-2 shadow-sm" required>
                        </div>
                    </div>
                </div>


                <button type="submit" class="w-full py-2 bg-indigo-600 text-white rounded-md shadow-md hover:bg-indigo-700">Book Now</button>
            </form>
        </div>
    </div>
</body>

</html>