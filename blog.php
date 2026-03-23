<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
init_session();

$current_page = 'blog';
$page_title = 'Blog';
$page_description = 'Insights, tips, and updates from Lugomax Logistics';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = get_setting('blog_posts_per_page', 9);
$offset = ($page - 1) * $per_page;

// Category filter
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Get database connection
$db = getDB();

// Count total posts
$count_query = "SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published'";
if ($category_id) {
    $count_query .= " AND category_id = $category_id";
}
$total_posts = $db->query($count_query)->fetch()['count'];
$total_pages = ceil($total_posts / $per_page);

// Fetch blog posts
$query = "SELECT bp.*, bc.name as category_name, u.full_name as author_name 
          FROM blog_posts bp
          LEFT JOIN blog_categories bc ON bp.category_id = bc.id
          LEFT JOIN users u ON bp.author_id = u.id
          WHERE bp.status = 'published'";
if ($category_id) {
    $query .= " AND bp.category_id = $category_id";
}
$query .= " ORDER BY bp.published_at DESC LIMIT $per_page OFFSET $offset";

$posts = $db->query($query)->fetchAll();

// Get categories for filter
$categories = $db->query("SELECT * FROM blog_categories ORDER BY id ASC")->fetchAll();

// Get featured post
$featured = $db->query("SELECT bp.*, bc.name as category_name 
                        FROM blog_posts bp
                        LEFT JOIN blog_categories bc ON bp.category_id = bc.id
                        WHERE bp.is_featured = TRUE AND bp.status = 'published'
                        ORDER BY bp.published_at DESC LIMIT 1")->fetch();

function getCatName($id, $item)
{
    $sql = "SELECT * FROM blog_categories WHERE `id`=$id";
    $stmt = query_sql($sql);
    $row = mysqli_fetch_assoc($stmt);
    return $row[$item];
}
include 'includes/header.php';
?>

<section class="hero-simple">
    <div class="container">
        <h1>Lugomax Logistics Blog</h1>
        <p>Insights, tips, and updates from the world of courier services and logistics.</p>
    </div>
</section>

<div class="filter-nav">
    <a href="blog" class="<?= !$category_id ? 'active' : '' ?>"><button class="filter-btn <?php if (!isset($_GET['category'])) print 'active'; ?>">All Posts</button></a>
    <?php foreach ($categories as $cat): ?>
        <a href="blog?category=<?= $cat['id'] ?>" class="<?= $category_id == $cat['id'] ? 'active' : '' ?>">
            <button class="filter-btn <?php if (@$_GET['category'] == @$cat['id']) print 'active'; ?>"><?= escape($cat['name']) ?></button>
        </a>
    <?php endforeach; ?>
</div>

<section style="background: #f8f8f8;" class="blog-section">
    <!-- Filter Navigation -->
    <div class="container">

        <?php $sql = query_sql("SELECT * FROM blog_posts WHERE status='published' ORDER BY id DESC LIMIT 1");
        if (mysqli_num_rows($sql) > 0) {
            $c = 0;
            while ($row = mysqli_fetch_assoc($sql)) { ?>
                <article class="featured-card">
                    <div class="card-image" style="background-image: url('assets/images/blog/<?php print $row['featured_image']; ?>');"></div>
                    <div class="card-content">
                        <div class="tag-wrapper">
                            <span class="featured-tag">Featured</span>
                            <span class="category-tag">
                                <?php $is_cat = $row['category_id'];
                                print getCatName($is_cat, 'name'); ?></span>
                        </div>

                        <h2 class="card-title"><?php print $row['title']; ?></h2>

                        <p class="card-description">
                            <?php print reduceTextLength($row['excerpt'], '200'); ?>
                        </p>

                        <div class="card-meta">
                            <div class="meta-item">
                                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?php print format_date($row['published_at']); ?></span>
                            </div>
                            <div class="meta-item">
                                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Editor Team</span>
                            </div>
                        </div>

                        <a href="blog-post?slug=<?php print $row['slug']; ?>" class="read-more-btn">
                            Read More
                            <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </article>
        <?php $c++;
            }
        }  ?>

    </div>
</section>


<section class="latest-articles-section">
    <div class="container">

        <div class="section-header">
            <h2 class="section-title mt-5">Latest Articles</h2>
            <p class="section-subtitle">Stay informed with our latest insights and industry updates.</p>
        </div>


        <div class="articles-grid">
            <?php foreach ($posts as $post): ?>
                <article class="article-card">
                    <div class="card-image-wrapper">
                        <img src="assets/images/blog/<?= escape($post['featured_image']) ?>" alt="<?= escape($post['title']) ?>" class="card-image">
                        <span class="category-badge"><?= escape($post['category_name']) ?></span>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?= escape($post['title']) ?></h3>
                        <p class="card-description"><?= escape(substr($post['excerpt'], 0, 120)) ?>...</p>
                        <div class="card-meta">
                            <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span><?= format_date($post['published_at']) ?></span>
                        </div>
                        <a href="blog-post?slug=<?= escape($post['slug']) ?>" class="read-more-link">
                            Read More
                            <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

        </div>

        <?php if ($total_pages > 1): ?>
            <div style="text-align: center; margin-top: 60px;">
                <?php if ($page > 1): ?>
                    <a href="blog.php?page=<?= $page - 1 ?><?= $category_id ? '&category=' . $category_id : '' ?>" class="btn btn-outline">← Previous</a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="blog.php?page=<?= $page + 1 ?><?= $category_id ? '&category=' . $category_id : '' ?>" class="btn btn-primary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<section style="background: #0d2d7a;" class="cta-section mt-5">
    <div class="container">
        <svg style="color: #f77f00;" xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up h-12 w-12 mx-auto mb-6 text-[#F77F00]" aria-hidden="true" data-fg-9lz79=":2.11252:/components/pages/Blog.tsx:218:13:10379:64:e:TrendingUp::::::tM8">
            <path d="M16 7h6v6"></path>
            <path d="m22 7-8.5 8.5-5-5L2 17"></path>
        </svg>
        <h2>Stay Updated</h2>
        <p>Subscribe to our newsletter for the latest logistics insights, tips, and company updates.</p>
        <form style="display: none;" class="newsletter-form">
            <input type="email" class="email-input" placeholder="Enter your email" required>
            <button type="submit" class="subscribe-btn">Subscribe</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    .blog-section {
        max-width: 100%;
        margin: 0 auto;
        justify-content: center;
        padding-top: 50px;
        padding-bottom: 50px;
    }

    /* Filter Navigation */
    .filter-nav {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding-bottom: 20px;
        padding-top: 20px;
        border-bottom: solid 1px #9ba6c4;
        text-align: center;
        justify-content: center;
        /* Add this line */
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        background-color: #fff;
        color: #666;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        background-color: #f0f0f0;
    }

    .filter-btn.active {
        background-color: #1e3a8a;
        color: #fff;
        border-color: #1e3a8a;
    }

    /* Featured Card */
    .featured-card {
        display: flex;
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .card-image {
        flex: 1;
        min-height: 400px;
        background-size: cover;
        background-position: center;
    }

    .card-content {
        flex: 1;
        padding: 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .tag-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .featured-tag {
        background-color: #f97316;
        color: #fff;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .category-tag {
        color: #666;
        font-size: 14px;
        font-weight: 500;
    }

    .card-title {
        font-size: 28px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 16px;
        line-height: 1.3;
    }

    .card-description {
        font-size: 16px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .card-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        color: #999;
        font-size: 14px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .meta-icon {
        width: 16px;
        height: 16px;
    }

    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #1e3a8a;
        color: #fff;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: background-color 0.3s ease;
        width: fit-content;
    }

    .read-more-btn:hover {
        background-color: #1e40af;
    }

    .arrow-icon {
        width: 16px;
        height: 16px;
        transition: transform 0.3s ease;
    }

    .read-more-btn:hover .arrow-icon {
        transform: translateX(4px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .featured-card {
            flex-direction: column;
        }

        .card-image {
            min-height: 250px;
        }

        .card-content {
            padding: 32px 24px;
        }

        .card-title {
            font-size: 24px;
        }
    }
</style>

<style>
    .latest-articles-section {
        max-width: 100%;
        margin: 0 auto;
    }

    /* Section Header */
    .section-header {
        text-align: center;
        margin-bottom: 48px;
    }

    .section-title {
        font-size: 32px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 12px;
    }

    .section-subtitle {
        font-size: 16px;
        color: #666;
    }

    /* Articles Grid */
    .articles-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    /* Article Card */
    .article-card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: box-shadow 0.3s ease;
    }

    .article-card:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .card-image-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background-color: #fff;
        color: #374151;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card-content {
        padding: 24px;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .card-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .card-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #9ca3af;
        font-size: 13px;
        margin-bottom: 16px;
    }

    .meta-icon {
        width: 14px;
        height: 14px;
    }

    .read-more-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #f97316;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: gap 0.3s ease;
    }

    .read-more-link:hover {
        gap: 10px;
    }

    .arrow-icon {
        width: 14px;
        height: 14px;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .articles-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .articles-grid {
            grid-template-columns: 1fr;
        }

        .section-title {
            font-size: 28px;
        }
    }

    /* Form */
    .newsletter-form {
        display: flex;
        gap: 12px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .email-input {
        flex: 1;
        min-width: 280px;
        max-width: 320px;
        padding: 14px 18px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        color: #374151;
        background-color: #fff;
        outline: none;
    }

    .email-input::placeholder {
        color: #9ca3af;
    }

    .subscribe-btn {
        padding: 14px 28px;
        background-color: #f97316;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .subscribe-btn:hover {
        background-color: #ea580c;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .newsletter-section {
            padding: 60px 20px;
        }

        .newsletter-title {
            font-size: 24px;
        }

        .email-input {
            min-width: 100%;
            max-width: 100%;
        }

        .subscribe-btn {
            width: 100%;
        }
    }
</style>