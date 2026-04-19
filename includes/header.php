<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <!-- Font Awesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php echo isset($custom_head) ? $custom_head : ''; ?>
</head>

<body>
    <?php
    $is_admin_path = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    if (isset($_SESSION['admin_id']) && $is_admin_path) {
        $current_page = basename($_SERVER['PHP_SELF']);
        ?>
        <div class="admin-wrapper">
            <aside class="sidebar">
                <a href="<?php echo $base_url; ?>admin/dashboard.php" class="sidebar-brand">
                    <span>IVote</span> Admin
                </a>
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/dashboard.php"
                            class="sidebar-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-th-large"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/approve-voters.php"
                            class="sidebar-link <?php echo $current_page == 'approve-voters.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-check"></i> <span>Manage Voters</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/create-election.php"
                            class="sidebar-link <?php echo $current_page == 'create-election.php' ? 'active' : ''; ?>">
                            <i class="fas fa-plus-circle"></i> <span>Add Election</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/add-candidate.php"
                            class="sidebar-link <?php echo $current_page == 'add-candidate.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> <span>Add Candidate</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/results.php"
                            class="sidebar-link <?php echo $current_page == 'results.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-bar"></i> <span>Live Results</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?php echo $base_url; ?>admin/analysis-list.php"
                            class="sidebar-link <?php echo ($current_page == 'analysis-list.php' || $current_page == 'analysis.php') ? 'active' : ''; ?>">
                            <i class="fas fa-chart-line"></i> <span>Election Analysis</span>
                        </a>
                    </li>
                </ul>
                <div class="sidebar-footer">
                    <a href="<?php echo $base_url; ?>logout.php" class="sidebar-link" style="color: #ff4d4d; opacity: 0.8;">
                        <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                    </a>
                </div>
            </aside>
            <main class="admin-main">
            <?php } else { ?>
                <nav class="navbar">
                    <div class="nav-container">
                        <a href="<?php echo $base_url; ?>index.php" class="nav-brand"><span>i</span>Vote</a>

                        <ul class="nav-links">
                            <li><a href="<?php echo $base_url; ?>index.php" class="nav-link">Home</a></li>
                            <li><a href="<?php echo $base_url; ?>index.php#features" class="nav-link">Features</a></li>
                            <li><a href="<?php echo $base_url; ?>index.php#about" class="nav-link">About</a></li>
                            <li><a href="<?php echo $base_url; ?>index.php#faqs" class="nav-link">FAQs</a></li>

                            <?php if (isset($_SESSION['voter_id'])) {
                                // Fetch voter initials
                                $voter_id = $_SESSION['voter_id'];
                                $nav_voter_res = mysqli_query($conn, "SELECT name FROM voters WHERE voter_id = '$voter_id'");
                                $nav_voter = mysqli_fetch_assoc($nav_voter_res);
                                $initial = strtoupper(substr($nav_voter['name'] ?? 'V', 0, 1));
                                ?>
                                <li class="user-menu">
                                    <div class="avatar-circle"><?php echo $initial; ?></div>
                                    <div class="dropdown-menu">
                                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border);">
                                            <p
                                                style="font-weight: 700; color: var(--text-main); margin: 0; font-size: 0.875rem;">
                                                <?php echo htmlspecialchars($nav_voter['name']); ?>
                                            </p>
                                        </div>
                                        <a href="<?php echo $base_url; ?>voter/profile.php" class="dropdown-item">
                                            <i class="far fa-user"></i> My Profile
                                        </a>
                                        <a href="<?php echo $base_url; ?>voter/dashboard.php" class="dropdown-item">
                                            <i class="fas fa-vote-yea"></i> Active Elections
                                        </a>
                                        <a href="<?php echo $base_url; ?>voter/results.php" class="dropdown-item">
                                            <i class="fas fa-chart-bar"></i> Election Results
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="<?php echo $base_url; ?>logout.php" class="dropdown-item logout">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </a>
                                    </div>
                                </li>
                            <?php } else { ?>
                                <li style="display: flex; align-items: center;">
                                    <a href="<?php echo $base_url; ?>voter/login.php" class="nav-link btn btn-primary"
                                        style="color: white; padding: 0.6rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; margin-left: 0.5rem;">
                                        <i class="fas fa-sign-in-alt"></i> <span>Login</span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </nav>
                <main>
                <?php } ?>