<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'services';
$page_title = 'Our Services';
$page_description = 'Professional courier and logistics services across the UK';

// Fetch all active services from database
$db = getDB();
$stmt = $db->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY display_order");
$services = $stmt->fetchAll();

include 'includes/header.php';
?>

<style>
  .service-feat li {
    padding: 10px 0;
    padding-left: 0px;
    position: relative;
    color: var(--text-dark);
  }

  .service-feat li::before {
    position: absolute;
    left: 0;
    color: var(--accent-orange);
    font-weight: bold;
    font-size: 1.2rem;
  }

  .service-detail {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    transition: all 0.35s ease;
    position: relative;
  }

  /* Hover Effect */
  .service-detail:hover {
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
    transform: translateY(-6px);
  }

  /* GRID LAYOUT */
  .grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    /* space between cards */
  }

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

<section class="hero-simple">
  <div class="container">
    <h1>Courier & Logistics Services Across the UK</h1>
    <p>Professional, reliable courier services tailored to your needs. From urgent same-day deliveries to comprehensive business logistics solutions.</p>
    <div class="hero-buttons mt-3">
      <a href="quote" class="btn-primary">Get a Quote</a>
      <a href="about" class="btn btn-outline-white">Learn More</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 style="color: #0a2463;">Our Main Services</h2>
      <p>Comprehensive courier and logistics services designed to meet the diverse needs of individuals and businesses across the UK.</p>
    </div>

    <!-- <?php if (count($services) > 0): ?>
      <?php foreach ($services as $index => $service): ?>
        <?php
              $features = json_decode($service['features'] ?? '[]', true);
              $iconType = $service['icon'] ?? 'package';
        ?>
        <div class="service-detail" id="<?= escape($service['slug']) ?>">
          <div class="service-detail-header">
            <div class="service-detail-icon">
              <svg width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <?php if ($iconType === 'package'): ?>
                  <rect x="1" y="3" width="15" height="13"></rect>
                  <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                  <circle cx="5.5" cy="18.5" r="2.5"></circle>
                  <circle cx="18.5" cy="18.5" r="2.5"></circle>
                <?php elseif ($iconType === 'clock'): ?>
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                <?php elseif ($iconType === 'building'): ?>
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                <?php else: ?>
                  <rect x="3" y="8" width="18" height="12" rx="2" />
                  <path d="M7 8V6C7 4.89543 7.89543 4 9 4H15C16.1046 4 17 4.89543 17 6V8" />
                <?php endif; ?>
              </svg>
            </div>
            <div>
              <h3><?= escape($service['title']) ?></h3>
              <p class="service-description"><?= escape($service['short_description']) ?></p>
            </div>
          </div>

          <?php if (!empty($features) && is_array($features)): ?>
            <ul class="service-features">
              <?php foreach ($features as $feature): ?>
                <li><?= escape($feature) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php elseif (!empty($service['full_description'])): ?>
            <div class="service-full-description">
              <?= nl2br(escape($service['full_description'])) ?>
            </div>
          <?php endif; ?>

          <a href="quote.php" class="btn btn-primary">Get Started →</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>

    <?php endif; ?> -->

    <!-- Fallback if no services in database -->
    <div class="service-detail">
      <div class="service-detail-header">
        <div style="background: #0a2463;" class="service-detail-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package h-10 w-10 text-white" aria-hidden="true" data-fg-c8qz32=":34.272:/components/pages/Services.tsx:128:23:5825:49:e:service.icon">
            <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"></path>
            <path d="M12 22V12"></path>
            <polyline points="3.29 7 12 12 20.71 7"></polyline>
            <path d="m7.5 4.27 9 5.15"></path>
          </svg>
        </div>
      </div>
      <div>
        <h4 style="color: #0a2463;">General Goods Delivery (UK-wide)</h4>
        <p class="service-description">Comprehensive nationwide delivery solutions for all types of goods across the United Kingdom. From single parcels to bulk shipments, we handle your deliveries with care and precision.</p>
      </div>

      <ul style="list-style-type: none;" class="space-y-3 mb-6 service-feat " data-fg-c8qz38=":34.272:/components/pages/Services.tsx:133:23:6146:456:e:ul:x">
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete">
          <svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Full UK coverage including England, Scotland, Wales, and Northern Ireland</span>
        </li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Flexible delivery options for various parcel sizes and weights</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Secure handling and professional packaging advice</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Real-time delivery confirmation</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Insurance options available for valuable items</span></li>
      </ul>
      <a href="quote" class="btn btn-primary mt-3">Get Started →</a>
    </div>

    <div class="service-detail">
      <div class="service-detail-header">
        <div style="background: #0a2463;" class="service-detail-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-10 w-10 text-white" aria-hidden="true" data-fg-c8qz32=":34.272:/components/pages/Services.tsx:128:23:5825:49:e:service.icon">
            <path d="M12 6v6l4 2"></path>
            <circle cx="12" cy="12" r="10"></circle>
          </svg>
        </div>
      </div>
      <div>
        <h4 style="color: #0a2463;">Same-Day, Errands & Scheduled Deliveries (England)</h4>
        <p class="service-description">Urgent delivery needs? Our same-day and express services ensure your time-sensitive parcels reach their destination quickly and reliably.</p>
      </div>

      <ul style="list-style-type: none;" class="space-y-3 mb-6 service-feat " data-fg-c8qz38=":34.272:/components/pages/Services.tsx:133:23:6146:456:e:ul:x">
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete">
          <svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Same-day delivery available in major English cities</span>
        </li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Express errand services for urgent pickups and drop-offs</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Scheduled delivery slots to suit your convenience</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Priority handling for time-critical shipments</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Weekend and bank holiday delivery options available</span></li>
      </ul>
      <a href="quote" class="btn btn-primary mt-3">Get Started →</a>
    </div>

    <div class="service-detail">
      <div class="service-detail-header">
        <div style="background: #0a2463;" class="service-detail-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-10 w-10 text-white" aria-hidden="true" data-fg-c8qz32=":34.272:/components/pages/Services.tsx:128:23:5825:49:e:service.icon">
            <path d="M10 12h4"></path>
            <path d="M10 8h4"></path>
            <path d="M14 21v-3a2 2 0 0 0-4 0v3"></path>
            <path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path>
            <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path>
          </svg>
        </div>
      </div>
      <div>
        <h4 style="color: #0a2463;">Business & Commercial Logistics (UK)</h4>
        <p class="service-description">Tailored logistics solutions designed to support businesses of all sizes. From startups to established enterprises, we provide the infrastructure you need to succeed.</p>
      </div>

      <ul style="list-style-type: none;" class="space-y-3 mb-6 service-feat " data-fg-c8qz38=":34.272:/components/pages/Services.tsx:133:23:6146:456:e:ul:x">
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete">
          <svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Dedicated account management for business clients</span>
        </li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Volume discounts and flexible contracts</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Integration with your existing systems and processes</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Regular scheduled collections and deliveries</span></li>
        <li class="flex items-start gap-3" data-fg-c8qz40=":34.272:/components/pages/Services.tsx:135:27:6279:267:e:li:ete"><svg style="margin-right: 10px; margin-bottom: -3px; color:  #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-5 w-5 text-[#F77F00] flex-shrink-0 mt-0.5" aria-hidden="true" data-fg-c8qz41=":34.272:/components/pages/Services.tsx:136:29:6366:71:e:CheckCircle::::::C4Wc">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg><span class="text-gray-700" data-fg-c8qz42=":34.272:/components/pages/Services.tsx:137:29:6466:48:e:span:x">Bespoke solutions designed around your needs</span></li>
      </ul>
      <a href="quote" class="btn btn-primary mt-3">Get Started →</a>
    </div>

  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container">
    <div class="section-header">
      <h2 style="color: #0a2463;">Why Choose Our Services?</h2>
      <p>Every delivery is backed by our commitment to quality, security, and customer satisfaction.</p>
    </div>

    <div class="grid-3">

      <div class="feature-box">
        <div style="background: #ff8c1a;" class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
          </svg>
        </div>
        <h4 style="color: #0a2463;">Secure & Insured</h4>
        <p class="mt-3">Comprehensive insurance options and secure handling protocols protect your valuable shipments.</p>
      </div>

      <div class="feature-box">
        <div style="background: #ff8c1a;" class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
            <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
            <circle cx="12" cy="10" r="3"></circle>
          </svg>
        </div>
        <h4 style="color: #0a2463;">Nationwide Coverage</h4>
        <p class="mt-3">Extensive network across all UK regions ensures we can deliver anywhere, anytime.</p>
      </div>

      <div class="feature-box">
        <div style="background: #ff8c1a;" class="feature-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
            <path d="m9 11 3 3L22 4"></path>
          </svg>
        </div>
        <h4 style="color: #0a2463;">Proof of Delivery</h4>
        <p class="mt-3">Digital signatures, photos, and delivery confirmations provide complete peace of mind.</p>
      </div>

    </div>

  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 style="color: #0a2463;">How It Works</h2>
      <p>Simple, transparent and efficient. Here's how we handle your deliveries.</p>
    </div>
    <div class="process-steps">
      <div class="process-step">
        <div style="background: #0a2463;" class="step-number">01</div>
        <h4 style="color: #0a2463;">Request a Quote</h4>
        <p>Tell us what you need delivered and where.</p>
      </div>

      <div class="process-step">
        <div style="background: #0a2463;" class="step-number">02</div>
        <h4 style="color: #0a2463;">Schedule Pickup</h4>
        <p>Choose a convenient time for collection.</p>
      </div>

      <div class="process-step">
        <div style="background: #0a2463;" class="step-number">03</div>
        <h4 style="color: #0a2463;">Track in Real-Time</h4>
        <p>Monitor your delivery every step of the way.</p>
      </div>

      <div class="process-step">
        <div style="background: #0a2463;" class="step-number">04</div>
        <h4 style="color: #0a2463;">Delivery Confirmed</h4>
        <p>Receive confirmation with proof of delivery.</p>
      </div>

    </div>
  </div>
</section>

<section class="cta-strip">
  <div class="container mt-5 mb-5">
    <h2 style="color: #0a2463;">Need Something More Specialised?</h2>
    <p class="mt-2 mb-3">We offer a range of specialised logistics services including cold chain, warehousing, <br>HGV transport, and more.
    </p>
    <center><a href="specialised" class="btn btn-primary btn-sm" style="color: white; font-weight: 600;">Explore Our Solutions →</a></center>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Ready to Experience Professional Courier Services?</h2>
    <p>Get an instant quote or speak with our team to discuss your specific requirements.</p>
    <div class="cta-actions">
      <a href="quote" class="btn btn-primary">Get a Quote Now →</a>
      <a href="contact" class="btn btn-outline-white">Contact Us</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>