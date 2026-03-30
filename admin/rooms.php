<?php 
include '../includes/header.php'; 
include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Handle Image Upload
function uploadImage($file) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_name = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($file["size"] > 5000000) return false; // 5MB limit
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) return false;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $file_name;
    }
    return false;
}

// Add New Room
if (isset($_POST['add_room'])) {
    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = (float)$_POST['price'];
    $capacity    = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $status      = $_POST['status'];

    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
    }

    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, price, capacity, description, image, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$room_number, $room_type, $price, $capacity, $description, $image, $status])) {
        $message = "<div class='alert alert-success'>New Room Added Successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to add room!</div>";
    }
}

// Update Room
if (isset($_POST['update_room'])) {
    $id = (int)$_POST['room_id'];
    $room_number = trim($_POST['room_number']);
    $room_type   = trim($_POST['room_type']);
    $price       = (float)$_POST['price'];
    $capacity    = (int)$_POST['capacity'];
    $description = trim($_POST['description']);
    $status      = $_POST['status'];

    $image_sql = "";
    $params = [$room_number, $room_type, $price, $capacity, $description, $status, $id];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $new_image = uploadImage($_FILES['image']);
        if ($new_image) {
            $image_sql = ", image = ?";
            $params = [$room_number, $room_type, $price, $capacity, $description, $new_image, $status, $id];
        }
    }

    $stmt = $pdo->prepare("UPDATE rooms SET room_number=?, room_type=?, price=?, capacity=?, description=?, status=? $image_sql WHERE id=?");
    if ($stmt->execute($params)) {
        $message = "<div class='alert alert-success'>Room Updated Successfully!</div>";
    }
}

// Delete Room
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "<div class='alert alert-success'>Room Deleted Successfully!</div>";
    }
}

// Fetch all rooms
$stmt = $pdo->query("SELECT * FROM rooms ORDER BY room_number");
$rooms = $stmt->fetchAll();

// For Edit - Get single room if edit id passed
$edit_room = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_room = $stmt->fetch();
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Rooms</h2>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <?= $message ?>

    <!-- Add / Edit Room Form -->
    <div class="card mb-5">
        <div class="card-header bg-primary text-white">
            <h5><?= $edit_room ? 'Edit Room' : 'Add New Room' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if($edit_room): ?>
                    <input type="hidden" name="room_id" value="<?= $edit_room['id'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="form-control" 
                               value="<?= $edit_room ? htmlspecialchars($edit_room['room_number']) : '' ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Room Type</label>
                        <input type="text" name="room_type" class="form-control" 
                               value="<?= $edit_room ? htmlspecialchars($edit_room['room_type']) : '' ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Price per Night (₹)</label>
                        <input type="number" name="price" class="form-control" step="0.01" 
                               value="<?= $edit_room ? $edit_room['price'] : '' ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Capacity</label>
                        <input type="number" name="capacity" class="form-control" min="1" 
                               value="<?= $edit_room ? $edit_room['capacity'] : '' ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= $edit_room ? htmlspecialchars($edit_room['description']) : '' ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="available" <?= ($edit_room && $edit_room['status']=='available') ? 'selected' : '' ?>>Available</option>
                            <option value="booked" <?= ($edit_room && $edit_room['status']=='booked') ? 'selected' : '' ?>>Booked</option>
                            <option value="maintenance" <?= ($edit_room && $edit_room['status']=='maintenance') ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Room Image (Optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if($edit_room && $edit_room['image']): ?>
                            <small>Current Image: <img src="../uploads/<?= htmlspecialchars($edit_room['image']) ?>" width="80" height="60" style="object-fit:cover;"></small>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" name="<?= $edit_room ? 'update_room' : 'add_room' ?>" class="btn btn-success w-100">
                    <?= $edit_room ? 'Update Room' : 'Add Room' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- All Rooms Table -->
    <h4>All Rooms (<?= count($rooms) ?>)</h4>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Room No.</th>
                <th>Type</th>
                <th>Price/Night</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rooms as $room): ?>
            <tr>
                <td>
                    <?php if($room['image']): ?>
                        <img src="../uploads/<?= htmlspecialchars($room['image']) ?>" width="80" height="60" style="object-fit:cover;">
                    <?php else: ?>
                        <span class="text-muted">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($room['room_number']) ?></td>
                <td><?= htmlspecialchars($room['room_type']) ?></td>
                <td>₹ <?= number_format($room['price'], 2) ?></td>
                <td><?= $room['capacity'] ?></td>
                <td>
                    <span class="badge bg-<?= $room['status']=='available' ? 'success' : ($room['status']=='booked' ? 'warning' : 'secondary') ?>">
                        <?= ucfirst($room['status']) ?>
                    </span>
                </td>
                <td>
                    <a href="?edit=<?= $room['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $room['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this room?')">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>