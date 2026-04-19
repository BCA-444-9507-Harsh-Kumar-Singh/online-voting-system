<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get latest inactive election
if (!isset($_GET['election_id'])) {
    echo "No election selected.";
    exit;
}

$election_id = $_GET['election_id'];

$election_q = mysqli_prepare(
    $conn,
    "SELECT * FROM elections WHERE election_id = ? AND status = 'inactive'"
);
mysqli_stmt_bind_param($election_q, "i", $election_id);
mysqli_stmt_execute($election_q);
$election = mysqli_fetch_assoc(mysqli_stmt_get_result($election_q));

if (!$election) {
    echo "Invalid or active election.";
    exit;
}


// Fetch final results
$result_q = mysqli_prepare(
    $conn,
    "SELECT c.name, c.party, COUNT(v.vote_id) AS votes
     FROM candidates c
     LEFT JOIN votes v ON c.candidate_id = v.candidate_id
     WHERE c.election_id = ?
     GROUP BY c.candidate_id
     ORDER BY votes DESC"
);
mysqli_stmt_bind_param($result_q, "i", $election_id);
mysqli_stmt_execute($result_q);
$results = mysqli_stmt_get_result($result_q);
?>

<h2>Final Election Results</h2>

<h3><?php echo $election['title']; ?></h3>

<table border="1" cellpadding="8">
    <tr>
        <th>Candidate</th>
        <th>Party</th>
        <th>Total Votes</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($results)) { ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['party']; ?></td>
            <td><?php echo $row['votes']; ?></td>
        </tr>
    <?php } ?>
</table>

<a href="dashboard.php">Back to Dashboard</a>
