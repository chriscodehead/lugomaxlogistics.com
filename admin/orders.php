<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$success = $error = '';

// ── DELETE ORDER ──────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM orders WHERE id=?")->execute([(int)$_GET['delete']]);
        $success = 'Order deleted.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// ── ADD NEW UPDATE (Modal) ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_update'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize_input($_POST['status']);
    $current_location = sanitize_input($_POST['current_location']);
    $time_arrival = sanitize_input($_POST['time_of_arrival']);
    $time_departure = sanitize_input($_POST['time_of_departure']);
    $notes = sanitize_input($_POST['notes']);
    $description = sanitize_input($_POST['description']);
    $current_state = sanitize_input($_POST['current_state']);
    $amount = !empty($_POST['amount_charged']) ? (float)$_POST['amount_charged'] : null;
    $discount = !empty($_POST['discount_percent']) ? (float)$_POST['discount_percent'] : null;
    $tax = !empty($_POST['tax_percent']) ? (float)$_POST['tax_percent'] : null;

    try {
        // Insert into history
        $stmt = $db->prepare("INSERT INTO order_status_history 
            (order_id, status, location, time_of_arrival, time_of_departure, 
             notes, description, amount_override, discount_override, tax_override, current_state, created_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())");

        $stmt->execute([
            $order_id,
            $status,
            $current_location,
            $time_arrival ?: null,
            $time_departure ?: null,
            $notes,
            $description,
            $amount,
            $discount,
            $tax,
            $current_state
        ]);

        // Update order status & location
        // $db->prepare("UPDATE orders SET status=?, current_location=?, updated_at=NOW() WHERE id=?")
        //     ->execute([$status, $current_location, $order_id]);

        // Update financials if provided
        if ($amount !== null || $discount !== null || $tax !== null) {
            $updates = [];
            $params = [];
            if ($amount !== null) {
                $updates[] = "amount_charged=?";
                $params[] = $amount;
            }
            if ($discount !== null) {
                $updates[] = "discount_percent=?";
                $params[] = $discount;
            }
            if ($tax !== null) {
                $updates[] = "tax_amount=?";
                // Calculate tax amount from percentage
                $stmt2 = $db->prepare("SELECT price FROM orders WHERE id=?");
                $stmt2->execute([$order_id]);
                $order_price = $stmt2->fetchColumn();
                $tax_amt = $order_price * ($tax / 100);
                $params[] = $tax_amt;
            }
            if ($updates) {
                $params[] = $order_id;
                $db->prepare("UPDATE orders SET " . implode(',', $updates) . " WHERE id=?")
                    ->execute($params);
            }
        }

        $success = 'Update added successfully!';
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// ── LOAD ALL ORDERS ───────────────────────────────────────────────────────────
$orders = [];
try {
    $orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error loading orders.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Order List - Lugomax Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa
        }

        .hdr {
            background: #0A1F44;
            color: white;
            padding: 18px 0
        }

        .hdr-i {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px
        }

        .hdr h1 {
            font-size: 1.4rem
        }

        .nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap
        }

        .nav a {
            color: white;
            text-decoration: none;
            opacity: .85;
            font-size: .9rem
        }

        .nav a:hover {
            opacity: 1
        }

        .wrap {
            max-width: 1600px;
            margin: 30px auto;
            padding: 0 30px
        }

        .ph {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px
        }

        .ph h2 {
            font-size: 1.9rem;
            color: #0A1F44
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            padding: 0;
            margin-bottom: 24px;
            overflow: hidden
        }

        .card h3 {
            color: #0A1F44;
            margin: 0;
            padding: 24px 28px;
            background: #f8f9fa;
            font-size: 1.3rem;
            border-bottom: 2px solid #e2e8f0
        }

        .table-wrap {
            overflow-x: auto
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1400px
        }

        th {
            background: #f8f9fa;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            font-size: .85rem;
            white-space: nowrap
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f4f8;
            font-size: .88rem;
            vertical-align: middle
        }

        tr:hover {
            background: #fafbfc
        }

        .track-num {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #FF6B2C;
            font-size: .9rem
        }

        .badge {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 12px;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap
        }

        .badge-pending,
        .badge-on_hold {
            background: #fef3c7;
            color: #92400e
        }

        .badge-in_transit,
        .badge-departed {
            background: #e0e7ff;
            color: #3730a3
        }

        .badge-arrived,
        .badge-clearance {
            background: #dbeafe;
            color: #1e40af
        }

        .badge-out_for_delivery {
            background: #fce7f3;
            color: #9f1239
        }

        .badge-delivered {
            background: #d1fae5;
            color: #065f46
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #dc2626
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            border-radius: 7px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-size: .8rem;
            transition: all .2s;
            white-space: nowrap
        }

        .btn-primary {
            background: #FF6B2C;
            color: white
        }

        .btn-primary:hover {
            background: #e55a1f
        }

        .btn-secondary {
            background: #64748B;
            color: white
        }

        .btn-secondary:hover {
            background: #4a5568
        }

        .btn-success {
            background: #10b981;
            color: white
        }

        .btn-success:hover {
            background: #059669
        }

        .btn-info {
            background: #3b82f6;
            color: white
        }

        .btn-info:hover {
            background: #2563eb
        }

        .btn-danger {
            background: #dc2626;
            color: white
        }

        .btn-danger:hover {
            background: #b91c1c
        }

        .btn-sm {
            padding: 6px 11px;
            font-size: .78rem
        }

        .acts {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #64748B
        }

        /* Modal */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            z-index: 999;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 20px
        }

        .overlay.open {
            display: flex
        }

        .modal {
            background: white;
            border-radius: 14px;
            padding: 32px;
            width: 100%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            margin: auto
        }

        .modal-hdr {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f4f8
        }

        .modal-hdr h3 {
            font-size: 1.5rem;
            color: #0A1F44;
            margin: 0
        }

        .x {
            background: none;
            border: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #64748B;
            line-height: 1
        }

        .fg {
            margin-bottom: 16px
        }

        .fg label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #0A1F44;
            font-size: .88rem
        }

        .fg input,
        .fg select,
        .fg textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: .95rem;
            transition: border .2s
        }

        .fg input:focus,
        .fg select:focus,
        .fg textarea:focus {
            outline: none;
            border-color: #FF6B2C;
            box-shadow: 0 0 0 3px rgba(255, 107, 44, .1)
        }

        .fg textarea {
            resize: vertical
        }

        .row2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        .row3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px
        }

        .modal-foot {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f4f8
        }

        .section-label {
            font-size: .82rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 20px 0 12px;
            padding-top: 16px;
            border-top: 1px solid #f0f4f8
        }

        .section-label:first-child {
            margin-top: 0;
            padding-top: 0;
            border: none
        }
    </style>
