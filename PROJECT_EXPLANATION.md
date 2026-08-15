# Fair Law Firm LTD - Project Documentation

## 1. Project Overview

**Fair Law Firm LTD** is a full-stack PHP web application built for a Rwandan company that provides two core services:
1. **Legal Services** - Court representation, mediation, business transaction facilitation, contract drafting, and legal advisory across civil, commercial, social, and administrative fields.
2. **Property Management** - Rental/sales management, rent recovery, marketing, compliance with administrative directives, and property maintenance.

The application serves as both a public-facing marketing website and an internal management dashboard for content administration.

---

## 2. Technology Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.4 (PDO) |
| Database | MySQL/MariaDB (`helloshi_fairdb`) |
| Frontend | HTML5, CSS3, JavaScript (jQuery) |
| CSS Framework | Bootstrap 5 + Firdip Template |
| Development Server | XAMPP (Apache + MySQL) |
| Dependency Manager | Composer |
| Server OS | Windows (XAMPP localhost) |

---

## 3. Database Schema

The database `helloshi_fairdb` contains the following tables:

### 3.1 `users`
- **Purpose**: Stores user profile information
- **Key Fields**: `id`, `first_name`, `last_name`, `email`, `phone`, `gender`, `profile_image`, `role_id`, `status`
- **Relationships**: Linked to `roles` via `role_id`

### 3.2 `login`
- **Purpose**: Handles user authentication (email/password + usertype)
- **Key Fields**: `email` (PK), `password` (bcrypt hashed), `usertype` (admin/user)
- **Note**: Separate from `users` table for authentication logic

### 3.3 `roles`
- **Purpose**: Defines user roles
- **Values**: Admin (full access), Employer (limited access)

### 3.4 `about_content`
- **Purpose**: Stores dynamic "About Us" content
- **Key Fields**: `image`, `title`, `description`, `more_description`, `client`, `cases_won`, `achievements`, `our_team`, `status`

### 3.5 `add_property`
- **Purpose**: Lists managed properties (image, location, title)
- **Key Fields**: `image`, `location`, `title`, `status`

### 3.6 `properties`
- **Purpose**: Detailed property listings for rent/sale
- **Key Fields**: `title`, `description`, `property_status` (For Rent/For Sale), `property_type`, `price`, `property_size`, `bedroom`, `bathroom`, `street`, `sector`, `district`, `country`, `floor`, `months`

### 3.7 `property_images`
- **Purpose**: Multiple images per property
- **Key Fields**: `property_id`, `image_name`, `is_featured`, `image_path`

### 3.8 `blog`
- **Purpose**: Blog posts
- **Key Fields**: `image`, `title`, `description_blog`, `blog_description_details`, `date`, `category_blog`, `status` (active/pending)

### 3.9 `blog_attachments`
- **Purpose**: File attachments for blog posts
- **Key Fields**: `blog_id`, `file_name`, `file_path`, `file_type`, `file_size`

### 3.10 `home_backgrounds`
- **Purpose**: Carousel background images for the homepage
- **Key Fields**: `image_path`, `status`

### 3.11 `videos`
- **Purpose**: Embedded video links
- **Key Fields**: `video_link`, `status`

### 3.12 `password_reset`
- **Purpose**: Stores password reset tokens with expiry

---

## 4. Project Structure

