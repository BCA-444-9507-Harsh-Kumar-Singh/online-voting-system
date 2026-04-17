<?php
include 'config/db.php';
mysqli_query($conn, "UPDATE elections SET status = 'active' WHERE title LIKE '%President%' LIMIT 1");
$res = mysqli_query($conn, "SELECT election_id, title, status FROM elections WHERE status = 'active' LIMIT 1");
print_r(mysqli_fetch_assoc($res));
?>