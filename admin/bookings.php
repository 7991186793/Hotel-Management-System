<?php 
include '../includes/header.php'; 
include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Update Booking Status
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $booking_id])) {
        $message = "<div class='alert alert-success'>Booking status updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to update status!</div>";
    }
}

// Delete Booking (Optional)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "<div class='alert alert-success'>Booking deleted successfully!</div>";
    }
}

// Fetch all bookings with room and user details
$stmt = $pdo->query("
    SELECT b.*, u.name as customer_name, u.email, 
           r.room_number, r.room_type, r.price 
    FROM bookings b 
    LEFT JOIN users u ON b.user_id = u.id 
    LEFT JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Bookings</h2>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?= $message ?>

    <?php if(empty($bookings)): ?>
        <div class="alert alert-info">No bookings found yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Room</th>
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
                            <?= htmlspecialchars($booking['customer_name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($booking['email']) ?></small>
                        </td>
                        <td>
                            <?= htmlspecialchars($booking['room_type']) ?><br>
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
                        <td><?= date('d M, Y h:i A', strtotime($booking['booking_date'])) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                <select name="status" class="form-select form-select-sm d-inline w-auto">
                                    <option value="pending" <?= $booking['status']=='pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $booking['status']=='confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="checked_in" <?= $booking['status']=='checked_in' ? 'selected' : '' ?>>Checked In</option>
                                    <option value="checked_out" <?= $booking['status']=='checked_out' ? 'selected' : '' ?>>Checked Out</option>
                                    <option value="cancelled" <?= $booking['status']=='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary mt-1">Update</button>
                            </form>
                            <br>
                            <a href="?delete=<?= $booking['id'] ?>" 
                               class="btn btn-sm btn-danger mt-1"
                               onclick="return confirm('Delete this booking?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>