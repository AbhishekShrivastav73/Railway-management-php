<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include('../api/db.php');
include '../components/usernavbar.php';

// Get the train ID from the URL
$train_id = isset($_GET['train_id']) ? $_GET['train_id'] : null;

// Fetch train details
if ($train_id) {
    $train_query = "SELECT * FROM trains WHERE id = $train_id";
    $train_result = mysqli_query($con, $train_query);

    if (!$train_result) {
        die("Error fetching train details: " . mysqli_error($con));
    }

    $train = mysqli_fetch_assoc($train_result);
} else {
    die("No train selected.");
}

// Fetch station details related to the selected train by joining train_seats and train_stations tables
$stations_query = "
    SELECT ts.station_id, ts.available_seats, tr.station_name
    FROM train_seats ts
    JOIN train_stations tr ON ts.station_id = tr.id
    WHERE ts.train_id = $train_id
";
$stations_result = mysqli_query($con, $stations_query);

if (!$stations_result) {
    die("Error fetching stations: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

    <div class="flex">
        <!-- Sidebar (User Navbar) -->
        <?php renderUserNavbar() ?>

        <!-- Main Content Area -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-bold mb-6">Book Ticket for Train: <?php echo htmlspecialchars($train['train_name']); ?></h2>

            <!-- Booking Form -->
            <form action="process_booking.php" method="POST" class="space-y-4">
                <input type="hidden" name="train_id" value="<?php echo $train_id; ?>">

                <!-- Source Station -->
                <div>
                    <label for="source" class="block text-lg font-semibold">Source</label>
                    <select name="source" id="source" class="w-full p-2 border border-gray-300 rounded-md" required>
                        <option value="">Select Source</option>
                        <?php while ($station = mysqli_fetch_assoc($stations_result)): ?>
                            <option value="<?php echo $station['station_id']; ?>" data-available-seats="<?php echo $station['available_seats']; ?>">
                                <?php echo htmlspecialchars($station['station_name']); ?> (Available Seats: <?php echo $station['available_seats']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Destination Station -->
                <div>
                    <label for="destination" class="block text-lg font-semibold">Destination</label>
                    <select name="destination" id="destination" class="w-full p-2 border border-gray-300 rounded-md" required>
                        <option value="">Select Destination</option>
                        <?php
                            mysqli_data_seek($stations_result, 0); // Reset pointer to the beginning
                            while ($station = mysqli_fetch_assoc($stations_result)): ?>
                            <option value="<?php echo $station['station_id']; ?>" data-available-seats="<?php echo $station['available_seats']; ?>">
                                <?php echo htmlspecialchars($station['station_name']); ?> (Available Seats: <?php echo $station['available_seats']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Number of Seats -->
                <div>
                    <label for="seats" class="block text-lg font-semibold">Number of Seats</label>
                    <input type="number" id="seats" name="number_of_seats" class="w-full p-2 border border-gray-300 rounded-md" min="1" required>
                </div>

                <!-- Fare Display -->
                <div id="fareDisplay" class="mt-4 hidden">
                    <h3 class="text-xl font-semibold">Fare: ₹<span id="fareAmount"></span></h3>
                </div>

                <!-- Passenger Information -->
                <div id="passengerInfo" class="space-y-4 hidden">
                    <h3 class="text-xl font-semibold">Enter Passenger Details</h3>
                    <div id="passengerForm"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Book Now</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('seats').addEventListener('input', calculateFare);
        document.getElementById('source').addEventListener('change', calculateFare);
        document.getElementById('destination').addEventListener('change', calculateFare);

        function calculateFare() {
            const source = document.getElementById('source');
            const destination = document.getElementById('destination');
            const seats = document.getElementById('seats').value;
            const fareDisplay = document.getElementById('fareDisplay');
            const fareAmount = document.getElementById('fareAmount');
            const passengerInfo = document.getElementById('passengerInfo');
            const passengerForm = document.getElementById('passengerForm');
            
            const sourceOption = source.options[source.selectedIndex];
            const destinationOption = destination.options[destination.selectedIndex];

            if (source.value && destination.value && source.value !== destination.value && seats > 0) {
                const sourceSeats = parseInt(sourceOption.getAttribute('data-available-seats'));
                const destinationSeats = parseInt(destinationOption.getAttribute('data-available-seats'));

                if (seats <= sourceSeats && seats <= destinationSeats) {
                    const farePerSeat = 500; // Example fare per seat (this can be dynamic based on train or distance)
                    const totalFare = seats * farePerSeat;

                    fareAmount.textContent = totalFare;
                    fareDisplay.classList.remove('hidden');
                    passengerInfo.classList.remove('hidden');

                    // Generate passenger input fields
                    let passengerInputs = '';
                    for (let i = 1; i <= seats; i++) {
                        passengerInputs += `  
                            <div>
                                <label for="passenger_${i}_name" class="block">Passenger ${i} Name</label>
                                <input type="text" id="passenger_${i}_name" name="passenger_${i}_name" required class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="passenger_${i}_age" class="block">Passenger ${i} Age</label>
                                <input type="number" id="passenger_${i}_age" name="passenger_${i}_age" required class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="passenger_${i}_aadhaar" class="block">Passenger ${i} Aadhaar</label>
                                <input type="text" id="passenger_${i}_aadhaar" name="passenger_${i}_aadhaar" required class="w-full p-2 border border-gray-300 rounded-md">
                            </div>
                            <hr>
                        `;
                    }
                    passengerForm.innerHTML = passengerInputs;
                } else {
                    alert('Not enough seats available.');
                }
            }
        }
    </script>
</body>
</html>
