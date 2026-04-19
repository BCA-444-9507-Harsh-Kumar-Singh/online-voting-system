<?php
session_start();
include "../config/db.php";

$error = "";
$base_url = "../";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Admin not found";
    }
}

include "../includes/header.php";
?>

<div style="max-width: 450px; margin: 4rem auto;">
    <div class="card" style="border-top: 4px solid var(--primary);">
        <h2 style="text-align: center; margin-bottom: 0.5rem;">Admin Access</h2>
        <p class="text-muted" style="text-align: center; margin-bottom: 2rem;">Authorized access only. Please sign in to
            manage the voting system.</p>

        <?php if ($error) { ?>
            <div
                style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; border: 1px solid #fecaca; font-size: 0.875rem;">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="admin_username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Enter Admin Panel
            </button>
        </form>

        <p style="text-align: center; margin-top: 2rem; font-size: 0.875rem; color: var(--text-muted);">
            Forgot credentials? Contact system administrator.
        </p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>