<?php 
include 'includes/header.php'; 
include 'config/db.php';
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Our All Rooms</h2>
    
    <div class="row">
        <?php
        $stmt = $pdo->query("SELECT * FROM rooms WHERE status = 'available'");
        while($room = $stmt->fetch()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card card-room h-100">
                <?php if(!empty($room['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($room['image']) ?>" class="card-img-top room-img" alt="">
                <?php else: ?>
                    <img src="assets/images/hotel.jpg" class="card-img-top room-img" alt="">
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h5><?= htmlspecialchars($room['room_type']) ?> (#<?= htmlspecialchars($room['room_number']) ?>)</h5>
                    <p class="fs-4 text-danger fw-bold">₹ <?= number_format($room['price']) ?> / night</p>
                    <p><strong>Capacity:</strong> <?= $room['capacity'] ?> Persons</p>
                    <p class="flex-grow-1"><?= htmlspecialchars($room['description']) ?></p>
                    <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-danger">Book This Room</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>