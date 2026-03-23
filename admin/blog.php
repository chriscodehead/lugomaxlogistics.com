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

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Get image to delete
        $stmt = $db->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete image file if exists
        if ($post && $post['featured_image']) {
            $image_path = '../assets/images/blog/' . $post['featured_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete post
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Blog post deleted successfully!";
    } catch (Exception $e) {
        $error = "Error deleting post: " . $e->getMessage();
    }
}

// Handle CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = sanitize_input($_POST['title']);
    $slug = sanitize_input($_POST['slug']);
    $excerpt = sanitize_input($_POST['excerpt']);
    $content = $_POST['content'];
    $category_id = (int)$_POST['category_id'];
    $status = sanitize_input($_POST['status']);
    $author_id = 1;
    
    $featured_image = null;
    
    // Handle image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $upload_dir = '../assets/images/blog/';
        
        // Create directory if doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file = $_FILES['featured_image'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Generate unique filename
            $new_filename = uniqid() . '_' . time() . '.' . $ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $featured_image = $new_filename;
                
                // Delete old image if updating
                if ($id) {
                    $stmt = $db->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_post = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($old_post && $old_post['featured_image']) {
                        $old_path = $upload_dir . $old_post['featured_image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                }
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Allowed: JPG, PNG, GIF, WEBP";
        }
    }
    
    try {
        if ($id) {
            // UPDATE
            if ($featured_image) {
                $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, category_id=?, status=?, featured_image=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$title, $slug, $excerpt, $content, $category_id, $status, $featured_image, $id]);
            } else {
                $stmt = $db->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, category_id=?, status=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$title, $slug, $excerpt, $content, $category_id, $status, $id]);
            }
            $success = "Blog post updated successfully!";
        } else {
            // CREATE
            $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, category_id, author_id, status, featured_image, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$title, $slug, $excerpt, $content, $category_id, $author_id, $status, $featured_image]);
            $success = "Blog post created successfully!";
        }
    } catch (Exception $e) {
        $error = "Error saving post: " . $e->getMessage();
    }
}

// Get post for editing
$editing_post = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$id]);
    $editing_post = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all posts
$posts = [];
try {
    $stmt = $db->query("SELECT bp.*, bc.name as category_name, u.full_name as author 
                        FROM blog_posts bp 
                        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                        LEFT JOIN users u ON bp.author_id = u.id 
                        ORDER BY bp.created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching posts: " . $e->getMessage();
}

// Fetch categories
$categories = [];
try {
    $stmt = $db->query("SELECT * FROM blog_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Lugomax CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; }
        .header { background: #0A1F44; color: white; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-content { max-width: 1400px; margin: 0 auto; padding: 0 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.5rem; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: white; text-decoration: none; opacity: 0.9; }
        .nav a:hover { opacity: 1; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 30px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #dc2626; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: #0A1F44; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; transition: all 0.3s; }
        .btn-primary { background: #FF6B2C; color: white; }
        .btn-primary:hover { background: #e55a1f; }
        .btn-secondary { background: #64748B; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .btn-sm { padding: 8px 16px; font-size: 0.85rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 15px; border-bottom: 1px solid #f5f7fa; }
        table tr:hover { background: #f9fafb; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .badge-draft { background: #f5f7fa; color: #4a5568; }
        .badge-published { background: #d1fae5; color: #065f46; }
        .actions { display: flex; gap: 8px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 12px; padding: 30px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .modal-header h3 { font-size: 1.5rem; color: #0A1F44; }
        .close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #0A1F44; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; }
        .form-group textarea { min-height: 150px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #FF6B2C; box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .image-preview { margin-top: 10px; max-width: 200px; border-radius: 8px; }
        .featured-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🚚 Lugomax CMS</h1>
            <div class="nav">
                <a href="index.php">Dashboard</a>
                <a href="blog.php">Blog</a>
                <a href="settings.php">Settings</a>
                <a href="track.php">Tracking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h2>📝 Blog Management</h2>
            <button class="btn btn-primary" onclick="openModal()">+ Create New Post</button>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-bottom: 20px; color: #0A1F44;">All Blog Posts (<?= count($posts) ?>)</h3>
            
            <?php if (count($posts) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['featured_image']): ?>
                                    <img src="../assets/images/blog/<?= htmlspecialchars($post['featured_image']) ?>" class="featured-img" alt="">
                                <?php else: ?>
                                    <div style="width:60px;height:60px;background:#f5f7fa;border-radius:8px;display:flex;align-items:center;justify-content:center;">📝</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($post['title']) ?></strong></td>
                            <td><?= htmlspecialchars($post['category_name'] ?? 'None') ?></td>
                            <td><?= htmlspecialchars($post['author'] ?? 'Unknown') ?></td>
                            <td><span class="badge badge-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-secondary btn-sm" onclick="editPost(<?= $post['id'] ?>)">Edit</button>
                                    <a href="?delete=<?= $post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #64748B;">
                    <h3>No Blog Posts Yet</h3>
                    <p>Create your first post!</p>
                    <button class="btn btn-primary" onclick="openModal()" style="margin-top: 20px;">Create First Post</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal" id="postModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Create New Post</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="id" id="postId">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Slug *</label>
                        <input type="text" name="slug" id="slug" required>
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" id="category_id" required>
                            <option value="">Select</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Featured Image</label>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" onchange="previewImage(this)">
                    <img id="imagePreview" class="image-preview" style="display:none;">
                </div>
                
                <div class="form-group">
                    <label>Excerpt</label>
                    <textarea name="excerpt" id="excerpt"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Content *</label>
                    <textarea name="content" id="content" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="status" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Post</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('postModal').classList.add('active');
            document.getElementById('postForm').reset();
            document.getElementById('postId').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Create New Post';
        }

        function closeModal() {
            document.getElementById('postModal').classList.remove('active');
        }

        function editPost(id) {
            window.location.href = '?edit=' + id;
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('title').addEventListener('input', function(e) {
            const slug = e.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            document.getElementById('slug').value = slug;
        });

        document.getElementById('postModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        <?php if ($editing_post): ?>
        document.addEventListener('DOMContentLoaded', function() {
            openModal();
            document.getElementById('postId').value = '<?= $editing_post['id'] ?>';
            document.getElementById('title').value = <?= json_encode($editing_post['title']) ?>;
            document.getElementById('slug').value = <?= json_encode($editing_post['slug']) ?>;
            document.getElementById('excerpt').value = <?= json_encode($editing_post['excerpt']) ?>;
            document.getElementById('content').value = <?= json_encode($editing_post['content']) ?>;
            document.getElementById('category_id').value = '<?= $editing_post['category_id'] ?>';
            document.getElementById('status').value = '<?= $editing_post['status'] ?>';
            <?php if ($editing_post['featured_image']): ?>
            document.getElementById('imagePreview').src = '../assets/images/blog/<?= $editing_post['featured_image'] ?>';
            document.getElementById('imagePreview').style.display = 'block';
            <?php endif; ?>
            document.getElementById('modalTitle').textContent = 'Edit Post';
        });
        <?php endif; ?>
    </script>
</body>
</html>
