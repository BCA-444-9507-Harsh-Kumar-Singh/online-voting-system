<?php
// This helper script automatically transitions 'active' elections 
// to 'completed' status once their scheduled end_date has passed.

// Ensure db connection is available
if (isset($conn)) {
    // Only update elections that are currently 'active' and have passed their end_date
    // Using MySQL NOW() directly to avoid timezone mismatches between PHP and the database
    $update_sql = "UPDATE elections 
                   SET status = 'completed' 
                   WHERE status = 'active' 
                   AND end_date IS NOT NULL 
                   AND end_date != '0000-00-00 00:00:00' 
                   AND end_date <= NOW()";

    mysqli_query($conn, $update_sql);
}
?>