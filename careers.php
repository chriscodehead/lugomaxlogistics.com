<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'careers';
$page_title = 'Careers';
$page_description = 'Join the Lugomax team — explore open positions and apply online.';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid form submission. Please try again.');
        header('Location: careers');
        exit;
    }

    $full_name    = sanitize_input($_POST['name']         ?? '');
    $email        = sanitize_input($_POST['email']        ?? '');
    $phone        = sanitize_input($_POST['phone']        ?? '');
    $position     = sanitize_input($_POST['position']     ?? '');
    $cover_letter = sanitize_input($_POST['cover_letter'] ?? '');
    $availability = sanitize_input($_POST['availability'] ?? '');
    $exp_years    = (int)($_POST['experience_years']      ?? 0);

    // Validation
    if (!$full_name || !$email || !$phone || !$position) {
        set_flash('error', 'Please fill in all required fields.');
        header('Location: careers#apply');
        exit;
    }

    if (!validate_email($email)) {
        set_flash('error', 'Please enter a valid email address.');
        header('Location: careers#apply');
        exit;
    }

    // CV upload
    $resume_file = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
        $upload_dir = 'uploads/resumes/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext     = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx'];

        if (!in_array($ext, $allowed)) {
            set_flash('error', 'CV must be a PDF, DOC, or DOCX file.');
            header('Location: careers#apply');
            exit;
        }
        if ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
            set_flash('error', 'CV file must be under 5 MB.');
            header('Location: careers#apply');
            exit;
        }

        $resume_file = 'resume_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $upload_dir . $resume_file)) {
            $resume_file = ''; // save application even if upload failed
        }
    }

    // Save to database
    try {
        $db->prepare("INSERT INTO job_applications
            (position, full_name, email, phone, resume_file, cover_letter,
             experience_years, availability, status, created_at)
            VALUES (?,?,?,?,?,?,?,?,'new',NOW())")
            ->execute([
                $position,
                $full_name,
                $email,
                $phone,
                $resume_file,
                $cover_letter,
                $exp_years,
                $availability
            ]);

        @send_email(
            get_setting('site_email', 'info@lugomax.co.uk'),
            "New Job Application — {$position}",
            "<h3>New Application Received</h3>
             <p><b>Name:</b> " . escape($full_name) . "</p>
             <p><b>Email:</b> " . escape($email) . "</p>
             <p><b>Phone:</b> " . escape($phone) . "</p>
             <p><b>Position:</b> " . escape($position) . "</p>
             <p><b>Cover Letter:</b><br>" . nl2br(escape($cover_letter)) . "</p>"
        );

        // ✅ KEY FIX: redirect with ?submitted=1 query param (NOT #hash — browsers drop hashes on redirect)
        header('Location: careers?submitted=1&for=' . urlencode($full_name));
        exit;
    } catch (Exception $e) {
        set_flash('error', 'Something went wrong saving your application. Please try again.');
        header('Location: careers#apply');
        exit;
    }
}

// Was the form just successfully submitted?
$just_submitted = isset($_GET['submitted']) && $_GET['submitted'] === '1';
$applicant_name = escape(sanitize_input($_GET['for'] ?? ''));

// Load active jobs from DB
$jobs = [];
try {
    $jobs = $db->query("SELECT * FROM jobs WHERE is_active=1 ORDER BY display_order ASC, created_at DESC")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $jobs = []; // table may not exist yet
}

include 'includes/header.php';
?>

<?php if ($just_submitted): ?>
    <!-- ═══════════════════════════════════════════════════════════════════════════
     SUCCESS OVERLAY — shown immediately after form submission
     ══════════════════════════════════════════════════════════════════════════ -->
    <div id="successOverlay" style="
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.65);
    z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    animation: ovFadeIn 0.3s ease;">

        <div style="
        background: white;
        border-radius: 18px;
        padding: 52px 44px;
        max-width: 520px;
        width: 92%;
        text-align: center;
        box-shadow: 0 24px 70px rgba(0,0,0,0.3);
        animation: cardSlideUp 0.4s ease;">

            <!-- Animated tick circle -->
            <div style="
            width: 90px; height: 90px;
            background: #d1fae5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            animation: tickPop 0.5s ease 0.2s both;">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                    stroke="#065f46" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h2 style="color:#0A1F44;font-size:1.9rem;margin-bottom:14px;font-weight:700;">
                Application Submitted! 🎉
            </h2>

            <?php if ($applicant_name): ?>
                <p style="color:#374151;font-size:1.05rem;margin-bottom:10px;">
                    Thank you, <strong style="color:#0A1F44;"><?= $applicant_name ?></strong>!
                </p>
            <?php endif; ?>

            <p style="color:#64748B;line-height:1.75;margin-bottom:36px;font-size:0.97rem;">
                We've received your application and our team will review it shortly.
                We'll be in touch within <strong>5 working days</strong> —
                keep an eye on your inbox!
            </p>

            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <button onclick="closeOverlay()"
                    style="background:#FF6B2C;color:white;border:none;
                       padding:14px 30px;border-radius:9px;
                       font-weight:700;font-size:1rem;cursor:pointer;
                       transition:background 0.2s;"
                    onmouseover="this.style.background='#e55a1f'"
                    onmouseout="this.style.background='#FF6B2C'">
                    View More Jobs
                </button>
                <a href="careers"
                    style="background:#f1f5f9;color:#0A1F44;
                      padding:14px 30px;border-radius:9px;
                      font-weight:600;font-size:1rem;
                      text-decoration:none;display:inline-flex;align-items:center;">
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes ovFadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes cardSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes tickPop {
            0% {
                transform: scale(0)
            }

            65% {
                transform: scale(1.15)
            }

            100% {
                transform: scale(1)
            }
        }
    </style>

    <script>
        function closeOverlay() {
            var ov = document.getElementById('successOverlay');
            ov.style.opacity = '0';
            ov.style.transition = 'opacity 0.2s';
            setTimeout(function() {
                ov.remove();
            }, 220);
        }
        // Close on backdrop click
        document.getElementById('successOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeOverlay();
        });
    </script>
