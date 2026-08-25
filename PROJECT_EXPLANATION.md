# Fair Law Firm LTD - Complete Project Documentation

## 1. Executive Summary

**Fair Law Firm LTD** is a full-stack PHP web application serving as both a public-facing marketing platform and an internal content management system for a Rwandan law and property management firm founded in 2021. The system enables the firm to showcase legal services, manage property listings (rent/sale), puyeahblish blog content, handle client inquiries via email, and administer all content through a role-based dashboard.

---

## 2. Business Context

### 2.1 Company Profile
- **Name**: Fair Law Firm LTD
- **Founded**: 2021
- **Country**: Rwanda
- **Core Business**:
  - Legal Services (court representation, mediation, contract drafting, legal advisory)
  - Property Management (rental/sales management, rent recovery, marketing, compliance)

### 2.2 Target Audience
- Individuals seeking legal representation in penal, civil, commercial, social, and administrative fields
- Property owners and tenants looking for rental/sales management
- Corporate clients requiring business transaction facilitation

---

## 3. Technology Stack

| Layer | Technology | Version/Details |
|-------|-----------|-----------------|
| **Backend** | PHP | 8.4 (procedural style) |
| **Database** | MySQL/MariaDB | 10.11.18-MariaDB |
| **Frontend** | HTML5, CSS3, JavaScript (jQuery 3.6.0/3.7.0) | - |
| **CSS Framework** | Bootstrap 5.0.2 | Custom Firdip template |
| **Template** | Firdip HTML Template | Originally designed for fire departments, repurposed |
| **Mail Library** | PHPMailer 6.1 | Composer + bundled copy |
| **ORM/Query** | PDO (PHP Data Objects) | Prepared statements for SQL injection prevention |
| **Dependency Manager** | Composer | 2.x |
| **Server Stack** | XAMPP | Apache + MySQL on Windows |
| **Password Hashing** | bcrypt | `password_hash()` / `password_verify()` |
| **Icons** | FontAwesome 6.2.1, Flaticon | CDN + local |

---

## 4. System Architecture

### 4.1 High-Level Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                         PUBLIC LAYER                         │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │ Homepage │ │ About Us │ │ Contact  │ │ Services │ ...    │
│  │ index.php│ │about_us.php│ │contact.php│ │legal_   │      │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘      │
│       │             │            │            │             │
│       └─────────────┴────────────┴────────────┘             │
│                         │                                    │
│              ┌──────────▼──────────┐                        │
│              │   include/header    │                        │
│              │   include/footer    │                        │
│              └──────────┬──────────┘                        │
└─────────────────────────┼───────────────────────────────────┘
                          │
                          │ AJAX/Form POST
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                      EMAIL HANDLERS                          │
│            contactEmail.php / bookingEmail.php               │
│                    (PHPMailer SMTP)                          │
└─────────────────────────────────────────────────────────────┘
                          │
                          │ Session Auth Check
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                      ADMIN LAYER (/data/)                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │  Login   │ │Dashboard │ │ Property │ │   Blog   │ ...    │
│  │ index.php│ │dashboard│ │  Mgmt    │ │  Mgmt    │      │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘      │
│       │             │            │            │             │
│       └─────────────┴────────────┴────────────┘             │
│                         │                                    │
│              ┌──────────▼──────────┐                        │
│              │ data/include/header │                        │
│              │ data/include/footer │                        │
│              └──────────┬──────────┘                        │
│                         │                                    │
│              ┌──────────▼──────────┐                        │
│              │ data/propertyMgt/   │                        │
│              │    config.php       │                        │
│              │   (PDO Connection)   │                        │
│              └──────────┬──────────┘                        │
│                         │                                    │
│              ┌──────────▼──────────┐                        │
│              │   Database (PDO)     │                       │
│              │  helloshi_fairdb     │                       │
│              └─────────────────────┘                       │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Design Pattern
The application follows a **procedural PHP pattern** with no formal MVC framework. Each page is self-contained with inline PHP logic. Shared components are included via PHP `require_once`.

### 4.3 Authentication Flow
```
User → Login Form (data/index.php)
         │
         ▼
   PDO Query: users table (email existence check)
         │
         ▼
   PDO Query: login table (password + usertype)
         │
         ▼
   password_verify() → Session Variables
         │
         ▼
   Redirect → dashboard.php
```

