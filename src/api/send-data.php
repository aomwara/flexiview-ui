<?php
include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();
$user_id = 1;

$timestamp = date('d-m-y H:i:s');
$Time_Neck_Incorrect = $_GET['Time_Neck_Incorrect'];
$Time_Back_Incorrect = $_GET['Time_Back_Incorrect'];
$Time_Shoulder_Incorrect = $_GET['Time_Shoulder_Incorrect'];
$neck_status = $_GET['neck_status'];
$back_status = $_GET['back_status'];
$shoulder_status = $_GET['shoulder_status'];
$distance_status = $_GET['distance_status'];
$total_correct_time     = $_GET['total_correct_time'];
$total_incorrect_time = $_GET['total_incorrect_time'];


try {
    // Build the update query
    if (!empty($Time_Neck_Incorrect)) {
        echo "ok";
        $query = "INSERT INTO data_user (user_id, timestamp, Time_Neck_Incorrect, Time_Back_Incorrect, Time_Shoulder_Incorrect, 
                        neck_status, back_status, shoulder_status, distance_status, 
                        total_correct_time, total_incorrect_time) 
                    VALUES (:user_id, :timestamp, :Time_Neck_Incorrect, :Time_Back_Incorrect, :Time_Shoulder_Incorrect,
                        :neck_status, :back_status, :shoulder_status, :distance_status,
                        :total_correct_time, :total_incorrect_time)";
        $stmt = $db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $timestamp = date('d-m-y H:i:s');
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':Time_Neck_Incorrect', $Time_Neck_Incorrect);
        $stmt->bindParam(':Time_Back_Incorrect', $Time_Back_Incorrect);
        $stmt->bindParam(':Time_Shoulder_Incorrect', $Time_Shoulder_Incorrect);
        $stmt->bindParam(':neck_status', $neck_status);
        $stmt->bindParam(':back_status', $back_status);
        $stmt->bindParam(':shoulder_status', $shoulder_status);
        $stmt->bindParam(':distance_status', $distance_status);
        $stmt->bindParam(':total_correct_time', $total_correct_time);
        $stmt->bindParam(':total_incorrect_time', $total_incorrect_time);
    }

    // Execute the update
    if ($stmt->execute()) {
        $_SESSION['success'] = "add  successfully!";
    } else {
        $_SESSION['error'] = "Failed to add information.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
