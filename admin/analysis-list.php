<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$result = mysqli_query(
    $conn,
    "SELECT election_id, title, status, created_at, end_date, duration_minutes FROM elections ORDER BY created_at DESC"
);

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3rem;">
        <a href="dashboard.php"
            style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem;">Election Analysis</h1>
                <p class="text-muted" style="font-size: 1.125rem;">Select an election to view detailed participation and
                    turnout metrics.</p>
            </div>
            <div class="search-wrapper"
                style="margin-bottom: 0; box-shadow: var(--shadow-sm); border-radius: 1rem; overflow: hidden; border: 1px solid var(--border);">
                <i class="fas fa-search search-icon" style="color: var(--primary);"></i>
                <input type="text" id="electionSearch" class="search-input" placeholder="Search elections..."
                    style="padding: 0.75rem 1rem 0.75rem 3rem; font-size: 0.9375rem; width: 350px; border: none;">
            </div>
        </div>
    </div>

    <div style="margin-bottom: 3rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;"
            id="electionGrid">
            <?php if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_label = strtoupper($row['status']);
                    $status_class = ($row['status'] == 'active') ? 'badge-active' : 'badge-inactive';
                    $status_icon = ($row['status'] == 'active') ? 'fa-circle-play' : 'fa-circle-check';
                    ?>
                    <div class="card card-premium election-card"
                        style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; min-height: 240px; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); cursor: pointer; border-radius: 1.5rem;"
                        onclick="window.location='analysis.php?election_id=<?php echo $row['election_id']; ?>'">
                        <div>
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                                <span class="badge <?php echo $status_class; ?>"
                                    style="padding: 0.4rem 0.8rem; font-size: 0.75rem; font-weight: 700;">
                                    <i class="fas <?php echo $status_icon; ?>"
                                        style="margin-right: 0.4rem;"></i><?php echo $status_label; ?>
                                </span>
                                <span
                                    style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; font-family: monospace;">ID:
                                    #<?php echo $row['election_id']; ?></span>
                            </div>
                            <h3 class="election-title"
                                style="margin-bottom: 0.75rem; font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1.2;">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h3>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0; font-weight: 500;">
                                    <i class="far fa-calendar-alt"
                                        style="margin-right: 0.5rem; color: var(--primary); opacity: 0.7;"></i> Started
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </p>
                                <?php if ($row['status'] == 'inactive' && function_exists('format_duration')) { ?>
                                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0; font-weight: 500;">
                                        <i class="fas fa-clock"
                                            style="margin-right: 0.5rem; color: var(--primary); opacity: 0.7;"></i> Duration:
                                        <strong
                                            style="color: var(--text-main);"><?php echo format_duration($row['duration_minutes']); ?></strong>
                                    </p>
                                <?php } elseif ($row['end_date'] && $row['end_date'] !== '0000-00-00 00:00:00') { ?>
                                    <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0; font-weight: 500;">
                                        <i class="fas fa-calendar-check"
                                            style="margin-right: 0.5rem; color: var(--primary); opacity: 0.7;"></i> Ended
                                        <?php echo date('M d, Y', strtotime($row['end_date'])); ?>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>

                        <div
                            style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <span
                                style="font-size: 0.875rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                                Open Analysis <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </span>
                            <div
                                style="width: 36px; height: 36px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-pie" style="font-size: 1rem;"></i>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 6rem 2rem; border: 2px dashed var(--border); border-radius: 2rem;">
                    <div
                        style="width: 80px; height: 80px; background: #f8fafc; border-radius: 2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; color: #cbd5e1; border: 1px solid #e2e8f0;">
                        <i class="fas fa-box-open" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 style="font-weight: 700; color: var(--text-main);">No Elections Found</h3>
                    <p class="text-muted" style="max-width: 320px; margin: 0.5rem auto 0;">There are no past or current
                        elections available to analyze.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    document.getElementById('electionSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.election-card');

        cards.forEach(card => {
            const title = card.querySelector('.election-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<script>
    document.getElementById('electionSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.election-card');

        cards.forEach(card => {
            const title = card.querySelector('.election-title').textContent.toLowerCase();
            if (title.includes(searchTerm)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<?php include "../includes/footer.php"; ?>