---

## 5. Database Schema

### 5.1 Entity-Relationship Overview

```
users (1) ←── (1) login (via email)
users (*) ←── (*) roles (via role_id)

add_property (*) ←── (*) property_images (via property_id)
properties (*) ←── (*) property_images (via property_id)
properties (*) ←── (*) property (legacy table)

blog (1) ←── (*) blog_attachments (via blog_id)

home_backgrounds (standalone carousel)
videos (standalone embedded videos)
about_content (single row CMS)
password_reset (standalone tokens)
```

### 5.2 Detailed Table Specifications

#### Table: `users`
Stores user profile information.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| first_name | varchar | Required |
| last_name | varchar | Required |
| email | varchar(100) | Unique |
| phone | varchar | Required |
| gender | enum | male/female |
| profile_image | varchar | Uploaded image path |
| role_id | int(11) | FK → roles.role_id |
| status | enum | Active/Pending |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `login`
Handles authentication separately from profile data.
| Column | Type | Constraints |
|--------|------|-------------|
| email | varchar(100) | PK |
| password | varchar(255) | bcrypt hashed |
| usertype | enum | admin/user |

**Note**: The separation of `users` and `login` tables is an architectural decision that requires JOIN operations and cascading deletes.

#### Table: `roles`
| Column | Type | Constraints |
|--------|------|-------------|
| role_id | int(11) | PK, AUTO_INCREMENT |
| role_name | varchar(50) | e.g., "Admin", "Employer" |
| description | text | Nullable |
| created_at | datetime | DEFAULT CURRENT_TIMESTAMP |

#### Table: `about_content`
Single-row CMS table for "About Us" page.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK |
| image | varchar(255) | About page image |
| title | varchar(255) | Page title |
| description | text | Short description |
| more_description | text | Detailed description |
| client | varchar(50) | Client count for stats |
| cases_won | varchar(50) | Cases won for stats |
| achievements | varchar(50) | Achievements count |
| our_team | varchar(50) | Team size for stats |
| status | enum | Active/Pending |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `add_property`
Property showcase grid (image, location, title).
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| image | varchar(255) | Property image filename |
| location | varchar(255) | Property location |
| title | varchar(100) | Property title |
| status | varchar(12) | Active/Pending |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `properties`
Detailed property listings for rent/sale.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| title | varchar(255) | Required |
| description | text | Required |
| property_status | enum | For Rent / For Sale / Not Available |
| property_type | varchar(100) | e.g., Commercial Building |
| price | varchar(50) | Price range or value |
| property_size | varchar(50) | e.g., "200sqm" |
| bedroom | int(11) | Nullable |
| bathroom | int(11) | Nullable |
| street | varchar(255) | Required |
| status | enum | Active / Inactive / Pending |
| sector | varchar(255) | Administrative sector |
| district | varchar(255) | Administrative district |
| country | varchar(255) | Required |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |
| floor | varchar(50) | Available floors |
| months | int(11) | Rental duration in months |

#### Table: `property_images`
Multiple images per property with featured flag.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| property_id | int(11) | FK → properties.id |
| image_name | varchar(255) | Display filename |
| is_featured | tinyint(1) | 0/1 flag for primary image |
| image_path | varchar(255) | Full path to image |

#### Table: `blog`
Blog posts with rich content.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| image | varchar(250) | Featured image |
| title | varchar(200) | Required |
| description_blog | text | Short description/excerpt |
| blog_description_details | text | Full article content |
| date | timestamp | DEFAULT CURRENT_TIMESTAMP |
| category_blog | varchar(255) | Default: Uncategorized |
| status | enum | active/pending |

#### Table: `blog_attachments`
File attachments for blog posts.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| blog_id | int(11) | FK → blog.id |
| file_name | varchar(255) | Original filename |
| file_path | varchar(255) | Stored filename |
| file_type | varchar(50) | MIME type |
| file_size | int(11) | Size in bytes |
| upload_date | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `home_backgrounds`
Carousel background images for homepage.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| image_path | varchar(255) | Image filename |
| status | enum | active/pending |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `videos`
Embedded video links.
| Column | Type | Constraints |
|--------|------|-------------|
| video_link | varchar(255) | PK (used as identifier) |
| status | enum | active/pending |

