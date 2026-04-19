<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Activate selected election
if (isset($_GET['id'])) {
    $election_id = $_GET['id'];

    // Move any currently active election to inactive first (if only one should be active at a time)
    // mysqli_query($conn, "UPDATE elections SET status = 'inactive' WHERE status = 'active'");

    // Only allow activating elections that are currently 'inactive' (Drafts)
    // Calculate end_date based on duration_minutes at the moment of activation
    $sql = "UPDATE elections 
            SET status = 'active', 
                end_date = DATE_ADD(NOW(), INTERVAL duration_minutes MINUTE) 
            WHERE election_id = ? AND status = 'inactive'";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $election_id);
    mysqli_stmt_execute($stmt);

    header("Location: dashboard.php");
    exit;
}
