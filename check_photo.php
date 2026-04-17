<?php
include 'config/db.php';
$res = mysqli_query($conn, "SELECT photo FROM candidates WHERE photo IS NOT NULL AND photo != '' LIMIT 1");
print_r(mysqli_fetch_assoc($res));
?>