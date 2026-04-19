<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$result = mysqli_query(
    $conn,
    "SELECT election_id, title, status FROM elections ORDER BY created_at DESC"
);
?>

<h2>Select Election to View Final Results</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Election Title</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'inactive') { ?>
                    <a href="final-results.php?election_id=<?php echo $row['election_id']; ?>">
                        View Results
                    </a>
                <?php } else { ?>
                    Election Active
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

<a href="dashboard.php">Back to Dashboard</a>
