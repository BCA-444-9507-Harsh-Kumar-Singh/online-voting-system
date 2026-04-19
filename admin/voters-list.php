<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$result = mysqli_query($conn, "SELECT * FROM voters WHERE status = 'approved' ORDER BY name ASC");

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3rem;">
        <a href="approve-voters.php"
            style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <i class="fas fa-arrow-left"></i> Back to Approvals
        </a>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.5rem; font-size: 2.5rem;">Verified Voters</h1>
                <p class="text-muted" style="font-size: 1.125rem;">Directory of all registered and authorized
                    participants.</p>
            </div>
            <div class="search-wrapper"
                style="margin-bottom: 0; box-shadow: var(--shadow-sm); border-radius: 1rem; overflow: hidden; border: 1px solid var(--border);">
                <i class="fas fa-search search-icon" style="color: var(--primary);"></i>
                <input type="text" id="voterSearch" class="search-input" placeholder="Search by name or ID..."
                    style="padding: 0.75rem 1rem 0.75rem 3rem; font-size: 0.9375rem; width: 350px; border: none;">
            </div>
        </div>
    </div>

    <div class="card card-premium" style="padding: 0; border-radius: 2rem; overflow: hidden;">
        <div class="table-container">
            <table id="voterTable" style="margin: 0; border: none;">
                <thead>
                    <tr style="background: #fcfcfd;">
                        <th style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            Voter Name</th>
                        <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            College ID</th>
                        <th style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); border-top: none;">
                            System Voter ID</th>
                        <th
                            style="padding: 1.25rem 2.5rem; border-bottom: 1px solid var(--border); border-top: none; text-align: right;">
                            Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="voter-row" style="transition: all 0.2s; border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1.5rem 2.5rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div class="avatar-circle"
                                            style="width: 36px; height: 36px; font-size: 0.875rem; border-radius: 10px;">
                                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                        </div>
                                        <span class="voter-name"
                                            style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($row['name']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem 1.5rem;">
                                    <code
                                        style="background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.875rem; border: 1px solid #e2e8f0;"><?php echo htmlspecialchars($row['college_id']); ?></code>
                                </td>
                                <td style="padding: 1.5rem 1.5rem;">
                                    <span class="voter-id"
                                        style="color: var(--text-muted); font-size: 0.875rem; font-family: monospace;">
                                        <?php echo htmlspecialchars($row['voter_id']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1.5rem 2.5rem; text-align: right;">
                                    <span class="badge"
                                        style="background: #ecfdf5; color: #059669; padding: 0.4rem 1rem; border-radius: 2rem; border: 1px solid #a7f3d0; font-size: 0.75rem; font-weight: 700;">
                                        <i class="fas fa-check-circle" style="margin-right: 0.4rem;"></i>Verified
                                    </span>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 6rem 2rem;">
                                <div
                                    style="width: 80px; height: 80px; background: #f8fafc; border-radius: 2rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #cbd5e1; margin: 0 auto 2rem; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <h3 style="color: var(--text-main); margin-bottom: 0.5rem; font-weight: 700;">No Voters
                                    Found</h3>
                                <p class="text-muted" style="max-width: 300px; margin: 0 auto;">Currently there are no
                                    approved and verified voters in the system.</p>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('voterSearch').addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.voter-row');

        rows.forEach(row => {
            const name = row.querySelector('.voter-name').textContent.toLowerCase();
            const id = row.querySelector('.voter-id').textContent.toLowerCase();
            if (name.includes(searchTerm) || id.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

<?php include "../includes/footer.php"; ?>