<?php
include "../config/db.php";

$message = "";
$base_url = "../";

function generateVoterId($conn)
{
    $prefix = "VT";
    $random = rand(10000, 99999);
    $voter_id = $prefix . $random;

    // Ensure uniqueness
    $check = mysqli_prepare(
        $conn,
        "SELECT voter_id FROM voters WHERE voter_id = ?"
    );
    mysqli_stmt_bind_param($check, "s", $voter_id);
    mysqli_stmt_execute($check);

    if (mysqli_stmt_get_result($check)->num_rows > 0) {
        return generateVoterId($conn);
    }
    return $voter_id;
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $college_id = $_POST['college_id'];
    $password = $_POST['password'];

    if (empty($name) || empty($college_id) || empty($password)) {
        $message = "All fields are required";
    } else {

        // Check duplicate college ID
        $check = mysqli_prepare(
            $conn,
            "SELECT college_id FROM voters WHERE college_id = ?"
        );
        mysqli_stmt_bind_param($check, "s", $college_id);
        mysqli_stmt_execute($check);

        if (mysqli_stmt_get_result($check)->num_rows > 0) {
            $message = "College ID already registered";
        } else {

            $voter_id = generateVoterId($conn);
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO voters (voter_id, college_id, name, password)
                    VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $voter_id,
                $college_id,
                $name,
                $hashed
            );
            mysqli_stmt_execute($stmt);

            $message = "Registration submitted successfully. Please wait for admin approval.";
        }
    }
}

include "../includes/header.php";
?>

<div style="max-width: 500px; margin: 4rem auto; padding: 0 1rem;">
    <div class="card card-premium" style="padding: 3rem; border-radius: 2rem;">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div
                style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.5rem;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2 style="margin-bottom: 0.5rem; font-size: 1.75rem;">Voter Registration</h2>
            <p class="text-muted" style="font-size: 0.9375rem;">Join the digital democracy. Create your account to start
                voting.</p>
        </div>

        <?php if ($message) { ?>
            <div style="background-color: <?php echo strpos($message, 'successfully') !== false ? '#dcfce7' : '#fef2f2'; ?>; 
                        color: <?php echo strpos($message, 'successfully') !== false ? '#166534' : '#991b1b'; ?>; 
                        padding: 1rem; border-radius: 1rem; margin-bottom: 2rem; 
                        border: 1px solid <?php echo strpos($message, 'successfully') !== false ? '#bbf7d0' : '#fecaca'; ?>; 
                        font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
                <i
                    class="fas <?php echo strpos($message, 'successfully') !== false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name"
                    style="font-weight: 600; font-size: 0.875rem; color: var(--text-main); margin-bottom: 0.625rem; display: block;">Full
                    Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe"
                    style="width: 100%; padding: 0.875rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 1rem; transition: all 0.2s;"
                    required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="college_id"
                    style="font-weight: 600; font-size: 0.875rem; color: var(--text-main); margin-bottom: 0.625rem; display: block;">College
                    ID</label>
                <input type="text" id="college_id" name="college_id" placeholder="Enter your ID"
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

            <button type="submit" name="register" class="btn btn-primary"
                style="width: 100%; padding: 1rem; border-radius: 0.75rem; font-weight: 600; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                Register Account <i class="fas fa-user-check"></i>
            </button>
        </form>

        <p style="text-align: center; margin-top: 2.5rem; font-size: 0.9375rem; color: var(--text-muted);">
            Already have an account? <a href="login.php"
                style="color: var(--primary); font-weight: 700; text-decoration: none;">Sign in here</a>
        </p>
    </div>
</div>

<?php include "../includes/footer.php"; ?>