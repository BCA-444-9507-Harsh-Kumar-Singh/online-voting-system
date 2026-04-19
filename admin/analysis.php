<?php
session_start();
include "../config/db.php";
include "../includes/check-elections.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";

if (!isset($_GET['election_id'])) {
    header("Location: analysis-list.php");
    exit;
}

$election_id = $_GET['election_id'];

// Fetch election
$election_q = mysqli_prepare(
    $conn,
    "SELECT title, end_date FROM elections WHERE election_id = ?"
);
mysqli_stmt_bind_param($election_q, "i", $election_id);
mysqli_stmt_execute($election_q);
$election = mysqli_fetch_assoc(mysqli_stmt_get_result($election_q));

if (!$election) {
    echo "Invalid election.";
    exit;
}

// Total registered voters
$total_registered = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM voters")
)['total'];

// Total approved voters
$total_approved = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM voters WHERE status = 'approved'"
    )
)['total'];

// Total votes cast for this election
$votes_q = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM votes WHERE election_id = ?"
);
mysqli_stmt_bind_param($votes_q, "i", $election_id);
mysqli_stmt_execute($votes_q);
$total_votes = mysqli_fetch_assoc(
    mysqli_stmt_get_result($votes_q)
)['total'];

// Turnout percentage
$turnout = ($total_approved > 0)
    ? round(($total_votes / $total_approved) * 100, 2)
    : 0;

// Fetch candidate performance
$candidates_q = mysqli_prepare(
    $conn,
    "SELECT c.name, c.party, COUNT(v.vote_id) as total_votes 
     FROM candidates c 
     LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
     WHERE c.election_id = ? 
     GROUP BY c.candidate_id 
     ORDER BY total_votes DESC"
);
mysqli_stmt_bind_param($candidates_q, "i", $election_id);
mysqli_stmt_execute($candidates_q);
$candidates_res = mysqli_stmt_get_result($candidates_q);

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3rem;">
        <a href="analysis-list.php"
            style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Election List
        </a>
        <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem;">Election Analytics</h1>
        <p class="text-muted" style="font-size: 1.125rem;">Participation metrics for
            <strong><?php echo htmlspecialchars($election['title']); ?></strong>.
            <?php if ($election['end_date'] && $election['end_date'] !== '0000-00-00 00:00:00') { ?>
                &bull; Ended on <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
            <?php } ?>
        </p>
    </div>

    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <!-- Registered Voters Card -->
        <div class="card card-premium slate" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Registered
                        Voters</span>
                    <h3 style="margin: 0; font-size: 2.25rem; font-weight: 800;"><?php echo $total_registered; ?></h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem; font-weight: 500;">
                        <i class="fas fa-info-circle"></i> Total users in the system
                    </p>
                </div>
                <div
                    style="background: rgba(79, 70, 229, 0.1); color: var(--primary); width: 48px; height: 48px; font-size: 1.25rem; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
        </div>

        <!-- Eligible Voters Card -->
        <div class="card card-premium emerald" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Eligible
                        Voters</span>
                    <h3 style="margin: 0; font-size: 2.25rem; font-weight: 800;"><?php echo $total_approved; ?></h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem; font-weight: 500;">
                        <i class="fas fa-user-check"></i> Approved for participation
                    </p>
                </div>
                <div
                    style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 48px; height: 48px; font-size: 1.25rem; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
        </div>

        <!-- Votes Cast Card -->
        <div class="card card-premium amber" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">Votes
                        Cast</span>
                    <h3 style="margin: 0; font-size: 2.25rem; font-weight: 800;"><?php echo $total_votes; ?></h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem; font-weight: 500;">
                        <i class="fas fa-vote-yea"></i> Ballots successfully submitted
                    </p>
                </div>
                <div
                    style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; width: 48px; height: 48px; font-size: 1.25rem; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-box-archive"></i>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2.5rem; align-items: start;">
        <!-- Candidates Performance (Left) -->
        <div class="card card-premium indigo" style="padding: 2.5rem; border-radius: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div
                    style="width: 44px; height: 44px; background: rgba(79, 70, 229, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <i class="fas fa-trophy" style="font-size: 1.25rem;"></i>
                </div>
                <h3 style="font-size: 1.5rem; margin: 0; font-weight: 800; color: var(--text-main);">Candidate
                    Performance</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (mysqli_num_rows($candidates_res) > 0) {
                    while ($cand = mysqli_fetch_assoc($candidates_res)) { ?>
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 1.25rem; background: #fff; border: 1px solid var(--border); border-radius: 1.25rem; transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div
                                    style="width: 48px; height: 48px; background: #f8fafc; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-weight: 800; border: 1px solid var(--border);">
                                    <?php echo strtoupper(substr($cand['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--text-main);">
                                        <?php echo htmlspecialchars($cand['name']); ?></h4>
                                    <span
                                        style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 0.3rem; margin-top: 0.2rem;">
                                        <i class="fas fa-flag"
                                            style="font-size: 0.75rem; color: var(--primary); opacity: 0.6;"></i>
                                        <?php echo htmlspecialchars($cand['party'] ?: 'Independent'); ?>
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span
                                    style="display: block; font-size: 1.25rem; font-weight: 800; color: var(--primary);"><?php echo $cand['total_votes']; ?></span>
                                <span
                                    style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Votes
                                    Received</span>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div
                        style="text-align: center; padding: 2rem; background: #fcfcfd; border-radius: 1rem; border: 1px dashed var(--border);">
                        <p class="text-muted" style="margin: 0;">No candidates registered for this election yet.</p>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Voter Turnout (Right) -->
        <div class="card card-premium emerald" style="padding: 2.5rem; border-radius: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
                <div
                    style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <i class="fas fa-chart-pie" style="font-size: 1.25rem;"></i>
                </div>
                <h3 style="font-size: 1.5rem; margin: 0; font-weight: 800; color: var(--text-main);">Voter Turnout</h3>
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="position: relative; width: 160px; height: 160px; margin-bottom: 2rem;">
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#f1f5f9" stroke-width="2.5" />
                        <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none" stroke="#10b981" stroke-width="2.8" stroke-linecap="round"
                            stroke-dasharray="<?php echo $turnout; ?>, 100"
                            style="transition: stroke-dasharray 1.5s ease;" />
                    </svg>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <h2 style="margin: 0; font-size: 2.25rem; font-weight: 900; color: var(--text-main);">
                            <?php echo $turnout; ?><span style="font-size: 1rem; opacity: 0.5;">%</span></h2>
                    </div>
                </div>

                <div
                    style="width: 100%; padding-top: 1.5rem; border-top: 1px solid var(--border); display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <span
                            style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Participation</span>
                        <span
                            style="font-weight: 700; color: var(--text-main); font-size: 0.9rem;"><?php echo $total_votes; ?>
                            / <?php echo $total_approved; ?></span>
                    </div>
                    <div>
                        <span
                            style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Engagement</span>
                        <span
                            style="font-weight: 700; color: #10b981; font-size: 0.9rem;"><?php echo $turnout > 50 ? 'Strong' : 'Average'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>