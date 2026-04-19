</main>

<?php
$is_admin_path = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
if ($is_admin_path && isset($_SESSION['admin_id'])) {
    // Close admin-wrapper
    echo '</div>';
} else { ?>
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">

                <!-- Brand -->
                <div class="footer-col brand-col">
                    <a href="<?php echo $base_url; ?>index.php" class="footer-brand">iVote<span>.</span></a>
                    <p class="footer-desc">Secure. Transparent. Simple.</p>
                </div>

                <!-- Navigation -->
                <div class="footer-col nav-col">
                    <a href="<?php echo $base_url; ?>index.php" class="footer-link">Home</a>
                    <a href="<?php echo $base_url; ?>index.php#features" class="footer-link">Features</a>
                    <a href="<?php echo $base_url; ?>index.php#about" class="footer-link">About</a>
                    <a href="<?php echo $base_url; ?>index.php#faqs" class="footer-link">FAQs</a>
                </div>

                <!-- Social/Copyright -->
                <div class="footer-col social-col">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                    <p class="copyright">&copy; <?php echo date('Y'); ?> iVote Systems</p>
                </div>

            </div>
        </div>
    </footer>
<?php } ?>
</body>

</html>