#### Table: `password_reset`
Password reset token storage.
| Column | Type | Constraints |
|--------|------|-------------|
| email | varchar(255) | PK |
| token | varchar(255) | Hashed reset token |
| expiry | datetime | Token expiration |
| created_at | timestamp | DEFAULT CURRENT_TIMESTAMP |

#### Table: `property` (Legacy)
Older property table with minimal fields.
| Column | Type | Constraints |
|--------|------|-------------|
| id | int(11) | PK, AUTO_INCREMENT |
| img | varchar(50) | Image filename |
| location | varchar(50) | Location string |

**Note**: This appears to be a legacy/duplicate table partially superseded by `add_property` and `properties`.

---

## 6. Application Flow & Data Processing

### 6.1 Public Website Flow

#### Homepage (`index.php`)
```
1. Language initialization (Lang/lang.php)
2. Header include (navigation, topbar, social links)
3. Hero Carousel (3 slides from home_backgrounds table)
   - Dynamic content via <?= __('...') ?> translation function
4. About Preview Section (from about_content table)
5. Mission/Vision Cards (static HTML with translations)
6. Services Grid (Legal Services + Property Management cards)
7. Latest Blog Posts (SELECT * FROM blog LIMIT 3)
8. Call-to-Action Section
9. Footer include
```

#### Property Listing (`property.php`)
```
1. Connect to database
2. Parse GET parameters (status filter, page number)
3. SQL: SELECT * FROM properties WHERE property_status = :status LIMIT 6 OFFSET :offset
4. Pagination calculation
5. Render property cards with:
   - Featured image (FROM property_images WHERE is_featured = 1)
   - Title, price, location, property type
6. "Book Now" button → redirects to property_detail.php
```

#### Property Detail (`property_detail.php`)
```
1. GET parameter: property_id
2. Fetch property details from `properties` table
3. Fetch all images from `property_images` table
4. Render image gallery
5. Display full property details (bedrooms, bathrooms, floor, etc.)
6. Booking form (POST)
   → bookingEmail.php sends confirmation to customer + owner
```

#### Blog System (`blog.php`, `blog_details.php`)
```
1. blog.php: Fetch all active blog posts
2. blog_details.php: Fetch single post by id
   - Fetch attachments from blog_attachments
   - Render download links with file icons
3. Status filter: only 'active' posts shown publicly
```

#### Contact Form (`contact.php`)
```
1. Display contact form with fields: name, email, phone, subject, message
2. On submit → contactEmail.php
3. PHPMailer sends:
   - HTML confirmation to customer
   - Notification to fairlawfirmltd@gmail.com + info@fairlawfirmltd.com
4. Form uses basic validation (required fields)
```

### 6.2 Admin Dashboard Flow

#### Authentication (`data/index.php`)
```php
1. Start session
2. Check POST request
3. Validate email in `users` table (checks if account exists)
4. JOIN `login` and `users` tables on email
5. Verify password with password_verify()
6. Check user status (must be 'Active')
7. Set session variables: user_type, email, first_name, last_name, profile_image
8. Redirect to dashboard.php
```

#### Dashboard (`data/dashboard.php`)
```
1. Require authentication (session check in header)
2. GetCount() helper function executes COUNT queries
3. Display statistics:
   - Active employers (users WHERE status = 'Active')
   - Total properties (add_property)
   - Rental properties (properties WHERE property_status = 'For Rent')
```

#### User Management (`data/manage_users.php`)
```
1. Admin-only access check
2. DELETE operation (transaction-based):
   - SELECT email FROM users WHERE id = :id
   - DELETE FROM users WHERE id = :id
   - DELETE FROM login WHERE email = :email
   - Commit transaction
3. List all users with JOIN to roles table
4. Modal for user details
5. Edit/Delete actions
```

#### Property Management
```
add_property.php:
  1. Validate image upload (JPG, JPEG, PNG, GIF)
  2. Move file to propertyMgt/proImg/
  3. Filename: time()_originalname.ext
  4. INSERT INTO add_property (image, location, title)

add_rental_property.php:
  1. More detailed form (bedrooms, bathrooms, floor, months, price, etc.)
  2. INSERT INTO properties table

property_images.php:
  1. Bulk upload multiple images
  2. Set is_featured flag (only one featured per property)
  3. DELETE old images, INSERT new ones
```

