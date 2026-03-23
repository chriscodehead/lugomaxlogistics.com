<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? escape($page_title) . ' - ' : '' ?> Lugomax Logistics</title>
    <meta name="description" content="<?= isset($page_description) ? escape($page_description) : 'UK courier and logistics services' ?>">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">

    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
    <link rel="icon" href="../img/favicon.png" type="image/x-icon">
    <meta name="keywords" content="logistics services, freight forwarding, cargo shipping, international logistics, supply chain solutions, transportation services, shipping company, import and export logistics, air freight services, sea freight services, road transportation, warehouse management, delivery services, global logistics company, reliable logistics provider, freight management, real time shipment tracking, cargo handling services, logistics company UK, professional logistics solutions" />
    <meta name="Classification" content="Logistics, Founders">
    <meta name="target" content="Technology, Logistics, Website Design, Logistics Company in UK, United Kingdom">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="GOOGLEBOT" content="index follow" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="bingbot" content="index follow" />
    <meta name="Slurp" content="index follow" />

    <meta property="fb:app_id" content="">
    <meta property="og:locale" content="en_US" />
    <meta property="og:site_name" content="Lugomax" />
    <meta property="og:title" content="<?= isset($page_title) ? escape($page_title) . ' - ' : '' ?> Lugomax Logistics" />
    <meta property="og:type" content="article" />
    <meta property="og:description" content="<?= isset($page_description) ? escape($page_description) : 'UK courier and logistics services' ?>" />
    <meta property="og:url" content="https://lugomax.co.uk/">
    <meta property="og:image" content="../img/favicon.png">
    <meta property="og:image:secure_url" content="../img/favicon.png" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="415" />

    <meta name="twitter:card" content="DevByte" />
    <meta name="twitter:title" content="<?= isset($page_title) ? escape($page_title) . ' - ' : '' ?> Lugomax Logistics" />
    <meta name="twitter:url" content="https://lugomax.co.uk/">
    <meta name="twitter:description" content="<?= isset($page_description) ? escape($page_description) : 'UK courier and logistics services' ?>" />
    <meta name="twitter:image" content="../img/favicon.png" />
    <meta name="twitter:domain" content="https://lugomax.co.uk/">
    <meta name="twitter:creator" content="DevByte">
    <meta itemprop="name" content="DevByte">
    <meta itemprop="description" content="<?= isset($page_description) ? escape($page_description) : 'UK courier and logistics services' ?>">
    <meta itemprop="image" content="../img/favicon.png">

    <style>
        .nav-menu {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        .nav-menu li {
            position: relative;
        }

        /* Hide dropdown initially */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            list-style: none;
            padding: 10px 0;
            min-width: 220px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: 0.3s ease;
        }

        /* Dropdown links */
        .dropdown-menu li a {
            display: block;
            padding: 10px 18px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-menu li a:hover {
            background: #f5f5f5;
        }

        /* Show on hover */
        .has-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        @media (max-width: 768px) {

            .btn-primary,
            .btn-secondary,
            .btn-outline,
            .btn-outline-white,
            .btn-outline-light {
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Remove ::after underline only for submenu */
        .dropdown-menu a::after,
        .no-underline::after {
            display: none !important;
            content: none !important;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #0A1F44;
            color: #0A1F44;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline:hover {
            background: #0c296e;
            color: white;
        }

        /* Hide by default (desktop) */
        .mobile-only {
            display: none;
        }

        /* Show only on mobile */
        @media (max-width: 768px) {
            .mobile-only {
                display: block;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <nav class="nav-container">
            <a href="./" class="logo">
                <img src="assets/images/logo.png" alt="Lugomax" style="width: 150px;">
            </a>
            <ul class="nav-menu">
                <li><a href="./" class="<?= $current_page === 'home' ? 'active' : '' ?>">Home</a></li>
                <li><a href="about" class="<?= $current_page === 'about' ? 'active' : '' ?>">About Us</a></li>
                <li class="has-dropdown">
                    <a href="#" class="<?= $current_page === 'services' ? 'active' : '' ?>">
                        Services ▾
                    </a>

                    <ul style="background: white; z-index: 1000;" class="dropdown-menu">
                        <li style="background: white;"><a class="no-underline" href="services"><span>Core Services</span></a></li>
                        <li style="background: white;"><a class="no-underline" href="specialised">Specialised Services</a></li>
                    </ul>
                </li>
                <li><a href="careers" class="<?= $current_page === 'careers' ? 'active' : '' ?>">Careers</a></li>
                <li><a href="blog" class="<?= $current_page === 'blog' ? 'active' : '' ?>">Blog</a></li>
                <li><a href="resources" class="<?= $current_page === 'resources' ? 'active' : '' ?>">Resources</a></li>
                <li><a href="shop" class="<?= $current_page === 'shop' ? 'active' : '' ?>">Shop</a></li>
                <li><a href="contact" class="<?= $current_page === 'contact' ? 'active' : '' ?>">Contact</a></li>
                <li class="mobile-only">
                    <a href="Track" class="btn btn-outline btn-sm">
                        Track Order
                    </a>
                </li>

                <li class="mobile-only">
                    <a href="quote" class="btn btn-primary btn-sm" style="color:white;">
                        Get a Quote
                    </a>
                </li>
            </ul>
            <div class="nav-actions">
                <a href="track" class="btn btn-outline btn-sm">Track Order</a>
                <a href="quote" class="btn btn-primary btn-sm">Get a Quote</a>
            </div>
            <div class="mobile-toggle"><span></span><span></span><span></span></div>
        </nav>
    </header>