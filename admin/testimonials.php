<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
$db = getDB();
$message = ''; $error = '';

// Delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        $message = "Testimonial deleted!";
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id = $_POST['id'] ?? 0;
    $customer_name = sanitize_input($_POST['customer_name']);
    $company_name = sanitize_input($_POST['company_name']);
    $position = sanitize_input($_POST['position']);
    $content = sanitize_input($_POST['content']);
    $rating = (int)$_POST['rating'];
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    try {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE testimonials SET customer_name=?, company_name=?, position=?, content=?, rating=?, is_approved=?, is_featured=? WHERE id=?");
            $stmt->execute([$customer_name, $company_name, $position, $content, $rating, $is_approved, $is_featured, $id]);
            $message = "Testimonial updated!";
        } else {
            $stmt = $db->prepare("INSERT INTO testimonials (customer_name, company_name, position, content, rating, is_approved, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $company_name, $position, $content, $rating, $is_approved, $is_featured]);
            $message = "Testimonial created!";
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
}

// Get for edit
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all
$items = [];
try {
    $items = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $db_error = $e->getMessage(); }
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Testimonials CMS</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',sans-serif;background:#f5f7fa}.header{background:#0A1F44;color:white;padding:20px 0}.hc{max-width:1400px;margin:0 auto;padding:0 30px;display:flex;justify-content:space-between}.hc a{color:white;text-decoration:none;margin-left:20px}.container{max-width:1400px;margin:30px auto;padding:0 30px}.page-header{background:white;padding:30px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:30px;display:flex;justify-content:space-between;align-items:center}.btn{display:inline-block;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:600;cursor:pointer;border:none;transition:all 0.3s}.btn-primary{background:#FF6B2C;color:white}.btn-secondary{background:#64748B;color:white}.btn-danger{background:#dc2626;color:white}.btn-sm{padding:6px 12px;font-size:0.9rem}.alert{padding:15px;border-radius:8px;margin-bottom:20px}.alert-success{background:#d1fae5;color:#065f46}.alert-error{background:#fee2e2;color:#dc2626}.card{background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.05);padding:30px;margin-bottom:30px}.form-group{margin-bottom:20px}.form-group label{display:block;margin-bottom:8px;font-weight:600;color:#0A1F44}.form-group input,.form-group textarea,.form-group select{width:100%;padding:12px;border:2px solid #e2e8f0;border-radius:8px;font-family:inherit;font-size:1rem}.form-group textarea{min-height:120px}table{width:100%;border-collapse:collapse}table th{background:#f8f9fa;padding:15px;text-align:left;font-weight:600;border-bottom:2px solid #e2e8f0}table td{padding:15px;border-bottom:1px solid #f5f7fa}table tr:hover{background:#f9fafb}.actions{display:flex;gap:8px}</style></head><body><div class="header"><div class="hc"><h1>🚚 Lugomax CMS</h1><div><a href="index.php">Dashboard</a><a href="testimonials.php">Testimonials</a><a href="logout.php">Logout</a></div></div></div><div class="container"><div class="page-header"><h2>⭐ Testimonials</h2><button class="btn btn-primary" onclick="document.getElementById('form').style.display='block'">+ Add New</button></div><?php if($message):?><div class="alert alert-success"><?=htmlspecialchars($message)?></div><?php endif;?><?php if($error):?><div class="alert alert-error"><?=htmlspecialchars($error)?></div><?php endif;?><div id="form" class="card" style="display:<?=$edit?'block':'none'?>"><h3><?=$edit?'Edit':'Add New'?> Testimonial</h3><form method="POST"><input type="hidden" name="id" value="<?=$edit['id']??''?>"><div class="form-group"><label>Customer Name *</label><input type="text" name="customer_name" required value="<?=htmlspecialchars($edit['customer_name']??'')?>"></div><div class="form-group"><label>Company Name</label><input type="text" name="company_name" value="<?=htmlspecialchars($edit['company_name']??'')?>"></div><div class="form-group"><label>Position</label><input type="text" name="position" value="<?=htmlspecialchars($edit['position']??'')?>"></div><div class="form-group"><label>Testimonial *</label><textarea name="content" required><?=htmlspecialchars($edit['content']??'')?></textarea></div><div class="form-group"><label>Rating</label><select name="rating"><option value="5" <?=($edit&&$edit['rating']==5)?'selected':''?>>5 Stars</option><option value="4" <?=($edit&&$edit['rating']==4)?'selected':''?>>4 Stars</option><option value="3" <?=($edit&&$edit['rating']==3)?'selected':''?>>3 Stars</option></select></div><div class="form-group"><label><input type="checkbox" name="is_approved" <?=($edit&&$edit['is_approved'])?'checked':''?>> Approved</label><label><input type="checkbox" name="is_featured" <?=($edit&&$edit['is_featured'])?'checked':''?>> Featured</label></div><button type="submit" name="save" class="btn btn-primary">Save</button> <button type="button" class="btn btn-secondary" onclick="window.location='testimonials.php'">Cancel</button></form></div><div class="card"><h3>All Testimonials (<?=count($items)?>)</h3><?php if(count($items)>0):?><table><thead><tr><th>Customer</th><th>Company</th><th>Rating</th><th>Content</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($items as $i):?><tr><td><?=htmlspecialchars($i['customer_name'])?></td><td><?=htmlspecialchars($i['company_name']??'N/A')?></td><td><?=str_repeat('⭐',$i['rating'])?></td><td><?=htmlspecialchars(substr($i['content'],0,50))?>...</td><td><?=$i['is_approved']?'✅':' ⏳'?><?=$i['is_featured']?' 🌟':''?></td><td><div class="actions"><a href="?edit=<?=$i['id']?>" class="btn btn-secondary btn-sm">Edit</a><a href="?delete=<?=$i['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a></div></td></tr><?php endforeach;?></tbody></table><?php else:?><p style="text-align:center;padding:40px;color:#64748B">No testimonials yet.</p><?php endif;?></div></div></body></html>