#### Blog Management (`data/add_blog.php`)
```
1. Upload featured image → blogImg/
2. Handle multiple file attachments → blogFiles/
3. Allowed types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR
4. INSERT INTO blog
5. For each attachment: INSERT INTO blog_attachments
```

---

## 7. Frontend Asset Organization

### 7.1 Public Assets (`assets/`)
```
assets/
├── css/
│   ├── firdip.css              # Main template styles (~50KB)
│   ├── firdip-rtl.css          # RTL language support
│   ├── firdip-dark.css         # Dark theme variant
│   └── firdip-custom-rtl.css   # Custom RTL overrides
├── images/
│   ├── favicons/               # Favicon variants
│   ├── backgrounds/            # Carousel backgrounds
│   └── ...                     # Theme images, logos
├── js/
│   └── firdip.js               # Main template JS
└── vendors/
    ├── bootstrap/              # Bootstrap 5 JS/CSS
    ├── jquery/                 # jQuery 3.7.0
    ├── owl-carousel/           # Carousel plugin
    ├── wow/                    # Animation library
    ├── magnific-popup/         # Lightbox
    ├── jarallax/               # Parallax scrolling
    ├── nouislider/             # Range sliders
    ├── jquery-ui/              # jQuery UI components
    ├── slick-carousel/         # Slick carousel
    └── fontawesome/            # Icons (CDN also used)
```

### 7.2 Admin Dashboard Assets (`data/`)
```
data/
├── css/                        # Dashboard-specific styles
├── js/
│   ├── jquery-3.3.1.min.js    # Older jQuery (dashboard)
│   ├── jquery.min.js           # Another jQuery copy
│   ├── bootstrap.min.js        # Bootstrap JS
│   ├── Chart.min.js            # Chart.js for statistics
│   ├── Chart.bundle.min.js     # Chart.js bundle
│   ├── custom.js               # Dashboard custom JS
│   ├── chart_custom_style*.js  # Chart styling
│   ├── perfect-scrollbar/      # Custom scrollbar
│   ├── owl.carousel.js         # Carousel
│   ├── semantic.min.js         # Semantic UI
│   └── ...
├── images/
│   └── layout_img/             # Dashboard layout images
├── fonts/
│   ├── Flaticon/               # Flaticon font icons
│   └── FontAwesome/            # Font Awesome fonts
├── include/
│   ├── header.php              # Dashboard header
│   └── footer.php              # Dashboard footer
└── PHPMailer/                  # Bundled PHPMailer copy
```

### 7.3 Upload Directories (`data/propertyMgt/`)
```
data/propertyMgt/
├── config.php                  # Database connection (PDO)
├── aboutImg/                   # About page images
├── blogImg/                    # Blog featured images
├── blogFiles/                  # Blog attachments (PDF, DOC, etc.)
├── proImg/                     # Property showcase images
├── rentalImg/                  # Rental property images (5MB limit)
├── propertyImg/                # Additional property images (5MB limit)
├── logoImg/                    # Logo uploads
├── userImg/                    # User profile images (5MB limit)
├── videoImg/                   # Video thumbnails
└── uploads/                    # General uploads
```

---

## 8. Multilingual System

### 8.1 Architecture
The application uses a simple session-based i18n system:

```php
// Lang/lang.php
function get_language_file() {
    $_SESSION['lang'] = $_SESSION['lang'] ?? 'en';
    $_SESSION['lang'] = $_GET['lang'] ?? $_SESSION['lang'];
    return $_SESSION['lang'] . ".php";
}

function __($str) {
    global $lang;
    return $lang[$str] ?? $str;  // Fallback to English key
}
```

### 8.2 Language Files
- `Lang/en.php` - **Empty array** (not implemented; fallback to English keys)
- `Lang/fr.php` - **131 lines** of French translations

### 8.3 Usage in Templates
```php
<?= __('Home') ?>
<?= __('Property Management & Legal Services') ?>
<?= __('Contact Us') ?>
```

### 8.4 Current State
- URL parameter: `?lang=fr` or `?lang=en`
- Session-based persistence
- French translations exist; English file is empty (redundant)
- Not all strings are translated; many are hardcoded in English

