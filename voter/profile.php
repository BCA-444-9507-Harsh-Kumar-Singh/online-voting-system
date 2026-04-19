<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$voter_id = $_SESSION['voter_id'];
$success_msg = "";
$error_msg = "";

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if (empty($name) || empty($email)) {
        $error_msg = "Name and Email are required.";
    } else {
        $update = mysqli_query($conn, "UPDATE voters SET name = '$name', email = '$email' WHERE voter_id = '$voter_id'");
        if ($update) {
            $success_msg = "Profile updated successfully!";
        } else {
            $error_msg = "Failed to update profile: " . mysqli_error($conn);
        }
    }
}

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $res = mysqli_query($conn, "SELECT password FROM voters WHERE voter_id = '$voter_id'");
    $row = mysqli_fetch_assoc($res);

    if (!password_verify($current, $row['password'])) {
        $error_msg = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error_msg = "New passwords do not match.";
    } elseif (strlen($new) < 6) {
        $error_msg = "Password must be at least 6 characters.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE voters SET password = '$hashed' WHERE voter_id = '$voter_id'");
        if ($update) {
            $success_msg = "Password changed successfully!";
        } else {
            $error_msg = "Failed to change password: " . mysqli_error($conn);
        }
    }
}

$res = mysqli_query($conn, "SELECT * FROM voters WHERE voter_id = '$voter_id'");
$voter = mysqli_fetch_assoc($res);

include "../includes/header.php";
?>

<style>
    .profile-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        padding: 6rem 0 10rem;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.05) 1px, transparent 0);
        background-size: 40px 40px;
    }

    .profile-container {
        max-width: 1000px;
        margin: -6rem auto 4rem;
        position: relative;
        z-index: 10;
        padding: 0 1.5rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        background: white;
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border);
    }

    .profile-sidebar {
        background: #f8fafc;
        border-right: 1px solid var(--border);
        padding: 2.5rem 1.5rem;
    }

    .profile-nav-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border: none;
        background: none;
        color: var(--text-muted);
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .profile-nav-btn:hover {
        background: rgba(79, 70, 229, 0.05);
        color: var(--primary);
    }

    .profile-nav-btn.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
    }

    .profile-content {
        padding: 3rem;
    }

    .profile-tab {
        display: none;
    }

    .profile-tab.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-readonly {
        background: #f1f5f9;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        font-family: monospace;
        font-weight: 600;
        color: var(--text-main);
        border: 1px solid var(--border);
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-sidebar {
            border-right: none;
            border-bottom: 1px solid var(--border);
            padding: 1.5rem;
            display: flex;
            gap: 1rem;
            overflow-x: auto;
        }

        .profile-nav-btn {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .profile-content {
            padding: 2rem;
        }
    }
</style>

<section class="profile-hero">
    <div class="container" style="position: relative; z-index: 1;">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Account Settings</h1>
        <p style="font-size: 1.125rem; opacity: 0.8;">Manage your personal information and security preferences</p>
    </div>
</section>

<div class="profile-container">
    <?php if ($success_msg) { ?>
        <div
            style="background: #dcfce7; color: #166534; padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
        </div>
    <?php } ?>

    <?php if ($error_msg) { ?>
        <div
            style="background: #fef2f2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
        </div>
    <?php } ?>

    <div class="profile-grid">
        <aside class="profile-sidebar">
            <button class="profile-nav-btn active" onclick="switchTab('personal')">
                <i class="fas fa-user-circle"></i> Personal Details
            </button>
            <button class="profile-nav-btn" onclick="switchTab('security')">
                <i class="fas fa-shield-alt"></i> Security & Privacy
            </button>
            <div
                style="margin-top: 2rem; padding: 1.5rem; background: white; border-radius: 1rem; border: 1px solid var(--border);">
                <div class="avatar-circle" style="width: 60px; height: 60px; margin: 0 auto 1rem; font-size: 1.5rem;">
                    <?php echo strtoupper(substr($voter['name'], 0, 1)); ?>
                </div>
                <div style="text-align: center;">
                    <div style="font-weight: 700; color: var(--text-main);">
                        <?php echo htmlspecialchars($voter['name']); ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Member since
                        <?php echo date('M Y', strtotime($voter['created_at'])); ?></div>
                </div>
            </div>
        </aside>

        <main class="profile-content">
            <!-- Personal Details Tab -->
            <div id="personal" class="profile-tab active">
                <h2 style="margin-bottom: 2rem;">Personal Information</h2>
                <form method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($voter['name']); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($voter['email']); ?>"
                                placeholder="john@example.com" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
                        <div class="form-group">
                            <label>Voter ID (Backend)</label>
                            <div class="info-readonly"><?php echo htmlspecialchars($voter['voter_id']); ?></div>
                        </div>
                        <div class="form-group">
                            <label>College ID</label>
                            <div class="info-readonly"><?php echo htmlspecialchars($voter['college_id']); ?></div>
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary" style="padding: 1rem 2rem;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="security" class="profile-tab">
                <h2 style="margin-bottom: 2rem;">Security & Password</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Update your password to keep your account
                    secure.</p>
                <form method="POST" style="max-width: 500px;">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 2.5rem;">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-secondary" style="padding: 1rem 2rem;">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    function switchTab(tabId) {
        // Toggle Buttons
        document.querySelectorAll('.profile-nav-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('onclick').includes(tabId)) {
                btn.classList.add('active');
            }
        });

        // Toggle Tabs
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(tabId).classList.add('active');
    }
</script>

<?php include "../includes/footer.php"; ?>