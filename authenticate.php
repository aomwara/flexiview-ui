<?php
session_start();
include_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email and password from POST request
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password with MD5
    $hashedPassword = md5($password);

    // Database connection
    $database = new Database();
    $db = $database->getConnection();

    try {
        // Prepare SQL statement
        $query = "SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1";
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute query
        $stmt->execute();

        // Check if any record matched
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set session variables or redirect to dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: main.php");
            exit;
        } else {
            // Invalid credentials
            $_SESSION['error'] = "Invalid email or password";
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect if accessed without POST request
    header("Location: index.php");
    exit;
}
