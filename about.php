<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'about';
$page_title = 'About Us';
$page_description = 'Learn about Lugomax Logistics - Your trusted UK courier and logistics partner';

include 'includes/header.php';
?>

<style>
  /* HOVER EFFECT */
  .feature-card:hover {
    border-color: #28a745;
    /* green border */
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    transform: translateY(-6px);
  }


  .feature-card:hover .feature-icon {
    transform: scale(1.08);
  }
</style>

<section class="hero-simple">
  <div class="container">
    <h1>About Lugomax Logistics & Services Limited</h1>
    <p></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="about-content">
      <div class="about-text">
        <h2 style="color: #0a2463;">Who We Are</h2>
        <p class="about-text">A registered courier and logistics company committed to providing reliable, efficient, and customer- focused delivery solutions across the nation.</p>
        <p class="about-text">From documents, packages, and parcels to comprehensive business logistics, we provide clients across the UK with dedicated delivery services that prioritize speed, reliability, and customer satisfaction.</p>
      </div>
      <div class="about-image">
        <img src="assets/images/about-lugomax.jpeg" alt="About Lugomax" onerror="this.src='assets/images/placeholder.jpg'">
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container text-center">
    <div class="feature-icon" style="margin-bottom: 24px; background: #ff8c1a;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target h-10 w-10 text-white" aria-hidden="true" data-fg-snq30=":34.272:/components/pages/About.tsx:102:17:4666:43:e:Target::::::DxzD">
        <circle cx="12" cy="12" r="10"></circle>
        <circle cx="12" cy="12" r="6"></circle>
        <circle cx="12" cy="12" r="2"></circle>
      </svg>
    </div>
    <h2 style="color: #0a2463;">Our Mission</h2>
    <p style="max-width: 800px; margin: 20px auto; color: var(--text-gray); font-size: 1.1rem;">To provide dependable, efficient, and affordable logistics solutions that empower businesses and individuals, while upholding the highest standards of professionalism, safety, and customer satisfaction.</p>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container text-center">
    <div class="feature-icon" style="margin-bottom: 24px; background: #0a2463;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-10 w-10 text-white" aria-hidden="true" data-fg-snq37=":34.272:/components/pages/About.tsx:118:17:5548:40:e:Eye::::::EJvT">
        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
        <circle cx="12" cy="12" r="3"></circle>
      </svg>
    </div>
    <h2 style="color: #0a2463;">Our Vision</h2>
    <p style="max-width: 800px; margin: 20px auto; color: var(--text-gray); font-size: 1.1rem;">To be recognised by service users for reliability, integrity, and service excellence in courier and logistics operations.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 style="color: #0a2463;">Our Core Values</h2>
      <p>These principles guide every decision we make and every service we provide.</p>
    </div>
    <div class="grid-3">
      <div class="card feature-card">
        <div style="background: #0a2463;" class="card-icon orange">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-snq55=":34.272:/components/pages/About.tsx:150:21:7143:45:e:value.icon">
            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
          </svg>
        </div>
        <h5 style="color: #0a2463;">Reliability</h5>
        <p class="mt-4">We deliver on our promises with consistent, dependable service that our clients can count on every single time.</p>
      </div>

      <div class="card feature-card">
        <div style="background: #0a2463;" class="card-icon orange">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users h-8 w-8 text-white" aria-hidden="true" data-fg-snq55=":34.272:/components/pages/About.tsx:150:21:7143:45:e:value.icon">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
            <circle cx="9" cy="7" r="4"></circle>
          </svg>
        </div>
        <h5 style="color: #0a2463;">Professionalism</h5>
        <p class="mt-4">Our team upholds the highest standards of conduct, appearance, and service delivery in every interaction.</p>
      </div>

      <div class="card feature-card">
        <div style="background: #0a2463;" class="card-icon orange">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart h-8 w-8 text-white" aria-hidden="true" data-fg-snq55=":34.272:/components/pages/About.tsx:150:21:7143:45:e:value.icon">
            <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"></path>
          </svg>
        </div>
        <h5 style="color: #0a2463;">Customer Focus</h5>
        <p class="mt-4">Your satisfaction is everything to us. We listen, adapt, and go the extra mile to meet your needs.</p>
      </div>

      <div class="card feature-card">
        <div style="background: #0a2463;" class="card-icon orange">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target h-8 w-8 text-white" aria-hidden="true" data-fg-snq55=":34.272:/components/pages/About.tsx:150:21:7143:45:e:value.icon">
            <circle cx="12" cy="12" r="10"></circle>
            <circle cx="12" cy="12" r="6"></circle>
            <circle cx="12" cy="12" r="2"></circle>
          </svg>
        </div>
        <h5 style="color: #0a2463;">Integrity</h5>
        <p class="mt-4">Honesty and transparency are at the foundation of our business. We conduct ourselves with the utmost ethical standards.</p>
      </div>

      <div class="card feature-card">
        <div style="background: #0a2463;" class="card-icon orange">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target h-8 w-8 text-white" aria-hidden="true" data-fg-snq55=":34.272:/components/pages/About.tsx:150:21:7143:45:e:value.icon">
            <circle cx="12" cy="12" r="10"></circle>
            <circle cx="12" cy="12" r="6"></circle>
            <circle cx="12" cy="12" r="2"></circle>
          </svg>
        </div>
        <h5 style="color: #0a2463;">Efficiency</h5>
        <p class="mt-4">Streamlined operations and smart logistics ensure fast, cost-effective delivery without compromising quality.</p>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background: var(--bg-light);">
  <div class="container">
    <div class="about-content">
      <div class="about-image">
        <img src="assets/images/banner.jpeg" alt="Why Choose Lugomax">
      </div>
      <div class="about-text">
        <h2>Why Choose Lugomax?</h2>
        <ul class="about-features" style="list-style-type: none;">
          <li><strong>UK Coverage & Logistics Operators:</strong> We specialize in UK courier and logistics operations, with deep knowledge of routes, regulations, and best practices across England, Scotland, Wales, and Northern Ireland.</li>
          <li><strong>Professional Network:</strong> Our network of professional drivers and partners ensures reliable service across all major cities, with the flexibility to handle various delivery requirements.</li>

          <li><strong>Commitment to Excellence:</strong> From parcel couriers, we treat every delivery with the care and urgency it deserves. Every package is important to us throughout the journey.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Partner with a Trusted UK Logistics Provider</h2>
    <p>Join hundreds of businesses and individuals who trust Lugomax for their delivery needs.</p>
    <div class="cta-actions">
      <a href="quote" class="btn btn-primary">Get Started Today →</a>
      <a href="contact" class="btn btn-outline-white">Contact Us</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>