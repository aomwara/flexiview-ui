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
    $sum_neck_correct = 0;
    $sum_back_correct = 0;
    $sum_shoulder_correct = 0;
?>
<!-- Switch to HTML syntax for cleaner structure -->
<div class="container-fluid p-4">
    <h2>Report</h2>
    <p>This is your report from AI camera</p>

    <!-- Add 'id' to the table for DataTables initialization -->
    <table id="reportTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Time Neck Incorrect</th>
                <th>Time Back Incorrect</th>
                <th>Time Shoulder Incorrect</th>
                <th>Neck Status</th>
                <th>Back Status</th>
                <th>Shoulder Status</th>
                <th>Distance Status</th>
                <th>Total Correct Time</th>
                <th>Total Incorrect Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sum_neck_correct += $row['neck_status'] == "Correct" ? 1 : 0;
                    $sum_back_correct += $row['back_status'] == "Correct" ? 1 : 0;
                    $sum_shoulder_correct += $row['shoulder_status'] == "Correct" ? 1 : 0;
                ?>
            <tr>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($row['Time_Neck_Incorrect']); ?></td>
                <td><?php echo htmlspecialchars($row['Time_Back_Incorrect']); ?></td>
                <td><?php echo htmlspecialchars($row['Time_Shoulder_Incorrect']); ?></td>
                <td><?php echo htmlspecialchars($row['neck_status']); ?></td>
                <td><?php echo htmlspecialchars($row['back_status']); ?></td>
                <td><?php echo htmlspecialchars($row['shoulder_status']); ?></td>
                <td><?php echo htmlspecialchars($row['distance_status']); ?></td>
                <td><?php echo htmlspecialchars($row['total_correct_time']); ?></td>
                <td><?php echo htmlspecialchars($row['total_incorrect_time']); ?></td>
            </tr>
            <?php
                }
                ?>
        </tbody>
    </table>
</div>
<div>sum_neck_correct => <?php echo $sum_neck_correct; ?></div>
<div>sum_back_correct => <?php echo $sum_back_correct; ?></div>
<div>sum_shoulder_correct => <?php echo $sum_shoulder_correct; ?></div>

<!-- Add DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js">
</script>

<!-- Initialize DataTable -->
<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [
            [0, 'desc']
        ], // Sort by timestamp (first column) in descending order
        dom: 'Bfrtip',
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
});
</script>
<?php
} else {
    echo '<div class="container-fluid p-4">';
    echo '<h2>Report</h2>';
    echo '<p>No data available in the database.</p>';
    echo '</div>';
}