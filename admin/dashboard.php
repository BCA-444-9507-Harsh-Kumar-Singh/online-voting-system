<?php
session_start();
include "../config/db.php";
include "../includes/check-elections.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$result = mysqli_query($conn, "SELECT * FROM elections ORDER BY created_at DESC");

// Fetch some stats for the dashboard
$total_voters = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM voters"))['count'];
$pending_voters = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM voters WHERE status = 'pending'"))['count'];
$active_elections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM elections WHERE status = 'active'"))['count'];

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3rem;">
        <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem; letter-spacing: -0.025em;">Administrator Dashboard</h1>
        <p class="text-muted" style="font-size: 1.125rem;">Control center for election lifecycle and participation
            oversight.</p>
    </div>

    <!-- Stats Overview -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 4rem;">
        <div class="card card-premium indigo" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div
                    style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Total
                        Voters</span>
                    <h3 style="margin: 0; font-size: 1.875rem; font-weight: 800; color: var(--text-main);">
                        <?php echo $total_voters; ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="card card-premium amber" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div
                    style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.1); color: #d97706; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Pending
                        Approval</span>
                    <h3 style="margin: 0; font-size: 1.875rem; font-weight: 800; color: var(--text-main);">
                        <?php echo $pending_voters; ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="card card-premium emerald" style="padding: 1.75rem; border-radius: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div
                    style="width: 56px; height: 56px; background: rgba(16, 185, 129, 0.1); color: #059669; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-tower-broadcast"></i>
                </div>
                <div>
                    <span class="text-muted"
                        style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Active
                        Elections</span>
                    <h3 style="margin: 0; font-size: 1.875rem; font-weight: 800; color: var(--text-main);">
                        <?php echo $active_elections; ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div
        style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem; font-weight: 800; color: var(--text-main);">Electoral
                Lifecycle</h2>
            <p class="text-muted" style="font-size: 0.9375rem;">Status and management of your latest transitions.</p>
        </div>

        <div style="display: flex; gap: 1rem; align-items: center;">
            <div class="search-wrapper"
                style="margin-bottom: 0; box-shadow: var(--shadow-sm); border-radius: 0.875rem; overflow: hidden; border: 1px solid var(--border);">
                <i class="fas fa-search search-icon" style="color: var(--primary);"></i>
                <input type="text" id="electionSearch" class="search-input" placeholder="Search elections..."
                    style="padding: 0.625rem 1rem 0.625rem 3rem; font-size: 0.875rem; border: none; width: 280px;">
            </div>
            <a href="create-election.php" class="btn btn-primary"
                style="padding: 0.75rem 1.25rem; font-size: 0.875rem; border-radius: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i> Create New
            </a>
        </div>
    </div>

    <div class="card card-premium" style="padding: 0; border-radius: 2rem; overflow: hidden; margin-bottom: 4rem;">
        <div class="table-container">
            <table id="electionTable" style="margin: 0; border: none;">
                <thead>
                    <tr style="background: #fcfcfd;">
                        <th style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            Election Details</th>
                        <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            Current Status</th>
                        <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            Lifecycle Controls</th>
                        <th
                            style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none; text-align: right;">
                            Analytics</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="election-row" style="transition: all 0.2s; border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1.75rem 2.5rem;">
                                    <div style="display: flex; flex-direction: column;">
                                        <span class="election-title"
                                            style="font-weight: 700; color: var(--text-main); font-size: 1.125rem;"><?php echo htmlspecialchars($row['title']); ?></span>
                                        <div
                                            style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.4rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                            <span
                                                style="background: #f1f5f9; padding: 0.15rem 0.4rem; border-radius: 4px; font-family: monospace;">#ELX-<?php echo $row['election_id']; ?></span>
                                            &bull;
                                            <?php if ($row['status'] == 'inactive') { ?>
                                                <i class="far fa-clock"></i> Duration:
                                                <?php echo format_duration($row['duration_minutes']); ?>
                                            <?php } elseif ($row['end_date'] && $row['end_date'] !== '0000-00-00 00:00:00') { ?>
                                                <i class="far fa-calendar-alt"></i> Ends
                                                <?php echo date('M d, Y H:i', strtotime($row['end_date'])); ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.75rem 1.5rem;">
                                    <?php if ($row['status'] == 'active') { ?>
                                        <span class="badge badge-active"
                                            style="padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700;">
                                            <i class="fas fa-circle-play" style="margin-right: 0.4rem;"></i>LIVE NOW
                                        </span>
                                    <?php } else if ($row['status'] == 'completed') { ?>
                                            <span class="badge"
                                                style="background: #f1f5f9; color: #64748b; border: 1px solid var(--border); padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700;">
                                                <i class="fas fa-check-circle" style="margin-right: 0.4rem;"></i>ARCHIVED
                                            </span>
                                    <?php } else { ?>
                                            <span class="badge"
                                                style="background: #fff; color: #94a3b8; border: 1px solid var(--border); padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700;">
                                                <i class="fas fa-file-pen" style="margin-right: 0.4rem;"></i>DRAFT
                                            </span>
                                    <?php } ?>
                                </td>
                                <td style="padding: 1.75rem 1.5rem;">
                                    <div style="display: flex; gap: 0.75rem;">
                                        <?php if ($row['status'] == 'inactive') { ?>
                                            <a href="activate-election.php?id=<?php echo $row['election_id']; ?>"
                                                class="btn btn-primary"
                                                style="padding: 0.5rem 1rem; font-size: 0.8125rem; font-weight: 600; border-radius: 0.625rem;">
                                                <i class="fas fa-play" style="margin-right: 0.3rem;"></i> Start
                                            </a>
                                            <a href="election-details.php?id=<?php echo $row['election_id']; ?>"
                                                class="btn btn-secondary"
                                                style="padding: 0.5rem 1rem; font-size: 0.8125rem; background: #fff; color: var(--text-main); border: 1px solid var(--border); border-radius: 0.625rem; font-weight: 600;">
                                                <i class="fas fa-gear" style="margin-right: 0.3rem;"></i> Manage
                                            </a>
                                        <?php } else if ($row['status'] == 'active') { ?>
                                                <a href="end-election.php?id=<?php echo $row['election_id']; ?>"
                                                    class="btn btn-secondary"
                                                    style="padding: 0.5rem 1rem; font-size: 0.8125rem; background: #ef4444; border: none; color: white; border-radius: 0.625rem; font-weight: 600;">
                                                    <i class="fas fa-stop" style="margin-right: 0.3rem;"></i> Stop Election
                                                </a>
                                                <a href="results.php" class="btn btn-secondary"
                                                    style="padding: 0.5rem 1rem; font-size: 0.8125rem; color: var(--primary); background: rgba(79, 70, 229, 0.05); border: 1px solid rgba(79, 70, 229, 0.2); border-radius: 0.625rem; font-weight: 600;">
                                                    <i class="fas fa-tower-broadcast" style="margin-right: 0.3rem;"></i> Monitor Live
                                                </a>
                                        <?php } else { ?>
                                                <a href="election-details.php?id=<?php echo $row['election_id']; ?>"
                                                    class="btn btn-secondary"
                                                    style="padding: 0.5rem 1rem; font-size: 0.8125rem; background: #fff; color: var(--text-main); border: 1px solid var(--border); border-radius: 0.625rem; font-weight: 600;">
                                                    <i class="fas fa-eye" style="margin-right: 0.3rem;"></i> View Details
                                                </a>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td style="padding: 1.75rem 2.5rem; text-align: right;">
                                    <a href="analysis.php?election_id=<?php echo $row['election_id']; ?>"
                                        class="btn btn-secondary"
                                        style="padding: 0.5rem 1rem; font-size: 0.8125rem; background: #fff; color: var(--primary); border: 1px solid rgba(79, 70, 229, 0.2); border-radius: 0.625rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <i class="fas fa-chart-pie"></i> Report
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 6rem 2rem;">
                                <div
                                    style="width: 80px; height: 80px; background: #f8fafc; border-radius: 2rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #cbd5e1; margin: 0 auto 2rem; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h3 style="color: var(--text-main); font-weight: 700;">No Elections Found</h3>
                                <p class="text-muted" style="max-width: 320px; margin: 0.5rem auto 0;">Start by creating
                                    your first digital election to manage candidates and voters.</p>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('electionSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.election-row');

        rows.forEach(row => {
            const title = row.querySelector('.election-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

<script>
    document.getElementById('electionSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.election-row');

        rows.forEach(row => {
            const title = row.querySelector('.election-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

<?php include "../includes/footer.php"; ?>