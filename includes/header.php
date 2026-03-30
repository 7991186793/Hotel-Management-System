<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grand Palace Hotel - Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="fas fa-hotel"></i> Grand Palace
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php"><i class="fas fa-bed"></i> Rooms</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php"><i class="fas fa-calendar-check"></i> Book Now</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="mybookings.php"><i class="fas fa-list"></i> My Bookings</a></li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>