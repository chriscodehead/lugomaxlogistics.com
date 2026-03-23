<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$tracking_number = sanitize_input($_POST['tracking'] ?? '');

if (empty($tracking_number)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a tracking number']);
    exit;
}

$db = getDB();

// Get order details
$stmt = $db->prepare("SELECT o.*, u.full_name as driver 
                       FROM orders o 
                       LEFT JOIN users u ON o.assigned_driver = u.id 
                       WHERE o.tracking_number = ?");
$stmt->execute([$tracking_number]);
$order = $stmt->fetch();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Tracking number not found']);
    exit;
}

// Get status history
$stmt = $db->prepare("SELECT * FROM order_status_history 
                       WHERE order_id = ? 
                       ORDER BY created_at DESC");
$stmt->execute([$order['id']]);
$history = $stmt->fetchAll();

// Format history
$formatted_history = [];
foreach ($history as $h) {
    $formatted_history[] = [
        'status' => ucwords(str_replace('_', ' ', $h['status'])),
        'location' => $h['location'],
        'notes' => $h['notes'],
        'timestamp' => date('M j, Y g:i A', strtotime($h['created_at'])),
        'time_ago' => time_ago($h['created_at'])
    ];
}

// Calculate progress percentage
$status_progress = [
    'pending' => 10,
    'picked_up' => 30,
    'in_transit' => 60,
    'out_for_delivery' => 85,
    'delivered' => 100,
    'cancelled' => 0
];

$progress = $status_progress[$order['status']] ?? 0;

// Format response
$response = [
    'success' => true,
    'data' => [
        'order' => [
            'tracking_number' => $order['tracking_number'],
            'status' => $order['status'],
            'status_label' => ucwords(str_replace('_', ' ', $order['status'])),
            'pickup_address' => $order['pickup_address'],
            'delivery_address' => $order['delivery_address'],
            'service_type' => $order['service_type'],
            'estimated_delivery' => $order['estimated_delivery'] ? date('M j, Y', strtotime($order['estimated_delivery'])) : null,
            'current_location' => $order['current_location'],
            'driver' => $order['driver'],
            'progress_percentage' => $progress
        ],
        'history' => $formatted_history
    ]
];

echo json_encode($response);
