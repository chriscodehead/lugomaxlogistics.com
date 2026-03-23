<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
$db = getDB();
$message = '';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = sanitize_input($_POST['status']);
    try {
        $stmt = $db->prepare("UPDATE quotes SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $message = "Quote status updated!";
    } catch (Exception $e) {
    }
}

// Delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $db->prepare("DELETE FROM quotes WHERE id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        $message = "Quote deleted!";
    } catch (Exception $e) {
    }
}

// Get all
$items = [];
try {
    $items = $db->query("SELECT * FROM quotes ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Quotes - Lugomax CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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

        .header {
            background: #0A1F44;
            color: white;
            padding: 20px 0
        }

        .hc {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            display: flex;
            justify-content: space-between
        }

        .hc a {
            color: white;
            text-decoration: none;
            margin-left: 20px
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 30px
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #f5f7fa
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e
        }

        .badge-quoted {
            background: #dbeafe;
            color: #1e40af
        }

        .badge-accepted {
            background: #d1fae5;
            color: #065f46
        }

        .btn-danger {
            background: #dc2626;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="hc">
            <h1>🚚 Lugomax CMS</h1>
            <div><a href="index.php">Dashboard</a><a href="quotes.php">Quotes</a><a href="logout.php">Logout</a></div>
        </div>
    </div>
    <div class="container">
        <div class="page-header">
            <h2>💬 Quote Requests (<?= count($items) ?>)</h2>
        </div><?php if ($message): ?><div class="alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?><div class="card"><?php if (count($items) > 0): ?><table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>From/To</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($items as $i): ?><tr>
                                <td>#<?= $i['id'] ?></td>
                                <td><?= htmlspecialchars($i['customer_name']) ?></td>
                                <td><?= htmlspecialchars($i['customer_email']) ?></td>
                                <td><?= htmlspecialchars($i['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($i['service_type']) ?></td>
                                <td><?= htmlspecialchars($i['pickup_postcode']) ?> → <?= htmlspecialchars($i['delivery_postcode']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline"><input type="hidden" name="id" value="<?= $i['id'] ?>"><select name="status" onchange="this.form.submit()">
                                            <option value="">Update...</option>
                                            <option value="pending">Pending</option>
                                            <option value="quoted">Quoted</option>
                                            <option value="accepted">Accepted</option>
                                            <option value="declined">Declined</option>
                                        </select><input type="hidden" name="update_status" value="1"></form><span class="badge badge-<?= $i['status'] ?? 'pending' ?>"><?= ucfirst($i['status'] ?? 'pending') ?></span>
                                </td>
                                <td><?= date('M d,Y', strtotime($i['created_at'])) ?></td>
                                <td><a href="?delete=<?= $i['id'] ?>" class="btn-danger" onclick="return confirm('Delete?')">Delete</a></td>
                            </tr><?php endforeach; ?></tbody>
                </table><?php else: ?><p style="text-align:center;padding:60px;color:#64748B">No quote requests yet.</p><?php endif; ?></div>
    </div>
</body>

</html>