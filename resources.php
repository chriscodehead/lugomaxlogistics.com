<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'resources';
$page_title = 'Resources & Help Centre';

// Get database connection
$db = getDB();
//Fetch  resources 
$stmt = $db->query("SELECT * FROM resources WHERE status ='active' ORDER BY id DESC");
$resources = $stmt->fetchAll();

$videos = $db->query("SELECT * FROM video_resources ORDER BY created_at DESC")
    ->fetchAll(PDO::FETCH_ASSOC);

$faqs = $db->query("SELECT * FROM faqs ORDER BY display_order ASC")
    ->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<section class="hero-simple">
    <div class="container">
        <h1>Resources & Help Centre</h1>
        <p>Find answers, guides, and support to make your experience with Lugomax seamless.</p>
    </div>
</section>

<section style="background: white; padding:10px; border-bottom: 1px solid rgba(154, 163, 186, 0.87);" class="help-support-section">
    <div class="container">
        <div class="help-search">
            <input type="text" placeholder="Search for help...">
            <span class="search-icon">🔍</span>
        </div>
    </div>
</section>

<section class="help-support-section">

    <div class="container">

        <!-- Support Cards -->
        <div class="support-grid">

            <!-- Call -->
            <div class="support-card">
                <div class="support-icon">
                    ☎
                </div>
                <h4>Call Us</h4>
                <p>Speak with our support team</p>
                <a href="tel:<?= escape(get_setting('site_phone', '+44 (0) XXX XXX XXXX')) ?>"><?= escape(get_setting('site_phone', '+44 (0) XXX XXX XXXX')) ?></a>
            </div>

            <!-- Email -->
            <div class="support-card">
                <div class="support-icon">
                    ✉
                </div>
                <h4>Email Us</h4>
                <p>We'll respond within 24 hours</p>
                <a href="mailto:<?= escape(get_setting('contact_email', 'contact@lugomaxservices.com')) ?>"><?= escape(get_setting('contact_email', 'contact@lugomaxservices.com')) ?></a>
            </div>

            <!-- Live Chat -->
            <div class="support-card">
                <div class="support-icon">
                    💬
                </div>
                <h4>Live Chat</h4>
                <p>Chat with us in real-time</p>
                <a href="<?= escape(get_setting('whatsapp_link')) ?>">Start Chat</a>
            </div>

        </div>

    </div>

</section>


<section class="section" style="background: white;">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <p>Find quick answers to common questions about our services.</p>
        </div>

        <div style="max-width: 900px; margin: 0 auto;">
            <?php if (count($faqs) > 0): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><?= escape($faq['title']) ?></div>
                        <div class="faq-answer">
                            <p><?= escape($faq['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>


        </div>
    </div>
</section>

<!-- =========================
   GUIDES & RESOURCES SECTION
========================= -->
<?php if (count($resources) > 0): ?>
    <section class="resources-section">

        <div class="container">

            <h2 class="section-title">Guides & Resources</h2>
            <p class="section-subtitle">
                Download helpful guides and documentation to support your logistics needs.
            </p>

            <div class="resource-grid">

                <!-- CARD 1 -->
                <?php foreach ($resources as $resource): ?>
                    <div class="resource-card">
                        <div class="icon">📄</div>
                        <h4><?= escape($resource['title']) ?></h4>
                        <p><?= escape($resource['description']) ?></p>
                        <a href="assets/resources/<?= escape($resource['file_name']) ?>" target="_blank" class="download">⬇ Download <?= escape($resource['file_type']) ?></a>
                    </div>
                <?php endforeach; ?>


            </div>

        </div>
    </section>
<?php endif; ?>

<!-- =========================
   VIDEO TUTORIALS SECTION
========================= -->
<?php if (count($videos) > 0): ?>
    <section class="video-section" style="background: white;">

        <div class="container">

            <h2 class="section-title">Video Tutorials</h2>
            <p class="section-subtitle">
                Visual guides to help you navigate our services.
            </p>

            <div class="video-grid">

                <!-- VIDEO 1 -->
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <a href="<?= escape($video['video_link']) ?>">
                            <div style="background-image: url('assets/video-covers/<?= escape($video['cover_image']) ?>'); background-size: cover; background-position: center;" class="video-thumb">
                                <span class="duration"></span>
                                <div class="play-btn">▶</div>
                            </div>
                        </a>
                        <p><?= escape($video['title']) ?></p>
                    </div>
                <?php endforeach; ?>


            </div>

        </div>
    </section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <h2>Still Need Help?</h2>
        <p>Our customer support team is ready to assist you with any questions or concerns.</p>
        <div class="cta-actions">
            <a href="contact" class="btn btn-primary">Contact Support</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    .help-support-section {
        background: #f6f7fb;
        padding: 60px 0 80px;
    }

    /* SEARCH BAR */
    .help-search {
        max-width: 520px;
        margin: 0 auto 0px;
        position: relative;
    }

    .help-search input {
        width: 100%;
        padding: 14px 45px 14px 18px;
        border: 1px solid #e2e5ec;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: #fff;
    }

    .help-search input:focus {
        border-color: #1e3a8a;
    }

    .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        opacity: .5;
    }

    /* GRID */
    .support-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        max-width: 900px;
        margin: 0 auto;
    }

    /* CARD */
    .support-card {
        background: #fff;
        border: 1px solid #e6e8ef;
        border-radius: 12px;
        text-align: center;
        padding: 35px 25px;
        transition: 0.25s ease;
    }

    .support-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    /* ICON */
    .support-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 18px;
        background: #162f6a;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 22px;
    }

    /* TEXT */
    .support-card h4 {
        margin-bottom: 8px;
        color: #0f2a5f;
        font-weight: 600;
    }

    .support-card p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 12px;
    }

    .support-card a {
        color: #ff7a00;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
    }

    /* MOBILE */
    @media(max-width:768px) {
        .support-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    .section-title {
        color: #0b2c6b;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .section-subtitle {
        color: #6b7280;
        margin-bottom: 40px;
    }

    /* ===== RESOURCES ===== */
    .resources-section {
        background: #f5f6f8;
        padding: 70px 0;
    }

    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
    }

    .resource-card {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        text-align: left;
        box-shadow: 0 0 0 1px #eee;
        transition: .3s;
    }

    .resource-card:hover {
        transform: translateY(-5px);
    }

    .resource-card .icon {
        font-size: 22px;
        color: #ff7a00;
        margin-bottom: 15px;
    }

    .resource-card h4 {
        color: #0b2c6b;
        margin-bottom: 10px;
    }

    .resource-card p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 18px;
    }

    .download {
        color: #ff7a00;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }


    /* ===== VIDEOS ===== */
    .video-section {
        background: #f5f6f8;
        padding: 40px 0 80px;
    }

    .video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
    }

    .video-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 0 1px #eee;
    }

    .video-thumb {
        height: 160px;
        background: #d1d5db;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .play-btn {
        background: #ff7a00;
        color: #fff;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 22px;
    }

    .duration {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #333;
        color: #fff;
        padding: 3px 7px;
        font-size: 12px;
        border-radius: 4px;
    }

    .video-card p {
        padding: 15px;
        font-weight: 600;
        color: #0b2c6b;
    }
</style>