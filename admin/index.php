<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
$db = getDB();

// Get stats
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'delivered_orders' => 0,
    'total_quotes' => 0,
    'total_contacts' => 0,
    'new_contacts' => 0,
    'blog_posts' => 0,
    'testimonials' => 0,
    'services' => 0,
    'total_jobs' => 0,
    'active_jobs' => 0,
    'total_applications' => 0,
    'new_applications' => 0,
    'total_resources' => 0,
    'video_resources' => 0,
    'faqs' => 0,
];

try {
    $stats['total_orders']     = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['pending_orders']   = $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
    $stats['delivered_orders'] = $db->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn();
    $stats['total_quotes']     = $db->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
    $stats['total_contacts']   = $db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    $stats['new_contacts']     = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status='new'")->fetchColumn();
    $stats['blog_posts']       = $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    $stats['testimonials']     = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    $stats['services']         = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $stats['resources']      = $db->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    $stats['video_resources']      = $db->query("SELECT COUNT(*) FROM video_resources")->fetchColumn();
    $stats['faqs']      = $db->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
} catch (Exception $e) {
}

try {
    $stats['total_jobs']         = $db->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
    $stats['active_jobs']        = $db->query("SELECT COUNT(*) FROM jobs WHERE is_active=1")->fetchColumn();
    $stats['total_applications'] = $db->query("SELECT COUNT(*) FROM job_applications")->fetchColumn();
    $stats['new_applications']   = $db->query("SELECT COUNT(*) FROM job_applications WHERE status='new'")->fetchColumn();
} catch (Exception $e) {
} // jobs table may not exist yet
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lugomax CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
        }

        /* Header */
        .header {
            background: #0A1F44;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            opacity: 0.9;
            transition: opacity 0.3s;
            font-size: 0.9rem;
        }

        .header-nav a:hover {
            opacity: 1;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 30px;
        }

        /* Page header */
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-size: 2rem;
            color: #0A1F44;
            margin-bottom: 8px;
        }

        /* Stat cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 14px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #FF6B2C;
        }

        .stat-card.blue {
            border-left-color: #3b82f6;
        }

        .stat-card.green {
            border-left-color: #10b981;
        }

        .stat-card.purple {
            border-left-color: #8b5cf6;
        }

        .stat-card.amber {
            border-left-color: #f59e0b;
        }

        .stat-card.navy {
            border-left-color: #0A1F44;
        }

        .stat-label {
            font-size: 0.88rem;
            color: #718096;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2.4rem;
            font-weight: 700;
            color: #0A1F44;
            line-height: 1;
        }

        .stat-sub {
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 6px;
        }

        .stat-sub.alert {
            color: #FF6B2C;
            font-weight: 600;
        }

        /* Section labels */
        .section-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 28px 0 12px;
        }

        /* Action cards */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
        }

        .action-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #FF6B2C;
        }

        .action-card a {
            text-decoration: none;
            color: #0A1F44;
            display: block;
        }

        .action-icon {
            font-size: 2.4rem;
            margin-bottom: 12px;
        }

        .action-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 6px;
        }

        .action-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #64748B;
        }

        .action-badge.new {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-badge.warn {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <h1>🚚 Lugomax CMS</h1>
            <div class="header-nav">
                <a href="index.php">Dashboard</a>
                <a href="orders.php">Orders</a>
                <a href="quotes.php">Quotes</a>
                <a href="contacts.php">Contacts</a>
                <a href="blog.php">Blog</a>
                <a href="faq.php">FAQs</a>
                <a href="resources.php">Resources</a>
                <a href="video-resources.php">Video Resources</a>
                <a href="testimonials.php">Testimonials</a>
                <!-- <a href="services.php">Services</a> -->
                <a href="jobs.php">Jobs</a>
                <a href="applications.php">Applications</a>
                <a href="settings.php">Settings</a>
                <a href="track.php">Tracking</a>
                <a href="../" target="_blank">View Site</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">

        <div class="page-header">
            <h2>Dashboard</h2>
            <p style="color: #64748B;">Welcome to your Lugomax content management system</p>
        </div>

        <!-- Orders & Deliveries -->
        <div class="section-label">Orders &amp; Deliveries</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?= $stats['total_orders'] ?></div>
                <div class="stat-sub">All time</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Pending Orders</div>
                <div class="stat-value"><?= $stats['pending_orders'] ?></div>
                <div class="stat-sub <?= $stats['pending_orders'] > 0 ? 'alert' : '' ?>">
                    <?= $stats['pending_orders'] > 0 ? 'Needs attention' : 'All clear' ?>
                </div>
            </div>
            <!-- <div class="stat-card green">
                <div class="stat-label">Delivered</div>
                <div class="stat-value"><?= $stats['delivered_orders'] ?></div>
                <div class="stat-sub">Completed</div>
            </div> -->
            <div class="stat-card blue">
                <div class="stat-label">Quote Requests</div>
                <div class="stat-value"><?= $stats['total_quotes'] ?></div>
                <div class="stat-sub">Total received</div>
            </div>
        </div>

        <!-- Content -->
        <div class="section-label">Content &amp; Messages</div>
        <div class="stats-grid">
            <div class="stat-card navy">
                <div class="stat-label">Contact Messages</div>
                <div class="stat-value"><?= $stats['total_contacts'] ?></div>
                <div class="stat-sub <?= $stats['new_contacts'] > 0 ? 'alert' : '' ?>">
                    <?= $stats['new_contacts'] > 0 ? $stats['new_contacts'] . ' unread' : 'All read' ?>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Blog Posts</div>
                <div class="stat-value"><?= $stats['blog_posts'] ?></div>
                <div class="stat-sub">Published &amp; drafts</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Testimonials</div>
                <div class="stat-value"><?= $stats['testimonials'] ?></div>
                <div class="stat-sub">Customer reviews</div>
            </div>
            <!-- <div class="stat-card green">
                <div class="stat-label">Services</div>
                <div class="stat-value"><?= $stats['services'] ?></div>
                <div class="stat-sub">Active offerings</div>
            </div> -->
            <div class="stat-card green">
                <div class="stat-label">Resources</div>
                <div class="stat-value"><?= $stats['resources'] ?></div>
                <div class="stat-sub">Active Resources</div>
            </div>

            <div class="stat-card green">
                <div class="stat-label">Video Resources</div>
                <div class="stat-value"><?= $stats['video_resources'] ?></div>
                <div class="stat-sub">Active Video Resources</div>
            </div>

            <div class="stat-card green">
                <div class="stat-label">FAQs</div>
                <div class="stat-value"><?= $stats['faqs'] ?></div>
                <div class="stat-sub">FAQs</div>
            </div>
        </div>

        <!-- Careers -->
        <div class="section-label">Careers &amp; Recruitment</div>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); max-width: 660px;">
            <div class="stat-card blue">
                <div class="stat-label">Active Job Listings</div>
                <div class="stat-value"><?= $stats['active_jobs'] ?></div>
                <div class="stat-sub"><?= $stats['total_jobs'] ?> total listings</div>
            </div>
            <div class="stat-card amber">
                <div class="stat-label">Applications</div>
                <div class="stat-value"><?= $stats['total_applications'] ?></div>
                <div class="stat-sub <?= $stats['new_applications'] > 0 ? 'alert' : '' ?>">
                    <?= $stats['new_applications'] > 0 ? $stats['new_applications'] . ' new — review now' : 'All reviewed' ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h3 style="margin: 30px 0 16px; color: #0A1F44;">Quick Actions</h3>
        <div class="quick-actions">
            <div class="action-card">
                <a href="orders.php">
                    <div class="action-icon">📦</div>
                    <div class="action-title">Orders</div>
                    <?php if ($stats['pending_orders'] > 0): ?>
                        <div class="action-badge warn"><?= $stats['pending_orders'] ?> pending</div>
                    <?php else: ?>
                        <div class="action-badge"><?= $stats['total_orders'] ?> total</div>
                    <?php endif; ?>
                </a>
            </div>
            <div class="action-card">
                <a href="quotes.php">
                    <div class="action-icon">💬</div>
                    <div class="action-title">Quotes</div>
                    <div class="action-badge"><?= $stats['total_quotes'] ?></div>
                </a>
            </div>
            <div class="action-card">
                <a href="contacts.php">
                    <div class="action-icon">📧</div>
                    <div class="action-title">Contacts</div>
                    <?php if ($stats['new_contacts'] > 0): ?>
                        <div class="action-badge new"><?= $stats['new_contacts'] ?> new</div>
                    <?php else: ?>
                        <div class="action-badge"><?= $stats['total_contacts'] ?></div>
                    <?php endif; ?>
                </a>
            </div>
            <div class="action-card">
                <a href="blog.php">
                    <div class="action-icon">📝</div>
                    <div class="action-title">Blog Posts</div>
                    <div class="action-badge"><?= $stats['blog_posts'] ?></div>
                </a>
            </div>
            <div class="action-card">
                <a href="testimonials.php">
                    <div class="action-icon">⭐</div>
                    <div class="action-title">Testimonials</div>
                    <div class="action-badge"><?= $stats['testimonials'] ?></div>
                </a>
            </div>
            <!-- <div class="action-card">
                <a href="services.php">
                    <div class="action-icon">🚛</div>
                    <div class="action-title">Services</div>
                    <div class="action-badge"><?= $stats['services'] ?></div>
                </a>
            </div> -->
            <div class="action-card">
                <a href="resources.php">
                    <div class="action-icon">📋</div>
                    <div class="action-title">Resources</div>
                    <div class="action-badge"><?= $stats['resources'] ?></div>
                </a>
            </div>

            <div class="action-card">
                <a href="video-resources.php">
                    <div class="action-icon">📋</div>
                    <div class="action-title">Video Resources</div>
                    <div class="action-badge"><?= $stats['video_resources'] ?></div>
                </a>
            </div>

            <div class="action-card">
                <a href="faq.php">
                    <div class="action-icon">📋</div>
                    <div class="action-title">FAQs</div>
                    <div class="action-badge"><?= $stats['faqs'] ?></div>
                </a>
            </div>

            <div class="action-card">
                <a href="jobs.php">
                    <div class="action-icon">💼</div>
                    <div class="action-title">Job Listings</div>
                    <div class="action-badge"><?= $stats['active_jobs'] ?> active</div>
                </a>
            </div>
            <div class="action-card">
                <a href="applications.php">
                    <div class="action-icon">📋</div>
                    <div class="action-title">Applications</div>
                    <?php if ($stats['new_applications'] > 0): ?>
                        <div class="action-badge new"><?= $stats['new_applications'] ?> new</div>
                    <?php else: ?>
                        <div class="action-badge"><?= $stats['total_applications'] ?></div>
                    <?php endif; ?>
                </a>
            </div>
            <div class="action-card">
                <a href="track.php">
                    <div class="action-icon">🗺️</div>
                    <div class="action-title">Tracking</div>
                </a>
            </div>
            <div class="action-card">
                <a href="settings.php">
                    <div class="action-icon">⚙️</div>
                    <div class="action-title">Settings</div>
                </a>
            </div>
        </div>

    </div>
</body>

</html>