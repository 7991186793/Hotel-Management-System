<?php 
include 'includes/header.php'; 
include 'config/db.php';
?>

<!-- Hero Section -->
<div class="hero text-center">
    <div class="container">
        <h1 class="display-3 fw-bold">Welcome to Grand Palace Hotel</h1>
        <p class="lead mb-4">Experience Luxury & Comfort at Affordable Prices</p>
        <a href="rooms.php" class="btn btn-danger btn-lg px-5">Browse Rooms & Book Now</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-5">Our Popular Rooms</h2>
    
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM rooms LIMIT 3");
        while($room = $stmt->fetch()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card card-room h-100">
                <?php if(!empty($room['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($room['image']) ?>" class="card-img-top room-img" alt="">
                <?php else: ?>
                    <img src="assets/images/hotel.jpeg" class="card-img-top room-img" alt="Room">
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($room['room_type']) ?> - Room <?= htmlspecialchars($room['room_number']) ?></h5>
                    <p class="text-success fw-bold fs-4">₹ <?= number_format($room['price'], 2) ?> / night</p>
                    <p class="card-text"><?= substr(htmlspecialchars($room['description'] ?? ''), 0, 80) ?>...</p>
                    <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-book text-white mt-auto">Book Now</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>