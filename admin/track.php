<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$db = getDB();
$success = $error = '';
$tracking_result = null;

// Handle tracking search
if (isset($_GET['track'])) {
    $tracking_number = sanitize_input($_GET['track']);
    
    try {
        $stmt = $db->prepare("SELECT o.*, 
                              (SELECT COUNT(*) FROM order_status_history WHERE order_id = o.id) as history_count
                              FROM orders o 
                              WHERE o.tracking_number = ?");
        $stmt->execute([$tracking_number]);
        $tracking_result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tracking_result) {
            // Get status history
            $stmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC");
            $stmt->execute([$tracking_result['id']]);
            $tracking_result['history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get recent orders for quick access
$recent_orders = [];
try {
    $stmt = $db->query("SELECT tracking_number, customer_name, status, created_at FROM orders ORDER BY created_at DESC LIMIT 10");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking - Lugomax CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; }
        .header { background: #0A1F44; color: white; padding: 20px 0; }
        .hc { max-width: 1400px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.5rem; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: white; text-decoration: none; opacity: 0.9; }
        .nav a:hover { opacity: 1; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 30px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #dc2626; }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px; }
        .search-box { display: flex; gap: 10px; max-width: 600px; margin: 0 auto 40px; }
        .search-box input { flex: 1; padding: 15px 20px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .search-box input:focus { outline: none; border-color: #FF6B2C; }
        .search-box button { padding: 15px 30px; background: #FF6B2C; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .search-box button:hover { background: #e55a1f; }
        .timeline { position: relative; padding-left: 40px; }
        .timeline-item { position: relative; padding-bottom: 30px; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-item::before { content: ''; position: absolute; left: -31px; top: 8px; width: 16px; height: 16px; border-radius: 50%; background: #FF6B2C; border: 3px solid white; box-shadow: 0 0 0 2px #FF6B2C; }
        .timeline-item::after { content: ''; position: absolute; left: -24px; top: 24px; width: 2px; height: calc(100% - 8px); background: #e2e8f0; }
        .timeline-item:last-child::after { display: none; }
        .timeline-content { background: #f8f9fa; padding: 15px 20px; border-radius: 8px; }
        .timeline-status { font-weight: 600; color: #0A1F44; margin-bottom: 5px; }
        .timeline-location { color: #64748B; font-size: 0.9rem; margin-bottom: 5px; }
        .timeline-time { color: #94a3b8; font-size: 0.85rem; }
        .order-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .detail-item { }
        .detail-label { font-size: 0.85rem; color: #64748B; margin-bottom: 5px; }
        .detail-value { font-size: 1.1rem; font-weight: 600; color: #0A1F44; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-picked_up { background: #dbeafe; color: #1e40af; }
        .status-in_transit { background: #e0e7ff; color: #3730a3; }
        .status-out_for_delivery { background: #fce7f3; color: #9f1239; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .quick-access { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .quick-link { display: block; padding: 15px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #0A1F44; transition: all 0.3s; }
        .quick-link:hover { background: #e2e8f0; transform: translateY(-2px); }
        .quick-link .track-num { font-weight: 600; margin-bottom: 5px; }
        .quick-link .customer { font-size: 0.9rem; color: #64748B; }
    </style>
</head>
<body>
    <div class="header">
        <div class="hc">
            <h1>🚚 Lugomax CMS</h1>
            <div class="nav">
                <a href="index.php">Dashboard</a>
                <a href="blog.php">Blog</a>
                <a href="orders.php">Orders</a>
                <a href="settings.php">Settings</a>
                <a href="track.php">Tracking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 style="text-align: center; color: #0A1F44; margin-bottom: 30px;">📦 Order Tracking</h2>
        
        <form class="search-box" method="GET">
            <input type="text" name="track" placeholder="Enter tracking number (e.g., LGX123456)" value="<?= htmlspecialchars($_GET['track'] ?? '') ?>" required>
            <button type="submit">Track Order</button>
        </form>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['track']) && !$tracking_result): ?>
            <div class="alert alert-error">
                <strong>Tracking number not found!</strong><br>
                Please check the tracking number and try again.
            </div>
        <?php endif; ?>

        <?php if ($tracking_result): ?>
            <div class="card">
                <h3 style="color: #0A1F44; margin-bottom: 20px;">Order Details</h3>
                
                <div class="order-details">
                    <div class="detail-item">
                        <div class="detail-label">Tracking Number</div>
                        <div class="detail-value"><?= htmlspecialchars($tracking_result['tracking_number']) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Customer</div>
                        <div class="detail-value"><?= htmlspecialchars($tracking_result['customer_name']) ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge status-<?= $tracking_result['status'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $tracking_result['status'])) ?>
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Estimated Delivery</div>
                        <div class="detail-value"><?= $tracking_result['estimated_delivery'] ? date('M d, Y', strtotime($tracking_result['estimated_delivery'])) : 'TBD' ?></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <div class="detail-label">Pickup Address</div>
                        <div style="color: #0A1F44; line-height: 1.6;"><?= nl2br(htmlspecialchars($tracking_result['pickup_address'])) ?></div>
                    </div>
                    <div>
                        <div class="detail-label">Delivery Address</div>
                        <div style="color: #0A1F44; line-height: 1.6;"><?= nl2br(htmlspecialchars($tracking_result['delivery_address'])) ?></div>
                    </div>
                </div>

                <?php if ($tracking_result['current_location']): ?>
                <div style="background: #fffbeb; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 30px;">
                    <strong style="color: #92400e;">Current Location:</strong>
                    <span style="color: #78350f;"><?= htmlspecialchars($tracking_result['current_location']) ?></span>
                </div>
                <?php endif; ?>

                <h4 style="color: #0A1F44; margin-bottom: 20px;">📍 Tracking History</h4>
                
                <?php if (!empty($tracking_result['history'])): ?>
                    <div class="timeline">
                        <?php foreach ($tracking_result['history'] as $history): ?>
                            <div class="timeline-item">
                                <div class="timeline-content">
                                    <div class="timeline-status"><?= ucfirst(str_replace('_', ' ', $history['status'])) ?></div>
                                    <?php if ($history['location']): ?>
                                        <div class="timeline-location">📍 <?= htmlspecialchars($history['location']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($history['notes']): ?>
                                        <div class="timeline-location"><?= htmlspecialchars($history['notes']) ?></div>
                                    <?php endif; ?>
                                    <div class="timeline-time">🕐 <?= date('M d, Y g:i A', strtotime($history['created_at'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #64748B; text-align: center; padding: 20px;">No tracking history available yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (count($recent_orders) > 0): ?>
            <div class="card">
                <h3 style="color: #0A1F44; margin-bottom: 20px;">🔍 Recent Orders (Quick Access)</h3>
                <div class="quick-access">
                    <?php foreach ($recent_orders as $order): ?>
                        <a href="?track=<?= htmlspecialchars($order['tracking_number']) ?>" class="quick-link">
                            <div class="track-num"><?= htmlspecialchars($order['tracking_number']) ?></div>
                            <div class="customer"><?= htmlspecialchars($order['customer_name']) ?></div>
                            <div style="margin-top: 5px;">
                                <span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst(str_replace('_', ' ', $order['status'])) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
