<?php
session_start();
include "../config/db.php";

$error = "";
$base_url = "../";

if (isset($_POST['login'])) {
    $college_id = $_POST['college_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM voters WHERE college_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $college_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // 🔒 Check approval status
        if ($row['status'] !== 'approved') {
            $error = "Your registration is pending admin approval.";
        }
        // 🔑 Check password
        else if (password_verify($password, $row['password'])) {
            $_SESSION['voter_id'] = $row['voter_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid College ID";
    }
}

include "../includes/header.php";
?>

<div style="max-width: 500px; margin: 4rem auto; padding: 0 1rem;">
    <div class="card card-premium" style="padding: 3rem; border-radius: 2rem;">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div
                style="width: 64px; height: 64px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.5rem;">
                <i class="fas fa-fingerprint"></i>
            </div>
            <h2 style="margin-bottom: 0.5rem; font-size: 1.75rem;">Voter Login</h2>
            <p class="text-muted" style="font-size: 0.9375rem;">Enter your credentials to securely access the iVote
                portal.</p>
        </div>

        <?php if ($error) { ?>
            <div
                style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; border: 1px solid #fecaca; font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="college_id"
                    style="font-weight: 600; font-size: 0.875rem; color: var(--text-main); margin-bottom: 0.625rem; display: block;">College
                    ID</label>
                <input type="text" id="college_id" name="college_id" placeholder="e.g. 2024-CS-001"
                    style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; transition: all 0.2s;"
                    required>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password"
                    style="font-weight: 600; font-size: 0.875rem; color: var(--text-main); margin-bottom: 0.625rem; display: block;">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••"
                    style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; transition: all 0.2s;"
                    required>
            </div>

            <button type="submit" name="login" class="btn btn-primary"
                style="width: 100%; padding: 1rem; border-radius: 0.75rem; font-weight: 600; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                Sign In <i class="fas fa-right-to-bracket"></i>
            </button>
        </form>

        <p style="text-align: center; margin-top: 2.5rem; font-size: 0.9375rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php"
                style="color: var(--primary); font-weight: 700; text-decoration: none;">Create one now</a>
        </p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>