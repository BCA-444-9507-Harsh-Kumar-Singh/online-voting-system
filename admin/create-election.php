<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$message = "";

if (isset($_POST['create'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $duration_hr = (int) $_POST['duration_hr'];
    $duration_min = (int) $_POST['duration_min'];
    $total_duration_minutes = ($duration_hr * 60) + $duration_min;
    if (!empty($title)) {
        // Check if election title already exists
        $check_sql = "SELECT election_id FROM elections WHERE title = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $title);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = "An election with this title already exists. Please choose a unique name.";
        } else {
            $sql = "INSERT INTO elections (title, description, duration_minutes) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $total_duration_minutes);
            mysqli_stmt_execute($stmt);

            $message = "Election created successfully";
        }
        mysqli_stmt_close($check_stmt);
    } else {
        $message = "Election title cannot be empty";
    }
}

include "../includes/header.php";
?>


<div class="container">
    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 3rem; align-items: start;">
        <!-- Left: Context & Guidelines -->
        <div style="position: sticky; top: 100px;">
            <div style="margin-bottom: 2.5rem;">
                <a href="dashboard.php"
                    style="text-decoration: none; color: var(--text-muted); font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; background: #fff; padding: 0.6rem 1.25rem; border-radius: 2rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: all 0.2s;">
                    <i class="fas fa-arrow-left"></i> Return to Dashboard
                </a>
                <h1 style="margin-bottom: 0.5rem; font-size: 2.25rem; letter-spacing: -0.04em; font-weight: 800;">
                    New Election</h1>
                <p class="text-muted" style="font-size: 1rem; line-height: 1.5;">Launch a new electronic ballot.
                    Configure the timeline and basic details.</p>
            </div>

            <div class="card card-premium emerald"
                style="padding: 1.75rem; border-radius: 1.5rem; border: 1px solid var(--border);">
                <h4
                    style="font-size: 0.9375rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main); display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fas fa-lightbulb" style="color: #10b981;"></i> Setup Guide
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.875rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.2rem; flex-shrink: 0;"></i>
                        <span><strong>Be Descriptive:</strong> Use a clear, unique title (e.g., "Class President
                            2026").</span>
                    </li>
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.875rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.2rem; flex-shrink: 0;"></i>
                        <span><strong>Duration:</strong> Set a reasonable voting window. You can manually end it early
                            if needed.</span>
                    </li>
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.875rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: #10b981; margin-top: 0.2rem; flex-shrink: 0;"></i>
                        <span><strong>Preparation:</strong> After creating, you'll be redirected to add
                            candidates.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: The Form -->
        <div style="max-width: 600px;">
            <div class="card card-premium indigo" style="padding: 2rem; border-radius: 1.5rem;">
                <?php if ($message) { ?>
                    <div
                        style="background-color: <?php echo strpos($message, 'successfully') !== false ? '#ecfdf5' : '#fef2f2'; ?>; 
                                color: <?php echo strpos($message, 'successfully') !== false ? '#065f46' : '#991b1b'; ?>; 
                                padding: 1rem; border-radius: 0.75rem; margin-bottom: 2rem; 
                                border: 1px solid <?php echo strpos($message, 'successfully') !== false ? '#a7f3d0' : '#fecaca'; ?>; 
                                font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600;">
                        <i
                            class="fas <?php echo strpos($message, 'successfully') !== false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <form method="post">
                    <!-- Section 1: Identity -->
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                            <span
                                style="width: 28px; height: 28px; background: var(--primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">1</span>
                            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 800;">Election Identity</h3>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label for="title"
                                style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Official
                                Title</label>
                            <input type="text" id="title" name="title" placeholder="e.g. Student Council Elections 2026"
                                required
                                style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.9375rem; transition: all 0.2s; background: #fff;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="description"
                                style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Context
                                / Description</label>
                            <textarea id="description" name="description"
                                placeholder="Provide a brief overview for the voters..."
                                style="width: 100%; min-height: 100px; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.9375rem; transition: all 0.2s; background: #fff; resize: vertical;"></textarea>
                        </div>
                    </div>

                    <!-- Section 2: Timing -->
                    <div style="margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                            <span
                                style="width: 28px; height: 28px; background: var(--primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">2</span>
                            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 800;">Voting Window</h3>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label
                                style="display: block; margin-bottom: 0.75rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Scheduled
                                Duration</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: start;">
                                <div>
                                    <div style="position: relative;">
                                        <input type="number" name="duration_hr" placeholder="0" min="0" value="0"
                                            required
                                            style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; font-weight: 700; text-align: center; background: #fff;">
                                        <span
                                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Hours</span>
                                    </div>
                                </div>
                                <div>
                                    <div style="position: relative;">
                                        <input type="number" name="duration_min" placeholder="0" min="0" max="59"
                                            value="0" required
                                            style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; font-weight: 700; text-align: center; background: #fff;">
                                        <span
                                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Mins</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            style="display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid #f1f5f9;">
                            <i class="fas fa-clock"
                                style="color: var(--primary); margin-top: 0.125rem; font-size: 0.875rem;"></i>
                            <p
                                style="font-size: 0.8125rem; color: #64748b; margin: 0; line-height: 1.4; font-weight: 500;">
                                Timer starts upon activation. You can modify this later if needed.</p>
                        </div>
                    </div>

                    <div style="padding-top: 1.5rem; border-top: 1px solid var(--border);">
                        <button type="submit" name="create" class="btn btn-primary"
                            style="width: 100%; padding: 0.875rem; border-radius: 1rem; font-weight: 800; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 4px 12px -3px rgba(79, 70, 229, 0.3);">
                            <i class="fas fa-plus-circle"></i> Create Election Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>