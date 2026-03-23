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
            $stmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC");
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
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="track-hero">
        <div class="container">
            <h1>Track Your Order</h1>
            <p>Enter your tracking number to see real-time updates</p>
        </div>
    </section>
    
    <section class="track-section">
        <div class="container">
            <form class="track-form" method="GET">
                <div class="search-box">
                    <input type="text" name="track" placeholder="Enter tracking number (e.g., LGX123456)" 
                           value="<?= htmlspecialchars($_GET['track'] ?? '') ?>" required>
                    <button type="submit" class="btn-primary">Track Order</button>
                </div>
            </form>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($tracking_result): ?>
                <div class="tracking-result">
                    <div class="result-header">
                        <h2>Tracking Details</h2>
                        <div class="tracking-badge status-<?= $tracking_result['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $tracking_result['status'])) ?>
                        </div>
                    </div>
                    
                    <div class="order-info">
                        <div class="info-item">
                            <div class="info-label">Tracking Number</div>
                            <div class="info-value"><?= htmlspecialchars($tracking_result['tracking_number']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Service Type</div>
                            <div class="info-value"><?= htmlspecialchars($tracking_result['service_type']) ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Estimated Delivery</div>
                            <div class="info-value"><?= date('l, M d, Y', strtotime($tracking_result['estimated_delivery'])) ?></div>
                        </div>
                    </div>
                    
                    <?php if ($tracking_result['current_location']): ?>
                    <div class="current-location">
                        <strong>📍 Current Location:</strong> <?= htmlspecialchars($tracking_result['current_location']) ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="addresses-display">
                        <div class="address-item">
                            <h4>From</h4>
                            <p><?= nl2br(htmlspecialchars($tracking_result['pickup_address'])) ?></p>
                        </div>
                        <div class="arrow-icon">→</div>
                        <div class="address-item">
                            <h4>To</h4>
                            <p><?= nl2br(htmlspecialchars($tracking_result['delivery_address'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($tracking_result['history'])): ?>
                        <div class="timeline-section">
                            <h3>Tracking History</h3>
                            <div class="timeline">
                                <?php foreach ($tracking_result['history'] as $history): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content">
                                            <div class="timeline-status"><?= ucfirst(str_replace('_', ' ', $history['status'])) ?></div>
                                            <?php if ($history['location']): ?>
                                                <div class="timeline-location">📍 <?= htmlspecialchars($history['location']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($history['notes']): ?>
                                                <div class="timeline-notes"><?= htmlspecialchars($history['notes']) ?></div>
                                            <?php endif; ?>
                                            <div class="timeline-time">🕐 <?= date('M d, Y g:i A', strtotime($history['created_at'])) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
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
        
        .track-section {
            padding: 60px 0;
            background: #f8f9fa;
            min-height: 60vh;
        }
        
        .track-form {
            max-width: 700px;
            margin: 0 auto 40px;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .search-box input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #FF6B2C;
        }
        
        .tracking-result {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .result-header h2 {
            color: #0A1F44;
            font-size: 2rem;
        }
        
        .tracking-badge {
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-picked_up { background: #dbeafe; color: #1e40af; }
        .status-in_transit { background: #e0e7ff; color: #3730a3; }
        .status-out_for_delivery { background: #fce7f3; color: #9f1239; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .info-label {
            color: #64748B;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }
        
        .info-value {
            color: #0A1F44;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .current-location {
            padding: 15px 20px;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            margin-bottom: 30px;
            color: #78350f;
        }
        
        .addresses-display {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 20px;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .address-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        
        .address-item h4 {
            color: #0A1F44;
            margin-bottom: 10px;
        }
        
        .address-item p {
            color: #64748B;
            line-height: 1.6;
        }
        
        .arrow-icon {
            font-size: 2rem;
            color: #FF6B2C;
            font-weight: bold;
        }
        
        .timeline-section h3 {
            color: #0A1F44;
            margin-bottom: 30px;
        }
        
        .timeline {
            position: relative;
            padding-left: 40px;
        }
        
        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }
        
        .timeline-item:last-child {
            padding-bottom: 0;
        }
        
        .timeline-dot {
            position: absolute;
            left: -31px;
            top: 8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #FF6B2C;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #FF6B2C;
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -24px;
            top: 24px;
            width: 2px;
            height: calc(100% - 8px);
            background: #e2e8f0;
        }
        
        .timeline-item:last-child::after {
            display: none;
        }
        
        .timeline-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }
        
        .timeline-status {
            font-weight: 600;
            color: #0A1F44;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        
        .timeline-location {
            color: #64748B;
            margin-bottom: 5px;
        }
        
        .timeline-notes {
            color: #64748B;
            margin-bottom: 8px;
        }
        
        .timeline-time {
            color: #94a3b8;
            font-size: 0.9rem;
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
</body>
</html>
