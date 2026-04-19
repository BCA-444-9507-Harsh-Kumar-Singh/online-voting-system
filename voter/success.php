<?php
session_start();
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}
$base_url = "../";
include "../config/db.php";
include "../includes/header.php";
?>

<div class="container"
    style="min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
    <div class="card card-premium"
        style="max-width: 500px; text-align: center; padding: 4rem 3rem; border-radius: 2rem;">
        <div
            style="width: 100px; height: 100px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 2.5rem; box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.4);">
            <i class="fas fa-check"></i>
        </div>

        <h1 style="font-size: 2.25rem; margin-bottom: 1rem;">Vote Recorded!</h1>
        <p class="text-muted" style="font-size: 1.125rem; margin-bottom: 2.5rem;">
            Thank you for participating. Your vote has been securely cast and encrypted in the system.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="dashboard.php" class="btn btn-primary"
                style="width: 100%; border-radius: 1rem; padding: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fas fa-th-large"></i> Back to Dashboard
            </a>
            <a href="../logout.php"
                style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; margin-top: 0.5rem;">
                Logout Securely
            </a>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>