---

## 9. Security Analysis

### 9.1 Implemented Security Measures
| Measure | Implementation | Location |
|---------|---------------|----------|
| **Password Hashing** | `password_hash()` (bcrypt, cost 10) | `data/index.php` |
| **SQL Injection Prevention** | PDO prepared statements with bound parameters | All database queries |
| **Input Sanitization** | `filter_var()` for email, `htmlspecialchars()` for output | Various forms |
| **Session Authentication** | Session-based login with role check | Admin pages |
| **File Upload Validation** | Extension whitelist, `getimagesize()` for images | Upload handlers |
| **Transaction Support** | PDO transactions for multi-step operations | User deletion, editing |
| **Status-based Access** | User status check (Active/Pending) | Login process |

### 9.2 Security Vulnerabilities & Concerns

#### Critical
1. **Hardcoded SMTP Credentials** (`contactEmail.php`, `bookingEmail.php`)
   - Username and password embedded in source code
   - Visible in version control if committed
   - **Recommendation**: Move to environment variables or `.env` file

2. **No CSRF Protection**
   - All forms lack CSRF tokens
   - Vulnerable to cross-site request forgery
   - **Recommendation**: Implement CSRF token generation and validation

3. **Duplicate PHPMailer Copies**
   - `composer/vendor/phpmailer/` and `data/PHPMailer/`
   - Increases attack surface and maintenance burden
   - **Recommendation**: Remove bundled copy, use Composer autoload exclusively

#### High
4. **Inconsistent Input Sanitization**
   - Some forms use `htmlspecialchars()`, others don't
   - Some POST data is unsanitized before database insertion
   - **Recommendation**: Standardize input sanitization across all endpoints

5. **Session Fixation Risk**
   - No `session_regenerate_id()` after login
   - **Recommendation**: Regenerate session ID on authentication

6. **No Rate Limiting**
   - Login, contact, and booking forms have no rate limiting
   - Vulnerable to brute force and spam
   - **Recommendation**: Implement rate limiting or CAPTCHA

#### Medium
7. **Error Message Disclosure**
   - Database errors displayed to users (`die("Connection failed: ...")`)
   - **Recommendation**: Log errors server-side, show generic messages to users

8. **Missing HTTPS Enforcement**
   - No HSTS or HTTPS redirect
   - SMTP uses SSL (port 465) which is good
   - **Recommendation**: Enforce HTTPS site-wide

9. **Weak Password Policy**
   - No minimum length or complexity requirements visible
   - **Recommendation**: Implement password strength requirements

10. **File Upload Directory Permissions**
    - Upload directories created with `0777` permissions
    - **Recommendation**: Use `0755` with proper ownership

### 9.3 Database Credentials Exposure
- `data/propertyMgt/config.php` contains plaintext database credentials
- Path is predictable (`data/propertyMgt/config.php`)
- **Recommendation**: Move outside web root or use environment variables

---

## 10. Code Quality Observations

### 10.1 Architectural Issues
1. **Procedural Spaghetti Code**
   - No MVC separation
   - Business logic mixed with presentation
   - No routing system; scattered PHP files

2. **Code Duplication**
   - Database connection included via `require_once` in many files
   - Repeated file upload logic across handlers
   - Similar CRUD patterns repeated

3. **Legacy Code Artifacts**
   - `property` table (legacy) vs `properties` and `add_property`
   - Multiple jQuery versions (3.3.1, 3.6.0, 3.7.0)
   - Mixed Bootstrap versions (CDN 5.0.2 + local vendor)

4. **Template Repurposing Evidence**
   - Meta description still references "fire department"
   - Some CSS classes suggest original template purpose
   - SEO metadata not fully customized

### 10.2 Database Issues
1. **Schema Inconsistencies**
   - `email` column in `login` is PK but `users` also has `email`
   - No foreign key constraints enforced at DB level
   - `property` table appears unused/legacy

2. **Data Integrity**
   - No foreign key constraints (InnoDB supports them but not used)
   - Cascading deletes handled in application code, not DB
   - Some nullable fields that should probably be required

3. **Missing Indexes**
   - No visible indexes on foreign keys
   - Could impact query performance as data grows

