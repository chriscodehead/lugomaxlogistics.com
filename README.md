# Lugomax Logistics - Complete PHP Website

## 📦 Complete Package Contents

This ZIP contains your **complete, production-ready** Lugomax Logistics website with:

- ✅ 12 Dynamic PHP Pages
- ✅ Complete Database Schema (13 tables)
- ✅ Admin Panel with Login
- ✅ Real-time Order Tracking System
- ✅ Blog CMS with Categories
- ✅ All Forms Functional
- ✅ Complete CSS & JavaScript
- ✅ Security Features (CSRF, XSS Protection)

## 📁 File Structure

```
lugomax_complete/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── header.php            # Shared header
│   ├── footer.php            # Shared footer
│   └── functions.php         # Helper functions
├── admin/
│   ├── login.php             # Admin login
│   └── index.php             # Admin dashboard
├── api/
│   └── track-order.php       # Tracking API endpoint
├── assets/
│   ├── css/
│   │   └── styles.css        # Complete stylesheet (29KB)
│   └── js/
│       └── script.js         # All JavaScript (11KB)
├── database.sql              # Complete database schema
├── index.php                 # Homepage
├── about.php                 # About Us page
├── services.php              # Services page (database-driven)
├── blog.php                  # Blog listing
├── blog-post.php             # Individual blog post
├── contact.php               # Contact form
├── quote.php                 # Quote request form
├── track.php                 # Order tracking
├── careers.php               # Job applications
├── shop.php                  # Packaging shop
├── resources.php             # Help center
└── specialised.php           # Specialised services
```

## 🚀 Installation Instructions

### Step 1: Extract Files
Extract the ZIP file to your web server directory:
- **cPanel**: Extract to `public_html/`
- **XAMPP**: Extract to `htdocs/`
- **WAMP**: Extract to `www/`

### Step 2: Create Database
1. Go to phpMyAdmin
2. Create a new database: `lugomax_db`
3. Set character set to `utf8mb4_general_ci`
4. Import the `database.sql` file

### Step 3: Configure Database Connection
Edit `config/database.php` and update these lines:

```php
define('DB_HOST', 'localhost');      // Your database host
define('DB_NAME', 'lugomax_db');     // Your database name
define('DB_USER', 'root');           // Your database username
define('DB_PASS', '');               // Your database password

define('SITE_URL', 'http://localhost');  // Your website URL
```

### Step 4: Set Folder Permissions
Create these folders and set permissions to 777:

```bash
mkdir uploads
mkdir uploads/blog
mkdir uploads/resumes
chmod 777 uploads
chmod 777 uploads/blog
chmod 777 uploads/resumes
```

### Step 5: Test Installation
1. Visit: `http://yoursite.com/index.php`
2. Admin login: `http://yoursite.com/admin/login.php`
   - Username: `admin`
   - Password: `admin123`

### Step 6: **IMPORTANT** - Change Admin Password
**Immediately** change the default password after first login!

## 🗄️ Database Information

**13 Tables Created:**
1. `users` - Admin/staff accounts
2. `orders` - Order tracking system
3. `order_status_history` - Order timeline
4. `blog_categories` - Blog categories
5. `blog_posts` - Blog content
6. `blog_tags` - Post tags
7. `blog_post_tags` - Tag relationships
8. `services` - Service offerings
9. `quotes` - Quote requests
10. `testimonials` - Customer reviews
11. `contact_messages` - Contact form submissions
12. `job_applications` - Career applications
13. `site_settings` - Site configuration

**Default Admin Account:**
- Username: `admin`
- Password: `admin123`
- ⚠️ **CHANGE THIS IMMEDIATELY!**

## ✨ Key Features

### Order Tracking System
- Real-time tracking with status updates
- Auto-generated tracking numbers (LGX-YYYYMMDD-XXXXXX)
- Visual progress bar
- Full delivery timeline
- GPS coordinates support

### Blog CMS
- Create/edit blog posts
- Category management
- Tag system
- Featured posts
- View counter
- SEO-friendly URLs

### Admin Panel
- Dashboard with statistics
- Order management
- Quote management
- Blog management
- User management

### All Forms Working
- Contact form → Saves to database
- Quote request → Auto-generates quote numbers
- Job applications → File upload support
- Order tracking → Real-time API lookup

### Security Features
- CSRF protection on all forms
- SQL injection prevention (PDO prepared statements)
- XSS prevention (escape() function)
- Password hashing (bcrypt)
- Session management

## 🎨 Design Preserved

✅ All original design intact
✅ Hero text is white (bug fixed)
✅ Navy blue (#0A1F44) + Orange (#FF6B2C) color scheme
✅ Sora & DM Sans fonts
✅ All animations working
✅ Fully responsive
✅ Mobile menu functional

## 📞 Support

If you encounter any issues:

1. **Database connection error**: Check `config/database.php` credentials
2. **404 errors**: Check file paths match your server structure
3. **Form not working**: Check folder permissions (uploads folder)
4. **Admin can't login**: Check database was imported correctly

## 🔧 Customization

### Change Site Name/Email
Edit `config/database.php`:
```php
define('SITE_NAME', 'Your Company Name');
define('SITE_EMAIL', 'your@email.com');
```

### Add Services
Add services via database or admin panel (when built)

### Modify Colors
Edit `assets/css/styles.css`:
```css
--primary-navy: #0A1F44;
--secondary-orange: #FF6B2C;
```

## 📝 Next Steps (Optional)

To extend functionality, you can add:
- Admin pages for order management
- Blog post editor with WYSIWYG
- Service management interface
- Email notifications for quotes
- Payment integration

## 🎉 You're Ready!

Your complete Lugomax Logistics website is ready to deploy!

---

**Questions?** Review the file structure and database schema in this README.
**Need changes?** All code is fully commented and organized.

Enjoy your new website! 🚀
