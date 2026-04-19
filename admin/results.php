<?php
session_start();
include "../config/db.php";
include "../includes/check-elections.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";

// Get active election
$election_q = mysqli_query(
    $conn,
    "SELECT * FROM elections WHERE status = 'active' LIMIT 1"
);
$election = mysqli_fetch_assoc($election_q);

if (!$election) {
    include "../includes/header.php";
    ?>
    <div class="container" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
        <div class="card card-premium" style="text-align: center; padding: 4rem; max-width: 500px; border-radius: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 2rem;">
                <i class="fas fa-calendar-xmark"></i>
            </div>
            <h2 class="text-muted" style="margin-bottom: 1rem;">No Active Election</h2>
            <p class="text-muted" style="margin-bottom: 2rem;">There is currently no live election to monitor. You can start one from the dashboard.</p>
            <a href="dashboard.php" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 1rem;">
                <i class="fas fa-th-large"></i> Back to Dashboard
            </a>
        </div>
    </div>
    <?php
    include "../includes/footer.php";
    exit;
}

$election_id = $election['election_id'];

// Total votes cast
$total_votes_q = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM votes WHERE election_id = ?"
);
mysqli_stmt_bind_param($total_votes_q, "i", $election_id);
mysqli_stmt_execute($total_votes_q);
$total_votes = mysqli_fetch_assoc(
    mysqli_stmt_get_result($total_votes_q)
)['total'];

// Candidate-wise votes
$candidate_q = mysqli_prepare(
    $conn,
    "SELECT c.name, c.party, c.photo, COUNT(v.vote_id) AS votes
     FROM candidates c
     LEFT JOIN votes v ON c.candidate_id = v.candidate_id
     WHERE c.election_id = ?
     GROUP BY c.candidate_id
     ORDER BY votes DESC"
);
mysqli_stmt_bind_param($candidate_q, "i", $election_id);
mysqli_stmt_execute($candidate_q);
$candidates_res = mysqli_stmt_get_result($candidate_q);

$candidates = [];
while($row = mysqli_fetch_assoc($candidates_res)) {
    $candidates[] = $row;
}

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; flex-wrap: wrap;">
        <div>
            <a href="dashboard.php"
                style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem;">Live Results</h1>
            <p class="text-muted" style="font-size: 1.125rem;">Monitoring: <strong><?php echo htmlspecialchars($election['title']); ?></strong></p>
        </div>
        <div class="card card-premium" style="padding: 1rem 2rem; display: inline-flex; align-items: center; gap: 1.5rem; border-radius: 1.5rem;">
            <div style="text-align: right;">
                <span class="text-muted" style="font-size: 0.75rem; display: block; font-weight: 700; text-transform: uppercase;">Total Ballots</span>
                <h2 style="margin: 0; color: var(--primary); font-size: 2rem; font-weight: 800;"><?php echo $total_votes; ?></h2>
            </div>
            <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
    </div>

    <div class="card card-premium indigo" style="padding: 2.5rem; border-radius: 2rem; margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Election Leaderboard</h2>
                <p class="text-muted" style="font-size: 0.875rem;">Real-time candidate standings based on current votes.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1.25rem; background: #f8fafc; border-radius: 2rem; border: 1px solid var(--border); color: #64748b; font-size: 0.875rem; font-weight: 600;">
                <span class="pulse-dot"></span> Live Tracking
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <?php 
            $rank = 1;
            foreach ($candidates as $row) { 
                $percentage = ($total_votes > 0) ? round(($row['votes'] / $total_votes) * 100, 1) : 0;
                $photo_path = (!empty($row['photo'])) ? "../assets/images/candidates/" . $row['photo'] : null;
            ?>
                <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                    <div style="width: 40px; font-size: 1.5rem; font-weight: 900; color: <?php echo $rank == 1 ? 'var(--primary)' : 'var(--text-muted)'; ?>; opacity: <?php echo 1.1 - ($rank * 0.1); ?>;">
                        #<?php echo $rank++; ?>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1.25rem; min-width: 250px; flex: 1;">
                        <?php if ($photo_path && file_exists($photo_path)) { ?>
                            <img src="<?php echo $photo_path; ?>" style="width: 60px; height: 60px; border-radius: 12px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        <?php } else { ?>
                            <div class="avatar-circle" style="width: 60px; height: 60px; font-size: 1.5rem; border-radius: 12px; flex-shrink: 0;">
                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                            </div>
                        <?php } ?>
                        <div>
                            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 700;"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <span class="text-muted" style="font-size: 0.875rem; font-weight: 500;"><i class="fas fa-users" style="font-size: 0.75rem; margin-right: 0.25rem;"></i> <?php echo htmlspecialchars($row['party'] ?: 'Independent'); ?></span>
                        </div>
                    </div>

                    <div style="flex: 3; min-width: 300px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600;">
                            <span style="color: var(--text-main);"><?php echo $row['votes']; ?> Votes</span>
                            <span style="color: var(--primary); font-weight: 700;"><?php echo $percentage; ?>%</span>
                        </div>
                        <div style="height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden; border: 1px solid rgba(0,0,0,0.02);">
                            <div style="height: 100%; width: <?php echo $percentage; ?>%; background: <?php echo $rank == 2 ? 'linear-gradient(90deg, var(--primary), #818cf8)' : '#94a3b8'; ?>; border-radius: 6px; transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="card card-premium" style="text-align: center; padding: 2rem; max-width: 450px; margin: 0 auto; border-radius: 1.5rem; border: 1px dashed var(--border); box-shadow: none;">
        <p class="text-muted" style="margin: 0; font-size: 0.875rem; font-weight: 500;">
            <i class="fas fa-sync-alt fa-spin" style="margin-right: 0.5rem; color: var(--primary);"></i>
            Auto-refreshing live data every 10 seconds.
        </p>
    </div>
</div>

<style>
    .pulse-dot {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<script>
    setTimeout(() => {
        location.reload();
    }, 10000);
</script>

<?php include "../includes/footer.php"; ?>
