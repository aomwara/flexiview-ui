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
    $sum_distance_correct = 0;
    $sum_all_correct = 0;
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
                <th>All Correct</th>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sum_neck_correct += $row['neck_status'] == "Correct" ? 1 : 0;
                    $sum_back_correct += $row['back_status'] == "Correct" ? 1 : 0;
                    $sum_shoulder_correct += $row['shoulder_status'] == "Correct" ? 1 : 0;
                    $sum_distance_correct += $row['distance_status'] == "correct distance!" ? 1 : 0;
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
                <td><?php
                            if ($row['neck_status'] == "Correct" && $row['back_status'] == "Correct" && $row['shoulder_status'] == "Correct" && $row['distance_status'] == "correct distance!") {
                                echo "<font color='green'>correct</font>";
                                $sum_all_correct += 1;
                            } else {
                                echo "<font color='red'>incorrect</font>";
                            }

                            ?></td>
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
<div>sum_distance_correct => <?php echo $sum_distance_correct; ?></div>
<br />
<br />

<div class="container mt-4">
    <div class="row">
        <!-- Individual Stats Cards -->
        <div class="col-md-3 mb-4">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">Neck Posture</h5>
                    <h2 class="card-text text-primary">
                        <?php echo round($sum_neck_correct / $stmt->rowCount() * 100, 1) ?>%</h2>
                    <p class="card-text text-muted">Correct: <?php echo $sum_neck_correct; ?> times</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <h5 class="card-title">Back Posture</h5>
                    <h2 class="card-text text-success">
                        <?php echo round($sum_back_correct / $stmt->rowCount() * 100, 1) ?>%</h2>
                    <p class="card-text text-muted">Correct: <?php echo $sum_back_correct; ?> times</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <h5 class="card-title">Shoulder Posture</h5>
                    <h2 class="card-text text-info">
                        <?php echo round($sum_shoulder_correct / $stmt->rowCount() * 100, 1) ?>%</h2>
                    <p class="card-text text-muted">Correct: <?php echo $sum_shoulder_correct; ?> times</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title">Distance</h5>
                    <h2 class="card-text text-warning">
                        <?php echo round($sum_distance_correct / $stmt->rowCount() * 100, 1) ?>%</h2>
                    <p class="card-text text-muted">Correct: <?php echo $sum_distance_correct; ?> times</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Stats Card -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-dark">
                <div class="card-body text-center">
                    <h5 class="card-title">Overall Posture Score</h5>
                    <h2
                        class="card-text <?php echo ($sum_all_correct / $stmt->rowCount() * 100 > 70) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo round($sum_all_correct / $stmt->rowCount() * 100, 1) ?>%
                    </h2>
                    <p class="card-text text-muted">All metrics correct: <?php echo $sum_all_correct; ?> times</p>
                </div>
            </div>
        </div>
    </div>
</div>

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