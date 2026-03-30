<?php 
include '../includes/header.php'; 
include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Statistics
$total_rooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$confirmed_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();

?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard</h2>
        <div>
            Welcome, <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong> | 
            <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Rooms</h5>
                    <h2><?= $total_rooms ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5>Total Bookings</h5>
                    <h2><?= $total_bookings ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Pending Bookings</h5>
                    <h2><?= $pending_bookings ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Confirmed</h5>
                    <h2><?= $confirmed_bookings ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <a href="rooms.php" class="btn btn-primary btn-lg w-100 py-3 mb-3">
                Manage Rooms
            </a>
        </div>
        <div class="col-md-6">
            <a href="bookings.php" class="btn btn-success btn-lg w-100 py-3 mb-3">
                Manage Bookings
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>