</head>

<body>
    <div class="hdr">
        <div class="hdr-i">
            <h1>🚚 Lugomax CMS</h1>
            <div class="nav">
                <a href="index.php">Dashboard</a>
                <a href="orders.php">Orders</a>
                <a href="quotes.php">Quotes</a>
                <a href="track.php">Tracking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="wrap">
        <div class="ph">
            <h2>📋 Order List</h2>
            <div style="color:#64748B;font-size:.95rem">All Active Orders</div>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="card">
            <h3>Active Orders (<?= count($orders) ?>)</h3>

            <?php if ($orders): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tracking Number</th>
                                <th>Package Name</th>
                                <th>Sender Info</th>
                                <th>Receiver Info</th>
                                <th>Total Amount</th>
                                <th>Tax</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><span class="track-num"><?= htmlspecialchars($o['tracking_number']) ?></span></td>
                                    <td><strong><?= htmlspecialchars($o['package_name'] ?: $o['package_type']) ?></strong></td>
                                    <td>
                                        <div style="line-height:1.5">
                                            <strong><?= htmlspecialchars($o['sender_name'] ?: $o['customer_name']) ?></strong><br>
                                            <small style="color:#64748B">
                                                📞 <?= htmlspecialchars($o['sender_phone'] ?: $o['customer_phone']) ?><br>
                                                ✉️ <?= htmlspecialchars($o['sender_email'] ?: $o['customer_email']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="line-height:1.5">
                                            <strong><?= htmlspecialchars($o['receiver_country'] ?: 'UK') ?></strong><br>
                                            <small style="color:#64748B"><?= htmlspecialchars(substr($o['delivery_address'], 0, 50)) ?>...</small>
                                        </div>
                                    </td>
                                    <td><strong>£<?= number_format($o['amount_charged'] ?: $o['price'], 2) ?></strong></td>
                                    <td>
                                        <?php
                                        $tax_amt = $o['tax_amount'] ?: 0;
                                        $tax_pct = $o['discount_percent'] ?: 0;
                                        ?>
                                        £<?= number_format($tax_amt, 2) ?>
                                        <?php if ($tax_pct > 0): ?>
                                            <br><small style="color:#64748B">(<?= $tax_pct ?>%)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $o['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $o['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="acts">
                                            <a href="order-edit.php?id=<?= $o['id'] ?>" class="btn btn-secondary btn-sm">✏️ Edit</a>
                                            <button class="btn btn-success btn-sm" onclick="openModal(<?= $o['id'] ?>, '<?= htmlspecialchars($o['tracking_number']) ?>')">
                                                ➕ Add Update
                                            </button>
                                            <a href="../track.php?track=<?= urlencode($o['tracking_number']) ?>"
                                                target="_blank" class="btn btn-info btn-sm">👁️ View</a>
                                            <a href="?delete=<?= $o['id'] ?>" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this order?')">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty">
                    <div style="font-size:3rem;margin-bottom:12px">📦</div>
                    <h3>No Orders Yet</h3>
                    <p>Orders will appear here when customers place them.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════════
     ADD NEW UPDATE MODAL
     ══════════════════════════════════════════════════════════════════════════ -->
    <div class="overlay" id="updateModal">
        <div class="modal">
            <div class="modal-hdr">
                <h3>➕ Add New Update</h3>
                <button class="x" onclick="closeModal()">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="add_update" value="1">
                <input type="hidden" name="order_id" id="modalOrderId">

                <div style="background:#f8f9fa;padding:12px 16px;border-radius:8px;margin-bottom:20px">
                    <strong>Order:</strong> <span id="modalTrackNum" style="color:#FF6B2C;font-family:'Courier New',monospace"></span>
                </div>

                <div class="fg">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="Order Placed">Order Placed</option>
                        <option value="picked_up">Picked Up</option>
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>

                <div class="fg"><label>Current State *</label>
                    <select name="current_state" required>
                        <option value="completed">Completed</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- LOCATION & STATUS -->
                <div class="section-label">Location & Status</div>
                <div class="fg">
                    <label>Current Location *</label>
                    <input type="text" name="current_location" required placeholder="e.g., MANILA - PHILIPPINES">
                </div>



                <!-- TIME OF ARRIVAL & DEPARTURE -->
                <div class="section-label">Timing</div>
                <div class="row2">
                    <div class="fg">
                        <label>Time of Arrival</label>
                        <input type="datetime-local" name="time_of_arrival">
                    </div>
                    <div class="fg">
                        <label>Time of Departure</label>
                        <input type="datetime-local" name="time_of_departure">
                    </div>
                </div>

                <!-- COMMENTS & DESCRIPTION -->
                <div class="section-label">Notes</div>
                <div class="fg">
                    <label>Comments</label>
                    <textarea name="notes" rows="2" placeholder="Brief status notes..."></textarea>
                </div>
                <div style="display: none;" class="fg">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Detailed shipment notes..."></textarea>
                </div>

                <!-- FINANCIAL OVERRIDES -->
                <div style="display: none;" class="section-label">Financial Overrides (Optional)</div>
                <div style="display: none;" class="row3">
                    <div class="fg">
                        <label>Amount Charged (£)</label>
                        <input type="number" name="amount_charged" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="fg">
                        <label>Discount (%)</label>
                        <input type="number" name="discount_percent" step="0.01" min="0" max="100" placeholder="0">
                    </div>
                    <div class="fg">
                        <label>Tax (%)</label>
                        <input type="number" name="tax_percent" step="0.01" min="0" max="100" placeholder="0">
                    </div>
                </div>

                <div class="modal-foot">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">✅ Add Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(orderId, trackNum) {
            document.getElementById('modalOrderId').value = orderId;
            document.getElementById('modalTrackNum').textContent = trackNum;
            document.getElementById('updateModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('updateModal').classList.remove('open');
        }
        document.getElementById('updateModal').addEventListener('click', e => {
            if (e.target === document.getElementById('updateModal')) closeModal();
        });
    </script>
</body>

</html>