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

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM site_settings WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Setting deleted successfully!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $setting_key = sanitize_input($_POST['setting_key']);
    $setting_value = $_POST['setting_value'];
    $setting_type = sanitize_input($_POST['setting_type']);
    $description = sanitize_input($_POST['description']);
    
    try {
        if ($id) {
            // UPDATE
            $stmt = $db->prepare("UPDATE site_settings SET setting_key=?, setting_value=?, setting_type=?, description=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$setting_key, $setting_value, $setting_type, $description, $id]);
            $success = "Setting updated successfully!";
        } else {
            // CREATE
            $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, description, updated_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$setting_key, $setting_value, $setting_type, $description]);
            $success = "Setting created successfully!";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// GET FOR EDIT
$editing = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM site_settings WHERE id = ?");
    $stmt->execute([$id]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// FETCH ALL
$settings = [];
try {
    $stmt = $db->query("SELECT * FROM site_settings ORDER BY setting_key ASC");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Lugomax CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; }
        .header { background: #0A1F44; color: white; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .hc { max-width: 1400px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.5rem; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: white; text-decoration: none; opacity: 0.9; }
        .nav a:hover { opacity: 1; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 30px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .alert-error { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .ph { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .ph h2 { font-size: 2rem; color: #0A1F44; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; transition: all 0.3s; }
        .btn-primary { background: #FF6B2C; color: white; }
        .btn-primary:hover { background: #e55a1f; }
        .btn-secondary { background: #64748B; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-sm { padding: 8px 16px; font-size: 0.85rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #4a5568; border-bottom: 2px solid #e2e8f0; font-size: 0.9rem; }
        table td { padding: 15px; border-bottom: 1px solid #f5f7fa; }
        table tr:hover { background: #f9fafb; }
        .actions { display: flex; gap: 8px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .mc { background: white; border-radius: 12px; padding: 30px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .mh { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .mh h3 { font-size: 1.5rem; color: #0A1F44; }
        .close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748B; }
        .fg { margin-bottom: 20px; }
        .fg label { display: block; margin-bottom: 8px; font-weight: 600; color: #0A1F44; }
        .fg input, .fg select, .fg textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.95rem; }
        .fg textarea { min-height: 80px; resize: vertical; }
        .fg input:focus, .fg select:focus, .fg textarea:focus { outline: none; border-color: #FF6B2C; box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.1); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
        .badge-text { background: #dbeafe; color: #1e40af; }
        .badge-number { background: #fef3c7; color: #92400e; }
        .badge-boolean { background: #d1fae5; color: #065f46; }
        .badge-json { background: #e0e7ff; color: #3730a3; }
        .setting-value { font-family: 'Courier New', monospace; background: #f5f7fa; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="hc">
            <h1>🚚 Lugomax CMS</h1>
            <div class="nav">
                <a href="index.php">Dashboard</a>
                <a href="blog.php">Blog</a>
                <a href="testimonials.php">Testimonials</a>
                <a href="services.php">Services</a>
                <a href="settings.php">Settings</a>
                <a href="track.php">Tracking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="ph">
            <h2>⚙️ Site Settings</h2>
            <button class="btn btn-primary" onclick="openModal()">+ Add Setting</button>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 20px; color: #0A1F44;">All Settings (<?= count($settings) ?>)</h3>
            
            <?php if (count($settings) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Setting Key</th>
                            <th>Value</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($settings as $setting): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($setting['setting_key']) ?></strong></td>
                            <td><span class="setting-value"><?= htmlspecialchars(substr($setting['setting_value'] ?? '(empty)', 0, 50)) ?></span></td>
                            <td><span class="badge badge-<?= $setting['setting_type'] ?>"><?= ucfirst($setting['setting_type']) ?></span></td>
                            <td><?= htmlspecialchars($setting['description'] ?? 'No description') ?></td>
                            <td><?= date('M d, Y', strtotime($setting['updated_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="?edit=<?= $setting['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="?delete=<?= $setting['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this setting?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #64748B;">
                    <h3>No Settings Yet</h3>
                    <p>Add your first site setting!</p>
                    <button class="btn btn-primary" onclick="openModal()" style="margin-top: 20px;">Add First Setting</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal">
        <div class="mc">
            <div class="mh">
                <h3 id="mtitle">Add Setting</h3>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="id" id="id">
                
                <div class="fg">
                    <label for="setting_key">Setting Key *</label>
                    <input type="text" name="setting_key" id="setting_key" required placeholder="site_name, smtp_host, etc.">
                </div>
                
                <div class="fg">
                    <label for="setting_type">Setting Type *</label>
                    <select name="setting_type" id="setting_type" required>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="boolean">Boolean</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
                
                <div class="fg">
                    <label for="setting_value">Setting Value</label>
                    <textarea name="setting_value" id="setting_value" placeholder="Enter value"></textarea>
                </div>
                
                <div class="fg">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description" placeholder="What this setting controls">
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Setting</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modal').classList.add('active');
            document.querySelector('form').reset();
            document.getElementById('id').value = '';
            document.getElementById('mtitle').textContent = 'Add Setting';
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('active');
        }

        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        <?php if ($editing): ?>
        document.addEventListener('DOMContentLoaded', function() {
            openModal();
            document.getElementById('id').value = '<?= $editing['id'] ?>';
            document.getElementById('setting_key').value = <?= json_encode($editing['setting_key']) ?>;
            document.getElementById('setting_value').value = <?= json_encode($editing['setting_value']) ?>;
            document.getElementById('setting_type').value = '<?= $editing['setting_type'] ?>';
            document.getElementById('description').value = <?= json_encode($editing['description']) ?>;
            document.getElementById('mtitle').textContent = 'Edit Setting';
        });
        <?php endif; ?>
    </script>
</body>
</html>
