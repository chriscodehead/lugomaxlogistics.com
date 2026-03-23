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
$error = '';

// Delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        $message = "Service deleted!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = $_POST['id'] ?? 0;
    $title = sanitize_input($_POST['title']);
    $slug = sanitize_input($_POST['slug']);
    $icon = sanitize_input($_POST['icon']);
    $short_description = sanitize_input($_POST['short_description']);
    $full_description = $_POST['full_description'];
    $display_order = (int)$_POST['display_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE services SET title=?, slug=?, icon=?, short_description=?, full_description=?, display_order=?, is_active=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$title, $slug, $icon, $short_description, $full_description, $display_order, $is_active, $id]);
            $message = "Service updated!";
        } else {
            $stmt = $db->prepare("INSERT INTO services (title, slug, icon, short_description, full_description, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $icon, $short_description, $full_description, $display_order, $is_active]);
            $message = "Service created!";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get for edit
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all
$items = [];
try {
    $items = $db->query("SELECT * FROM services ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Services CMS</title>
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none
        }

        .btn-primary {
            background: #FF6B2C;
            color: white
        }

        .btn-secondary {
            background: #64748B;
            color: white
        }

        .btn-danger {
            background: #dc2626;
            color: white
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.9rem
        }

        .alert {
            padding: 15px;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px
        }

        .form-group {
            margin-bottom: 20px
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit
        }

        .form-group textarea {
            min-height: 150px
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

        .actions {
            display: flex;
            gap: 8px
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="hc">
            <h1>🚚 Lugomax CMS</h1>
            <div><a href="index.php">Dashboard</a><a href="services.php">Services</a><a href="logout.php">Logout</a></div>
        </div>
    </div>
    <div class="container">
        <div class="page-header">
            <h2>🚛 Services</h2><button class="btn btn-primary" onclick="document.getElementById('form').style.display='block'">+ Add New</button>
        </div><?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?><?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?><div id="form" class="card" style="display:<?= $edit ? 'block' : 'none' ?>">
            <h3><?= $edit ? 'Edit' : 'Add New' ?> Service</h3>
            <form method="POST"><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                <div class="form-group"><label>Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title'] ?? '') ?>" onkeyup="document.getElementById('slug').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-')"></div>
                <div class="form-group"><label>Slug *</label><input type="text" name="slug" id="slug" required value="<?= htmlspecialchars($edit['slug'] ?? '') ?>"></div>
                <div class="form-group"><label>Icon</label><input type="text" name="icon" value="<?= htmlspecialchars($edit['icon'] ?? '') ?>" placeholder="e.g., package, truck, clock"></div>
                <div class="form-group"><label>Short Description</label><textarea name="short_description" rows="3"><?= htmlspecialchars($edit['short_description'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Full Description</label><textarea name="full_description"><?= htmlspecialchars($edit['full_description'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Display Order</label><input type="number" name="display_order" value="<?= $edit['display_order'] ?? 0 ?>"></div>
                <div class="form-group"><label><input type="checkbox" name="is_active" <?= ($edit && $edit['is_active']) ? 'checked' : '' ?>> Active</label></div><button type="submit" name="save" class="btn btn-primary">Save</button> <button type="button" class="btn btn-secondary" onclick="window.location='services.php'">Cancel</button>
            </form>
        </div>
        <div class="card">
            <h3>All Services (<?= count($items) ?>)</h3><?php if (count($items) > 0): ?><table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Icon</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($items as $i): ?><tr>
                                <td><strong><?= htmlspecialchars($i['title']) ?></strong></td>
                                <td><?= htmlspecialchars($i['slug']) ?></td>
                                <td><?= htmlspecialchars($i['icon'] ?? 'N/A') ?></td>
                                <td><?= $i['display_order'] ?></td>
                                <td><?= $i['is_active'] ? '✅ Active' : '❌ Inactive' ?></td>
                                <td>
                                    <div class="actions"><a href="?edit=<?= $i['id'] ?>" class="btn btn-secondary btn-sm">Edit</a><a href="?delete=<?= $i['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a></div>
                                </td>
                            </tr><?php endforeach; ?></tbody>
                </table><?php else: ?><p style="text-align:center;padding:40px;color:#64748B">No services yet.</p><?php endif; ?>
        </div>
    </div>
</body>

</html>