```
first-legal-service/
|-- index.php                          # Homepage with hero, services, blog preview, CTA
|-- about_us.php                       # About page (dynamic from DB)
|-- contact.php                        # Contact page with form + Google Maps
|-- legal_services.php                 # Legal services details page
|-- property_service.php               # Property management services page
|-- property.php                       # Property listing (for rent/sale) with pagination
|-- property_detail.php                # Single property detail view
|-- manage_property.php                # Admin: list and manage properties
|-- blog.php                           # Blog listing page
|-- blog_details.php                   # Single blog post view
|-- 404.php                            # Error page
|-- first-legal-service.php            # Legal service details (template variant)
|-- contactEmail.php                   # Contact form handler (sends email)
|-- bookingEmail.php                   # Booking/form email handler
|-- fairdb.sql                         # Database dump (schema + seed data)
|-- README.md                          # Minimal README
|
|-- include/
|   |-- header.php                     # Global header (nav, topbar, language switcher)
|   |-- footer.php                     # Global footer
|
|-- Lang/                              # Internationalization (i18n)
|   |-- lang.php                       # Language loader (session-based EN/FR)
|   |-- en.php                         # English translations (empty array - mostly unused)
|   |-- fr.php                         # French translations
|
|-- data/                              # Admin dashboard & backend logic
|   |-- index.php                      # Login page
|   |-- dashboard.php                  # Admin dashboard (statistics)
|   |-- register.php                   # User registration
|   |-- register_process.php           # Registration handler
|   |-- forgot_password.php            # Forgot password form
|   |-- reset_password.php             # Password reset handler
|   |-- propertyMgt/
|   |   |-- config.php                 # Database connection (PDO)
|   |   |-- aboutImg/                  # About page images
|   |   |-- blogImg/                   # Blog images
|   |   |-- blogFiles/                 # Blog attachments (PDF, DOC, etc.)
|   |   |-- proImg/                    # Property listing images
|   |   |-- rentalImg/                 # Property rental images
|   |   |-- propertyImg/               # Additional property images
|   |   |-- logoImg/                   # Logo uploads
|   |   |-- userImg/                   # User profile images
|   |   |-- videoImg/                  # Video thumbnails
|   |-- css/                           # Dashboard styles
|   |-- include/                       # Dashboard-specific header/footer
|   |-- uploads/                       # General uploads
|   |-- add_property.php               # Add property handler
|   |-- add_rental_property.php        # Add rental property handler
|   |-- add_about.php                  # Add/edit about content
|   |-- add_blog.php                   # Add blog post with attachments
|   |-- add_video.php                  # Add video
|   |-- edit_property.php              # Edit property
|   |-- edit_rental.php                # Edit rental
|   |-- edit_blog.php                  # Edit blog post
|   |-- edit_about.php                 # Edit about content
|   |-- edit_background.php            # Edit homepage backgrounds
|   |-- edit_user.php                  # Edit user profile
|   |-- display_properties.php         # List all properties
|   |-- display_blog.php               # List all blog posts
|   |-- display_about.php              # List about content entries
|   |-- display_video.php              # List videos
|   |-- display_rental.php             # List rental properties
|   |-- home_background.php            # Manage homepage backgrounds
|   |-- property_details.php           # Property details management
|   |-- property_images.php            # Manage property images
|   |-- delete_property_image.php      # Delete property image
|   |-- delete_attachment.php          # Delete blog attachment
|   |-- view_blog.php                  # View single blog
|   |-- view_about.php                 # View about content
|   |-- 404_error.php                  # Error handler
|
|-- assets/                            # Frontend assets
|   |-- css/
|   |   |-- firdip.css                 # Main template CSS
|   |   |-- firdip-rtl.css             # RTL support
|   |   |-- firdip-dark.css            # Dark theme
|   |   |-- firdip-custom-rtl.css      # Custom RTL overrides
|   |-- images/                        # Theme images, logos, backgrounds
|   |-- js/
|   |   |-- firdip.js                  # Main template JS
|   |-- vendors/                       # Third-party libraries
|
|-- composer/                           # Composer PHP dependency manager
|   |-- composer.json
|   |-- composer.lock
|   |-- vendor/
|       |-- autoload.php
```

---

## 5. Key Features

### 5.1 Public Website Features
- **Homepage**: Hero carousel, about section, mission/vision, services grid, latest blog posts, call-to-action
- **Services**: Detailed legal and property management service pages
- **Properties**: Paginated property listings (For Rent / For Sale) with filtering
- **Blog**: Public blog with categories, post details, and downloadable attachments
- **Contact**: Contact form with email notification, embedded Google Maps
- **Multilingual**: English and French language toggle (session-based)
- **Responsive Design**: Bootstrap 5 + custom CSS, mobile-friendly

