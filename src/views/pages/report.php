<?php
include_once 'config/database.php';

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Query to fetch data from the 'data_user' table
$query = "SELECT 
            timestamp, 
            Time_Neck_Incorrect, 
            Time_Back_Incorrect, 
            Time_Shoulder_Incorrect, 
            neck_status, 
            back_status, 
            shoulder_status, 
            distance_status, 
            total_correct_time, 
            total_incorrect_time 
          FROM data_user where user_id = $_SESSION[user_id]";

$stmt = $db->prepare($query);
$stmt->execute();

// Check if records were found
if ($stmt->rowCount() > 0) {
    echo '<div class="container-fluid p-4">';
    echo '<h2>Report</h2>';
    echo '<p>This is your report from AI camera</p>';

    // Display table header
    echo '<table class="table table-bordered">';
    echo '<thead><tr>';
    echo '<th>Timestamp</th>';
    echo '<th>Time Neck Incorrect</th>';
    echo '<th>Time Back Incorrect</th>';
    echo '<th>Time Shoulder Incorrect</th>';
    echo '<th>Neck Status</th>';
    echo '<th>Back Status</th>';
    echo '<th>Shoulder Status</th>';
    echo '<th>Distance Status</th>';
    echo '<th>Total Correct Time</th>';
    echo '<th>Total Incorrect Time</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    // Fetch and display each row of data
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['timestamp']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Time_Neck_Incorrect']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Time_Back_Incorrect']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Time_Shoulder_Incorrect']) . '</td>';
        echo '<td>' . htmlspecialchars($row['neck_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['back_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['shoulder_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['distance_status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['total_correct_time']) . '</td>';
        echo '<td>' . htmlspecialchars($row['total_incorrect_time']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<div class="container-fluid p-4">';
    echo '<h2>Report</h2>';
    echo '<p>No data available in the database.</p>';
    echo '</div>';
}
