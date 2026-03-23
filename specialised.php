<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'services';
$page_title = 'Specialised Services';

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

<section class="hero-simple">
    <div class="container">
        <h1>Specialised Logistics Services</h1>
        <p>Beyond standard delivery, we partner with experts to deliver specialized logistics solutions designed for unique and industry-specific requirements.</p>
    </div>
</section>

<section class="section" style="background: white; border-bottom: 1px solid #CCC; padding: 43px 0; text-align:center;">
    <div class="container">
        <p>Our Specialized Services complement our Core Courier Operations, providing additional capabilities <br>for clients and businesses with unique or complex logistics requirements. </p>
    </div>
</section>

<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #0a2463;">Our Specialised Services</h2>
            <p>When your delivery needs require extra care, expertise, or specific handling.</p>
        </div>

        <div class="grid-3">
            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"></path>
                        <path d="M12 22V12"></path>
                        <polyline points="3.29 7 12 12 20.71 7"></polyline>
                        <path d="m7.5 4.27 9 5.15"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Last Mile Delivery</h4>
                <p class="mt-3">The final step in the delivery journey is crucial.Through our trusted partners, we ensure your products reach customers efficiently and professionally, maintaining the quality of service your brand promises.</p>
            </div>

            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                        <path d="M15 18H9"></path>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
                        <circle cx="17" cy="18" r="2"></circle>
                        <circle cx="7" cy="18" r="2"></circle>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">HGV/LGV</h4>
                <p class="mt-3">Large vehicle logistics for bulk shipments and heavy goods. Our partners' fleet of HGV and LGV vehicles, operated by certified drivers, handles substantial cargo across the UK safely and efficiently.</p>
            </div>

            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-snowflake h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="m10 20-1.25-2.5L6 18"></path>
                        <path d="M10 4 8.75 6.5 6 6"></path>
                        <path d="m14 20 1.25-2.5L18 18"></path>
                        <path d="m14 4 1.25 2.5L18 6"></path>
                        <path d="m17 21-3-6h-4"></path>
                        <path d="m17 3-3 6 1.5 3"></path>
                        <path d="M2 12h6.5L10 9"></path>
                        <path d="m20 10-1.5 2 1.5 2"></path>
                        <path d="M22 12h-6.5L14 15"></path>
                        <path d="m4 10 1.5 2L4 14"></path>
                        <path d="m7 21 3-6-1.5-3"></path>
                        <path d="m7 3 3 6h4"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Cold Chain Logistics</h4>
                <p class="mt-3">Temperature - controlled transportation for sensitive goods and items requiring a precise climate condition throughout transit.</p>
            </div>

            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">E-Commerce Fulfillment</h4>
                <p class="mt-3">Complete fulfillment solutions for online retailers, including warehousing, pickup, packing, and delivery integration with your e-commerce platform.</p>
            </div>

            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Fragile & High- Value Goods Handling</h4>
                <p class="mt-3">Specialist handling for delicate, valuable, or sensitive items requiring requiring extra care and secure handling, with insurance arranged where applicable.</p>
            </div>

            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path>
                        <path d="m7 16.5-4.74-2.85"></path>
                        <path d="m7 16.5 5-3"></path>
                        <path d="M7 16.5v5.17"></path>
                        <path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path>
                        <path d="m17 16.5-5-3"></path>
                        <path d="m17 16.5 4.74-2.85"></path>
                        <path d="M17 16.5v5.17"></path>
                        <path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path>
                        <path d="M12 8 7.26 5.15"></path>
                        <path d="m12 8 4.74-2.85"></path>
                        <path d="M12 13.5V8"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Palletised Freight & Bulk Transport</h4>
                <p class="mt-3">Efficient handling of palletised goods and bulk shipments with the right equipment, secure, loading, and experienced handling.</p>
            </div>


            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-warehouse h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11"></path>
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z"></path>
                        <path d="M6 13h12"></path>
                        <path d="M6 17h12"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Warehouse & Storage Solutions</h4>
                <p class="mt-3">Secure warehousing facilities offering short - term storage options, inventory management, and flexible access managed through our reliable partners.</p>
            </div>


            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                        <path d="M2 12h20"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">International Freight Forwarding</h4>
                <p class="mt-3">Coordinate International shipping services for businesses requiring cross- border logistics.
                </p>
            </div>


            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Returns Management & Reverse Logistics</h4>
                <p class="mt-3">Comprehensive returns handling service, including collection, processing, and redistribution, helping businesses manage product returns efficiently.</p>
            </div>


            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-8 w-8 text-white" aria-hidden="true" data-fg-dxgf28=":2.13337:/components/pages/SpecialisedServices.tsx:131:21:5805:47:e:service.icon">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Special Event Logistics</h4>
                <p class="mt-3">Dedicated logistics support for events, exhibitions, and temporary installations requiring
                    time-sensitive delivery, logistics coordination, setup support, and collection services.
                </p>
            </div>


            <div class="card feature-card">
                <div style="background: #0a2463;" class="card-icon orange">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-8 w-8 text-white" aria-hidden="true" data-fg-buz246=":34.548:/components/pages/Home.tsx:159:21:6520:47:e:service.icon">
                        <path d="M16 7h6v6"></path>
                        <path d="m22 7-8.5 8.5-5-5L2 17"></path>
                    </svg>
                </div>
                <h4 style="color: #0a2463;">Furniture & Apartments Move-Outs</h4>
                <p class="mt-3">Professional moving and relocation support for homes and apartments, including careful handling of furniture and personal belongings, delivered by experienced moving partners.</p>
            </div>

        </div>

        <div class="cta-actions mt-5">
            <a href="services" class="btn btn-primary  btn-sm">View All Services →</a>
        </div>
    </div>
