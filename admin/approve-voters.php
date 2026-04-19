<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";

// Approve voter
if (isset($_GET['approve'])) {
    $voter_id = $_GET['approve'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE voters SET status = 'approved' WHERE voter_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "s", $voter_id);
    mysqli_stmt_execute($stmt);

    header("Location: approve-voters.php");
    exit;
}

// Fetch pending voters
$result = mysqli_query(
    $conn,
    "SELECT voter_id, name, college_id FROM voters WHERE status = 'pending'"
);

include "../includes/header.php";
?>

<div class="container">
    <div
        style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 2rem;">
        <div>
            <a href="dashboard.php"
                style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem;">Voter Approvals</h1>
            <p class="text-muted" style="font-size: 1.125rem;">Verify new registrations to authorize them for
                participation.</p>
        </div>
        <a href="voters-list.php" class="btn btn-secondary"
            style="padding: 0.75rem 1.5rem; border-radius: 1rem; font-size: 0.875rem; background: #fff; border: 1px solid var(--border); color: var(--text-main); font-weight: 600; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
            <i class="fas fa-users-viewfinder" style="color: var(--primary);"></i> View Verified Voters
        </a>
    </div>

    <div class="card card-premium" style="padding: 0; border-radius: 2rem; overflow: hidden;">
        <div style="padding: 2rem 2.5rem; border-bottom: 1px solid var(--border); background: #fcfcfd;">
            <h2 style="font-size: 1.25rem; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-clock" style="color: #f59e0b;"></i> Pending Registrations
                <span class="badge"
                    style="background: #fef3c7; color: #92400e; font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 2rem; border: 1px solid #fde68a;">
                    <?php echo mysqli_num_rows($result); ?> Waiting
                </span>
            </h2>
        </div>

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <div style="overflow-x: auto;">
                <table style="margin: 0; border: none;">
                    <thead>
                        <tr style="background: transparent;">
                            <th style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                                Voter Name</th>
                            <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                                College ID</th>
                            <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                                System Voter ID</th>
                            <th
                                style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none; text-align: right;">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr style="transition: all 0.2s; border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1.5rem 2.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div class="avatar-circle"
                                            style="width: 40px; height: 40px; font-size: 1rem; border-radius: 10px;">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <span
                                            style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($row['name']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem 1.5rem;">
                                    <code
                                        style="background: #f1f5f9; padding: 0.3 \rem 0.6rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.875rem; border: 1px solid #e2e8f0;"><?php echo htmlspecialchars($row['college_id']); ?></code>
                                </td>
                                <td
                                    style="padding: 1.5rem 1.5rem; color: var(--text-muted); font-size: 0.875rem; font-family: monospace;">
                                    <?php echo htmlspecialchars($row['voter_id']); ?>
                                </td>
                                <td style="padding: 1.5rem 2.5rem; text-align: right;">
                                    <a href="?approve=<?php echo $row['voter_id']; ?>" class="btn btn-secondary"
                                        style="padding: 0.625rem 1.25rem; font-size: 0.875rem; background-color: #10b981; color: white; border-radius: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; transition: all 0.2s; border: none; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div style="text-align: center; padding: 6rem 2rem;">
                <div
                    style="width: 80px; height: 80px; background: #f8fafc; border-radius: 2rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #cbd5e1; margin: 0 auto 2rem; border: 1px solid #e2e8f0;">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 style="color: var(--text-main); margin-bottom: 0.5rem; font-weight: 700;">All Caught Up!</h3>
                <p class="text-muted" style="max-width: 300px; margin: 0 auto;">There are no pending registrations waiting
                    for review.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>