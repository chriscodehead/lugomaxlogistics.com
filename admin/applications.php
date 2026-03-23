<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); exit;
}

$db = getDB();
$success = $error = '';
$filter_position = sanitize_input($_GET['position'] ?? '');

// ── DELETE ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM job_applications WHERE id=?")->execute([(int)$_GET['delete']]);
        $success = 'Application deleted.';
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// ── UPDATE STATUS ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $db->prepare("UPDATE job_applications SET status=? WHERE id=?")
           ->execute([sanitize_input($_POST['status']), (int)$_POST['id']]);
        header('Location: applications.php' . ($filter_position ? '?position='.urlencode($filter_position) : ''));
        exit;
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// ── FETCH APPLICATIONS ────────────────────────────────────────────────────────
$apps = [];
try {
    if ($filter_position) {
        $stmt = $db->prepare("SELECT * FROM job_applications WHERE position=? ORDER BY created_at DESC");
        $stmt->execute([$filter_position]);
    } else {
        $stmt = $db->query("SELECT * FROM job_applications ORDER BY created_at DESC");
    }
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $error = $e->getMessage(); }

// ── STATUS COLOURS ────────────────────────────────────────────────────────────
$status_styles = [
    'new'       => 'background:#fef3c7;color:#92400e',
    'reviewing' => 'background:#dbeafe;color:#1e40af',
    'interview' => 'background:#e0e7ff;color:#3730a3',
    'accepted'  => 'background:#d1fae5;color:#065f46',
    'rejected'  => 'background:#fee2e2;color:#dc2626',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Applications – Lugomax CMS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#f5f7fa}
.hdr{background:#0A1F44;color:white;padding:18px 0}
.hdr-i{max-width:1400px;margin:0 auto;padding:0 30px;display:flex;justify-content:space-between;align-items:center}
.hdr h1{font-size:1.4rem}
.nav{display:flex;gap:20px}.nav a{color:white;text-decoration:none;opacity:.85;font-size:.95rem}.nav a:hover{opacity:1}
.wrap{max-width:1400px;margin:30px auto;padding:0 30px}
.ph{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.ph h2{font-size:1.9rem;color:#0A1F44}
.alert{padding:14px 18px;border-radius:8px;margin-bottom:20px}
.alert-success{background:#d1fae5;color:#065f46}
.alert-error{background:#fee2e2;color:#dc2626}
.card{background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05);padding:28px;margin-bottom:24px}
table{width:100%;border-collapse:collapse}
th{background:#f8f9fa;padding:12px 14px;text-align:left;font-weight:600;color:#4a5568;border-bottom:2px solid #e2e8f0;font-size:.87rem}
td{padding:12px 14px;border-bottom:1px solid #f0f4f8;font-size:.9rem;vertical-align:middle}
tr:hover{background:#fafbfc}
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer;border:none;text-decoration:none;font-size:.85rem;transition:all .2s}
.btn-danger{background:#dc2626;color:white}.btn-danger:hover{background:#b91c1c}
.btn-secondary{background:#64748B;color:white}
.btn-sm{padding:6px 12px;font-size:.8rem}
.badge{display:inline-block;padding:5px 12px;border-radius:12px;font-size:.8rem;font-weight:600}
select.status-sel{padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-family:inherit;font-size:.85rem;cursor:pointer}
.filter-bar{background:white;padding:16px 20px;border-radius:10px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,.05);display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.filter-bar label{font-weight:600;color:#0A1F44;font-size:.9rem}
.filter-bar select{padding:8px 12px;border:1px solid #e2e8f0;border-radius:7px;font-family:inherit}
.detail-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center}
.detail-modal.open{display:flex}
.dm-box{background:white;border-radius:12px;padding:32px;max-width:620px;width:93%;max-height:88vh;overflow-y:auto}
.dm-row{margin-bottom:14px}
.dm-lbl{font-size:.82rem;color:#64748B;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px}
.dm-val{color:#0A1F44;font-weight:500;line-height:1.6}
.x{background:none;border:none;font-size:1.5rem;cursor:pointer;color:#64748B}
.empty{text-align:center;padding:60px 20px;color:#64748B}
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
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="ph">
        <h2>📋 Job Applications
            <?php if ($filter_position): ?>
                <span style="font-size:1.1rem;color:#64748B;font-weight:400"> — <?= htmlspecialchars($filter_position) ?></span>
            <?php endif; ?>
        </h2>
        <a href="jobs.php" class="btn btn-secondary">← Back to Jobs</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Filter bar -->
    <form class="filter-bar" method="GET">
        <label for="pos-filter">Filter by Position:</label>
        <select name="position" id="pos-filter" onchange="this.form.submit()">
            <option value="">All Positions</option>
            <?php
            try {
                $positions = $db->query("SELECT DISTINCT position FROM job_applications ORDER BY position")
                               ->fetchAll(PDO::FETCH_COLUMN);
                foreach ($positions as $p):
            ?>
                <option value="<?= htmlspecialchars($p) ?>" <?= $p === $filter_position ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p) ?>
                </option>
            <?php endforeach; } catch(Exception $e) {} ?>
        </select>
        <?php if ($filter_position): ?>
            <a href="applications.php" class="btn btn-sm btn-secondary">Clear Filter</a>
        <?php endif; ?>
    </form>

    <div class="card">
        <h3 style="margin-bottom:20px;color:#0A1F44">Applications (<?= count($apps) ?>)</h3>

        <?php if ($apps): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Experience</th>
                    <th>Availability</th>
                    <th>CV</th>
                    <th>Status</th>
                    <th>Applied</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($apps as $a): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($a['full_name']) ?></strong></td>
                    <td><a href="mailto:<?= htmlspecialchars($a['email']) ?>"><?= htmlspecialchars($a['email']) ?></a></td>
                    <td><?= htmlspecialchars($a['phone']) ?></td>
                    <td><?= htmlspecialchars($a['position']) ?></td>
                    <td>
                        <?php
                        $exp = (int)$a['experience_years'];
                        if ($exp === 0)      echo '<1 yr';
                        elseif ($exp === 1)  echo '1–2 yrs';
                        elseif ($exp === 3)  echo '3–5 yrs';
                        elseif ($exp === 6)  echo '6–10 yrs';
                        elseif ($exp === 11) echo '10+ yrs';
                        else echo $exp . ' yrs';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($a['availability'] ?: '—') ?></td>
                    <td>
                        <?php if ($a['resume_file']): ?>
                            <a href="../uploads/resumes/<?= htmlspecialchars($a['resume_file']) ?>"
                               target="_blank" style="color:#FF6B2C;font-weight:600;font-size:.85rem;">
                               📄 Download CV
                            </a>
                        <?php else: ?>
                            <span style="color:#94a3b8;font-size:.85rem;">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="status-sel"
                                    style="<?= $status_styles[$a['status']] ?? '' ?>"
                                    onchange="this.form.submit()">
                                <option value="new"       <?= $a['status']==='new'       ?'selected':''?>>New</option>
                                <option value="reviewing" <?= $a['status']==='reviewing' ?'selected':''?>>Reviewing</option>
                                <option value="interview" <?= $a['status']==='interview' ?'selected':''?>>Interview</option>
                                <option value="accepted"  <?= $a['status']==='accepted'  ?'selected':''?>>Accepted</option>
                                <option value="rejected"  <?= $a['status']==='rejected'  ?'selected':''?>>Rejected</option>
                            </select>
                        </form>
                    </td>
                    <td style="font-size:.85rem;color:#64748B">
                        <?= date('d M Y', strtotime($a['created_at'])) ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="btn btn-sm btn-secondary"
                                    onclick='showDetail(<?= json_encode($a) ?>)'>View</button>
                            <a href="?delete=<?= $a['id'] ?><?= $filter_position ? '&position='.urlencode($filter_position) : '' ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this application?')">Del</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <div style="font-size:3rem;margin-bottom:12px;">📭</div>
                <h3>No Applications Yet</h3>
                <p>Applications submitted on the careers page will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Application detail modal -->
<div class="detail-modal" id="dm">
    <div class="dm-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #f0f4f8">
            <h3 style="color:#0A1F44" id="dm-name"></h3>
            <button class="x" onclick="closeDM()">×</button>
        </div>
        <div id="dm-body"></div>
    </div>
</div>

<script>
function showDetail(a) {
    document.getElementById('dm-name').textContent = a.full_name;
    document.getElementById('dm-body').innerHTML = `
        <div class="dm-row"><div class="dm-lbl">Position</div><div class="dm-val">${a.position}</div></div>
        <div class="dm-row"><div class="dm-lbl">Email</div><div class="dm-val"><a href="mailto:${a.email}" style="color:#FF6B2C">${a.email}</a></div></div>
        <div class="dm-row"><div class="dm-lbl">Phone</div><div class="dm-val">${a.phone}</div></div>
        <div class="dm-row"><div class="dm-lbl">Availability</div><div class="dm-val">${a.availability || '—'}</div></div>
        <div class="dm-row"><div class="dm-lbl">Applied</div><div class="dm-val">${a.created_at}</div></div>
        <div class="dm-row"><div class="dm-lbl">Cover Letter</div>
          <div class="dm-val" style="background:#f8f9fa;padding:14px;border-radius:8px;white-space:pre-wrap;">${a.cover_letter ? a.cover_letter.replace(/</g,'&lt;') : 'No cover letter provided.'}</div></div>
        ${a.resume_file ? `<div class="dm-row"><div class="dm-lbl">CV / Resume</div><div class="dm-val"><a href="../uploads/resumes/${a.resume_file}" target="_blank" style="color:#FF6B2C;font-weight:600">📄 Download CV</a></div></div>` : ''}
    `;
    document.getElementById('dm').classList.add('open');
}
function closeDM() { document.getElementById('dm').classList.remove('open'); }
document.getElementById('dm').addEventListener('click', e => { if (e.target===document.getElementById('dm')) closeDM(); });
</script>
</body>
</html>
