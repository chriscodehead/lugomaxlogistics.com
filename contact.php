<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'contact';
$page_title = 'Contact Us';
$page_description = 'Get in touch with Lugomax Logistics';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid form submission. Please try again.');
    } else {
        $name = sanitize_input($_POST['fullname']);
        $email = sanitize_input($_POST['email']);
        $phone = sanitize_input($_POST['phone'] ?? '');
        $subject = sanitize_input($_POST['subject']);
        $message = sanitize_input($_POST['message']);

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            set_flash('error', 'Please fill in all required fields.');
        } elseif (!validate_email($email)) {
            set_flash('error', 'Please enter a valid email address.');
        } else {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt->execute([$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? ''])) {
                set_flash('success', 'Thank you for your message! We will respond within 24 hours.');

                // Send email notification to admin
                send_email(
                    get_setting('site_email', 'info@lugomax.co.uk'),
                    "New Contact Message from " . $name,
                    "<h3>New Contact Form Submission</h3>
                    <p><strong>From:</strong> " . escape($name) . " (" . escape($email) . ")</p>
                    <p><strong>Phone:</strong> " . escape($phone) . "</p>
                    <p><strong>Subject:</strong> " . escape($subject) . "</p>
                    <p><strong>Message:</strong><br>" . nl2br(escape($message)) . "</p>"
                );

                redirect('contact');
            } else {
                set_flash('error', 'Something went wrong. Please try again.');
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="hero-simple">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with our team. We're here to answer your questions and support your logistics needs.</p>
    </div>
</section>

<section class="contact-info-section">
    <div class="container">

        <div class="contact-grid">

            <!-- Call Us -->
            <div class="contact-card">
                <div class="icon-circle">
                    ☎
                </div>

                <h3>Call Us</h3>

                <p class="label">Main Line</p>
                <p class="value"><?= escape(get_setting('site_phone', '+44 (0) XXX XXX XXXX')) ?></p>

                <p class="label">Business Hours</p>
                <p class="value"><?= escape(get_setting('business_hour')) ?></p>

                <p class="label">Weekend</p>
                <p class="value"><?= escape(get_setting('weekend_hours')) ?></p>
            </div>


            <!-- Email Us -->
            <div class="contact-card">
                <div class="icon-circle">
                    ✉
                </div>

                <h3>Email Us</h3>

                <p class="label">General Enquiries</p>
                <p class="value"><?= escape(get_setting('site_email')) ?></p>

                <p class="label">Business Accounts</p>
                <p class="value"><?= escape(get_setting('site_business_email')) ?></p>

                <p class="label">Support</p>
                <p class="value"><?= escape(get_setting('support_email')) ?></p>
            </div>


            <!-- Head Office -->
            <div class="contact-card">
                <div class="icon-circle">
                    📍
                </div>

                <h3>Head Office</h3>

                <p class="label">Address</p>
                <p class="value"><?= escape(get_setting('company_ddress')) ?></p>

                <p class="label">Company Number</p>
                <p class="value"><?= escape(get_setting('company_number')) ?></p>


            </div>

        </div>
    </div>
</section>

<section style="background: white;" class="contact-form-section mb-5">
    <div class="container contact-layout">

        <!-- LEFT : FORM -->
        <div class="contact-form-box">
            <h2>Send Us a Message</h2>
            <p class="subtitle">
                Fill out the form below and we'll get back to you as soon as possible.
            </p>

            <span style="color: #f58220;"><?php display_flash(); ?></span>

            <form method="POST" action="contact">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

                <label>Full Name *</label>
                <input id="fullname" name="fullname" type="text" placeholder="Enter your full name" required>

                <div class="two-col">
                    <div>
                        <label>Email Address *</label>
                        <input id="email" name="email" type="email" placeholder="your.email@example.com" required>
                    </div>

                    <div>
                        <label>Phone Number</label>
                        <input id="phone" name="phone" type="text" placeholder="+44 XXXX XXXXX">
                    </div>
                </div>

                <label>Subject *</label>
                <select id="subject" name="subject" required>
                    <option>Select a subject</option>
                    <option>General Enquiry</option>
                    <option>Request a Quote</option>
                    <option>Tracking Issue</option>
                    <option>Business Account</option>
                    <option>Complaint</option>
                    <option>Feedback</option>
                    <option>Other</option>
                </select>

                <label>Message *</label>
                <textarea id="message" name="message" placeholder="Please provide as much detail as possible..." required></textarea>

                <div class="checkbox-group mb-3">
                    <input type="checkbox" id="consent" name="consent" required>
                    <label for="consent">I consent to Lugomax Logistics & Services Limited processing my data to respond to my enquiry. *</label>
                </div>

                <button type="submit" class="send-btn"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send mr-2 h-5 w-5" aria-hidden="true" data-fg-cm5984=":2.13670:/components/pages/Contact.tsx:226:23:8908:33:e:Send::::::dtm">
                        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                        <path d="m21.854 2.147-10.94 10.939"></path>
                    </svg> Send Message</button>
            </form>
        </div>


        <!-- RIGHT SIDEBAR -->
        <div class="contact-sidebar">

            <!-- Office Hours -->
            <div class="side-card">
                <h4>Office Hours</h4>
                <p>Monday - Friday <span>8:00 AM - 6:00 PM</span></p>
                <p>Saturday <span>9:00 AM - 2:00 PM</span></p>
                <p>Sunday <span>Closed</span></p>
                <p>Bank Holidays <span>Closed</span></p>
            </div>

            <!-- Live Chat -->
            <div class="side-card live-chat">
                <h4 style="color: white;">
                    <span class="icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square h-6 w-6 text-white" aria-hidden="true" data-fg-cm59108=":2.13670:/components/pages/Contact.tsx:271:23:10916:48:e:MessageSquare::::::BITa">
                            <path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </span> Live Chat
                </h4>
                <p>Need immediate assistance? Chat with our team in real-time.</p>
                <a href="<?= escape(get_setting('whatsapp_link')) ?>"><button class="chat-btn">Start Chat on WhatsApp</button></a>
            </div>

            <!-- Quick Links -->
            <div class="side-card">
                <h4>Quick Links</h4>
                <a href="track">Track Your Order →</a>
                <a href="quote">Get a Quote →</a>
                <a href="contact">Help Centre →</a>
                <a href="resources">FAQ →</a>
            </div>

        </div>
    </div>
</section>

<section class="response-time-section">
    <div class="response-container">

        <h3 class="response-title">Our Response Times</h3>

        <div class="response-items">

            <div class="response-box">
                <h2>Quote Requests:</h2>
                <p><b>Core Services:</b> Within 2hrs (during business hours)</p>
                <p><b>Specialized Services:</b> Within 2 - 5 days
                    (allowing time for careful assessment and coordination)</p>
            </div>

            <div class="response-box">
                <h2>Email Enquiries:</h2>
                <p> Within 2 hours (during business hours)</p>
            </div>

            <div class="response-box">
                <h2>Whatsapp Messages:</h2>
                <p> Instant (Where possible)</p>
            </div>

            <div class="response-box">
                <h2>Calls & Live Chat:</h2>
                <p>Response Times may vary</p>
            </div>

        </div>

    </div>
</section>


<?php include 'includes/footer.php'; ?>
<style>
    .contact-info-section {
        background: #f5f6f8;
        padding: 80px 20px;
    }


    .contact-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    .contact-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 35px 30px;
        text-align: left;
        transition: 0.3s ease;
    }

    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        background: #f58220;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 18px;
    }

    .contact-card h3 {
        font-size: 18px;
        color: #0b2c6b;
        margin-bottom: 18px;
    }

    .label {
        font-size: 12px;
        color: #8a8f98;
        margin-top: 12px;
    }

    .value {
        font-size: 14px;
        color: #1f2937;
        margin-top: 4px;
    }

    /* Responsive */
    @media(max-width:900px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    .contact-form-section {
        background: #f5f6f8;
        padding: 80px 20px 0;
    }

    .contact-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    /* FORM */
    .contact-form-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 30px;
    }

    .contact-form-box h2 {
        color: #0b2c6b;
        margin-bottom: 5px;
    }

    .subtitle {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 20px;
    }

    form label {
        font-size: 13px;
        font-weight: 600;
        display: block;
        margin: 12px 0 6px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #f9fafb;
    }

    textarea {
        height: 100px;
        resize: none;
    }

    .two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .consent {
        display: flex;
        gap: 10px;
        margin: 15px 0;
        font-size: 12px;
        color: #6b7280;
    }

    .send-btn {
        width: 100%;
        background: #f58220;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }

    /* SIDEBAR */
    .contact-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .side-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
    }

    .side-card h4 {
        color: #0b2c6b;
        margin-bottom: 15px;
    }

    .side-card p {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin: 8px 0;
    }

    .side-card a {
        display: block;
        color: #f58220;
        margin: 8px 0;
        text-decoration: none;
    }

    .live-chat {
        background: #132f6b;
        color: #fff;
    }

    .chat-btn {
        margin-top: 15px;
        background: #f58220;
        color: #fff;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 6px;
        cursor: pointer;
    }
