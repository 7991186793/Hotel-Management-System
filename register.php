<?php 
include 'includes/header.php'; 
include 'config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone    = trim($_POST['phone']);

    // Check if email already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "<div class='alert alert-danger'>Email already registered!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
        if ($stmt->execute([$name, $email, $password, $phone])) {
            $message = "<div class='alert alert-success'>Registration Successful! <a href='login.php'>Login Now</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>Something went wrong!</div>";
        }
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4>Create New Account</h4>
                </div>
                <div class="card-body">
                    <?= $message ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" value="1234" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <div class="text-center mt-3">
                        <small>Already have an account? <a href="login.php">Login Here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>