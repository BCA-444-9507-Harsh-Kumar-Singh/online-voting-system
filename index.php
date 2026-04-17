<?php
session_start();
include "config/db.php";
$base_url = "./";

include "includes/header.php";
?>

<section class="hero">
    <div class="hero-content">
        <span
            style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 0.875rem; font-weight: 700; color: #818cf8; margin-bottom: 1.5rem; display: block;">Next-Gen
            Voting Technology</span>
        <h1>Secure. Transparent. Simple.</h1>
        <p>Empowering organizations with the world's most trusted digital voting platform. Your voice matters, and we
            ensure it is heard clearly and securely.</p>
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 3rem;">
            <a href="voter/login.php" class="btn btn-primary"
                style="padding: 1.25rem 3rem; font-size: 1.125rem; border-radius: 100px;">Start Voting Now</a>
            <a href="#about" class="btn"
                style="background: rgba(255,255,255,0.1); color: white; padding: 1.25rem 3rem; font-size: 1.125rem; backdrop-filter: blur(10px); border-radius: 100px; border: 1px solid rgba(255,255,255,0.2);">Learn
                More</a>
        </div>
    </div>
</section>

<section class="features" id="features">
    <div class="container">
        <h2 class="section-title">Why Choose iVote?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>Verified Student Access</h3>
                <p class="text-muted">Secure login using unique Student IDs ensures that only eligible campus members
                    can cast their vote.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-screen-button"></i>
                </div>
                <h3>Vote from Anywhere</h3>
                <p class="text-muted">Whether in the dorms, library, or cafeteria, students can vote instantly from
                    their smartphones.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-square-poll-vertical"></i>
                </div>
                <h3>Real-Time Results</h3>
                <p class="text-muted">Watch the election unfold live. Instant tabulation means results are ready the
                    moment polls close.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-building-columns"></i>
                </div>
                <h3>Department Scalable</h3>
                <p class="text-muted">Perfect for everything from small Class Representative polls to massive Student
                    Union elections.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Inclusive Interface</h3>
                <p class="text-muted">Designed for the entire student body and faculty, ensuring a smooth experience for
                    every voter.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Green Campus</h3>
                <p class="text-muted">Support your university's sustainability goals with a 100% paperless, eco-friendly
                    voting process.</p>
            </div>
        </div>
    </div>
</section>

<section class="info-split" style="padding: 6rem 2rem; background: #f8fafc;">
    <div class="container">
        <div class="split-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start;">
            
            <!-- Left: About Section -->
            <div class="about-column" id="about">
                <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;color:black">Redefining Democracy</h2>
                <div class="about-content" style="text-align: left; margin: 0; color: var(--text-muted); font-size: 1.125rem; line-height: 1.8;">
                    <p>iVote is a state-of-the-art digital voting platform built to solve the challenges of modern elections. By
                        combining cutting-edge security with an obsessive focus on user experience, we're making it easier than
                        ever for organizations and communities to conduct fair, efficient, and high-engagement polls.</p>
                    <p style="margin-top: 1.5rem;">Whether you're hosting a small organizational vote or a large-scale community
                        election, iVote provides the tools and trust needed to ensure every voice is heard and every vote
                        counts.</p>
                    <div style="margin-top: 2.5rem;">
                         <a href="voter/register.php" class="btn btn-primary" style="padding: 1rem 2rem;">Join the Movement</a>
                    </div>
                </div>
            </div>

            <!-- Right: FAQ Section -->
            <div class="faq-column" id="faqs">
                <h2 class="section-title" style="text-align: left; margin-bottom: 2rem;color:black">Common Questions</h2>
                <div class="accordion">
        
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span>How do I log in to vote?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-body">
                            <p>Simply click the "Login" button and enter your unique Student ID and password provided by the
                                administration. Verify your identity, and you'll be taken directly to the ballot.</p>
                        </div>
                    </div>
        
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span>Is my vote really anonymous?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-body">
                            <p>Absolutely. We use military-grade 256-bit encryption to scramble your vote data. Once cast, your
                                choice is decoupled from your identity, making it impossible to trace back to you.</p>
                        </div>
                    </div>
        
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span>Can I change my vote after submitting?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-body">
                            <p>No. To ensure the integrity of the election, all votes are final once the "Submit Ballot" button
                                is confirmed. Please review your choices carefully before finishing.</p>
                        </div>
                    </div>
        
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span>What if I lose internet connection while voting?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-body">
                            <p>Don't worry! Our system saves your session state. If you disconnect, simply log back in, and you
                                will be able to resume exactly where you left off without losing progress.</p>
                        </div>
                    </div>
        
                    <div class="accordion-item">
                        <button class="accordion-header">
                            <span>How are the results counted?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="accordion-body">
                            <p>Votes are tabulated automatically by our secure server in real-time. This eliminates human error
                                and ensures that accurate results are available instantly after the election ends.</p>
                        </div>
                    </div>
        
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.accordion-header').forEach(button => {
        button.addEventListener('click', () => {
            const accordionItem = button.parentElement;
            const isActive = accordionItem.classList.contains('active');

            // Close all other items
            document.querySelectorAll('.accordion-item').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.accordion-body').style.maxHeight = null;
            });

            // Toggle current item
            if (!isActive) {
                accordionItem.classList.add('active');
                const body = accordionItem.querySelector('.accordion-body');
                body.style.maxHeight = body.scrollHeight + "px";
            }
        });
    });
</script>

<?php include "includes/footer.php"; ?>