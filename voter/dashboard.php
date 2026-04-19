<?php
session_start();
include "../config/db.php";
include "../includes/check-elections.php";

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

$voter_id = $_SESSION['voter_id'];
$base_url = "../";

$elections = mysqli_query(
    $conn,
    "SELECT election_id, title FROM elections WHERE status = 'active'"
);

include "../includes/header.php";
?>

<div class="dashboard-hero">
    <div class="container">
        <span
            style="text-transform: uppercase; letter-spacing: 0.15em; font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; display: block;">Voter
            Portal</span>
        <h1>Voter's Dashboard</h1>
        <p class="text-muted">Welcome back! Access active elections and cast your vote securely.</p>

        <div class="hero-voter-card">
            <div
                style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.1); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fas fa-id-card"></i>
            </div>
            <div style="text-align: left;">
                <span
                    style="font-size: 0.75rem; display: block; font-weight: 600; text-transform: uppercase; opacity: 0.8;">Your
                    Voter ID</span>
                <code style="font-size: 1rem; font-family: 'JetBrains Mono', monospace;"><?php echo $voter_id; ?></code>
            </div>
        </div>
    </div>
</div>

<div class="container elections-container">
    <div style="margin-bottom: 3rem; display: flex; align-items: center; gap: 1rem;">
        <div style="height: 1px; flex: 1; background: var(--border);"></div>
        <h2
            style="font-size: 0.875rem; margin-bottom: 0; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 800;">
            Available Elections</h2>
        <div style="height: 1px; flex: 1; background: var(--border);"></div>
    </div>

    <div class="elections-grid">
        <?php
        if (mysqli_num_rows($elections) > 0) {
            while ($e = mysqli_fetch_assoc($elections)) {
                $check = mysqli_prepare($conn, "SELECT vote_id FROM votes WHERE voter_id = ? AND election_id = ?");
                mysqli_stmt_bind_param($check, "si", $voter_id, $e['election_id']);
                mysqli_stmt_execute($check);
                $voted = mysqli_stmt_get_result($check)->num_rows > 0;
                ?>
                <div class="card card-premium"
                    style="padding: 2.5rem; display: flex; flex-direction: column; justify-content: space-between; border-radius: 1.5rem;">
                    <div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                            <div
                                style="width: 56px; height: 56px; background: #EEF2FF; color: #4F46E5; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="fas fa-box-archive"></i>
                            </div>
                            <?php if ($voted) { ?>
                                <span class="badge"
                                    style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 0.5rem 1rem;">
                                    <i class="fas fa-check-circle" style="margin-right: 0.4rem;"></i>Voted
                                </span>
                            <?php } else { ?>
                                <span class="badge badge-active" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-bolt" style="margin-right: 0.4rem;"></i>Live Now
                                </span>
                            <?php } ?>
                        </div>
                        <h3 style="font-size: 1.5rem; line-height: 1.3; margin-bottom: 1rem;"><?php echo $e['title']; ?></h3>
                    </div>

                    <div style="margin-top: 2rem;">
                        <?php if (!$voted) { ?>
                            <a href="vote.php?election_id=<?php echo $e['election_id']; ?>" class="btn btn-primary"
                                style="width: 100%; padding: 1rem; border-radius: 12px; font-size: 1rem;">
                                Cast Your Vote <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
                            </a>
                        <?php } else { ?>
                            <button class="btn"
                                style="width: 100%; padding: 1rem; border-radius: 12px; background: #F8FAF9; color: #94A3B8; cursor: not-allowed; border: 1px solid var(--border);"
                                disabled>
                                Vote Recorded
                            </button>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        } else { ?>
            <div class="card card-premium"
                style="grid-column: 1 / -1; text-align: center; padding: 6rem 2rem; border-radius: 2rem; border: 2px dashed var(--border); background: transparent; box-shadow: none;">
                <div
                    style="width: 80px; height: 80px; background: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--text-muted); margin: 0 auto 2rem; box-shadow: var(--shadow);">
                    <i class="fas fa-calendar-xmark"></i>
                </div>
                <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">No Active Elections</h3>
                <p class="text-muted">There are no ongoing elections at this time. We'll notify you when a new one starts.
                </p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>