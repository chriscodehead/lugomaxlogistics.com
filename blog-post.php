<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$slug = sanitize_input($_GET['slug'] ?? '');

if (empty($slug)) {
  redirect('blog');
}

// Get post from database
$db = getDB();
$stmt = $db->prepare("SELECT bp.*, bc.name as category_name, u.full_name as author_name 
                      FROM blog_posts bp
                      LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                      LEFT JOIN users u ON bp.author_id = u.id
                      WHERE bp.slug = ? AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
  redirect('blog');
}

// Update view count
$db->exec("UPDATE blog_posts SET views = views + 1 WHERE id = " . (int)$post['id']);

// Get related posts
$stmt = $db->prepare("SELECT * FROM blog_posts 
                      WHERE category_id = ? AND id != ? AND status = 'published'
                      ORDER BY published_at DESC LIMIT 3");
$stmt->execute([$post['category_id'], $post['id']]);
$related_posts = $stmt->fetchAll();

$current_page = 'blog';
$page_title = escape($post['title']) . ' - Lugomax Blog';
$page_description = escape($post['excerpt']);

include 'includes/header.php';
?>

<article class="blog-post-page">
  <header class="blog-post-header">
    <div class="container" style="max-width: 800px;">
      <div class="blog-post-meta">
        <span class="article-category"><?= escape($post['category_name']) ?></span>
        <span>·</span>
        <span><?= format_date($post['published_at']) ?></span>
        <span>·</span>
        <span><?= number_format($post['views']) ?> views</span>
      </div>
      <h4 class="blog-post-title" style="color: white; font-size: 40px;"><?= escape($post['title']) ?></h4>
      <div class="blog-post-author">
        <span>By Editor Team</span>
      </div>
    </div>
  </header>

  <?php if ($post['featured_image']): ?>
    <div class="blog-post-featured-image">
      <div class="container" style="max-width: 1000px;">
        <img src="assets/images/blog/<?= escape($post['featured_image']) ?>" alt="<?= escape($post['title']) ?>">
      </div>
    </div>
  <?php endif; ?>

  <div class="blog-post-content">
    <div class="container" style="max-width: 800px;">
      <?= $post['content'] ?>
    </div>
  </div>


  <footer class="blog-post-footer">
    <div class="container" style="max-width: 800px;">
      <div class="blog-post-share">
        <h4>Share this article</h4>
        <div class="share-buttons">
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode('blog-post?slug=' . $post['slug']) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="share-btn">Twitter</a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('blog-post?slug=' . $post['slug']) ?>" target="_blank" class="share-btn">Facebook</a>
          <a href="https://www.linkedin.com/shareArticle?url=<?= urlencode('blog-post?slug=' . $post['slug']) ?>&title=<?= urlencode($post['title']) ?>" target="_blank" class="share-btn">LinkedIn</a>
        </div>
      </div>
    </div>
  </footer>
</article>

<?php if (count($related_posts) > 0): ?>
  <section class="section" style="background: var(--bg-light);">
    <div class="container">
      <div class="section-header">
        <h2>Related Articles</h2>
      </div>
      <div class="grid-3">
        <?php foreach ($related_posts as $related): ?>
          <div class="article-card">
            <?php if ($related['featured_image']): ?>
              <img src="assets/images/blog/<?= escape($related['featured_image']) ?>" alt="<?= escape($related['title']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0;">
            <?php else: ?>
              <div style="background: var(--bg-gray); width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; color: var(--text-light); border-radius: 12px 12px 0 0;">Article Image</div>
            <?php endif; ?>
            <div class="article-content">
              <h3><?= escape($related['title']) ?></h3>
              <p><?= escape(substr($related['excerpt'], 0, 100)) ?>...</p>
              <a href="blog-post.php?slug=<?= escape($related['slug']) ?>" class="btn btn-outline btn-sm">Read More →</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<style>
  .blog-post-header {
    background: linear-gradient(135deg, #0F2557 0%, #0A1940 100%);
    color: white;
    padding: 100px 0 60px;
    text-align: center;
  }

  .blog-post-meta {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 20px;
    font-size: 14px;
  }

  .blog-post-meta span {
    margin: 0 8px;
  }

  .blog-post-title {
    font-size: 3rem;
    margin: 20px 0;
    line-height: 1.2;
  }

  .blog-post-excerpt {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 30px;
  }

  .blog-post-author {
    color: rgba(255, 255, 255, 0.7);
  }

  .blog-post-featured-image {
    margin: 40px 0;
  }

  .blog-post-featured-image img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }

  .blog-post-content {
    padding: 60px 0;
    font-size: 1.1rem;
    line-height: 1.8;
  }

  .blog-post-content h2 {
    margin-top: 40px;
    margin-bottom: 20px;
  }

  .blog-post-content p {
    margin-bottom: 20px;
  }

  .blog-post-footer {
    padding: 40px 0;
    background: var(--bg-light);
  }

  .blog-post-share h4 {
    margin-bottom: 20px;
  }

  .share-buttons {
    display: flex;
    gap: 15px;
  }

  .share-btn {
    padding: 10px 20px;
    background: var(--primary-navy);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
  }

  .share-btn:hover {
    background: var(--accent-orange);
  }
</style>

<?php include 'includes/footer.php'; ?>