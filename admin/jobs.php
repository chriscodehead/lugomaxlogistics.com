<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); exit;
}

$db      = getDB();
$success = $error = '';

// ── DELETE ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM jobs WHERE id = ?")->execute([(int)$_GET['delete']]);
        $success = 'Job listing deleted.';
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// ── TOGGLE ACTIVE ─────────────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    try {
        $db->prepare("UPDATE jobs SET is_active = !is_active WHERE id = ?")->execute([(int)$_GET['toggle']]);
        header('Location: jobs.php'); exit;
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// ── CREATE / UPDATE ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $title        = sanitize_input($_POST['title']);
    $department   = sanitize_input($_POST['department']);
    $location     = sanitize_input($_POST['location']);
    $type         = sanitize_input($_POST['type']);
    $arrangement  = sanitize_input($_POST['arrangement']);
    $description  = sanitize_input($_POST['description']);
    $requirements = sanitize_input($_POST['requirements']);
    $salary_range = sanitize_input($_POST['salary_range']);
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active    = isset($_POST['is_active']) ? 1 : 0;

    try {
        if ($id) {
            $db->prepare("UPDATE jobs SET title=?,department=?,location=?,type=?,arrangement=?,
                          description=?,requirements=?,salary_range=?,display_order=?,is_active=?,
                          updated_at=NOW() WHERE id=?")
               ->execute([$title,$department,$location,$type,$arrangement,
                          $description,$requirements,$salary_range,$display_order,$is_active,$id]);
            $success = 'Job listing updated successfully!';
        } else {
            $db->prepare("INSERT INTO jobs (title,department,location,type,arrangement,
                          description,requirements,salary_range,display_order,is_active,created_at)
                          VALUES (?,?,?,?,?,?,?,?,?,?,NOW())")
               ->execute([$title,$department,$location,$type,$arrangement,
                          $description,$requirements,$salary_range,$display_order,$is_active]);
            $success = 'Job listing created successfully!';
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// ── EDIT LOAD ─────────────────────────────────────────────────────────────────
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM jobs WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ── FETCH ALL ─────────────────────────────────────────────────────────────────
$jobs = [];
try {
    $jobs = $db->query("SELECT * FROM jobs ORDER BY display_order ASC, created_at DESC")
               ->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $error = 'Jobs table not found. Please import add_jobs_table.sql first.'; }

// ── APPLICATIONS COUNT ────────────────────────────────────────────────────────
$app_counts = [];
try {
    $rows = $db->query("SELECT position, COUNT(*) as cnt FROM job_applications GROUP BY position")
               ->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) $app_counts[$r['position']] = $r['cnt'];
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Jobs Management – Lugomax CMS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#f5f7fa}
.hdr{background:#0A1F44;color:white;padding:18px 0}
.hdr-i{max-width:1400px;margin:0 auto;padding:0 30px;display:flex;justify-content:space-between;align-items:center}
.hdr h1{font-size:1.4rem}
.nav{display:flex;gap:20px}.nav a{color:white;text-decoration:none;opacity:.85;font-size:.95rem}.nav a:hover{opacity:1}
.wrap{max-width:1400px;margin:30px auto;padding:0 30px}
.ph{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.ph h2{font-size:1.9rem;color:#0A1F44}
.alert{padding:14px 18px;border-radius:8px;margin-bottom:20px;font-weight:500}
.alert-success{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
.alert-error{background:#fee2e2;color:#dc2626;border:1px solid #fca5a5}
.btn{display:inline-flex;align-items:center;gap:6px;padding:11px 22px;border-radius:8px;font-weight:600;cursor:pointer;border:none;text-decoration:none;font-size:.9rem;transition:all .2s}
.btn-primary{background:#FF6B2C;color:white}.btn-primary:hover{background:#e55a1f}
.btn-secondary{background:#64748B;color:white}.btn-secondary:hover{background:#4a5568}
.btn-success{background:#10b981;color:white}
.btn-danger{background:#dc2626;color:white}
.btn-sm{padding:7px 14px;font-size:.82rem}
.btn-ghost{background:#f1f5f9;color:#0A1F44;border:1px solid #e2e8f0}
.card{background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);padding:28px;margin-bottom:28px}
table{width:100%;border-collapse:collapse}
th{background:#f8f9fa;padding:13px 15px;text-align:left;font-weight:600;color:#4a5568;border-bottom:2px solid #e2e8f0;font-size:.88rem}
td{padding:13px 15px;border-bottom:1px solid #f0f4f8;font-size:.9rem;vertical-align:middle}
tr:hover{background:#fafbfc}
.badge{display:inline-block;padding:4px 10px;border-radius:12px;font-size:.78rem;font-weight:600}
.badge-active{background:#d1fae5;color:#065f46}
.badge-inactive{background:#fee2e2;color:#dc2626}
.badge-ft{background:#dbeafe;color:#1e40af}
.badge-pt{background:#fce7f3;color:#9f1239}
.badge-remote{background:#d1fae5;color:#065f46}
.badge-hybrid{background:#fef3c7;color:#92400e}
.acts{display:flex;gap:7px;flex-wrap:wrap}
/* Modal */
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center}
.overlay.open{display:flex}
.modal{background:white;border-radius:14px;padding:36px;max-width:760px;width:95%;max-height:92vh;overflow-y:auto}
.modal-hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.modal-hdr h3{font-size:1.5rem;color:#0A1F44}
.x{background:none;border:none;font-size:1.6rem;cursor:pointer;color:#64748B;line-height:1}
.fg{margin-bottom:18px}
.fg label{display:block;margin-bottom:7px;font-weight:600;color:#0A1F44;font-size:.92rem}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:.95rem;transition:border-color .2s}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:#FF6B2C;box-shadow:0 0 0 3px rgba(255,107,44,.1)}
.fg textarea{resize:vertical}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.chk-row{display:flex;align-items:center;gap:10px}
.chk-row input[type=checkbox]{width:18px;height:18px;accent-color:#FF6B2C}
.modal-foot{display:flex;gap:10px;justify-content:flex-end;margin-top:24px;padding-top:20px;border-top:1px solid #f0f4f8}
.empty{text-align:center;padding:60px 20px;color:#64748B}
.empty-icon{font-size:3rem;margin-bottom:12px}
.stat-pill{background:#f0fdf4;color:#065f46;border:1px solid #bbf7d0;padding:3px 10px;border-radius:12px;font-size:.78rem;font-weight:600}
</style>
</head>
<body>
<div class="hdr">
    <div class="hdr-i">
        <h1>🚚 Lugomax CMS</h1>
        <div class="nav">
            <a href="index.php">Dashboard</a>
            <a href="jobs.php">Jobs</a>
            <a href="applications.php">Applications</a>
            <a href="blog.php">Blog</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="ph">
        <h2>💼 Job Listings</h2>
        <button class="btn btn-primary" onclick="openModal()">+ Add New Job</button>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="card">
        <h3 style="margin-bottom:20px;color:#0A1F44">All Jobs (<?= count($jobs) ?>)</h3>

        <?php if ($jobs): ?>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Salary</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jobs as $j): ?>
                <tr>
                    <td><?= $j['display_order'] ?></td>
                    <td><strong><?= htmlspecialchars($j['title']) ?></strong></td>
                    <td><?= htmlspecialchars($j['department']) ?></td>
                    <td><?= htmlspecialchars($j['location']) ?></td>
                    <td>
                        <span class="badge badge-ft"><?= htmlspecialchars($j['type']) ?></span>
                        <?php if ($j['arrangement'] !== 'On-site'): ?>
                        <span class="badge badge-<?= strtolower($j['arrangement']) ?>"><?= htmlspecialchars($j['arrangement']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:.85rem;color:#64748B"><?= htmlspecialchars($j['salary_range'] ?: '—') ?></td>
                    <td>
                        <?php $cnt = $app_counts[$j['title']] ?? 0; ?>
                        <?php if ($cnt > 0): ?>
                            <a href="applications.php?position=<?= urlencode($j['title']) ?>" class="stat-pill"><?= $cnt ?> received</a>
                        <?php else: ?>
                            <span style="color:#94a3b8;font-size:.85rem;">None yet</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $j['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $j['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="acts">
                            <a href="?edit=<?= $j['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="?toggle=<?= $j['id'] ?>" class="btn btn-ghost btn-sm">
                                <?= $j['is_active'] ? 'Hide' : 'Show' ?>
                            </a>
                            <a href="?delete=<?= $j['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this job listing?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <div class="empty-icon">📋</div>
                <h3>No Job Listings Yet</h3>
                <p>Click "Add New Job" to post your first vacancy.</p>
                <button class="btn btn-primary" onclick="openModal()" style="margin-top:16px">+ Add New Job</button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick link to applications -->
    <?php
    $total_apps = array_sum($app_counts);
    if ($total_apps > 0): ?>
    <div class="card" style="background:linear-gradient(135deg,#0A1F44,#1a3a6b);color:white;padding:24px 28px;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:.88rem;opacity:.75;margin-bottom:4px;">Total Applications Received</div>
                <div style="font-size:2.2rem;font-weight:700;"><?= $total_apps ?></div>
            </div>
            <a href="applications.php" class="btn" style="background:#FF6B2C;color:white;">View All Applications →</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── MODAL ──────────────────────────────────────────────────────────────── -->
<div class="overlay" id="overlay">
    <div class="modal">
        <div class="modal-hdr">
            <h3 id="modal-title">Add New Job</h3>
            <button class="x" onclick="closeModal()">×</button>
        </div>
        <form method="POST" id="job-form">
            <input type="hidden" name="id" id="fid">

            <div class="fg">
                <label>Job Title *</label>
                <input type="text" name="title" id="ftitle" required placeholder="e.g. Delivery Driver">
            </div>

            <div class="row2">
                <div class="fg">
                    <label>Department *</label>
                    <input type="text" name="department" id="fdept" required placeholder="e.g. Logistics">
                </div>
                <div class="fg">
                    <label>Location *</label>
                    <input type="text" name="location" id="floc" required placeholder="e.g. London, UK">
                </div>
            </div>

            <div class="row3">
                <div class="fg">
                    <label>Employment Type</label>
                    <select name="type" id="ftype">
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Contract">Contract</option>
                        <option value="Freelance">Freelance</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Arrangement</label>
                    <select name="arrangement" id="farr">
                        <option value="On-site">On-site</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Display Order</label>
                    <input type="number" name="display_order" id="forder" value="0" min="0">
                </div>
            </div>

            <div class="fg">
                <label>Salary Range</label>
                <input type="text" name="salary_range" id="fsalary" placeholder="e.g. £28,000 – £34,000 per year">
            </div>

            <div class="fg">
                <label>Job Description *</label>
                <textarea name="description" id="fdesc" rows="5" required
                          placeholder="Describe the role, responsibilities, and what a typical day looks like..."></textarea>
            </div>

            <div class="fg">
                <label>Requirements</label>
                <textarea name="requirements" id="freq" rows="5"
                          placeholder="• Valid UK driving licence&#10;• Clean driving record&#10;• Ability to lift 25kg&#10;(one requirement per line)"></textarea>
            </div>

            <div class="fg">
                <div class="chk-row">
                    <input type="checkbox" name="is_active" id="factive" value="1" checked>
                    <label for="factive" style="margin:0;font-weight:600;color:#0A1F44;">Active (visible on careers page)</label>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Job Listing</button>
            </div>
        </form>
    </div>
</div>

<script>
const overlay = document.getElementById('overlay');

function openModal() {
    document.getElementById('job-form').reset();
    document.getElementById('fid').value = '';
    document.getElementById('factive').checked = true;
    document.getElementById('modal-title').textContent = 'Add New Job';
    overlay.classList.add('open');
}

function closeModal() { overlay.classList.remove('open'); }

overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

<?php if ($editing): ?>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('fid').value       = <?= json_encode((string)$editing['id']) ?>;
    document.getElementById('ftitle').value    = <?= json_encode($editing['title']) ?>;
    document.getElementById('fdept').value     = <?= json_encode($editing['department']) ?>;
    document.getElementById('floc').value      = <?= json_encode($editing['location']) ?>;
    document.getElementById('ftype').value     = <?= json_encode($editing['type']) ?>;
    document.getElementById('farr').value      = <?= json_encode($editing['arrangement']) ?>;
    document.getElementById('forder').value    = <?= json_encode((string)$editing['display_order']) ?>;
    document.getElementById('fsalary').value   = <?= json_encode($editing['salary_range']) ?>;
    document.getElementById('fdesc').value     = <?= json_encode($editing['description']) ?>;
    document.getElementById('freq').value      = <?= json_encode($editing['requirements']) ?>;
    document.getElementById('factive').checked = <?= $editing['is_active'] ? 'true' : 'false' ?>;
    document.getElementById('modal-title').textContent = 'Edit Job Listing';
    overlay.classList.add('open');
});
<?php endif; ?>
</script>
</body>
</html>
