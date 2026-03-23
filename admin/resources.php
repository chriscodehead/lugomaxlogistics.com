<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
init_session();

if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

$db = getDB();
$success = $error = "";

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {

  $id = (int)$_GET['delete'];

  $stmt = $db->prepare("SELECT file_name FROM resources WHERE id=?");
  $stmt->execute([$id]);
  $file = $stmt->fetchColumn();

  if ($file && file_exists("../assets/resources/" . $file)) {
    unlink("../assets/resources/" . $file);
  }

  $db->prepare("DELETE FROM resources WHERE id=?")->execute([$id]);
  $success = "Resource deleted successfully.";
}


/* ================= CREATE / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $id = $_POST['id'] ?? null;
  $title = sanitize_input($_POST['title']);
  $description = sanitize_input($_POST['description']);
  $status = $_POST['status'];

  $file_name = null;
  $file_type = null;
  $file_size = null;

  if (!empty($_FILES['resource_file']['name'])) {

    $upload_dir = "../assets/resources/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'zip'];
    $ext = strtolower(pathinfo($_FILES['resource_file']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
      $error = "Invalid file type.";
    } else {

      $file_name = uniqid() . "." . $ext;
      move_uploaded_file($_FILES['resource_file']['tmp_name'], $upload_dir . $file_name);

      $file_type = strtoupper($ext);
      $file_size = round($_FILES['resource_file']['size'] / 1024) . " KB";
    }
  }

  if (!$error) {

    if ($id) {

      if ($file_name) {

        $old = $db->prepare("SELECT file_name FROM resources WHERE id=?");
        $old->execute([$id]);
        $oldFile = $old->fetchColumn();

        if ($oldFile && file_exists("../assets/resources/" . $oldFile)) {
          unlink("../assets/resources/" . $oldFile);
        }

        $sql = "UPDATE resources SET
            title=?,description=?,file_name=?,file_type=?,file_size=?,status=?,updated_at=NOW()
          WHERE id=?";
        $db->prepare($sql)->execute([$title, $description, $file_name, $file_type, $file_size, $status, $id]);
      } else {

        $sql = "UPDATE resources SET
            title=?,description=?,status=?,updated_at=NOW()
          WHERE id=?";
        $db->prepare($sql)->execute([$title, $description, $status, $id]);
      }

      $success = "Resource updated successfully.";
    } else {

      $sql = "INSERT INTO resources
        (title,description,file_name,file_type,file_size,status)
        VALUES (?,?,?,?,?,?)";

      $db->prepare($sql)->execute([$title, $description, $file_name, $file_type, $file_size, $status]);

      $success = "Resource added successfully.";
    }
  }
}

/* ================= FETCH ================= */
$resources = $db->query("SELECT * FROM resources ORDER BY created_at DESC")
  ->fetchAll(PDO::FETCH_ASSOC);




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resources Management - Lugomax CMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
    }

    .header {
      background: #0A1F44;
      color: white;
      padding: 20px 0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-content {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h1 {
      font-size: 1.5rem;
    }

    .nav {
      display: flex;
      gap: 20px;
    }

    .nav a {
      color: white;
      text-decoration: none;
      opacity: 0.9;
    }

    .nav a:hover {
      opacity: 1;
    }

    .container {
      max-width: 1400px;
      margin: 30px auto;
      padding: 0 30px;
    }

    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
    }

    .alert-error {
      background: #fee2e2;
      color: #dc2626;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .page-header h2 {
      font-size: 2rem;
      color: #0A1F44;
    }

    .btn {
      display: inline-block;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
    }

    .btn-primary {
      background: #FF6B2C;
      color: white;
    }

    .btn-primary:hover {
      background: #e55a1f;
    }

    .btn-secondary {
      background: #64748B;
      color: white;
    }

    .btn-danger {
      background: #dc2626;
      color: white;
    }

    .btn-sm {
      padding: 8px 16px;
      font-size: 0.85rem;
    }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      padding: 30px;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th {
      background: #f8f9fa;
      padding: 15px;
      text-align: left;
      font-weight: 600;
      border-bottom: 2px solid #e2e8f0;
    }

    table td {
      padding: 15px;
      border-bottom: 1px solid #f5f7fa;
    }

    table tr:hover {
      background: #f9fafb;
    }

    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .badge-draft {
      background: #f5f7fa;
      color: #4a5568;
    }

    .badge-published {
      background: #d1fae5;
      color: #065f46;
    }

    .actions {
      display: flex;
      gap: 8px;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal.active {
      display: flex;
    }

    .modal-content {
      background: white;
      border-radius: 12px;
      padding: 30px;
      max-width: 800px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .modal-header h3 {
      font-size: 1.5rem;
      color: #0A1F44;
    }

    .close-modal {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #0A1F44;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-family: inherit;
    }

    .form-group textarea {
      min-height: 150px;
      resize: vertical;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #FF6B2C;
      box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.1);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .image-preview {
      margin-top: 10px;
      max-width: 200px;
      border-radius: 8px;
    }

    .featured-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }
  </style>
</head>

<body>
  <div class="header">
    <div class="header-content">
      <h1>🚚 Lugomax CMS</h1>
      <div class="nav">
        <a href="index.php">Dashboard</a>
        <a href="resources.php">Resources</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="page-header">
      <h2>📝 Resources Management</h2>
      <button class="btn btn-primary" onclick="openModal()">+ Create New Resources</button>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <h3>All Resources (<?= count($resources) ?>)</h3>

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Size</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($resources as $r): ?>
            <tr>

              <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
              <td><?= $r['file_type'] ?></td>
              <td><?= $r['file_size'] ?></td>
              <td><?= $r['status'] ?></td>
              <td><?= date('M d Y', strtotime($r['created_at'])) ?></td>

              <td class="actions">

                <button class="btn btn-secondary btn-sm"
                  onclick='editResource(<?= json_encode($r) ?>)'>
                  Edit
                </button>

                <a class="btn btn-secondary btn-sm"
                  href="../assets/resources/<?= $r['file_name'] ?>"
                  target="_blank">View</a>

                <a class="btn btn-danger btn-sm"
                  href="?delete=<?= $r['id'] ?>"
                  onclick="return confirm('Delete resource?')">
                  Delete
                </a>

              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </div>

  <div class="modal" id="resourceModal">
    <div class="modal-content">

      <div class="modal-header">
        <h3 id="modalTitle">Create Resource</h3>
        <button class="close-modal" onclick="closeModal()">&times;</button>
      </div>

      <form method="POST" enctype="multipart/form-data" id="resourceForm">

        <input type="hidden" name="id" id="resourceId">

        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="description"></textarea>
        </div>

        <div class="form-group">
          <label>Upload File</label>
          <input type="file" name="resource_file">
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status" id="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div style="text-align:right">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Resource</button>
        </div>

      </form>
    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('resourceModal').classList.add('active');
      document.getElementById('resourceForm').reset();
      document.getElementById('resourceId').value = '';
      document.getElementById('modalTitle').innerText = "Create Resource";
    }

    function closeModal() {
      document.getElementById('resourceModal').classList.remove('active');
    }

    function editResource(data) {

      openModal();

      document.getElementById('modalTitle').innerText = "Edit Resource";
      document.getElementById('resourceId').value = data.id;
      document.getElementById('title').value = data.title;
      document.getElementById('description').value = data.description;
      document.getElementById('status').value = data.status;
    }

    document.getElementById('resourceModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
  </script>

</body>

</html>