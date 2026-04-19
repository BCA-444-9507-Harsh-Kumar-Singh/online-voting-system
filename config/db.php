<?php
// Formatting helper for election durations
function format_duration($minutes)
{
    if (!$minutes)
        return "0 min";
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    $out = "";
    if ($h > 0)
        $out .= $h . "hr ";
    if ($m > 0 || $h == 0)
        $out .= $m . "min";
    return trim($out);
}
$host = "localhost";
$user = "root";
$pass = "";
$db = "online_voting";

$conn = mysqli_connect($host, $user, $pass, $db, 3307);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>