### 10.3 Frontend Issues
1. **Mixed CDN and Local Assets**
   - Some libraries loaded from CDN, others from local vendor
   - Inconsistent dependency management

2. **Hardcoded Values**
   - WhatsApp number hardcoded in footer
   - Email addresses hardcoded in multiple places
   - Office hours hardcoded in header

3. **Missing Error Handling**
   - AJAX calls lack error callbacks
   - File upload failures show generic messages

---

## 11. Key Features Breakdown

### 11.1 Public-Facing Features

#### Homepage
- **Hero Carousel**: 3-slide Owl Carousel with background images from `home_backgrounds`
- **About Preview**: Dynamic content from `about_content` table
- **Mission/Vision**: Static cards with translation support
- **Services Grid**: 14 service cards (7 legal + 7 property)
- **Blog Preview**: Latest 3 active posts
- **Call-to-Action**: Contact form link
- **Floating WhatsApp Button**: Direct link to `https://wa.me/message/CDM47NATCOISH1`

#### Legal Services Page (`legal_services.php`)
- 7 detailed service cards:
  1. Legal Advisory
  2. Court Representation
  3. Mediation Services
  4. Business Transactions
  5. Contract Drafting
  6. Internal Regulations
  7. Legal Consultation

#### Property Management Page (`property_service.php`)
- 7 service cards:
  1. Rental Management
  2. Sales Management
  3. Rent Recovery
  4. Marketing
  5. Compliance
  6. Tax Payments
  7. Maintenance

#### Property System
- **Listing** (`property.php`): Paginated (6 per page), filterable by status
- **Detail** (`property_detail.php`): Full description, image gallery, booking form
- **Booking**: Email notification to owner + customer with property details
- **Manage Properties** (`manage_property.php`): Public showcase grid

#### Blog System
- **Listing** (`blog.php`): All active posts
- **Detail** (`blog_details.php`): Full article + downloadable attachments
- **Attachment Types**: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR

#### Contact System
- Contact form with Google Maps embed
- Email notifications to multiple recipients
- Office hours and contact info display

### 11.2 Admin Dashboard Features

#### Authentication & Users
- Secure login with bcrypt password hashing
- Role-based access (admin/employer)
- Password reset via token with expiry
- User registration with profile image
- User listing, editing, and deletion

#### Content Management
- **About Content**: Edit company description and statistics
- **Properties**: Add/edit properties with multiple images
- **Rentals**: Detailed property management with bedrooms, bathrooms, etc.
- **Blog**: Create posts with featured images + attachments
- **Videos**: Manage embedded video links
- **Backgrounds**: Manage homepage carousel images

#### Dashboard Statistics
- Active user count
- Total properties count
- Rental properties count
- Visual stat cards with color coding

---

## 12. Email System Deep Dive

### 12.1 Contact Form Email (`contactEmail.php`)
```
Trigger: POST from contact.php
SMTP: mail.fairlawfirmltd.com:465 (SSL)
From: info@fairlawfirmltd.com

Emails Sent:
1. To Customer (HTML):
   - Subject: "Thank You for Contacting Fair Law Firm"
   - Content: Company branding, contact details, response timeline

2. To Owner (HTML):
   - Recipients: fairlawfirmltd@gmail.com, info@fairlawfirmltd.com
   - Content: Customer name, email, phone, subject, message
```

### 12.2 Booking Form Email (`bookingEmail.php`)
```
Trigger: POST from property_detail.php
SMTP: Same as above

Emails Sent:
1. To Customer (HTML):
   - Subject: "Property Booking Confirmation"
   - Content: Property ID, duration, contact details

2. To Owner (HTML):
   - Content: Customer name, property ID, duration, message/comments
```

### 12.3 SMTP Configuration
```php
$mail->Host = 'mail.fairlawfirmltd.com';
$mail->SMTPAuth = true;
$mail->Username = 'info@fairlawfirmltd.com';
$mail->Password = '2RxJfCkKA(jx';  // HARDCODED - SECURITY RISK
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
```

---

## 13. Configuration Reference

### 13.1 Database Connection (`data/propertyMgt/config.php`)
```php
$host = "localhost";
$dbname = "helloshi_fairdb";  // Note: PROJECT_EXPLANATION.md says "fairdb", actual is "fairdb"
$charset = "utf8";
$username = "helloshi_fairUser";
$password = "Allin@12345";
```

