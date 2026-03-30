<?php 
include 'includes/header.php'; 
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's bookings with room details
$stmt = $pdo->prepare("
    SELECT b.*, r.room_number, r.room_type, r.price 
    FROM bookings b 
    LEFT JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

$message = "";
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $booking_id = (int)$_GET['cancel'];
    
    // Only allow cancel if status is pending
    $check = $pdo->prepare("SELECT status FROM bookings WHERE id = ? AND user_id = ?");
    $check->execute([$booking_id, $user_id]);
    $booking = $check->fetch();
    
    if ($booking && $booking['status'] == 'pending') {
        $update = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        if ($update->execute([$booking_id])) {
            $message = "<div class='alert alert-success'>Booking Cancelled Successfully!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>You can only cancel pending bookings.</div>";
    }
}
?>

<div class="container my-5">
    <h2 class="mb-4">My Bookings</h2>

    <?= $message ?>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info text-center">
            You have no bookings yet.<br>
            <a href="rooms.php" class="btn btn-primary mt-3">Browse Rooms & Book Now</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Room Details</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Guests</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Booking Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bookings as $booking): ?>
                    <tr>
                        <td><strong>#<?= $booking['id'] ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($booking['room_type']) ?></strong><br>
                            <small>Room #<?= htmlspecialchars($booking['room_number']) ?></small>
                        </td>
                        <td><?= date('d M, Y', strtotime($booking['check_in'])) ?></td>
                        <td><?= date('d M, Y', strtotime($booking['check_out'])) ?></td>
                        <td><?= $booking['guests'] ?></td>
                        <td class="fw-bold text-success">₹ <?= number_format($booking['total_amount'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $booking['status'] == 'confirmed' ? 'success' : 
                                ($booking['status'] == 'pending' ? 'warning' : 
                                ($booking['status'] == 'checked_in' ? 'info' : 
                                ($booking['status'] == 'checked_out' ? 'secondary' : 'danger'))) ?>">
                                <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                            </span>
                        </td>
                        <td><?= date('d M, Y', strtotime($booking['booking_date'])) ?></td>
                        <td>
                            <?php if($booking['status'] == 'pending'): ?>
                                <a href="?cancel=<?= $booking['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Cancel this booking?')">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No Action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>