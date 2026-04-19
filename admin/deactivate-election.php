<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $election_id = $_GET['id'];

    // Only allow deactivating elections that are currently 'active'
    $sql = "UPDATE elections SET status = 'inactive' WHERE election_id = ? AND status = 'active'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $election_id);
    mysqli_stmt_execute($stmt);
}

header("Location: dashboard.php");
exit;
