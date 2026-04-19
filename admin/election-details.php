<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$election_id = $_GET['id'];
$base_url = "../";

// Fetch election details
$election_sql = "SELECT * FROM elections WHERE election_id = ?";
$stmt = mysqli_prepare($conn, $election_sql);
mysqli_stmt_bind_param($stmt, "i", $election_id);
mysqli_stmt_execute($stmt);
$election = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$election) {
    header("Location: dashboard.php");
    exit;
}

// Fetch candidates for this election
$candidates_sql = "SELECT * FROM candidates WHERE election_id = ?";
$stmt_cand = mysqli_prepare($conn, $candidates_sql);
mysqli_stmt_bind_param($stmt_cand, "i", $election_id);
mysqli_stmt_execute($stmt_cand);
$candidates_res = mysqli_stmt_get_result($stmt_cand);

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3.5rem;">
        <a href="dashboard.php"
            style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div
            style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.75rem; font-size: 2.75rem; letter-spacing: -0.025em;">
                    <?php echo htmlspecialchars($election['title']); ?></h1>
                <div style="display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;">
                    <?php if ($election['status'] == 'active') { ?>
                        <span class="badge badge-active"
                            style="padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.8125rem;">
                            <i class="fas fa-circle-play" style="margin-right: 0.5rem;"></i>Active Election
                        </span>
                    <?php } else { ?>
                        <span class="badge badge-inactive"
                            style="padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.8125rem;">
                            <i class="fas fa-circle-check"
                                style="margin-right: 0.5rem;"></i><?php echo strtoupper($election['status']); ?>
                        </span>
                    <?php } ?>

                    <span
                        style="font-size: 0.9375rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="far fa-calendar-alt" style="color: var(--primary);"></i> Created
                        <?php echo date('M d, Y', strtotime($election['created_at'])); ?>
                    </span>

                    <?php if ($election['status'] == 'inactive') { ?>
                        <span
                            style="font-size: 0.9375rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fas fa-clock" style="color: var(--primary);"></i>
                            <?php echo format_duration($election['duration_minutes']); ?>
                        </span>
                    <?php } elseif ($election['end_date'] && $election['end_date'] !== '0000-00-00 00:00:00') { ?>
                        <span
                            style="font-size: 0.9375rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fas fa-calendar-check" style="color: #10b981;"></i> Ends
                            <?php echo date('M d, Y H:i', strtotime($election['end_date'])); ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="add-candidate.php" class="btn btn-secondary"
                    style="background: #fff; border: 1px solid var(--border); color: var(--text-main); font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                    <i class="fas fa-user-plus" style="color: var(--primary);"></i> Add Candidate
                </a>
                <a href="analysis.php?election_id=<?php echo $election_id; ?>" class="btn btn-primary"
                    style="padding: 0.75rem 1.5rem; border-radius: 1rem; font-weight: 600; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2);">
                    <i class="fas fa-chart-line" style="margin-right: 0.5rem;"></i> View Report
                </a>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2.5rem; align-items: start;">
        <!-- Election Info -->
        <div class="card card-premium" style="padding: 2.5rem; border-radius: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div
                    style="width: 44px; height: 44px; background: rgba(79, 70, 229, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <i class="fas fa-file-lines" style="font-size: 1.25rem;"></i>
                </div>
                <h3 style="font-size: 1.5rem; margin: 0; font-weight: 800; color: var(--text-main);">Election Context
                </h3>
            </div>

            <div style="color: var(--text-main); line-height: 1.8; font-size: 1.0625rem;">
                <?php if (!empty($election['description'])) { ?>
                    <p style="margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($election['description']); ?>
                    </p>
                <?php } else { ?>
                    <div
                        style="padding: 2rem; background: #f8fafc; border-radius: 1rem; text-align: center; border: 1px dashed #e2e8f0;">
                        <p class="text-muted" style="font-style: italic; margin: 0;">No specific description provided for
                            this election ballot.</p>
                    </div>
                <?php } ?>
            </div>

            <div
                style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border); display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div
                    style="padding: 1.25rem; background: #fcfcfd; border-radius: 1.25rem; border: 1px solid var(--border);">
                    <span
                        style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Ballot
                        Status</span>
                    <span
                        style="font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-circle"
                            style="font-size: 0.5rem; color: <?php echo $election['status'] == 'active' ? '#10b981' : '#94a3b8'; ?>;"></i>
                        <?php echo ucfirst($election['status']); ?>
                    </span>
                </div>
                <div
                    style="padding: 1.25rem; background: #fcfcfd; border-radius: 1.25rem; border: 1px solid var(--border);">
                    <span
                        style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Internal
                        ID</span>
                    <span
                        style="font-weight: 700; color: var(--text-main); font-family: monospace;">#ELX-<?php echo $election['election_id']; ?></span>
                </div>
            </div>
        </div>

        <!-- Candidate List -->
        <div class="card card-premium" style="padding: 2.5rem; border-radius: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div
                        style="width: 44px; height: 44px; background: rgba(79, 70, 229, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-users" style="font-size: 1.25rem;"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; margin: 0; font-weight: 800; color: var(--text-main);">Candidates</h3>
                </div>
                <span class="badge"
                    style="background: var(--primary); color: white; padding: 0.35rem 0.75rem; border-radius: 1rem; font-size: 0.8125rem;">
                    <?php echo mysqli_num_rows($candidates_res); ?> Total
                </span>
            </div>

            <?php if (mysqli_num_rows($candidates_res) > 0) { ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php while ($cand = mysqli_fetch_assoc($candidates_res)) {
                        $photo_path = !empty($cand['photo']) ? "../assets/images/candidates/" . $cand['photo'] : "../assets/images/placeholder-user.png";
                        ?>
                        <div class="candidate-item"
                            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; border: 1px solid var(--border); border-radius: 1.25rem; transition: all 0.25s ease; background: #fff;">
                            <div
                                style="width: 56px; height: 56px; border-radius: 14px; overflow: hidden; border: 2px solid #f1f5f9; flex-shrink: 0; box-shadow: var(--shadow-sm);">
                                <img src="<?php echo $photo_path; ?>" alt="<?php echo $cand['name']; ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex-grow: 1;">
                                <h4 style="margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--text-main);">
                                    <?php echo htmlspecialchars($cand['name']); ?></h4>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                    <span
                                        style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                        <i class="fas fa-flag"
                                            style="font-size: 0.75rem; color: var(--primary); opacity: 0.6;"></i>
                                        <?php echo !empty($cand['party']) ? htmlspecialchars($cand['party']) : "Independent"; ?>
                                    </span>
                                </div>
                            </div>
                            <div
                                style="width: 32px; height: 32px; background: #f8fafc; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                <i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div
                    style="text-align: center; padding: 4rem 2rem; border: 2px dashed #e2e8f0; border-radius: 1.5rem; background: #fcfcfd;">
                    <div
                        style="width: 64px; height: 64px; background: #fff; border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #cbd5e1; margin: 0 auto 1.5rem; border: 1px solid #e2e8f0;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem;">No Candidates Added</h4>
                    <p class="text-muted" style="font-size: 0.875rem; margin-bottom: 1.5rem;">You need at least two
                        candidates to start an election.</p>
                    <a href="add-candidate.php" class="btn btn-primary"
                        style="padding: 0.625rem 1.25rem; font-size: 0.875rem; border-radius: 0.75rem; font-weight: 600;">Add
                        First Candidate</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>