### 5.2 Admin Dashboard Features
- **Authentication**: Login/logout with role-based access (admin vs employer)
- **Password Management**: Forgot password, reset password with token expiry
- **User Management**: Register, edit users, assign roles
- **About Content Management**: Add/edit company information and statistics
- **Property Management**: Full CRUD for properties and images
- **Blog Management**: Create posts with image + multiple file attachments, edit, delete
- **Video Management**: Add/remove embedded videos
- **Homepage Background Management**: Dynamic carousel background control
- **Dashboard Statistics**: Counters for users, properties, and rental properties

### 5.3 Security & Authentication
- Password hashing: `password_hash()` / `password_verify()` (bcrypt)
- Session-based authentication
- Role-based access control (admin/employer)
- Prepared statements (PDO) to prevent SQL injection
- Input sanitization with `filter_var()` and `htmlspecialchars()`
- File upload validation (type, size checks)

---

## 6. Configuration

### 6.1 Database Connection
File: `data/propertyMgt/config.php`
- Host: `localhost`
- Database: `helloshi_fairdb`
- Username: `helloshi_fairUser`
- Password: `Allin@12345`

### 6.2 Session & Language
File: `Lang/lang.php`
- Session auto-start
- Language stored in `$_SESSION['lang']`
- Supports `?lang=en` and `?lang=fr` URL parameters

---

## 7. Public Pages & Routes

| Page | File | Description |
|------|------|-------------|
| Home | `index.php` | Hero, about preview, services, blog, CTA |
| About | `about_us.php` | Full company description + stats |
| Contact | `contact.php` | Contact form + map |
| Legal Services | `legal_services.php` | Legal service details |
| Property Service | `property_service.php` | Property management details |
| Properties | `property.php` | Paginated property listings |
| Property Detail | `property_detail.php` | Single property view |
| Manage Properties | `manage_property.php` | Property showcase grid |
| Blog | `blog.php` | Blog listing |
| Blog Details | `blog_details.php` | Single blog post |
| 404 | `404.php` | Not found page |

---

## 8. Admin Pages (under `/data/`)

| Page | Purpose |
|------|---------|
| `index.php` | Login |
| `dashboard.php` | Statistics overview |
| `register.php` / `register_process.php` | User registration |
| `forgot_password.php` / `reset_password.php` | Password recovery |
| `add_property.php` | Create property |
| `edit_property.php` | Update property |
| `add_rental_property.php` | Create rental property |
| `edit_rental.php` | Update rental property |
| `add_about.php` / `edit_about.php` | Manage about content |
| `add_blog.php` / `edit_blog.php` | Manage blog posts |
| `add_video.php` | Add video |
| `home_background.php` / `edit_background.php` | Manage carousel images |
| `display_properties.php` | List properties |
| `display_blog.php` | List blogs |
| `display_about.php` | List about entries |
| `property_images.php` | Manage property images |
| `edit_user.php` | Edit user profile |

---

## 9. Email Functionality

The application uses PHP `mail()` for sending emails:
- **Contact Form**: `contactEmail.php` - Sends user inquiries to `fairlawfirmltd@gmail.com`
- **Booking/Forms**: `bookingEmail.php` - Handles booking requests

---

## 10. Notes & Observations

- The project is a modified/customized version of the **Firdip HTML template** (originally designed for fire departments, repurposed for a law firm)
- Some SEO meta descriptions still reference the original fire department theme
- The `en.php` language file is empty; most content is hardcoded in English with only partial French translations in `fr.php`
- Admin dashboard uses a different visual style (`white_shd`, `graph_head` classes) compared to the public site
- File uploads are stored in `data/propertyMgt/` subdirectories with time-prefixed filenames
- The project runs locally on XAMPP at `http://localhost/first-legal-service/`

---

## 11. Getting Started

1. Install XAMPP (Apache + MySQL + PHP 8.4)
2. Place project in `C:\xampp\htdocs\first-legal-service\`
3. Import `fairdb.sql` into phpMyAdmin (database: `helloshi_fairdb`)
4. Configure database credentials in `data/propertyMgt/config.php` if needed
5. Start Apache and MySQL via XAMPP Control Panel
6. Visit `http://localhost/first-legal-service/`

**Admin Access**: Use credentials from the `login` table in the database (e.g., `remymugisha64@gmail.com` / `ngirumpetse@yahoo.com` for admin accounts).