### 13.2 Composer Dependencies (`composer/composer.json`)
```json
{
    "require": {
        "phpmailer/phpmailer": "^6.1"
    }
}
```

### 13.3 Language Configuration (`Lang/lang.php`)
- Session-based language storage
- Default: English (`en`)
- Supported: `en`, `fr`
- URL parameter: `?lang=fr`

### 13.4 File Upload Limits
| Directory | Max Size | Allowed Types |
|-----------|----------|---------------|
| rentalImg/ | 5MB | JPG, JPEG, PNG, GIF |
| propertyImg/ | 5MB | JPG, JPEG, PNG, GIF |
| userImg/ | 5MB | JPG, JPEG, PNG, GIF |
| blogFiles/ | No limit visible | PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR |

---

## 14. Page Routes & Navigation

### 14.1 Public Pages
| URL | File | Purpose |
|-----|------|---------|
| `/` | `index.php` | Homepage |
| `/about_us.php` | `about_us.php` | About Us |
| `/contact.php` | `contact.php` | Contact form + map |
| `/legal_services.php` | `legal_services.php` | Legal services detail |
| `/property_service.php` | `property_service.php` | Property services detail |
| `/property.php` | `property.php` | Property listings |
| `/property_detail.php?id=X` | `property_detail.php` | Single property |
| `/manage_property.php` | `manage_property.php` | Property showcase grid |
| `/blog.php` | `blog.php` | Blog listing |
| `/blog_details.php?id=X` | `blog_details.php` | Single blog post |
| `/404.php` | `404.php` | Error page |

### 14.2 Admin Pages
| URL | File | Purpose |
|-----|------|---------|
| `/data/` | `index.php` | Login |
| `/data/dashboard.php` | `dashboard.php` | Statistics overview |
| `/data/register.php` | `register.php` | User registration |
| `/data/forgot_password.php` | `forgot_password.php` | Password recovery |
| `/data/profile.php` | `profile.php` | Current user profile |
| `/data/manage_users.php` | `manage_users.php` | User management |
| `/data/manage_property.php` | `manage_property.php` | Add property form |
| `/data/display_properties.php` | `display_properties.php` | List all properties |
| `/data/add_rental_property.php` | `add_rental_property.php` | Add rental property |
| `/data/display_rental.php` | `display_rental.php` | List rental properties |
| `/data/property_images.php` | `property_images.php` | Manage property images |
| `/data/add_blog.php` | `add_blog.php` | Create blog post |
| `/data/display_blog.php` | `display_blog.php` | List all blog posts |
| `/data/add_about.php` | `add_about.php` | Edit about content |
| `/data/add_video.php` | `add_video.php` | Add video link |
| `/data/home_background.php` | `home_background.php` | Manage carousel images |
| `/data/logout.php` | `logout.php` | Session destroy |

---

## 15. Setup & Deployment Instructions

### 15.1 Prerequisites
- XAMPP (Apache + MySQL + PHP 8.4) or equivalent
- Composer (dependency manager)
- Git (for version control)

### 15.2 Installation Steps
```bash
1. Install XAMPP with PHP 8.4
2. Clone or place project in C:\xampp\htdocs\first-legal-service\
3. Create database in phpMyAdmin: helloshi_fairdb
4. Import database: mysql -u root -p helloshi_fairdb < fairdb.sql
5. Configure database credentials in data/propertyMgt/config.php
6. Install Composer dependencies: cd composer && composer install
7. Update SMTP credentials in contactEmail.php and bookingEmail.php
8. Set write permissions on upload directories (data/propertyMgt/*Img/)
9. Start Apache and MySQL via XAMPP Control Panel
10. Visit: http://localhost/first-legal-service/
```

### 15.3 Admin Credentials
Default accounts from database seed:
- **Admin**: remymugisha64@gmail.com / ngirumpetse@yahoo.com
- **User**: user@gmail.com (and others)

---

## 16. Known Issues & Technical Debt

### 16.1 Critical Issues
- [ ] **SMTP credentials hardcoded** in source files
- [ ] **No CSRF protection** on any forms
- [ ] **Duplicate PHPMailer** copies in two locations
- [ ] **Database credentials** in predictable web-accessible path
- [ ] **Session fixation** vulnerability (no regeneration after login)

