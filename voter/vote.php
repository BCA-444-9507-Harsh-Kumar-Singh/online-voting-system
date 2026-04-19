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

if (!isset($_GET['election_id'])) {
    header("Location: dashboard.php");
    exit;
}

$election_id = $_GET['election_id'];

$election_q = mysqli_prepare(
    $conn,
    "SELECT * FROM elections WHERE election_id = ? AND status = 'active'"
);
mysqli_stmt_bind_param($election_q, "i", $election_id);
mysqli_stmt_execute($election_q);
$election = mysqli_fetch_assoc(mysqli_stmt_get_result($election_q));

if (!$election) {
    echo "Invalid or inactive election.";
    exit;
}

// Fetch voter
$voter_q = mysqli_prepare($conn, "SELECT * FROM voters WHERE voter_id = ?");
mysqli_stmt_bind_param($voter_q, "s", $voter_id);
mysqli_stmt_execute($voter_q);
$voter = mysqli_fetch_assoc(mysqli_stmt_get_result($voter_q));

// 🔒 Check if voter already voted in this election
$check_vote = mysqli_prepare(
    $conn,
    "SELECT vote_id FROM votes WHERE voter_id = ? AND election_id = ?"
);
mysqli_stmt_bind_param(
    $check_vote,
    "si",
    $voter_id,
    $election['election_id']
);
mysqli_stmt_execute($check_vote);

if (mysqli_stmt_get_result($check_vote)->num_rows > 0) {
    header("Location: dashboard.php");
    exit;
}

// Fetch candidates
$candidate_q = mysqli_prepare(
    $conn,
    "SELECT * FROM candidates WHERE election_id = ?"
);
mysqli_stmt_bind_param($candidate_q, "i", $election['election_id']);
mysqli_stmt_execute($candidate_q);
$candidates = mysqli_stmt_get_result($candidate_q);

// 🔒 Secondary approval check
if ($voter['status'] !== 'approved') {
    $error = "You are not approved to vote.";
}

// Handle vote submission
if (isset($_POST['vote'])) {
    if (!isset($_POST['candidate_id'])) {
        $error = "Please select a candidate before voting.";
    } else {
        $candidate_id = $_POST['candidate_id'];

        // Insert vote
        $sql = "INSERT INTO votes (voter_id, candidate_id, election_id)
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $voter_id,
            $candidate_id,
            $election['election_id']
        );
        mysqli_stmt_execute($stmt);

        header("Location: success.php");
        exit;
    }
}

include "../includes/header.php";
?>

