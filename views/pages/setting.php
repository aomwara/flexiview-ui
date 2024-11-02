<?php
include_once 'config/database.php';

if (isset($_GET['message']) && $_GET['message'] == "success") {
    echo '<script>
        swal({
            title: "Success!",
            text: "Your information has been updated successfully.",
            icon: "success",
            button: "OK"
        });
    </script>';
}

// Get user data from database
$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

try {
    $query = "SELECT name, email FROM users WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container-fluid p-4">
    <h2>Settings</h2>
    <p>Update your information</p>

    <form action="/api/update-user.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name"
                value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Leave blank if you don’t want to change your password.</small>
        </div>
        <button type="submit" class="btn btn-primary">Update Information</button>
    </form>
</div>