<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";

// Fetch last 3 completed elections
$elections_q = mysqli_query($conn, "SELECT * FROM elections WHERE status = 'completed' ORDER BY election_id DESC LIMIT 3");

include "../includes/header.php";
?>

<div class="dashboard-hero">
    <div class="container">
        <span style="text-transform: uppercase; letter-spacing: 0.15em; font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; display: block;">Voter Portal</span>
        <h1>Election Results</h1>
        <p class="text-muted">Explore the final outcomes of recently concluded elections.</p>
    </div>
</div>

<div class="container elections-container">
    <div style="margin-bottom: 3rem; display: flex; align-items: center; gap: 1rem;">
        <div style="height: 1px; flex: 1; background: var(--border);"></div>
        <h2 style="font-size: 0.875rem; margin-bottom: 0; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.2em; font-weight: 800;">Recently Held</h2>
        <div style="height: 1px; flex: 1; background: var(--border);"></div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 3rem; margin-bottom: 5rem;">
        <?php if (mysqli_num_rows($elections_q) > 0) { ?>
            <?php while ($election = mysqli_fetch_assoc($elections_q)) { 
                $election_id = $election['election_id'];
                
                // Fetch candidates and their votes for this election
                $candidates_q = mysqli_prepare($conn, "
                    SELECT c.name, c.party, COUNT(v.vote_id) as vote_count 
                    FROM candidates c 
                    LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
                    WHERE c.election_id = ? 
                    GROUP BY c.candidate_id 
                    ORDER BY vote_count DESC
                ");
                mysqli_stmt_bind_param($candidates_q, "i", $election_id);
                mysqli_stmt_execute($candidates_q);
                $candidates_res = mysqli_stmt_get_result($candidates_q);
                
                // Get total votes for percentage calculation
                $total_votes_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM votes WHERE election_id = $election_id");
                $total_votes = mysqli_fetch_assoc($total_votes_q)['total'];
            ?>
                <div class="card card-premium" style="padding: 2.5rem; border-radius: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
                        <div>
                            <h3 style="font-size: 1.75rem; margin-bottom: 0.5rem; color: var(--text-main);"><?php echo htmlspecialchars($election['title']); ?></h3>
                            <span class="badge" style="background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-clock-rotate-left" style="margin-right: 0.4rem;"></i>Concluded
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Total Ballots</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);"><?php echo $total_votes; ?></span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                        <?php 
                        $rank = 1;
                        while ($candidate = mysqli_fetch_assoc($candidates_res)) {
                            $percent = ($total_votes > 0) ? round(($candidate['vote_count'] / $total_votes) * 100, 1) : 0;
                            $is_winner = ($rank == 1 && $candidate['vote_count'] > 0);
                        ?>
                            <div style="padding: 1.5rem; border-radius: 1.25rem; background: <?php echo $is_winner ? 'rgba(79, 70, 229, 0.03)' : '#fcfcfc'; ?>; border: 1px solid <?php echo $is_winner ? 'rgba(79, 70, 229, 0.2)' : 'var(--border)'; ?>; position: relative;">
                                <?php if ($is_winner) { ?>
                                    <div style="position: absolute; top: -10px; right: 15px; background: #FFD700; color: #856404; font-size: 0.65rem; font-weight: 800; padding: 0.25rem 0.6rem; border-radius: 20px; text-transform: uppercase; border: 1px solid #eec200;">
                                        <i class="fas fa-crown"></i> Winner
                                    </div>
                                <?php } ?>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <div>
                                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main);"><?php echo htmlspecialchars($candidate['name']); ?></h4>
                                        <span class="text-muted" style="font-size: 0.8rem; font-weight: 500;"><?php echo htmlspecialchars($candidate['party'] ?: 'Independent'); ?></span>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="display: block; font-size: 1.25rem; font-weight: 800; color: <?php echo $is_winner ? 'var(--primary)' : 'var(--text-main)'; ?>;"><?php echo $candidate['vote_count']; ?></span>
                                        <span style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);"><?php echo $percent; ?>%</span>
                                    </div>
                                </div>
                                
                                <div style="height: 6px; background: #eceff2; border-radius: 3px; overflow: hidden;">
                                    <div style="height: 100%; width: <?php echo $percent; ?>%; background: <?php echo $is_winner ? 'var(--primary)' : '#94a3b8'; ?>; border-radius: 3px;"></div>
                                </div>
                            </div>
                        <?php 
                            $rank++;
                        } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="card card-premium" style="text-align: center; padding: 6rem 2rem; border-radius: 2rem; border: 2px dashed var(--border); background: transparent; box-shadow: none;">
                <div style="width: 80px; height: 80px; background: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--text-muted); margin: 0 auto 2rem; box-shadow: var(--shadow);">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 style="color: var(--text-main); margin-bottom: 0.5rem;">No History Yet</h3>
                <p class="text-muted">Concluded elections will appear here once they are finished.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