### 16.2 High Priority
- [ ] **English translation file** (`Lang/en.php`) is empty
- [ ] **No rate limiting** on forms (login, contact, booking)
- [ ] **Error disclosure** to end users (database errors shown)
- [ ] **Missing foreign key constraints** in database schema
- [ ] **Inconsistent jQuery versions** (3.3.1, 3.6.0, 3.7.0)
- [ ] **Hardcoded WhatsApp number** and email addresses

### 16.3 Medium Priority
- [ ] **No input validation** library or consistent validation layer
- [ ] **Missing unit tests** or integration tests
- [ ] **No logging system** for debugging or audit trail
- [ ] **Legacy `property` table** still in schema
- [ ] **SEO metadata** references original fire department template
- [ ] **No caching** for database queries (stats, blog posts)

### 16.4 Low Priority / Technical Debt
- [ ] **Mixed CDN/local assets** - inconsistent approach
- [ ] **No API layer** - everything is server-rendered
- [ ] **No automated deployment** process
- [ ] **Missing .htaccess** for URL rewriting/security headers
- [ ] **No backup strategy** documented
- [ ] **File permissions** use 0777 in some upload handlers

---

## 17. Recommendations for Improvement

### 17.1 Security Hardening
1. **Environment Configuration**: Implement `.env` file with `vlucas/phpdotenv`
2. **CSRF Protection**: Add tokens to all forms (e.g., `paragonie/anti-csrf`)
3. **HTTPS Enforcement**: Redirect all HTTP to HTTPS
4. **Session Security**: Regenerate ID on login, set secure/httponly flags
5. **Rate Limiting**: Implement on login, contact, and booking endpoints
6. **Content Security Policy**: Add CSP headers to prevent XSS
7. **Database Credentials**: Move outside web root or use environment variables

### 17.2 Code Architecture
1. **Adopt MVC Pattern**: Consider lightweight framework (Slim, Laminas) or custom router
2. **Service Layer**: Extract business logic from controllers
3. **Repository Pattern**: Abstract database queries
4. **Dependency Injection**: Reduce tight coupling
5. **Consolidate Uploads**: Single upload handler class

### 17.3 Database Improvements
1. **Add Foreign Keys**: Enforce referential integrity
2. **Add Indexes**: On foreign keys and frequently queried columns
3. **Normalize Schema**: Review `add_property` vs `properties` vs `property`
4. **Add Migrations**: Version-controlled schema changes
5. **Archive Strategy**: Plan for old blog posts and properties

### 17.4 Feature Enhancements
1. **Search Functionality**: Full-text search on properties and blog
2. **Image Optimization**: WebP conversion, lazy loading, CDN
3. **Advanced Filtering**: Property search by price, location, type
4. **User Notifications**: In-dashboard notification system
5. **Audit Logging**: Track admin actions
6. **API Layer**: RESTful API for mobile app integration
7. **Email Templates**: Separate template files for better maintenance

### 17.5 DevOps & Deployment
1. **Git Hooks**: Pre-commit validation, linting
2. **CI/CD Pipeline**: Automated testing and deployment
3. **Backup Strategy**: Automated database backups
4. **Monitoring**: Application performance monitoring
5. **Staging Environment**: Separate from production

---

## 18. Conclusion

Fair Law Firm LTD is a functional, dual-purpose web application that successfully combines public marketing with internal content management. Built on proven technologies (PHP, MySQL, Bootstrap), it serves the specific needs of a Rwandan legal and property management firm.

**Strengths**:
- Clean separation between public and admin areas
- Proper password hashing and PDO usage
- Comprehensive content management capabilities
- Working email integration with PHPMailer
- Multilingual support (French/English)

**Primary Concerns**:
- Security vulnerabilities (hardcoded credentials, missing CSRF)
- Technical debt (duplicate code, legacy artifacts)
- Architectural limitations (no framework, procedural style)
- Missing production hardening (HTTPS, rate limiting, logging)

The application is suitable for its current local deployment on XAMPP but requires significant security and architectural improvements before production deployment on a public server.

---

*Document generated: 2026-08-18*
*Project Location: C:\xampp\htdocs\first-legal-service\*
*Database: helloshi_fairdb*
