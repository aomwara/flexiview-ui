<?php
include '../session.php';
include_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user_id = $_SESSION['user_id'];

    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Build the update query
        if (!empty($password)) {
            // If a new password is provided, hash it and include it in the update
            $hashedPassword = md5($password);
            $query = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            // If no password change, update only name and email
            $query = "UPDATE users SET name = :name, email = :email WHERE id = :user_id";
            $stmt = $db->prepare($query);
        }

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);

        // Execute the update
        if ($stmt->execute()) {
            $_SESSION['success'] = "Information updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update information.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Redirect back to settings page
    header("Location: /main.php?page=setting&message=success");
    exit;
}