<div class="container">
    <div style="margin-bottom: 3.5rem;">
        <a href="dashboard.php"
            style="text-decoration: none; color: var(--text-muted); font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        <span
            style="text-transform: uppercase; letter-spacing: 0.15em; font-size: 0.75rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem; display: block;">Official
            Ballot</span>
        <h1 style="font-size: 2.75rem; margin: 0 0 1rem;"><?php echo htmlspecialchars($election['title']); ?></h1>
        <p class="text-muted" style="font-size: 1.125rem; max-width: 800px;">Please select your preferred candidate from
            the options below. Review your choice carefully before submitting, as your vote is final and cannot be
            altered once cast.</p>
    </div>

    <?php if (isset($error)) { ?>
        <div
            style="background-color: #fef2f2; color: #991b1b; padding: 1.25rem; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 1rem;">
            <i class="fas fa-circle-exclamation" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 500;"><?php echo $error; ?></span>
        </div>
    <?php } ?>

    <form method="post" id="voteForm">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 6rem;">
            <?php while ($c = mysqli_fetch_assoc($candidates)) {
                $initials = strtoupper(substr($c['name'], 0, 1));
                ?>
                <label style="cursor: pointer; position: relative;" class="candidate-label">
                    <input type="radio" name="candidate_id" value="<?php echo $c['candidate_id']; ?>" required
                        style="display: none;">
                    <div class="card card-premium candidate-card"
                        style="height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 2px solid transparent; padding: 2.5rem; border-radius: 2rem; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <div class="selection-indicator">
                            <i class="fas fa-circle-check"></i>
                        </div>

                        <div class="candidate-avatar">
                            <?php
                            $photo_path = !empty($c['photo']) ? $base_url . "assets/images/candidates/" . $c['photo'] : $base_url . "assets/images/placeholder-user.png";
                            ?>
                            <img src="<?php echo $photo_path; ?>" alt="<?php echo htmlspecialchars($c['name']); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div style="width: 100%;">
                            <h3 style="margin: 0 0 0.5rem; font-size: 1.5rem;"><?php echo htmlspecialchars($c['name']); ?>
                            </h3>
                            <div
                                style="display: inline-block; padding: 0.4rem 1rem; background: #EEF2FF; color: #4F46E5; border-radius: 100px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;">
                                <?php echo htmlspecialchars($c['party']); ?>
                            </div>
                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin: 0;">
                                Representing the <?php echo htmlspecialchars($c['party']); ?> for the
                                <?php echo htmlspecialchars($election['title']); ?>.
                            </p>
                        </div>
                    </div>
                </label>
            <?php } ?>
        </div>

        <div class="vote-sticky-bar">
            <div class="container"
                style="display: flex; justify-content: space-between; align-items: center; gap: 2rem;">
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div id="selectionIcon"
                        style="width: 48px; height: 48px; background: #F1F5F9; color: #94A3B8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fas fa-hand-pointer"></i>
                    </div>
                    <div>
                        <p id="selectionText"
                            style="margin: 0; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">No
                            candidate selected yet</p>
                        <p id="selectionName"
                            style="margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--text-main); display: none;">
                        </p>
                    </div>
                </div>
                <button type="submit" name="vote" class="btn btn-primary"
                    style="padding: 1rem 2.5rem; border-radius: 100px; font-size: 1.125rem; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);"
                    onclick="return confirm('Confirm your vote? This action is permanent and anonymous.')">
                    <i class="fas fa-paper-plane" style="margin-right: 0.75rem;"></i> Cast My Official Vote
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .candidate-avatar {
        width: 120px;
        height: 120px;
        background: #f8fafc;
        border-radius: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        overflow: hidden;
        border: 4px solid white;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .selection-indicator {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-size: 1.5rem;
        color: var(--primary);
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    input[type="radio"]:checked+.candidate-card {
        border-color: var(--primary);
        background-color: #F5F3FF;
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 10px 10px -5px rgba(79, 70, 229, 0.04);
    }

    input[type="radio"]:checked+.candidate-card .selection-indicator {
        opacity: 1;
        transform: scale(1);
    }

    input[type="radio"]:checked+.candidate-card .candidate-avatar {
        background: var(--primary);
        color: white;
        border-color: #E0E7FF;
    }

    .candidate-card:hover:not(input[type="radio"]:checked + .candidate-card) {
        border-color: #E2E8F0;
        transform: translateY(-4px);
    }

    .vote-sticky-bar {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 4rem);
        max-width: 1000px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        padding: 1.25rem;
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        z-index: 100;
    }
</style>

<script>
    const radioButtons = document.querySelectorAll('input[name="candidate_id"]');
    const selectionText = document.getElementById('selectionText');
    const selectionName = document.getElementById('selectionName');
    const selectionIcon = document.getElementById('selectionIcon');

    radioButtons.forEach(radio => {
        radio.addEventListener('change', (e) => {
            const card = e.target.nextElementSibling;
            const name = card.querySelector('h3').innerText;
            const party = card.querySelector('div').innerText;

            selectionText.innerHTML = `You are voting for`;
            selectionText.style.color = 'var(--primary)';
            selectionName.innerHTML = `${name} <span style="font-weight: 500; font-size: 0.875rem; color: var(--text-muted);">(${party})</span>`;
            selectionName.style.display = 'block';

            selectionIcon.style.background = 'var(--primary)';
            selectionIcon.style.color = 'white';
            selectionIcon.innerHTML = '<i class="fas fa-check"></i>';
        });
    });
</script>

<?php include "../includes/footer.php"; ?>