</section>

<section class="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-content col-lg-6">
                <h4 style="color: #0a2463; font-size: 34px;" class="section-title">When You Need More Than Standard Delivery</h4>
                <p class="about-text">Some shipments require specialised handling, equipment, or expertise. Through our network of trusted partners, we deliver professional solutions for these unique challenges.</p>
                <div style="margin: 0px;" class="about-features">
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Industry Expertise</h4>
                            <p>Handled by partners experienced in specialised cargo across various industries </p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Specialized Equipment</h4>
                            <p>Access to temperature - controlled vehicles, lifting equipment, and secure storage.</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Compliance & Certification</h4>
                            <p>Fully compliant with industry regulations and safety standards through expert partners.</p>
                        </div>
                    </div>

                    <div class="about-feature">
                        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-6 w-6 text-[#F77F00] flex-shrink-0 mt-1" aria-hidden="true" data-fg-buz289=":34.548:/components/pages/Home.tsx:238:19:10221:69:e:CheckCircle::::::C4Wc">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                            <path d="m9 11 3 3L22 4"></path>
                        </svg>
                        <div>
                            <h4>Bespoke Solutions</h4>
                            <p>Customised service packages designed around your specific needs, delivered with care and professionalism.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="about-image col-lg-6">
                <img src="assets/images/about-lugomax.jpeg" alt="Lugomax Logistics in action" onerror="this.style.display='none'" />
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: #fffbeb; border-bottom: 1px solid #b7a044; border-top: 1px solid #ecdfac; padding: 43px 0; text-align:center;">
    <div class="section-header">
        <svg style="color: #0a2463;" xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-globe h-12 w-12 text-[#0A2463] mx-auto mb-4" aria-hidden="true" data-fg-dxgf77=":2.13337:/components/pages/SpecialisedServices.tsx:207:13:9764:59:e:Globe::::::Cyd3">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
            <path d="M2 12h20"></path>
        </svg>
        <h4 style="color: #0a2463; margin-bottom: 25px;">International Freight Forwarding</h4>
        <p>While our primary focus is courier and logistics operations within the UK, we also assist with and organise international freight forwarding through our trusted global partners for clients requiring cross-border shipments.</p>
    </div>

</section>

<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 style="color: #0a2463;">Industries We Serve</h2>
            <p>Our specialised services support businesses across diverse sectors.</p>
        </div>

        <div class="grid-4">

            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">E-Commerce & Retail</h5>
            </div>

            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">Manufacturing & Industrial</h5>
            </div>


            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">Technology & Electronics</h5>
            </div>

            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">Events & Exhibitions</h5>
            </div>

            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">Construction & Building</h5>
            </div>

            <div class="card feature-card" style="text-align:center;">
                <svg style="color: #ff8c1a;" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big h-8 w-8 text-[#F77F00] mx-auto mb-3" aria-hidden="true" data-fg-dxgf93=":2.13337:/components/pages/SpecialisedServices.tsx:245:17:11734:63:e:CheckCircle::::::C4Wc">
                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                    <path d="m9 11 3 3L22 4"></path>
                </svg>
                <h5 style="color: #0a2463; font-size: 14px;">Fashion & Textiles</h5>
            </div>

        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Discuss Your Specialised Logistics Needs</h2>
        <p>Our team can create a tailored logistics plan for your specific requirements.</p>
        <div class="cta-actions">
            <a href="quote" class="btn btn-primary">Request a Quote →
            </a>
            <a href="contact" class="btn btn-outline-white">Contact Our Team</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>