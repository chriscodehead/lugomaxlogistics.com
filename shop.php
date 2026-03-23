<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'shop';
$page_title = 'Packaging Supplies Shop';

include 'includes/header.php';
?>

<section class="hero-simple">
    <div class="container">
        <span class="hero-badge" style="background: #f18300; padding: 5px; border-radius: 5px;">Coming Soon</span>
        <h1>Packaging Supplies Shop</h1>
        <p>Your one-stop destination for high-quality packaging materials designed to protect your goods at every stage of delivery.</p>
    </div>
</section>

<section class="section" style="background: var(--bg-light); padding: 40px 0;">
    <div class="container text-center">
        <p style="color: var(--text-gray);"><b>Online shop launching soon! </p>
    </div>
</section>

<section class="products-section">

    <div class="container">

        <h2 class="section-title">Available Products</h2>
        <p class="section-subtitle">
            Professional grade packaging materials to ensure your items arrive safely.
        </p>


        <!-- ================= CARDBOARD BOXES ================= -->
        <h3 class="category-title">Cardboard Boxes</h3>

        <div class="product-grid">

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Small Box (30x25x15cm)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Medium Box (45x35x30cm)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Large Box (60x40x40cm)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Extra Large Box (80x60x60cm)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

        </div>


        <!-- ================= PROTECTIVE MATERIALS ================= -->
        <h3 class="category-title">Protective Materials</h3>

        <div class="product-grid">

            <div class="product-card">
                <div class="product-icon">🛡️</div>
                <h4>Bubble Wrap Roll (50m)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📚</div>
                <h4>Foam Sheet Pack (20 sheets)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Packing Peanuts (Large Bag)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">🛡️</div>
                <h4>Air Pillows (Pack of 50)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

        </div>


        <!-- ================= TAPES ================= -->
        <h3 class="category-title">Tapes & Adhesives</h3>

        <div class="product-grid">

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Brown Packing Tape (6 rolls)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Fragile Tape (3 rolls)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Heavy Duty Tape (3 rolls)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Tape Dispenser</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

        </div>


        <!-- ================= SPECIALTY ITEMS ================= -->
        <h3 class="category-title">Specialty Items</h3>

        <div class="product-grid">

            <div class="product-card">
                <div class="product-icon">📦</div>
                <h4>Document Envelopes (Pack of 100)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">🛡️</div>
                <h4>Fragile Labels (Pack of 100)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">📚</div>
                <h4>Packing Paper (10kg)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

            <div class="product-card">
                <div class="product-icon">🛡️</div>
                <h4>Corner Protectors (Pack of 50)</h4>
                <span class="price"></span>
                <button class="coming-btn">Coming Soon</button>
            </div>

        </div>

    </div>
</section>


<section class="section" style="background: var(--bg-light); display: none;">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #0a2463;">Why Buy From Us?</h2>
        </div>

        <div class="grid-3">

            <div class="feature-box">
                <div style="background: #0a2463;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Quality Guaranteed</h4>
                <p class="mt-3">
                    All materials tested and approved for professional courier use.</p>
            </div>

            <div class="feature-box">
                <div style="background: #0a2463;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Bulk Discounts</h4>
                <p class="mt-3">
                    Volume pricing available for business customers.</p>
            </div>

            <div class="feature-box">
                <div style="background: #0a2463;" class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-white" aria-hidden="true" data-fg-c8qz61=":34.272:/components/pages/Services.tsx:177:21:8284:47:e:benefit.icon">
                        <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                        <path d="m9 11 3 3L22 4"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Fast Delivery</h4>
                <p class="mt-3">
                    Quick delivery of supplies right to your door.</p>
            </div>

        </div>

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <svg style="color: #f18300;" xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package h-16 w-16 mx-auto mb-6 text-[#F77F00]" aria-hidden="true" data-fg-df6d63=":2.8696:/components/pages/Shop.tsx:173:13:7526:61:e:Package::::::BczM">
            <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"></path>
            <path d="M12 22V12"></path>
            <polyline points="3.29 7 12 12 20.71 7"></polyline>
            <path d="m7.5 4.27 9 5.15"></path>
        </svg>
        <h2>Need Packaging Supplies Now?</h2>
        <p></p>
        <div class="cta-actions">
            <a href="contact" class="btn btn-primary">Contact Us</a> <a href="https://wa.me/447350171898" target="_blank" class="btn btn-outline-white"> 💬 Chat on WhatsApp: <?= escape(get_setting('site_phone', '+44 (0) XXX XXX XXXX')) ?></a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
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
    .products-section {
        background: #f4f5f7;
        padding: 80px 0;
    }



    .section-title {
        color: #0b2c6b;
        font-size: 26px;
        font-weight: 700;
    }

    .section-subtitle {
        color: #6b7280;
        margin: 10px 0 50px;
    }

    .category-title {
        text-align: left;
        color: #0b2c6b;
        margin: 40px 0 20px;
        font-size: 16px;
        font-weight: 700;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
    }

    .product-card {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 0 0 1px #e5e7eb;
        transition: .3s;
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    .product-icon {
        width: 45px;
        height: 45px;
        margin: auto;
        background: #eef2f7;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .product-card h4 {
        font-size: 14px;
        color: #0b2c6b;
        margin-bottom: 8px;
    }

    .price {
        display: block;
        color: #ff7a00;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .coming-btn {
        width: 100%;
        background: #e5e7eb;
        border: none;
        padding: 8px;
        border-radius: 6px;
        color: #9ca3af;
        font-size: 12px;
        cursor: not-allowed;
    }

    /* RESPONSIVE */
    @media(max-width:1024px) {
        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:600px) {
        .product-grid {
            grid-template-columns: 1fr;
        }
    }
</style>