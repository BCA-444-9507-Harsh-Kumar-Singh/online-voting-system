<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$base_url = "../";
$message = "";

// Fetch manageable elections - explicitly include active and inactive, exclude others
$elections = mysqli_query($conn, "SELECT election_id, title FROM elections WHERE status IN ('active', 'inactive') ORDER BY created_at DESC");

if (isset($_POST['add'])) {
    $election_id = $_POST['election_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $party = mysqli_real_escape_string($conn, $_POST['party']);
    $photo_name = "";

    // Handle Image Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../assets/images/candidates/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $photo_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $name) . "." . $file_ext;
        $target_file = $target_dir . $photo_name;

        // Simple validation
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        } else {
            $message = "Error: File is not an image.";
        }
    }

    if (empty($message) && !empty($election_id) && !empty($name)) {
        $sql = "INSERT INTO candidates (election_id, name, party, photo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $election_id, $name, $party, $photo_name);
        mysqli_stmt_execute($stmt);

        $message = "Candidate added successfully";
    } else if (empty($message)) {
        $message = "All required fields must be filled";
    }
}

$custom_head = '
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control { border-radius: var(--radius) !important; padding: 0.75rem 1rem !important; }
        .ts-wrapper.single .ts-control { background-image: none !important; }
        .file-upload-wrapper {
            border: 2px dashed var(--border);
            padding: 1.5rem;
            border-radius: var(--radius);
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .file-upload-wrapper:hover {
            border-color: var(--primary);
            background: rgba(79, 70, 229, 0.02);
        }
    </style>
';

include "../includes/header.php";
?>

<div class="container">
    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 3rem; align-items: start;">
        <!-- Left: Context & Guidelines -->
        <div style="position: sticky; top: 100px;">
            <div style="margin-bottom: 2.5rem;">
                <a href="dashboard.php"
                    style="text-decoration: none; color: var(--text-muted); font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; background: #fff; padding: 0.6rem 1.25rem; border-radius: 2rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: all 0.2s;">
                    <i class="fas fa-arrow-left"></i> Return to Portal
                </a>
                <h1 style="margin-bottom: 0.5rem; font-size: 2.25rem; letter-spacing: -0.04em; font-weight: 800;">
                    Register Candidate</h1>
                <p class="text-muted" style="font-size: 1rem; line-height: 1.5;">Enter the official credentials and
                    affiliation for the participating member.</p>
            </div>

            <div class="card card-premium slate"
                style="padding: 1.75rem; border-radius: 1.5rem; border: 1px solid var(--border);">
                <h4
                    style="font-size: 0.9375rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--text-main); display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Pro Tips
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: var(--secondary); margin-top: 0.2rem;"></i>
                        <span>Use a high-quality portrait with a neutral background for better visibility.</span>
                    </li>
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: var(--secondary); margin-top: 0.2rem;"></i>
                        <span>Verify the party affiliation name to ensure it matches official records.</span>
                    </li>
                    <li
                        style="display: flex; gap: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); line-height: 1.4;">
                        <i class="fas fa-check-circle" style="color: var(--secondary); margin-top: 0.2rem;"></i>
                        <span>Ensure the correct election is selected; this cannot be changed later.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: The Specialized Form -->
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

                <form method="post" enctype="multipart/form-data">
                    <!-- Election Assignment Header -->
                    <div style="margin-bottom: 2rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="election_id"
                                style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Target
                                Election</label>
                            <select id="election_id" name="election_id" placeholder="Select the designated election..."
                                required style="width: 100%;">
                                <option value="">-- Choose Election --</option>
                                <?php
                                mysqli_data_seek($elections, 0);
                                while ($e = mysqli_fetch_assoc($elections)) { ?>
                                    <option value="<?php echo $e['election_id']; ?>">
                                        <?php echo htmlspecialchars($e['title']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div style="margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="name"
                                    style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Full
                                    Name</label>
                                <input type="text" id="name" name="name" placeholder="John Doe" required
                                    style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.9375rem;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="party"
                                    style="display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-main); font-size: 0.875rem;">Party
                                    Name</label>
                                <input type="text" id="party" name="party" placeholder="Independent"
                                    style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border); font-size: 0.9375rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload with Preview -->
                    <div style="margin-bottom: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--border);">
                        <div style="display: grid; grid-template-columns: 80px 1fr; gap: 1.25rem; align-items: center;">
                            <div id="image-preview"
                                style="width: 80px; height: 80px; border-radius: 0.75rem; background: #f8fafc; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); overflow: hidden;">
                                <i class="fas fa-user" style="font-size: 1.5rem; opacity: 0.2;"></i>
                            </div>
                            <div class="file-upload-wrapper" onclick="document.getElementById('photo').click()"
                                style="border: 2px dashed var(--border); padding: 1rem; border-radius: 1rem; text-align: center; cursor: pointer; background: #fff; transition: all 0.2s;">
                                <p id="upload-text"
                                    style="margin: 0; font-size: 0.8125rem; color: var(--text-main); font-weight: 700;">
                                    Select Photograph</p>
                                <p id="upload-subtext"
                                    style="margin: 0.2rem 0 0; font-size: 0.7rem; color: var(--text-muted); font-weight: 500;">
                                    Click to browse library</p>
                                <input type="file" id="photo" name="photo" style="display: none;" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>
                        </div>
                    </div>

                    <div style="padding-top: 1.5rem; border-top: 1px solid var(--border);">
                        <button type="submit" name="add" class="btn btn-primary"
                            style="width: 100%; padding: 0.875rem; border-radius: 1rem; font-weight: 800; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 4px 12px -3px rgba(79, 70, 229, 0.3);">
                            <i class="fas fa-plus-circle"></i> Confirm & Finalize Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const uploadText = document.getElementById('upload-text');
        const uploadSubtext = document.getElementById('upload-subtext');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            re ader.onload = function (e) {
                preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                preview.style.borderColor = 'var(--primary)';
                uploadText.textContent = input.files[0].name;
                uploadText.style.color = 'var(--primary)';
                uploadSubtext.textContent = 'Image Ready for Upload';
                uploadSubtext.style.color = 'var(--secondary)';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    new TomSelect("#election_id", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        maxOptions: null
    });
</script>

<?php include "../includes/footer.php"; ?>