<?php endif; ?>

<style>
    /* FEATURE CARD */
    .feature-box {
        background: #fff;
        padding: 30px;
        border-radius: 14px;
        /* curved edge */
        border: 1px solid #e5e5e5;
        /* ash border */
        transition: all 0.35s ease;
    }

    /* HOVER EFFECT */
    .feature-box:hover {
        border-color: #ff8c1a;
        /* green border */
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        transform: translateY(-6px);
    }

    /* ICON ANIMATION (optional but premium look) */
    .feature-icon {
        transition: 0.3s ease;
    }

    .feature-box:hover .feature-icon {
        transform: scale(1.08);
    }

    @media (max-width: 992px) {
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    /* FORM CARD */
    .tracking-box {
        background: #fff;
        border-radius: 12px;
        padding: 40px;
        border: 1px solid #e5e7eb;
        max-width: 800px;
        margin: auto;
    }

    /* FORM */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        font-size: 14px;
        display: block;
        margin-bottom: 6px;
    }

    /* INPUTS */
    input,
    select,
    textarea {
        width: 100%;
        padding: 14px;
        border-radius: 8px;
        border: none;
        background: #f3f4f6;
        font-size: 14px;
        outline: none;
    }

    textarea {
        resize: none;
    }

    /* GRID */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* UPLOAD BOX */
    .upload-box {
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        padding: 35px;
        text-align: center;
        cursor: pointer;
        background: #fafafa;
        transition: .3s;
    }

    .upload-box:hover {
        border-color: #f97316;
        background: #fff7ed;
    }

    .upload-box.dragover {
        border-color: #f97316;
        background: #fff7ed;
    }

    .upload-icon {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .upload-main {
        font-weight: 600;
        margin-bottom: 5px;
    }

    .file-name {
        margin-top: 8px;
        font-size: 13px;
        color: #16a34a;
    }

    /* CHECKBOX */
    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: #374151;
        margin-top: 15px;
    }

    .checkbox-group input {
        width: auto;
        margin-top: 3px;
    }

    /* BUTTON */
    .btns {
        margin-top: 20px;
        width: 100%;
        background: #f97316;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
    }

    .btns:hover {
        background: #ea580c;
    }

    /* MOBILE */
    @media(max-width:768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    .careers-section {
        background: #f5f6f8;
        padding: 70px 20px;
    }

    /* GRID */
    .jobs-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        margin-top: 40px;
    }

    /* CARD */
    .career-card {
        background: #fff;
        border-radius: 14px;
        padding: 28px;
        border: 1px solid #e5e7eb;
        transition: .3s ease;
    }

    .career-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        transform: translateY(-3px);
    }

    /* HEADER */
    .career-header {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    /* ICON */
    .career-icon {
        width: 48px;
        height: 48px;
        background: #0a2463;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 20px;
    }

    /* TITLE */
    .career-header h4 {
        margin: 0;
        font-size: 18px;
        color: #0a2463;
        font-weight: 700;
    }

    /* TAGS */
    .career-tags {
        margin-top: 6px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tag {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .tag.gray {
        background: #eef0f3;
        color: #4b5563;
    }

    .tag.orange {
        background: #ffe8d9;
        color: #f97316;
    }

    /* DESCRIPTION */
    .career-desc {
        color: #4b5563;
        font-size: 14px;
        line-height: 1.6;
        margin: 18px 0;
    }

    /* APPLY LINK */
    .career-link {
        color: #f97316;
        font-weight: 600;
        text-decoration: none;
    }

    .career-link:hover {
        text-decoration: underline;
    }

    /* MOBILE */
    @media(max-width:768px) {
        .jobs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="hero-simple">
    <div class="container">
        <h1>Join the Lugomax Team</h1>
        <p>Build your career with a growing UK logistics company committed to reliability, professionalism, and excellence.</p>
    </div>
</section>

<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #0a2463;">Why Work With Us?</h2>
            <p>We invest in our people because they are the foundation of our success.</p>
        </div>

        <div class="grid-4">

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-8 w-8 text-white" aria-hidden="true" data-fg-eery22=":2.13088:/components/pages/Careers.tsx:130:21:4399:47:e:benefit.icon">
                        <path d="M16 7h6v6"></path>
                        <path d="m22 7-8.5 8.5-5-5L2 17"></path>
                    </svg>
                </div>
                <h6 style="color: #0a2463;">Career Growth</h6>
                <p class="mt-3">Clear progression pathways and opportunities for professional development.</p>
            </div>

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart h-8 w-8 text-white" aria-hidden="true" data-fg-eery22=":2.13088:/components/pages/Careers.tsx:130:21:4399:47:e:benefit.icon">
                        <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
                    </svg>
                </div>
                <h6 style="color: #0a2463;">Work-Life Balance</h6>
                <p class="mt-3">Flexible working arrangements and supportive management.</p>
            </div>

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-eery22=":2.13088:/components/pages/Careers.tsx:130:21:4399:47:e:benefit.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                    </svg>
                </div>
                <h6 style="color: #0a2463;">Competitive Benefits</h6>
                <p class="mt-3">Attractive salary packages, pension, and comprehensive benefits.</p>
            </div>

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-8 w-8 text-white" aria-hidden="true" data-fg-eery22=":2.13088:/components/pages/Careers.tsx:130:21:4399:47:e:benefit.icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h6 style="color: #0a2463;">Great Team</h6>
                <p class="mt-3">Join a professional, supportive team committed to excellence.</p>
            </div>

        </div>

    </div>
</section>

<section class="section careers-section">
    <div class="container">

        <div class="section-header">
            <h2>Current Opportunities</h2>
            <p>Explore our open positions and find your next career move.</p>
        </div>

        <div class="jobs-grid">

            <?php if (empty($jobs)): ?>
                <div style="text-align:center;padding:60px 20px;color:#64748B;">
                    <div style="font-size:3rem;margin-bottom:16px;">📋</div>
                    <h3 style="margin-bottom:8px;">No Open Positions Right Now</h3>
                    <p>We're always growing — send a speculative application using the form below!</p>
                </div>
            <?php else: ?>

                <?php foreach ($jobs as $job): ?>
                    <!-- CARD -->
                    <div class="career-card">
                        <div class="career-header">
                            <div class="career-icon">
                                🎁
                            </div>

                            <div>
                                <h4><?= escape($job['title']) ?></h4>
                                <div class="career-tags">
                                    <span class="tag gray"><?= escape($job['department']) ?> &bull; <?= escape($job['location']) ?></span>
                                    <span class="tag orange"><?= escape($job['type']) ?></span>

                                    <?php if ($job['arrangement'] !== 'On-site'): ?>
                                        <span style="background:#d1fae5;color:#065f46;padding:5px 12px;border-radius:20px;font-size:.82rem;font-weight:600;">
                                            <?= escape($job['arrangement']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($job['salary_range']): ?>
                                        <span style="background:#fef3c7;color:#92400e;padding:5px 12px;border-radius:20px;font-size:.82rem;font-weight:600;">
                                            <?= escape($job['salary_range']) ?>
                                        </span>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                        <p class="career-desc">
                            <?= nl2br(escape($job['description'])) ?>
                        </p>

                        <?php if ($job['requirements']): ?>
                            <details style="margin-bottom:16px;">
                                <summary style="cursor:pointer;color:#FF6B2C;font-weight:600;user-select:none;list-style:none;">
                                    ▶ View Requirements
                                </summary>
                                <div style="margin-top:12px;padding:16px;background:#f8f9fa;border-radius:8px;
                                    color:#374151;line-height:1.8;font-size:.95rem;white-space:pre-line;">
                                    <?= escape($job['requirements']) ?>
                                </div>
                            </details>
                        <?php endif; ?>

                        <a href="#apply" onclick="selectPosition('<?= escape($job['title']) ?>')" class="career-link">
                            Apply for this position →
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
</section>


<section class="section" id="apply">
    <div class="container">

        <div class="section-header">
            <h2>Apply Here</h2>
            <p>Submit your application and a member of our team will be in touch.</p>
        </div>



        <div class="tracking-box" id="apply">

            <form method="POST" enctype="multipart/form-data">
                <div style="text-align: center; color:#16a34a; font-size: 16px;" class="mb-3"><?php display_flash(); ?></div>

                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                <!-- FULL NAME -->
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>

                <!-- EMAIL + PHONE -->
                <div class="form-grid">

                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="your.email@example.com" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" name="phone" placeholder="+44 XXXX XXXXX" required>
                    </div>

                </div>

                <!-- Position -->
                <div style="margin-bottom:20px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;color:#0A1F44;">Position Applying For *</label>
                    <select name="position" id="positionSelect" required
                        style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:1rem;">
                        <option value="">Select a position...</option>
                        <?php foreach ($jobs as $job): ?>
                            <option value="<?= escape($job['title']) ?>">
                                <?= escape($job['title']) ?> — <?= escape($job['department']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="Speculative Application">Speculative Application</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Experience + Availability -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#0A1F44;">Years of Experience</label>
                        <select name="experience_years"
                            style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:1rem;">
                            <option value="0">Less than 1 year</option>
                            <option value="1">1–2 years</option>
                            <option value="3">3–5 years</option>
                            <option value="6">6–10 years</option>
                            <option value="11">10+ years</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-weight:600;color:#0A1F44;">Availability</label>
                        <select name="availability"
                            style="width:100%;padding:12px 15px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:1rem;">
                            <option value="Immediately">Immediately</option>
                            <option value="2 weeks notice">2 weeks notice</option>
                            <option value="1 month notice">1 month notice</option>
                            <option value="3 months notice">3 months notice</option>
                        </select>
                    </div>
                </div>

                <!-- COVER LETTER -->
                <div class="form-group">
                    <label>Cover Letter / Additional Information</label>
                    <textarea name="cover_letter" rows="4"
                        placeholder="Tell us why you'd be a great fit for this role..."></textarea>
                </div>

                <!-- UPLOAD -->
                <div class="form-group">
                    <label>Upload CV / Resume *</label>

                    <div class="upload-box" id="uploadBox">
                        <input type="file" name="resume" id="resumeInput"
                            accept=".pdf,.doc,.docx" required hidden>

                        <div class="upload-content">
                            <div class="upload-icon">⬆️</div>
                            <p class="upload-main">Click to upload or drag and drop</p>
                            <small>PDF, DOC, DOCX (MAX. 5MB)</small>
                        </div>
                    </div>

                    <p id="fileName" class="file-name"></p>
                </div>

                <!-- CONSENT -->
                <div class="checkbox-group">
                    <input type="checkbox" required>
                    <label>
                        I confirm that the information provided is accurate and I consent to
                        <strong>Lugomax Logistics & Services Limited</strong>
                        processing my data for recruitment purposes. *
                    </label>
                </div>

                <button type="submit" class="btns">
                    Submit Application
                </button>

            </form>

        </div>
    </div>
</section>

<section class="why-choose">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What We Value in Our Team</h2>
            <p class="section-description"></p>
        </div>
        <div class="features-grid">


            <div class="feature-item">
                <div style="background: #0a2463;" class="feature-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-eery115=":2.13088:/components/pages/Careers.tsx:309:21:12728:45:e:value.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Reliability</h3>
                <p class="feature-description">Dependable, punctual, and accountable in every task.</p>
            </div>

            <div class="feature-item">
                <div style="background: #0a2463;" class="feature-icon peach">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-8 w-8 text-white" aria-hidden="true" data-fg-eery115=":2.13088:/components/pages/Careers.tsx:309:21:12728:45:e:value.icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h3 class="feature-title">Professionalism</h3>
                <p class="feature-description">Courteous conduct and high standards in all interactions.</p>
            </div>

            <div class="feature-item">
                <div style="background: #0a2463;" class="feature-icon light-peach">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart h-8 w-8 text-white" aria-hidden="true" data-fg-eery115=":2.13088:/components/pages/Careers.tsx:309:21:12728:45:e:value.icon">
                        <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Customer Focus</h3>
                <p class="feature-description">Dedication to exceeding customer expectations.</p>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
    const uploadBox = document.getElementById("uploadBox");
    const fileInput = document.getElementById("resumeInput");
    const fileName = document.getElementById("fileName");

    uploadBox.addEventListener("click", () => fileInput.click());

    uploadBox.addEventListener("dragover", e => {
        e.preventDefault();
        uploadBox.classList.add("dragover");
    });

    uploadBox.addEventListener("dragleave", () => {
        uploadBox.classList.remove("dragover");
    });

    uploadBox.addEventListener("drop", e => {
        e.preventDefault();
        uploadBox.classList.remove("dragover");
        fileInput.files = e.dataTransfer.files;
        showFile();
    });

    fileInput.addEventListener("change", showFile);

    function showFile() {
        if (fileInput.files.length > 0) {
            fileName.textContent =
                "Selected: " + fileInput.files[0].name;
        }
    }
</script>