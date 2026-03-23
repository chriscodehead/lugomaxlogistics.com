<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$db = getDB();
$tracking_result = null;
$error = '';

if (isset($_GET['track'])) {
    $tracking_number = sanitize_input($_GET['track']);

    try {
        $stmt = $db->prepare("SELECT * FROM orders WHERE tracking_number = ?");
        $stmt->execute([$tracking_number]);
        $tracking_result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tracking_result) {
            $stmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at ASC");
            $stmt->execute([$tracking_result['id']]);
            $tracking_result['history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Tracking number not found. Please check and try again.";
        }
    } catch (Exception $e) {
        $error = "Error retrieving tracking information.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Lugomax Logistics</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<style>
    /* SECTION BACKGROUND */
    .track-package-section {
        background: #f5f6f8;
        padding: 90px 20px;
        display: flex;
        justify-content: center;
    }

    /* CARD */
    .track-card {
        background: #ffffff;
        max-width: 600px;
        width: 100%;
        padding: 40px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        text-align: left;
    }

    /* LABEL */
    .track-label {
        font-weight: 600;
        color: #111827;
        display: block;
        margin-bottom: 4px;
    }

    /* SUBTEXT */
    .track-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 18px;
    }

    /* INPUT */
    .track-input {
        width: 100%;
        padding: 14px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
        font-size: 14px;
        outline: none;
        margin-bottom: 18px;
    }

    .track-input:focus {
        border-color: #f58220;
        background: #fff;
    }

    /* BUTTON */
    .track-btn {
        width: 100%;
        background: #f58220;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s ease;
    }

    .track-btn:hover {
        background: #e46f0c;
    }

    /* DIVIDER */
    .track-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 28px 0 18px;
    }

    /* HELP TEXT */
    .track-help {
        text-align: center;
        font-size: 13px;
        color: #6b7280;
    }

    .track-help a {
        color: #f58220;
        text-decoration: none;
        font-weight: 500;
    }

    .track-help a:hover {
        text-decoration: underline;
    }
</style>

<style>
    .track-hero {
        background: linear-gradient(135deg, #0A1F44 0%, #1a3a6b 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
    }

    .track-hero h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }


    .alert-error {
        background: #fee2e2;
        color: #dc2626;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 20px auto;
        max-width: 700px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .order-info {
            grid-template-columns: 1fr;
        }

        .addresses-display {
            grid-template-columns: 1fr;
        }

        .arrow-icon {
            transform: rotate(90deg);
            text-align: center;
        }

        .search-box {
            flex-direction: column;
        }
    }
</style>

