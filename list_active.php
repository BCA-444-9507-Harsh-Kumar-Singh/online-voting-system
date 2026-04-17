<?php
include 'config/db.php';
$res = mysqli_query($conn, "SELECT * FROM elections WHERE status = 'active'");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>