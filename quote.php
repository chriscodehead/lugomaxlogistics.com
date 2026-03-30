<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'quote';
$page_title = 'Get a Quote';

init_session();
$db = getDB();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data 
    $pickup_postcode = sanitize_input($_POST['pickup_postcode']);
    $delivery_postcode = sanitize_input($_POST['delivery_postcode']);
    $service_type = sanitize_input($_POST['service_type']);
    $pickup_date = sanitize_input($_POST['pickup_date'] ?? '');

    $package_type = sanitize_input($_POST['package_type'] ?? 'Small Parcel');
    $package_weight = !empty($_POST['package_weight']) ? (float)$_POST['package_weight'] : null;
    $package_dimensions = '';
    $package_length = sanitize_input($_POST['package_length']);
    $package_width = sanitize_input($_POST['package_width']);
    $package_height = sanitize_input($_POST['package_height']);
    $special_requirements = sanitize_input($_POST['message'] ?? '');

    $customer_name = sanitize_input($_POST['name']);
    $customer_email = sanitize_input($_POST['email']);
    $customer_phone = sanitize_input($_POST['phone']);
    $company_name = sanitize_input($_POST['company'] ?? '');

    $pickup_address = '';
    $delivery_address = '';
    $delivery_date = sanitize_input($_POST['delivery_date'] ?? '');


    // Validate required fields
    if (
        empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($pickup_postcode) || empty($delivery_postcode) ||
        empty($service_type)
    ) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Generate unique tracking number
            $tracking_prefix = 'LGX';
            $tracking_number = $tracking_prefix . strtoupper(substr(uniqid(), -6));

            // Ensure uniqueness
            $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE tracking_number = ?");
            $stmt->execute([$tracking_number]);
            if ($stmt->fetchColumn() > 0) {
                $tracking_number = $tracking_prefix . strtoupper(substr(uniqid() . rand(1000, 9999), -6));
            }

            // Generate quote number
            $quote_number = 'QT' . date('Ymd') . rand(1000, 9999);

            // Calculate price
            $base_prices = [
                'same-day' => 65.00,
                'next-day' => 45.00,
                'standard' => 32.50,
                'express' => 55.00,
                'economy' => 25.00
            ];

            $service_key = strtolower(str_replace(' ', '-', $service_type));
            $estimated_price = $base_prices[$service_key] ?? 40.00;

            // Add weight pricing
            if ($package_weight && $package_weight > 10) {
                $estimated_price += ($package_weight - 10) * 2.50;
            }

            // Calculate delivery date
            $estimated_delivery = date('Y-m-d', strtotime('+2 days'));
            if (stripos($service_type, 'same-day') !== false || stripos($service_type, 'same day') !== false) {
                $estimated_delivery = date('Y-m-d');
            } elseif (stripos($service_type, 'next-day') !== false || stripos($service_type, 'next day') !== false) {
                $estimated_delivery = date('Y-m-d', strtotime('+1 day'));
            } elseif (stripos($service_type, 'express') !== false) {
                $estimated_delivery = date('Y-m-d', strtotime('+1 day'));
            }

            if (!empty($delivery_date)) {
                $estimated_delivery = $delivery_date;
            }

            // Insert into quotes table
            $stmt = $db->prepare("INSERT INTO quotes (
                quote_number, customer_name, customer_email, customer_phone, company_name,
                pickup_postcode, pickup_date, delivery_postcode, service_type, package_type, package_weight,
                package_dimensions, package_length, package_width, package_height, delivery_date, special_requirements, status, quoted_price, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())");

            $stmt->execute([
                $quote_number,
                $customer_name,
                $customer_email,
                $customer_phone,
                $company_name,
                $pickup_postcode,
                $delivery_postcode,
                $service_type,
                $package_type,
                $package_weight,
                $package_dimensions,
                $package_length,
                $package_width,
                $package_height,
                $delivery_date,
                $special_requirements,
                $estimated_price
            ]);

            // Insert into orders table
            $full_pickup = $pickup_address . "\n" . $pickup_postcode;
            $full_delivery = $delivery_address . "\n" . $delivery_postcode;

            $stmt = $db->prepare("INSERT INTO orders (
                tracking_number, pickup_postcode, delivery_postcode, customer_name, customer_email, customer_phone,
                pickup_address, delivery_address, package_type, package_weight, package_dimensions, pickup_date, package_length, package_width, package_height,
                service_type, status, estimated_delivery, price, payment_status, special_instructions,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, 'pending', ?, ?, 'pending', ?, NOW(), NOW())");

            $stmt->execute([
                $tracking_number,
                $pickup_postcode,
                $delivery_postcode,
                $customer_name,
                $customer_email,
                $customer_phone,
                $pickup_address,
                $delivery_address,
                $package_type,
                $package_weight,
                $package_dimensions,
                $package_length,
                $package_width,
                $package_height,
                $service_type,
                $estimated_delivery,
                $estimated_price,
                $special_requirements
            ]);

            $order_id = $db->lastInsertId();

            // Add initial tracking status
            $stmt = $db->prepare("INSERT INTO order_status_history (
                order_id, status, location, notes, created_at
            ) VALUES (?, 'Order Placed', ?, 'Quote request received and order created', NOW())");

            $stmt->execute([$order_id, $pickup_postcode . ', UK']);

            // Redirect to success page
            header("Location: quote-success.php?tracking=" . urlencode($tracking_number) . "&email=" . urlencode($customer_email));
            exit;
        } catch (Exception $e) {
            $error = "Error processing your request. Please try again. Details: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

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
    .quote-flow-section {
        background: #f5f7fb;
        padding: 80px 0;
    }

    .quote-flow {
        max-width: 650px;
        margin: auto;
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    /* STEP BAR */
    .stepper {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 40px;
    }

    .step {
        text-align: center;
    }

    .circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .step.active .circle {
        background: #f57c00;
        color: #fff;
    }

    .line {
        height: 2px;
        width: 80px;
        background: #e5e7eb;
        margin: 0 10px;
    }

    /* FORM */
    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }

    .subtitle {
        color: #6b7280;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f3f4f6;
    }

    .note-box {
        background: #eef4ff;
        border: 1px solid #c7d2fe;
        padding: 14px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 14px;
    }

    /* BUTTONS */
    .form-actions {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .btn-primary {
        background: #f57c00;
        color: #fff;
        border: none;
        padding: 14px 24px;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-secondary {
        background: #f1f1f1;
        border: none;
        padding: 14px 24px;
        border-radius: 8px;
    }
</style>

<section class="hero-simple">
    <div class="container" style="text-align: center; max-width: 1000px;">
        <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator h-16 w-16 mx-auto mb-6 text-[#F77F00]" aria-hidden="true" data-fg-btq75=":2.19023:/components/pages/Quote.tsx:74:13:1932:64:e:Calculator::::::Cuts">
            <rect width="16" height="20" x="4" y="2" rx="2"></rect>
            <line x1="8" x2="16" y1="6" y2="6"></line>
            <line x1="16" x2="16" y1="14" y2="18"></line>
            <path d="M16 10h.01"></path>
            <path d="M12 10h.01"></path>
            <path d="M8 10h.01"></path>
            <path d="M12 14h.01"></path>
            <path d="M8 14h.01"></path>
            <path d="M12 18h.01"></path>
            <path d="M8 18h.01"></path>
        </svg>
        <h1>Get a Quote</h1>
        <p>Instant, transparent pricing for your delivery needs. No hidden fees, just honest logistics.</p>
    </div>
</section>

<section class="section" style="padding-bottom: 10px; padding-top: 40px; padding-left: 20px; padding-right: 20px;">
    <div class="stepper">

        <div class="step active" data-step="1">
            <div class="circle">1</div>
            <span>Delivery Details</span>
        </div>

        <div class="line"></div>

        <div class="step" data-step="2">
            <div class="circle">2</div>
            <span>Parcel Information</span>
        </div>

        <div class="line"></div>

        <div class="step" data-step="3">
            <div class="circle">3</div>
            <span>Contact Information</span>
        </div>

    </div>
</section>

<section class="quote-flow-section">
    <div class="container">

        <form method="POST" class="quote-flow">

            <div class="form-step active">
                <center><?php if ($error): ?>
                        <div class="alert alert-error"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                </center>

                <h3>Delivery Details</h3>
                <p class="subtitle">Tell us where your parcel needs to go</p>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Pickup Postcode *</label>
                        <input type="text" name="pickup_postcode" placeholder="e.g. SW1A 1AA" required>
                    </div>

                    <div class="form-group">
                        <label>Delivery Postcode *</label>
                        <input type="text" name="delivery_postcode" placeholder="e.g. M1 1AE" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Delivery Type *</label>
                    <select name="service_type" required>
                        <option>Same-Day Delivery</option>
                        <option>Next-Day Delivery</option>
                        <option>Standard Delivery (2-3 days)</option>
                        <option>Economy Delivery (3-5 days)</option>
                        <option>Scheduled Delivery</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Preferred Pickup Date</label>
                    <input type="date" name="pickup_date" required>
                </div>

                <center><button style="width: 100%;" type="button" class="btn-primary next">Continue →</button></center>

            </div>


            <!-- ================= STEP 2 ================= -->
            <div class="form-step">

                <h3>Parcel Information</h3>
                <p class="subtitle">Provide details about your parcel</p>

                <div class="form-group">
                    <label>Parcel Type *</label>
                    <select name="package_type" required>
                        <option>Document / Envelope</option>
                        <option>Small Parcel</option>
                        <option>Medium Parcel</option>
                        <option>Large Parcel</option>
                        <option>Pallet</option>
                        <option>Multiple Items</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Weight (kg) *</label>
                    <input type="number" name="package_weight" step="0.1" placeholder="e.g. 2.5" required>
                </div>

                <div class="form-group">
                    <label>Dimensions (cm)</label>
                    <div class="grid-3">
                        <input name="package_length" type="text" placeholder="Length">
                        <input name="package_width" type="text" placeholder="Width">
                        <input name="package_height" type="text" placeholder="Height">
                    </div>
                </div>

                <div class="form-group">
                    <label>Additional Information</label>
                    <textarea name="message" placeholder="Any special handling requirements, fragile items, etc."></textarea>
                </div>

                <div class="form-actions">
                    <button style="width: 48%;" type="button" class="btn-secondary prev">Back</button>
                    <button style="width: 48%;" type="button" class="btn-primary next">Continue →</button>
                </div>

            </div>


            <!-- ================= STEP 3 ================= -->
            <div class="form-step">

                <h3>Contact Information</h3>
                <p class="subtitle">How can we send you the quote?</p>

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" required>
                </div>

                <div class="note-box">
                    <strong>Note:</strong> You will receive a detailed quote via email within 2hours (during business hours).
                    For urgent requests, please contact our team directly on Whatsapp.
                </div>

                <div class="form-actions">
                    <button style="width: 48%;" type="button" class="btn-secondary prev">Back</button>
                    <button style="width: 48%;" type="submit" class="btn-primary">Submit Quote Request →</button>
                </div>

            </div>

        </form>
    </div>
</section>


<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #0a2463;">Why Get a Quote from <?php print $site_name; ?>?</h2>
        </div>

        <div class="grid-3">

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Transparent Pricing</h4>
                <p class="mt-3">Clear, upfront costs with no hidden fees. What you see is what you pay.</p>
            </div>

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Fast Response</h4>
                <p class="mt-3">Receive your detailed quote within 2 hours during business hours.</p>
            </div>

            <div class="feature-box">
                <div style="background: #ff8c1a;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                        <path d="m9 11 3 3L22 4"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">No Obligation</h4>
                <p class="mt-3">Getting a quote is completely free with no commitment required.</p>
            </div>

        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const steps = document.querySelectorAll(".form-step");
        const nextBtns = document.querySelectorAll(".next");
        const prevBtns = document.querySelectorAll(".prev");
        const stepIndicators = document.querySelectorAll(".step");

        let currentStep = 0;

        // SHOW CURRENT STEP
        function showStep(index) {

            // show form step
            steps.forEach((step, i) => {
                step.classList.toggle("active", i === index);
            });

            // update stepper UI
            stepIndicators.forEach((step, i) => {
                step.classList.toggle("active", i <= index);
            });

            // scroll to top
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        }

        // VALIDATE CURRENT STEP
        function validateStep() {
            const inputs = steps[currentStep].querySelectorAll("input, select, textarea");

            for (let input of inputs) {
                if (input.hasAttribute("required") && !input.value.trim()) {
                    input.reportValidity();
                    return false;
                }
            }
            return true;
        }

        // NEXT BUTTON
        nextBtns.forEach(btn => {
            btn.addEventListener("click", function() {

                if (!validateStep()) return;

                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });
        });

        // BACK BUTTON
        prevBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

    });
</script>