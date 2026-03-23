<?php

/**
 * Homepage - Lugomax Logistics
 * Dynamic content from database
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

init_session();

// Page meta
$current_page = 'home';
$page_title = 'Home';
$page_description = 'Trusted UK courier and logistics services. Fast, reliable delivery across England, Scotland, Wales, and Northern Ireland.';

// Get database connection
$db = getDB();

// Fetch stats (from real data)
$stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'delivered'");
$deliveries = $stmt->fetch()['count'] ?? 10000;

$stmt = $db->query("SELECT COUNT(DISTINCT customer_email) as count FROM orders");
$clients = $stmt->fetch()['count'] ?? 500;

// Calculate on-time percentage
$stmt = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN actual_delivery <= estimated_delivery THEN 1 ELSE 0 END) as on_time
    FROM orders 
    WHERE status = 'delivered' AND actual_delivery IS NOT NULL AND estimated_delivery IS NOT NULL");
$delivery_stats = $stmt->fetch();
$on_time_percent = $delivery_stats['total'] > 0 ? round(($delivery_stats['on_time'] / $delivery_stats['total']) * 100) : 99;

// Fetch services from database
$stmt = $db->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY display_order LIMIT 3");
$services = $stmt->fetchAll();

// Fetch testimonials
$stmt = $db->query("SELECT * FROM testimonials WHERE is_approved = TRUE ORDER BY is_featured DESC, created_at DESC LIMIT 3");
$testimonials = $stmt->fetchAll();

include 'includes/header.php';
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* Desktop */
        gap: 20px;
        text-align: center;
    }

    /* Tablet */
    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile → col-6 effect (2 per row) */
    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* 4 equal columns */
        gap: 30px;
    }

    /* Tablet */
    @media (max-width: 992px) {
        .features-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Mobile */
    @media (max-width: 576px) {
        .features-grid {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    .about-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        /* keeps text left aligned */
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 60px;
        align-items: start;
        /* FIX */
    }

    .about-content {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    /* DESKTOP (current layout) */
    .about-grid {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        /* two columns */
        gap: 60px;
        align-items: start;
    }

    /* TABLET */
    @media (max-width: 992px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
    }

    /* MOBILE (col-12 behavior) */
    @media (max-width: 576px) {
        .about-grid {
            grid-template-columns: 1fr;
            /* makes both full width */
        }

        .about-content,
        .about-image {
            width: 100%;
        }

        .about-image img {
            width: 100%;
            height: auto;
        }
    }
</style>

<style>
    .hero {
        position: relative;
        color: white;
        overflow: hidden;
    }

    /* Curve container */
    .hero-curve {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        line-height: 0;
    }

    .hero-curve svg {
        width: 100%;
        height: 120px;
        display: block;
    }

    .hero-curve path {
        fill: #f5f5f5;
        /* match next section background */
    }
</style>

<!-- Hero Section -->
<section style="display: none;" class="hero">
    <div class="hero-background"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Your Trusted Courier & Logistics Solutions Across the UK</h1>
                <p class="hero-description">Flexible, fast, reliable, and professionally coordinated courier and logistics services for individuals and businesses nationwide.</p>
                <div class="hero-buttons">
                    <a href="quote" class="btn-primary">Get a Quote</a>
                    <a href="about" class="btn btn-outline-white">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hero">
    <div class="hero-background"></div>

    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Your Trusted Courier & Logistics Solutions Across the UK
                </h1>

                <p class="hero-description">
                    Flexible, fast, reliable, and professionally coordinated courier and logistics services for individuals and businesses nationwide.
                </p>

                <div class="hero-buttons">
                    <a href="quote" class="btn-primary">Get a Quote</a>
                    <a href="about" class="btn btn-outline-white">Learn More</a>
                </div>
            </div>
        </div>
    </div>

    <!-- CURVE -->
    <div class="hero-curve">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,40 C300,120 1100,0 1440,60 L1440,120 L0,120 Z"></path>
        </svg>
    </div>
</section>

<!-- Stats Section -->
<section class="stats" style="display: none;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <h2 style="font-size: 1.9rem !important; font-weight: 600;color: #0a2463 !important;">10,000+</h2>
                <p class="stat-label">Deliveries Completed</p>
            </div>
            <div class="stat-item">
                <h2 style="font-size: 1.9rem !important; font-weight: 600;color: #0a2463 !important;">500+</h2>
                <p class="stat-label">Business Partners</p>
            </div>
            <div class="stat-item">
                <h2 style="font-size: 1.9rem !important; font-weight: 600;color: #0a2463 !important;">
                    99%
                </h2>
                <p class="stat-label">On-Time Delivery</p>
            </div>
            <div class="stat-item">
                <h2 style="font-size: 1.9rem !important; font-weight: 600;color: #0a2463 !important;">24/7</h2>
                <p class="stat-label">Customer Support</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title" style="color: #0a2463;">Courier & Logistics Services Across the UK</h2>
            <p class="section-description">Professional delivery solutions tailored to meet your needs, from individual parcels to comprehensive business logistics.</p>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div style="background: #0a2463;" class="service-icon">
                        <svg style="color: white;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <?php if ($service['icon'] === 'package'): ?>
                                <rect x="3" y="8" width="18" height="12" rx="2" />
                                <path d="M7 8V6C7 4.89543 7.89543 4 9 4H15C16.1046 4 17 4.89543 17 6V8" />
                                <circle cx="12" cy="14" r="2" />
                            <?php elseif ($service['icon'] === 'clock'): ?>
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 6V12L16 14" />
                            <?php else: ?>
                                <path d="M3 9L12 2L21 9V20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V9Z" />
                                <path d="M9 21V12H15V21" />
                            <?php endif; ?>
                        </svg>
                    </div>
                    <h3 class="service-title"><?= escape($service['title']) ?></h3>
                    <p class="service-description"><?= escape($service['short_description']) ?></p>
                    <a href="services#<?= escape($service['slug']) ?>" class="service-link">
                        See Details
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 4L10 8L6 12" />
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="services-cta">
            <a href="services" class="btn-primary-dark">View All Services</a>
        </div>
    </div>
</section>

<!-- Why Choose Section -->
<section class="why-choose">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Why Choose Lugomax?</h2>
            <p class="section-description">We combine reliability, professionalism, and customer focus to deliver exceptional logistics services.</p>
        </div>
        <div class="features-grid">
            <div class="feature-item">
                <div style="background: #ff8c1a;" class="feature-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-10 w-10 text-white" aria-hidden="true" data-fg-buz271=":34.548:/components/pages/Home.tsx:207:19:8638:49:e:feature.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Reliable & Secure</h3>
                <p class="feature-description">Your parcels are handled with utmost care and professionalism throughout the delivery journey.</p>
            </div>

            <div class="feature-item">
                <div style="background: #ff8c1a;" class="feature-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-10 w-10 text-white" aria-hidden="true" data-fg-buz271=":34.548:/components/pages/Home.tsx:207:19:8638:49:e:feature.icon">
                        <path d="M12 6v6l4 2"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                <h3 class="feature-title">On-Time Delivery</h3>
                <p class="feature-description">We understand timing matters. Our coordinated network ensures prompt and reliable deliveries.</p>
            </div>

            <div class="feature-item">
                <div style="background: #ff8c1a;" class="feature-icon peach">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-10 w-10 text-white" aria-hidden="true" data-fg-buz271=":34.548:/components/pages/Home.tsx:207:19:8638:49:e:feature.icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h3 class="feature-title">Customer Focused</h3>
                <p class="feature-description">Dedicated support team available to assist you at every step of the delivery process.</p>
            </div>

            <div class="feature-item">
                <div style="background: #ff8c1a;" class="feature-icon light-peach">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zap h-10 w-10 text-white" aria-hidden="true" data-fg-buz271=":34.548:/components/pages/Home.tsx:207:19:8638:49:e:feature.icon">
                        <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Fast & Efficient</h3>
                <p class="feature-description">Streamlined processes and professional drivers ensure swift and efficient service.</p>
            </div>

        </div>
    </div>
</section>

<!-- About Section -->
<section class="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-content col-lg-6">
                <h2 class="section-title">About Lugomax Logistics</h2>
                <p class="about-text">Lugomax Logistics (also trading as Lugomax Services Limited) is a UK-registered logistics company dedicated to delivering reliable, efficient, and customer-centric delivery solutions.</p>
                <p class="about-text">Logistics is more than moving items. It is about trust, timing, and accountability. Every delivery is handled with professionalism, transparency, and care.</p>
                <div style="margin: 0px;" class="about-features">
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Reliability</h4>
                            <p>Dependable service you can count on every time.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Professionalism</h4>
                            <p>Trained drivers and staff committed to excellence.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Customer Focus</h4>
                            <p>Your satisfaction is our top priority.</p>
                        </div>
                    </div>
                </div>
                <a href="about" class="btn-primary">Learn More About Us</a>
            </div>
            <div class="about-image col-lg-6">
                <img src="assets/images/about-us.jpeg" alt="Lugomax Logistics in action" onerror="this.style.display='none'" />
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if (count($testimonials) > 0): ?>
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Clients Say</h2>
                <p class="section-description">Trusted by businesses and individuals across the UK.</p>
            </div>
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="#FFB800">
                                    <path d="M8 1L10 6L15 6.5L11.5 10L12.5 15L8 12.5L3.5 15L4.5 10L1 6.5L6 6L8 1Z" />
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <p class="testimonial-text">"<?= escape($testimonial['content']) ?>"</p>
                        <div class="testimonial-author">
                            <h4><?= escape($testimonial['customer_name']) ?></h4>
                            <p><?= escape($testimonial['position']) ?><br><?= escape($testimonial['company_name']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Get an instant quote for your delivery needs or speak with our team to discuss your logistics requirements.</p>
            <div class="cta-buttons">
                <a href="quote" class="btn-primary">Get a Quote Now →</a>
                <a href="contact" class="btn-outline-light">Contact Our Team</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>