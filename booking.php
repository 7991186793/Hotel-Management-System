<?php 
include 'includes/header.php'; 
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Fetch all available rooms
$rooms_stmt = $pdo->query("SELECT * FROM rooms WHERE status = 'available'");
$rooms = $rooms_stmt->fetchAll();

// If room selected from URL
$selected_room = null;
if ($room_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    $selected_room = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id     = (int)$_POST['room_id'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];
    $guests      = (int)$_POST['guests'];

    if ($check_in >= $check_out) {
        $message = "<div class='alert alert-danger'>Check-out date must be after check-in date!</div>";
    } else {
        // Check availability (simple check - overlapping bookings)
        $check_stmt = $pdo->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE room_id = ? 
            AND status IN ('pending', 'confirmed', 'checked_in')
            AND (
                (check_in <= ? AND check_out > ?) OR 
                (check_in < ? AND check_out >= ?)
            )
        ");
        $check_stmt->execute([$room_id, $check_out, $check_in, $check_out, $check_in]);
        $is_booked = $check_stmt->fetchColumn();

        if ($is_booked > 0) {
            $message = "<div class='alert alert-warning'>Sorry! This room is not available for selected dates.</div>";
        } else {
            // Get room price
            $room_stmt = $pdo->prepare("SELECT price, room_type FROM rooms WHERE id = ?");
            $room_stmt->execute([$room_id]);
            $room = $room_stmt->fetch();

            $days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
            $total_amount = $room['price'] * $days;

            // Insert booking
            $insert = $pdo->prepare("
                INSERT INTO bookings (user_id, room_id, check_in, check_out, guests, total_amount, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");

            if ($insert->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out, $guests, $total_amount])) {
                $message = "<div class='alert alert-success'>Booking Request Submitted Successfully!<br>
                            <strong>Room:</strong> " . htmlspecialchars($room['room_type']) . "<br>
                            <strong>Total Amount:</strong> ₹ " . number_format($total_amount, 2) . "</div>";
            } else {
                $message = "<div class='alert alert-danger'>Failed to book. Try again!</div>";
            }
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h4>Make a New Booking</h4>
                </div>
                <div class="card-body">
                    <?= $message ?>

                    <form method="POST" id="bookingForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Room</label>
                                <select name="room_id" class="form-select" required>
                                    <option value="">-- Choose Room --</option>
                                    <?php foreach($rooms as $room): ?>
                                        <option value="<?= $room['id'] ?>" <?= ($selected_room && $selected_room['id'] == $room['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($room['room_type']) ?> (#<?= htmlspecialchars($room['room_number']) ?>) - ₹<?= number_format($room['price']) ?>/night
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Number of Guests</label>
                                <input type="number" name="guests" class="form-control" min="1" max="10" value="2" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-In Date</label>
                                <input type="date" name="check_in" id="check_in" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Check-Out Date</label>
                                <input type="date" name="check_out" id="check_out" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-danger btn-lg px-5">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple client-side date validation
document.getElementById('check_out').addEventListener('change', function() {
    let checkIn = document.getElementById('check_in').value;
    if (checkIn && this.value <= checkIn) {
        alert("Check-out date must be after check-in date!");
        this.value = "";
    }
});
</script>

<?php include 'includes/footer.php'; ?>