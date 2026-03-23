<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

// ✅ FIX: This checks the session variable your login.php actually sets
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$success = $error = '';
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// UPDATE ORDER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    try {
        $stmt = $db->prepare("UPDATE orders SET
            customer_name=?, customer_email=?, customer_phone=?,
            pickup_address=?, delivery_address=?,
            package_type=?, package_weight=?, package_dimensions=?,
            service_type=?, current_location=?, estimated_delivery=?,
            price=?, special_instructions=?, status=?,  pickup_postcode=?, delivery_postcode=?, package_length=?, package_width=?, package_height=?, updated_at=NOW()
            WHERE id=?");

        $datetime = $_POST['estimated_delivery'];
        $stmt->execute([
            sanitize_input($_POST['customer_name']),
            sanitize_input($_POST['customer_email']),
            sanitize_input($_POST['customer_phone']),
            sanitize_input($_POST['pickup_address']),
            sanitize_input($_POST['delivery_address']),
            sanitize_input($_POST['package_type']),
            !empty($_POST['package_weight']) ? (float)$_POST['package_weight'] : null,
            sanitize_input($_POST['package_dimensions']),
            sanitize_input($_POST['service_type']),
            sanitize_input($_POST['current_location']),
            sanitize_input(date('Y-m-d H:i:s', strtotime($datetime))),
            !empty($_POST['price']) ? (float)$_POST['price'] : null,
            sanitize_input($_POST['special_instructions']),
            sanitize_input($_POST['status']),
            sanitize_input($_POST['pickup_postcode']),
            sanitize_input($_POST['delivery_postcode']),
            sanitize_input($_POST['package_length']),
            sanitize_input($_POST['package_width']),
            sanitize_input($_POST['package_height']),
            $order_id
        ]);

        $success = 'Order updated successfully!';
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// ADD STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_status'])) {
    try {
        $status   = sanitize_input($_POST['status']);
        $location = sanitize_input($_POST['location']);
        $notes    = sanitize_input($_POST['notes']);
        $current_state = sanitize_input($_POST['current_state']);

        $stmt = $db->prepare("INSERT INTO order_status_history (order_id,status,location,notes,current_state,created_at) VALUES (?,?,?,?,?,NOW())");
        $stmt->execute([$order_id, $status, $location, $notes, $current_state]);

        $stmt = $db->prepare("UPDATE orders SET status=?, current_location=?, updated_at=NOW() WHERE id=?");
        //$stmt->execute([$status, $location, $order_id]);

        $success = 'Status added successfully!';
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// DELETE STATUS
if (isset($_GET['delete_status'])) {
    try {
        $db->prepare("DELETE FROM order_status_history WHERE id=?")->execute([(int)$_GET['delete_status']]);
        header('Location: order-edit.php?id=' . $order_id);
        exit;
    } catch (Exception $e) {
    }
}

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status_state'])) {
    $id = (int)$_POST['id'];
    $status = sanitize_input($_POST['current_state_update']);
    try {
        $stmt = $db->prepare("UPDATE order_status_history SET current_state = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $success = "Current state updated!";
    } catch (Exception $e) {
    }
}

// LOAD ORDER
$order = null;
try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id=?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: orders.php');
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id=? ORDER BY created_at DESC");
    $stmt->execute([$order_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error loading order.');
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Order #<?= $order['tracking_number'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            max-width: 1400px;
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
            max-width: 1200px;
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
            font-size: 1.8rem;
            color: #0A1F44
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
            padding: 28px;
            margin-bottom: 24px
        }

        .card h3 {
            color: #0A1F44;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f4f8
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 11px 22px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-size: .9rem;
            transition: all .2s
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

        .btn-success {
            background: #10b981;
            color: white
        }

        .btn-danger {
            background: #dc2626;
            color: white
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: .82rem
        }

        .row2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px
        }

        .row3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px
        }

        .fg {
            margin-bottom: 18px
        }

        .fg label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #0A1F44;
            font-size: .9rem
        }

        .fg input,
        .fg select,
        .fg textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: .95rem
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

        table {
            width: 100%;
            border-collapse: collapse
        }

        th {
            background: #f8f9fa;
            padding: 12px 14px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid#e2e8f0;
            font-size: .87rem
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid #f0f4f8;
            font-size: .9rem
        }

        tr:hover {
            background: #fafbfc
        }

        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 999;
            align-items: center;
            justify-content: center
        }

        .overlay.open {
            display: flex
        }

        .modal {
            background: white;
            border-radius: 14px;
            padding: 32px;
            max-width: 600px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto
        }

        .modal-hdr {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px
        }

        .modal-hdr h3 {
            font-size: 1.4rem;
            color: #0A1F44
        }

        .x {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748B
        }

        .modal-foot {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f4f8
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
                <a href="track.php">Tracking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="wrap">
        <div class="ph">
            <h2>Edit: <?= htmlspecialchars($order['tracking_number']) ?></h2>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <a href="../track.php?track=<?= urlencode($order['tracking_number']) ?>" target="_blank" class="btn btn-secondary btn-sm">🔍 Public View</a>
                <a href="orders.php" class="btn btn-secondary btn-sm">← Orders</a>
            </div>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="card">
            <h3>📦 Order Details</h3>
            <form method="POST">
                <input type="hidden" name="update_order" value="1">

                <div class="row3">
                    <div class="fg"><label>Customer Name *</label>
                        <select name="status">
                            <option value="">Update...</option>
                            <option <?php if ($order['status'] == 'pending') print 'selected'; ?> value="pending">Pending</option>
                            <option <?php if ($order['status'] == 'processing') print 'selected'; ?> value="processing">Processing</option>
                            <option <?php if ($order['status'] == 'in_transit') print 'selected'; ?> value="in_transit">In Transit</option>
                            <option <?php if ($order['status'] == 'delivered') print 'selected'; ?> value="delivered">Delivered</option>
                            <option <?php if ($order['status'] == 'cancelled') print 'selected'; ?> value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="fg"><label>Pickup Postcode *</label><input type="text" name="pickup_postcode" value="<?= escape($order['pickup_postcode']) ?>" required></div>

                    <div class="fg"><label>Delivery Postcode *</label><input type="text" name="delivery_postcode" value="<?= escape($order['delivery_postcode']) ?>" required></div>
                </div>

                <div class="row3">
                    <div class="fg"><label>Customer Name *</label><input type="text" name="customer_name" value="<?= escape($order['customer_name']) ?>" required></div>
                    <div class="fg"><label>Email *</label><input type="email" name="customer_email" value="<?= escape($order['customer_email']) ?>" required></div>
                    <div class="fg"><label>Phone *</label><input type="tel" name="customer_phone" value="<?= escape($order['customer_phone']) ?>" required></div>
                </div>

                <div class="row2">
                    <div class="fg"><label>Pickup Address *</label><textarea name="pickup_address" rows="3" required><?= escape($order['pickup_address']) ?></textarea></div>
                    <div class="fg"><label>Delivery Address *</label><textarea name="delivery_address" rows="3" required><?= escape($order['delivery_address']) ?></textarea></div>
                </div>

                <div class="row3">
                    <div class="fg"><label>Package Type</label>
                        <select name="package_type">
                            <option <?= $order['package_type'] === 'Documents' ? 'selected' : '' ?>>Documents</option>
                            <option <?= $order['package_type'] === 'Parcel' ? 'selected' : '' ?>>Parcel</option>
                            <option <?= $order['package_type'] === 'Package' ? 'selected' : '' ?>>Package</option>
                            <option <?= $order['package_type'] === 'Pallet' ? 'selected' : '' ?>>Pallet</option>
                        </select>
                    </div>
                    <div class="fg"><label>Weight (kg)</label><input type="number" name="package_weight" step="0.1" value="<?= $order['package_weight'] ?>"></div>
                    <div style="display: none;" class="fg"><label>Dimensions</label><input type="text" name="package_dimensions" value="<?= escape($order['package_dimensions']) ?>" placeholder="L x W x H"></div>
                </div>

                <div class="row3">
                    <div class="fg"><label>Package Length</label><input type="text" name="package_length" value="<?= $order['package_length'] ?>">
                    </div>

                    <div class="fg"><label>Package Width</label><input type="text" name="package_width" value="<?= $order['package_width'] ?>"></div>

                    <div class="fg"><label>Package Height</label><input type="text" name="package_height" value="<?= escape($order['package_height']) ?>"></div>
                </div>


                <div class="row3">
                    <div class="fg"><label>Service Type</label><input type="text" name="service_type" value="<?= escape($order['service_type']) ?>"></div>
                    <div class="fg"><label>Current Location</label><input type="text" name="current_location" value="<?= escape($order['current_location']) ?>" placeholder="e.g., Birmingham Hub"></div>
                    <div class="fg"><label>Est. Delivery</label><input type="datetime-local" name="estimated_delivery" value="<?= $order['estimated_delivery'] ?>"></div>
                </div>

                <div class="row2">
                    <div class="fg"><label>Price (£)</label><input type="number" name="price" step="0.01" value="<?= $order['price'] ?>"></div>
                    <div class="fg"><label>Special Instructions</label><textarea name="special_instructions" rows="2"><?= escape($order['special_instructions']) ?></textarea></div>
                </div>

                <div style="text-align:right"><button type="submit" class="btn btn-primary">💾 Save Changes</button></div>
            </form>
        </div>

        <div class="card">
            <div style="display:flex;justify-content:space-between;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid #f0f4f8">
                <h3 style="margin:0;border:none;padding:0">📍 Tracking History</h3>
                <button class="btn btn-success btn-sm" onclick="openModal()">+ Add Update</button>
            </div>

            <?php if ($history): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Current State</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= ucfirst(str_replace('_', ' ', $h['status'])) ?></td>
                                <td>
                                    <form method="POST" style="display:inline"><input type="hidden" name="id" value="<?= $h['id'] ?>"><select name="current_state_update" onchange="this.form.submit()">
                                            <option value="">Update State</option>
                                            <option value="completed">Completed</option>
                                            <option value="active">Active</option>
                                            <option value="pending">Pending</option>
                                        </select><input type="hidden" name="update_status_state" value="1"></form> <?= ucfirst(str_replace('_', ' ', $h['current_state'])) ?>
                                </td>
                                <td><?= htmlspecialchars($h['location'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($h['notes'] ?: '—') ?></td>
                                <td><?= date('d M Y g:i A', strtotime($h['created_at'])) ?></td>
                                <td><a href="?id=<?= $order_id ?>&delete_status=<?= $h['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Del</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center;padding:40px;color:#94a3b8">No history. Add first update above.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="overlay" id="overlay">
        <div class="modal">
            <div class="modal-hdr">
                <h3>Add Status Update</h3><button class="x" onclick="closeModal()">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="add_status" value="1">
                <div class="fg"><label>Status *</label>
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
                <div class="fg"><label>Current Location *</label><input type="text" name="location" required placeholder="e.g., Birmingham Hub"></div>
                <div class="fg"><label>Notes</label><textarea name="notes" rows="3" placeholder="Package processed..."></textarea></div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        //overlay
        function openModal() {
            document.getElementById('overlay').classList.add('open')
        }

        function closeModal() {
            document.getElementById('overlay').classList.remove('open')
        }
        document.getElementById('overlay').addEventListener('click', e => {
            if (e.target === document.getElementById('overlay')) closeModal()
        });
    </script>
</body>

</html>