<body>
    <?php include 'includes/header.php'; ?>

    <section class="track-hero">
        <div class="container">
            <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package h-16 w-16 mx-auto mb-6 text-[#F77F00]" aria-hidden="true" data-fg-c4ys5=":2.11886:/components/pages/Tracking.tsx:79:13:2164:61:e:Package::::::BczM">
                <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"></path>
                <path d="M12 22V12"></path>
                <polyline points="3.29 7 12 12 20.71 7"></polyline>
                <path d="m7.5 4.27 9 5.15"></path>
            </svg>
            <h1 style="color: white;">Track Your Delivery</h1>
            <p>Enter your tracking number below to see real-time updates on your parcel's journey.</p>
        </div>
    </section>

    <section style="background: #fff;" class="track-section track-package-section">
        <div class="container">

            <?php if ($error): ?>
                <div class="alert-box ">
                    <strong>❌ <?= htmlspecialchars($error) ?></strong><br>
                    <small>Please verify your tracking number and try again.</small>
                </div>
            <?php endif; ?>

            <center>
                <div class="track-card  mb-3 mt-3">

                    <label class="track-label">Tracking Number</label>
                    <p class="track-subtitle">
                        Enter the tracking number from your confirmation email
                    </p>

                    <form class="track-form" method="GET">
                        <input type="text" name="track" placeholder="Enter tracking number (e.g., LGX123456)"
                            value="<?= htmlspecialchars($_GET['track'] ?? '') ?>" required>

                        <button type="submit" class="track-btn mt-1">
                            🔍 Track Package
                        </button>
                    </form>

                    <div class="track-divider"></div>

                    <p class="track-help">
                        Having trouble tracking?
                        <a href="contact">Contact our support team</a>
                    </p>

                </div>
            </center>


        </div>
    </section>

    <?php if ($tracking_result): ?>
        <section id="printTrackingHelp" class="tracking-result-section">
            <div class="container tracking-wrapper">

                <!-- STATUS CARD -->
                <div style="margin-bottom: 50px;" class="tracking-status-card">

                    <div class="status-header">
                        <div class="status-icon">🚚</div>

                        <div>
                            <h3><?php print cleanStatus(ucfirst(htmlspecialchars($tracking_result['status']))) ?></h3>
                            <p><strong>Tracking Number:</strong> <?= htmlspecialchars($tracking_result['tracking_number']) ?></p>
                            <p><strong>Current Location:</strong> <?= htmlspecialchars($tracking_result['current_location'] ?: '—') ?></p>
                            <p><strong>Estimated Delivery:</strong> <?= formatDeliveryTime($tracking_result['estimated_delivery']) ?></p>

                            <p style="display: none;"><strong>PickUp Address:</strong> <?= nl2br(htmlspecialchars($tracking_result['pickup_address'])) ?></p>

                            <p style="display: none;"><strong>Delivery Address:</strong> <?= nl2br(htmlspecialchars($tracking_result['delivery_address'])) ?></p>
                        </div>
                    </div>
                    <div class="status-alert">
                        ✅ Your parcel is on schedule and will arrive as expected.
                    </div>
                </div>

                <!-- TRACKING HISTORY -->
                <?php if (!empty($tracking_result['history'])): ?>
                    <div class="tracking-history-card">

                        <h4>Tracking History</h4>

                        <div class="timeline">

                            <?php
                            $total = count($tracking_result['history']);
                            foreach ($tracking_result['history'] as $idx => $h):
                                $is_active = ($idx === $total - 1);
                                $is_done = ($idx < $total - 1);
                            ?>
                                <div class="timeline-item <?= htmlspecialchars($h['current_state']) ?>">
                                    <div style="padding: 5px;" class="timeline-icon">
                                        <?php if ($h['current_state'] == 'completed') { ?> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-white" aria-hidden="true" data-fg-c4ys74=":2.11886:/components/pages/Tracking.tsx:197:29:7457:46:e:CheckCircle::::::C4Wc">
                                                <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                                <path d="m9 11 3 3L22 4"></path>
                                            </svg><?php } ?>

                                        <?php if ($h['current_state'] == 'active') { ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-6 w-6 text-white" aria-hidden="true" data-fg-c4ys73=":2.11886:/components/pages/Tracking.tsx:195:29:7356:40:e:Truck::::::Dl9l">
                                                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                                                <path d="M15 18H9"></path>
                                                <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
                                                <circle cx="17" cy="18" r="2"></circle>
                                                <circle cx="7" cy="18" r="2"></circle>
                                            </svg><?php } ?>

                                        <?php if ($h['current_state'] == 'pending') { ?><svg style="color: #ccc;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-6 w-6 text-gray-400" aria-hidden="true" data-fg-c4ys75=":2.11886:/components/pages/Tracking.tsx:200:27:7588:43:e:Clock::::::EGEm">
                                                <path d="M12 6v6l4 2"></path>
                                                <circle cx="12" cy="12" r="10"></circle>
                                            </svg><?php } ?>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>
                                            <?= htmlspecialchars($h['status']) ?>

                                            <?php if ($h['current_state'] == 'active') { ?><span style="background: #f58220; color:white; border-radius:10px; padding:3px; font-size:9px;">Current</span><?php } ?>

                                        </strong>
                                        <span>

                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4" aria-hidden="true" data-fg-c4ys84=":2.11886:/components/pages/Tracking.tsx:215:27:8342:30:e:MapPin::::::BveZ">
                                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <?= htmlspecialchars($h['location']) ?></span>
                                        <p style="color: #9ca3af; font-size:10px;"><?= htmlspecialchars($h['notes']) ?></p>
                                        <small><b><?= date('l, d M Y \a\t g:i A', strtotime($h['created_at'])) ?></b></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>

                    </div>
                <?php else: ?>
                    <div style="padding:40px;text-align:center;color:#64748B;">
                        No tracking history available yet.
                    </div>
                <?php endif; ?>

                <!-- ACTION BUTTONS -->
                <center>
                    <div class="tracking-actions">
                        <button onclick="printTrackingHelp()" style="width: 49%;" class="btn-outline">Print Tracking Details</button>
                        <a style="width: 49%;" href="track" class="btn-primary">Track Another Package</a>

                    </div>
                </center>

            </div>
        </section>
    <?php endif; ?>

    <section style="background: #fff;" class="tracking-help-section">
        <div class="container">

            <!-- Header -->
            <div class="section-header">
                <h2>Need Help with Tracking?</h2>
                <p>Here's what you need to know about tracking your parcel.</p>
            </div>

            <!-- FAQ GRID -->
            <div class="tracking-help-grid">

                <div class="help-card">
                    <h4>Where do I find my tracking number?</h4>
                    <p>
                        Your tracking number is included in the confirmation
                        email sent when your parcel was dispatched.
                    </p>
                </div>

                <div class="help-card">
                    <h4>How often is tracking updated?</h4>
                    <p>
                        Tracking information is updated in real-time as your
                        parcel moves through our network.
                    </p>
                </div>

                <div class="help-card">
                    <h4>What if tracking hasn't updated?</h4>
                    <p>
                        If tracking hasn't updated in 24 hours, please contact
                        our support team for assistance.
                    </p>
                </div>

                <div class="help-card">
                    <h4>Can I change my delivery address?</h4>
                    <p>
                        Contact us immediately if you need to change the delivery
                        address. Changes may be possible before dispatch.
                    </p>
                </div>

            </div>

            <!-- Bottom CTA -->
            <div class="help-footer">
                <p>Still have questions about your delivery?</p>

                <div class="help-buttons">
                    <a href="resources" class="btn-outline">Visit Help Centre</a>
                    <a href="contact" class="btn-primary">Contact Support</a>
                </div>
            </div>

        </div>
    </section>


    <?php include 'includes/footer.php'; ?>


</body>

<style>
    /* SECTION */
    .tracking-help-section {
        background: #f6f7f9;
        padding: 80px 20px;
        text-align: center;
    }

    /* HEADER */
    .section-header h2 {
        font-size: 32px;
        font-weight: 700;
        color: #0d2c6c;
        margin-bottom: 10px;
    }

    .section-header p {
        color: #6b7280;
        margin-bottom: 45px;
    }

    /* GRID */
    .tracking-help-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        max-width: 900px;
        margin: 0 auto 40px;
    }

    /* CARDS */
    .help-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 25px;
        text-align: left;
        transition: .25s ease;
    }

    .help-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .help-card h4 {
        font-size: 16px;
        font-weight: 600;
        color: #0d2c6c;
        margin-bottom: 10px;
    }

    .help-card p {
        color: #6b7280;
        line-height: 1.6;
        font-size: 14px;
    }

    /* FOOTER */
    .help-footer p {
        color: #6b7280;
        margin-bottom: 15px;
    }

    /* BUTTONS */
    .help-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }


    /* RESPONSIVE */
    @media(max-width:768px) {
        .tracking-help-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    .track-hero {
        background: linear-gradient(135deg, #0A1F44 0%, #1a3a6b 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
    }

    .track-hero h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }


    .alert-error {
        background: #fee2e2;
        color: #dc2626;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 20px auto;
        max-width: 700px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .order-info {
            grid-template-columns: 1fr;
        }

        .addresses-display {
            grid-template-columns: 1fr;
        }

        .arrow-icon {
            transform: rotate(90deg);
            text-align: center;
        }

        .search-box {
            flex-direction: column;
        }
    }
</style>

<style>
    .tracking-result-section {
        background: #f5f6f8;
        padding: 80px 20px;
    }

    .tracking-wrapper {
        max-width: 900px;
        margin: auto;
    }

    /* CARDS */
    .tracking-status-card,
    .tracking-history-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    /* STATUS HEADER */
    .status-header {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .status-icon {
        width: 42px;
        height: 42px;
        background: #f58220;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
    }

    .status-header h3 {
        margin: 0 0 6px;
        color: #0f2a5f;
    }

    /* ALERT */
    .status-alert {
        margin-top: 18px;
        background: #e9f8ee;
        border: 1px solid #9be3b1;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 14px;
        color: #1a7f37;
    }

    /* HISTORY */
    .tracking-history-card h4 {
        margin-bottom: 20px;
        color: #0f2a5f;
    }

    /* TIMELINE */
    .timeline {
        position: relative;
        margin-left: 18px;
    }

    .timeline:before {
        content: "";
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }

    .timeline-item {
        position: relative;
        display: flex;
        gap: 15px;
        margin-bottom: 28px;
    }

    .timeline-icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        z-index: 2;
    }

    /* STATES */
    .timeline-item.completed .timeline-icon {
        background: #22c55e;
        color: #fff;
    }

    .timeline-item.active .timeline-icon {
        background: #f58220;
        color: #fff;
    }

    .timeline-item.pending .timeline-icon {
        background: #e5e7eb;
    }

    .timeline-content strong {
        display: block;
        color: #0f2a5f;
    }

    .timeline-content span {
        font-size: 13px;
        color: #6b7280;
    }

    .timeline-content small {
        display: block;
        font-size: 12px;
        color: #9ca3af;
    }

    /* ACTION BUTTONS */
    .tracking-actions {
        display: flex;
        gap: 12px;
    }

    .alert-box {
        max-width: 600px;
        margin: 0 auto;
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #dc2626;
        padding: 20px 24px;
        border-radius: 12px;
        text-align: center;
    }
</style>

<script>
    function printTrackingHelp() {

        const printContent = document.getElementById("printTrackingHelp").innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
        <html>
            <head>
                <title>Print</title>
                <style>
                    body{
                        font-family: Arial, sans-serif;
                        padding:40px;
                        background:#fff;
                    }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `;

        window.print();

        // Restore original page after printing
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>

</html>