</style>

<style>
    .response-time-section {
        background: #f3f4f6;
        padding: 45px 20px;
        border-top: 1px solid #e5e7eb;
        border-bottom: 4px solid #0b2c6b;
        /* blue bottom line like design */
    }

    .response-container {
        max-width: 900px;
        margin: auto;
        text-align: center;
    }

    .response-title {
        color: #0b2c6b;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .response-items {
        display: flex;
        justify-content: center;
        gap: 90px;
    }

    .response-box h2 {
        color: #f58220;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .response-box p {
        font-size: 12px;
        color: #6b7280;
    }

    /* MOBILE */
    @media(max-width:768px) {
        .response-items {
            flex-direction: column;
            gap: 25px;
        }
    }

    /* ===== CONTACT LAYOUT ===== */
    .contact-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        /* desktop layout */
        gap: 40px;
        align-items: start;
    }

    /* LEFT */
    .contact-form-box {
        width: 100%;
    }

    /* RIGHT */
    .contact-sidebar {
        width: 100%;
    }

    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 768px) {

        .contact-layout {
            grid-template-columns: 1fr;
            /* stack */
            gap: 25px;
        }

        .contact-form-box,
        .contact-sidebar {
            width: 100%;
        }

        /* make email + phone stack too */
        .two-col {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }
</style>