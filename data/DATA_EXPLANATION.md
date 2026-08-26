# Fair Law Firm LTD — Complete Admin Data Directory Documentation

## 1. Overview

The `data/` directory contains the entire admin content management system (CMS) for Fair Law Firm LTD. It is a procedural PHP application running on XAMPP (Apache + MySQL + PHP 8.4) with PDO for database access, Bootstrap for UI, and PHPMailer for email.

**Authentication**: All admin pages (except `index.php`, `forgot_password.php`, `reset_password.php`, `display_reset_link.php`, and `404_error.php`) are protected by `include/header.php`, which enforces session-based authentication. Only users with a valid `$_SESSION["email"]` can access the dashboard and management pages.

**Access Control**: Role-based access is enforced via `$_SESSION['user_type']`. Pages like `manage_users.php` and `edit_user.php` restrict access to `admin` only.

---

## 2. Shared Infrastructure

### 2.1 `include/header.php`
- Starts session and redirects unauthenticated users to `index.php`
- Sets dynamic page titles based on current file
- Loads sidebar navigation with active-page highlighting
- Loads notification counts (pending blogs + pending users) from database
- Renders topbar with sidebar toggle, page title, property search form, notification bell, and user profile dropdown
- Admin-only nav items (Users) are conditionally rendered

### 2.2 `include/footer.php`
- Renders footer copyright
- Loads scripts: jQuery, Popper, Bootstrap, Bootstrap Select, Owl Carousel, Animate.js, Perfect Scrollbar, Custom JS, Chart.js stack
- Includes mobile menu handler (close sidebar on outside click / Escape key)

### 2.3 `propertyMgt/config.php`
- PDO connection to MySQL database `helloshi_faird`
- Host: `localhost`, User: `helloshi_fairUser`, Password: `Allin@12345`
- Error mode: `PDO::ERRMODE_EXCEPTION`
- Note: Database name in this file (`helloshi_faird`) differs from `PROJECT_EXPLANATION.md` (`helloshi_fairdb`) and the SQL file (`fairdb.sql`)

### 2.4 Upload Directories (`propertyMgt/`)
```
aboutImg/      - About page images
blogImg/       - Blog featured images
blogFiles/     - Blog attachments (PDF, DOC, XLS, PPT, TXT, ZIP, RAR)
proImg/        - Property showcase images
rentalImg/     - Rental property images (5MB limit)
propertyImg/   - Additional property images (5MB limit)
logoImg/       - Logo uploads
userImg/       - User profile images (5MB limit)
videoImg/      - Video thumbnails
uploads/       - General uploads
```

---

## 3. Authentication Pages

### 3.1 `index.php` — Login Page
**Access**: Public (no auth required)

**Flow**:
1. Displays split-screen login form (brand pane left, form pane right)
2. On POST: sanitizes email, checks `users` table for existence
3. If user exists, JOINs `login` and `users` tables on email
4. Verifies password with `password_verify()`
5. Checks user status (must be `Active`)
6. On success: sets session variables (`user_type`, `email`, `first_name`, `last_name`, `profile_image`) and redirects to `dashboard.php`
7. On failure: displays error message ("Invalid email or password", "Account not active", "Account deleted")

**Features**:
- Password visibility toggle
- "Remember me" checkbox
- "Forgot password?" link
- Loading state on submit button
- Success/error alert messages
- Branding: Fair Law Firm LTD logo, tagline, feature list

**Security Notes**:
- Uses `filter_var()` for email sanitization
- Password verified with bcrypt (`password_verify`)
- No CSRF token
- No rate limiting
- Database errors exposed to user (`die("Connection failed: ...")`)

### 3.2 `register.php` — User Registration Form
**Access**: Authenticated (session required via `include/header.php`)

**Purpose**: Form for admins to create new user accounts.

**Form Fields**:
- First Name, Last Name
- Email
- Phone
- Gender (radio: Male / Female / Other)
- Password, Confirm Password
- Role (dropdown from `roles` table)
- Profile Image (file upload, accepts `image/*`)

**Submission**: POSTs to `register_process.php` with `enctype="multipart/form-data"`

### 3.3 `register_process.php` — Registration Handler
**Access**: Public (no session required)

**Flow**:
1. Validates password match
2. Checks email uniqueness in `login` table
3. Uploads profile image to `propertyMgt/userImg/` (max 5MB, JPG/JPEG/PNG/GIF only, `getimagesize()` validation)
4. Inserts into `login` table (email, bcrypt password, usertype)
5. Inserts into `users` table (profile data)
6. Uses PDO transaction (`beginTransaction` / `commit` / `rollback`)
7. On success: redirects to `register.php` or `manage_users.php` (if referer)
8. On failure: rolls back transaction, redirects with error

**Business Logic**:
- `usertype` = `admin` if `role_id == 1`, else `user`
- Default status = `Active`

### 3.4 `forgot_password.php` — Password Reset Request
**Access**: Public

**Flow**:
1. Displays email input form
2. On POST: checks if email exists in `login` table
3. Generates 32-byte random token (`bin2hex(random_bytes(32))`)
4. Stores token + expiry (1 hour) in `password_reset` table (auto-creates table if not exists)
5. Sends reset email via PHPMailer (SMTP: smtp.hostinger.com:465)
6. On email success: redirects to `index.php` with success message
7. On email failure: redirects directly to `reset_password.php` with token (development fallback)

**Email Content**:
- Subject: "Fair Law Firm - Password Reset"
- Contains reset link with token
- States 1-hour expiry

### 3.5 `reset_password.php` — Password Reset Form
**Access**: Public (requires valid token)

**Flow**:
1. Reads `token` from GET parameter
2. Validates token exists in `password_reset` table and is not expired
3. Displays new password + confirm password form (min 8 characters)
4. On POST: verifies passwords match, hashes with `password_hash()`, updates `login` table, deletes token from `password_reset`
5. On success: redirects to `index.php` with success message

### 3.6 `display_reset_link.php` — Development Reset Link Display
**Access**: Development only (localhost / 127.0.0.1 only; requires `$_SESSION['reset_link']`)

**Purpose**: Displays the reset link directly in the browser for local development when email sending fails.

---

## 4. Dashboard

### 4.1 `dashboard.php` — Main Dashboard
**Access**: Authenticated (session required)

**Statistics Engine**:
- `getCount($conn, $table, $condition)` helper — safe COUNT queries, returns 0 on error
- **4 Stat Cards**:
  - Total Employers: `users` WHERE `status = 'Active'`
  - Total Properties: `add_property` (all records)
  - Rental Properties: `properties` WHERE `property_status = 'For Rent'`
  - Blog Posts: `blog` (all records)

**Charts (Chart.js)**:
1. **Property Status Distribution** (Doughnut)
   - Data: `SELECT property_status, COUNT(*) FROM properties GROUP BY property_status`
   - Palette: navy, gold, blue soft, gold light, navy dark, muted blue, bronze
   - Center plugin shows total listings count
   - Empty state: "No property listings available yet."

2. **User Growth — Last 12 Months** (Line)
   - Data: `SELECT DATE_FORMAT(created_at, '%Y-%m'), COUNT(*) FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY ym`
   - Zero-fill for months with no registrations
   - Gold gradient fill, navy border, gold point markers
   - Tension: 0.4 (smooth curves)

**Recent Activity Feed**:
- Last 5 registered users (`ORDER BY created_at DESC LIMIT 5`)
- Shows avatar (or default), full name, email, status badge, relative timestamp
- `timeAgo()` helper: "Just now" / "X min/hr/day(s) ago" / "M j, Y"

**Quick Actions**:
- New Blog → `add_blog.php`
- Add Property → `add_property.php`
- Add Rental → `add_rental_property.php`
- Add User → `register.php`

**Welcome Message**: Constructed from `$_SESSION['first_name']` and `$_SESSION['last_name']`

---

## 5. User Management

### 5.1 `manage_users.php` — User List
**Access**: Admin only (`$_SESSION['user_type'] == 'admin'`)

**Features**:
- Lists all users with JOIN to `roles` table
- Table columns: ID, Profile Image, Name, Email, Role, Status, Actions
- Status badges: Active (green), Pending (yellow), Inactive (red)
- Actions per user: View (modal), Edit, Delete
- Delete uses transaction: SELECT email → DELETE FROM users → DELETE FROM login → commit/rollback
- "Add New User" button → `register.php`

**Modal**: View User Details modal shows full profile info in a Bootstrap modal dialog

### 5.2 `edit_user.php` — Edit User
**Access**: Admin only

**Flow**:
1. Fetches user by `id` (JOIN with `roles`)
2. On POST: updates `users` table fields (name, email, phone, gender, role_id, status)
3. If email changed: updates `login` table email and usertype
4. If new profile image uploaded: validates (5MB, JPG/PNG/GIF), deletes old image, saves new
5. Uses PDO transaction with `SET FOREIGN_KEY_CHECKS=0/1`

### 5.3 `profile.php` — Current User Profile
**Access**: Authenticated

**Purpose**: Read-only view of the currently logged-in user's profile.

**Displays**:
- Profile image (or default avatar)
- Full name, role, email, phone, gender
- Fetches from `users` JOIN `roles` on `$_SESSION['email']`

---

## 6. Property Management (Showcase)

### 6.1 `manage_property.php` — Add Property Form
**Access**: Authenticated

**Purpose**: Form to add a simple property listing (image, location, title).

**Form Fields**:
- Image (file upload, required, JPG/JPEG/PNG/GIF)
- Location (text)
- Title (text)

**Submission**: POSTs to `add_property.php` with `enctype="multipart/form-data"`

### 6.2 `add_property.php` — Add Property Handler
**Access**: Authenticated

**Flow**:
1. Validates image type (JPG, JPEG, PNG, GIF)
2. Moves uploaded file to `propertyMgt/proImg/` with timestamp prefix
3. Inserts into `add_property` table (image, location, title)
4. On success: redirects to `manage_property.php` with success message
5. On failure: redirects to `manage_property.php` with error

### 6.3 `display_properties.php` — Property Listings
**Access**: Authenticated

**Features**:
- Lists all active properties from `add_property` WHERE `status = 'Active'` ORDER BY `id DESC`
- Table columns: ID, Image, Location, Title, Actions
- Delete: removes image file from `proImg/`, deletes DB record
- "Add New Property" button → `manage_property.php`

### 6.4 `edit_property.php` — Edit Property
**Access**: Authenticated

**Flow**:
1. Fetches property by `id` from `add_property`
2. On POST: updates location and title
3. If new image uploaded: validates, moves to `proImg/` with timestamp prefix, deletes old image
4. On success: redirects to `display_properties.php`

### 6.5 `property_details.php` — Property Details View
**Access**: Authenticated

**Note**: This page actually reads from the `properties` table (rental properties), not `add_property`. It serves as a detailed view for rental properties.

**Displays**:
- Image carousel from `property_images` WHERE `property_id = :id` (ordered by `is_featured DESC`)
- Featured badge on carousel images
- Property title, status badges (Active/Inactive/Pending, For Rent/For Sale/Not Available)
- Property type, price (formatted with `formatDisplayPrice()` — adds "Rwf" suffix)
- Bedrooms, Bathrooms (hidden for Commercial Building), Floors (for Commercial Building)
- Property size, months (rental duration)
- Address: street, sector, district, country
- Description
- Thumbnail gallery with featured badges
- Actions: Edit → `edit_rental.php`, Manage Images → `property_images.php`

**Helper**: `formatDisplayPrice($price)` — converts price ranges to "X,Rwf - Y,Rwf" format, single values to "X,Rwf"

---

## 7. Property Management (Rental)

### 7.1 `add_rental_property.php` — Add Rental Property Form
**Access**: Authenticated

**Purpose**: Detailed form for adding rental/sale properties to the `properties` table.

**Form Fields**:
- Title, Description
- Property Status (For Rent / For Sale / Not Available)
- Months (dropdown 1-12, hidden for "For Sale")
- Property Type (Apartment / House / Commercial Building)
- Floors (checkboxes Ground Floor through 15th Floor, shown only for Commercial Building)
- Price (text input, accepts ranges like "100000 - 500000")
- Property Size (sq ft)
- Bedrooms, Bathrooms (hidden for Commercial Building)
- Street, Sector, District, Country

**JavaScript**:
- Toggles bedroom/bathroom fields and floor checkboxes based on property type
- Hides months field when "For Sale" is selected
- Price input validation (numbers and hyphens only)

**Submission**: POSTs to self, on success redirects to `property_images.php?property_id={new_id}`

### 7.2 `edit_rental.php` — Edit Rental Property
**Access**: Authenticated

**Flow**:
1. Fetches property by `id` from `properties`
2. On POST: updates all fields (title, description, status, type, price, size, bedrooms, bathrooms, street, sector, district, country, status, floors, months)
3. Price processing same as add form
4. On success: redirects to `display_rental.php`

### 7.3 `display_rental.php` — Rental Properties List
**Access**: Authenticated

**Features**:
- Lists all properties from `properties` table
- Table columns: Title, Type, Property Status, Price, Bed/Bath, Actions
- Delete: removes associated images from `property_images` and filesystem, then deletes property
- Actions: View Details → `property_details.php`, Edit → `edit_rental.php`, Delete, Manage Images → `property_images.php`

### 7.4 `property_images.php` — Bulk Image Upload
**Access**: Authenticated

**Features**:
- Property selection dropdown (all properties from `properties` table)
- Bulk upload multiple images (max 5MB each, JPG/JPEG/PNG/GIF)
- First uploaded image auto-set as featured (unless featured checkbox is set)
- Existing images gallery with:
  - Featured badge
  - Set as Featured button (for non-featured images)
  - Delete button
- Delete: removes DB record and file from `propertyMgt/rentalImg/`
- Set Featured: resets all featured flags for property, sets selected image as featured

### 7.5 `delete_property_image.php` — Delete Property Image
**Access**: Authenticated

**Flow**:
1. Requires `image_id` and `property_id` GET parameters
2. Fetches image path from `property_images`
3. Deletes DB record
4. Deletes file from `propertyMgt/propertyImg/`
5. Redirects to `property_details.php?id={property_id}`

---

## 8. Blog Management

### 8.1 `add_blog.php` — Add Blog Post
**Access**: Authenticated

**Form Fields**:
- Title
- Description (short excerpt)
- Details (full article content)
- Featured Image (JPG/JPEG/PNG/GIF, required)
- Attachments (multiple files: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR)
- Category (text input)
- Status (Active / Pending)

**Flow**:
1. Uploads featured image to `propertyMgt/blogImg/` (uniqid filename)
2. Inserts blog post into `blog` table
3. For each attachment: inserts into `blog_attachments` with file metadata (name, path, type, size)
4. On success: redirects to `display_blog.php`

### 8.2 `edit_blog.php` — Edit Blog Post
**Access**: Authenticated

**Flow**:
1. Fetches blog by `id` from `blog` table
2. Fetches existing attachments from `blog_attachments`
3. On POST:
   - Updates blog fields (title, description, details, status, category)
   - If new image uploaded: deletes old image, uploads new
   - If new attachments uploaded: adds to `blog_attachments`
4. On success: redirects to `display_blog.php`

**Attachment Management**:
- Shows current attachments with download and delete buttons
- Delete attachment → `delete_attachment.php`

### 8.3 `display_blog.php` — Blog List
**Access**: Authenticated

**Features**:
- Lists all blog posts from `blog` table
- Table columns: Image, Title, Category, Attachments (count badge), Status, Date, Actions
- Delete: removes blog image, all attachment files, DB records for blog and attachments
- Actions: View → `view_blog.php`, Edit → `edit_blog.php`, Delete

### 8.4 `view_blog.php` — View Single Blog
**Access**: Authenticated

**Displays**:
- Featured image
- Title, date, category
- Description and full details
- Attachments list with download buttons and file icons
- "Back to Blog List" button

### 8.5 `delete_attachment.php` — Delete Blog Attachment
**Access**: Authenticated

**Flow**:
1. Requires `id` (attachment ID) and `blog_id` GET parameters
2. Fetches file path from `blog_attachments`
3. Deletes DB record
4. Deletes file from `propertyMgt/blogFiles/`
5. Redirects to `edit_blog.php?id={blog_id}`

---

## 9. About Content Management

### 9.1 `add_about.php` — Add About Content
**Access**: Authenticated

**Form Fields**:
- Title
- Description (short)
- More Description (detailed)
- Client (text — for statistics)
- Cases Won (text — for statistics)
- Achievements (text — for statistics)
- Our Team (text — for statistics)
- Image (file upload, required)
- Status (Active / Pending)

**Flow**:
1. Uploads image to `propertyMgt/aboutImg/`
2. Inserts into `about_content` table
3. On success: redirects to `display_about.php`

### 9.2 `edit_about.php` — Edit About Content
**Access**: Authenticated

**Flow**:
1. Fetches content by `id` from `about_content`
2. On POST: updates all fields
3. If new image uploaded: deletes old image, uploads new
4. On success: redirects to `display_about.php`

### 9.3 `display_about.php` — About Content List
**Access**: Authenticated

**Features**:
- Lists active about content from `about_content` WHERE `status = 'Active'`
- Table columns: ID, Image, Title, Description, Cases Won, Actions
- Delete: removes image file and DB record
- Actions: View → `view_about.php`, Edit → `edit_about.php`, Delete

### 9.4 `view_about.php` — View About Content
**Access**: Authenticated

**Displays**:
- All fields in read-only form layout (two columns)
- Title, Description, More Description, Client, Cases Won, Achievements, Our Team
- Image preview

---

## 10. Video Management

### 10.1 `add_video.php` — Add/Edit Video Links
**Access**: Authenticated

**Purpose**: Combined add and edit page for embedded video links.

**Features**:
- Add form: video link + status (Active/Pending)
- Edit mode: pre-fills form when `?edit={id}` is present
- If setting video to Active: sets all other videos to Pending (only one active at a time)
- Table below form lists all videos with Edit/Delete actions
- Delete: removes from `videos` table

**Database**: `videos` table — `video_link` (PK), `status` (active/pending), `created_at`

### 10.2 `edit_video.php` — Edit Video Link
**Access**: Authenticated (no header include, starts session directly)

**Purpose**: Standalone edit form for video links.

**Flow**:
1. Fetches video by `id` from `videos`
2. On POST: updates video_link and status
3. If setting to Active: sets all others to Pending
4. On success: redirects to `dashboard.php`

### 10.3 `display_video.php` — Display Active Video
**Access**: No auth required (public-facing component)

**Purpose**: Displays the active video link as a play button (for embedding on public pages).

**Features**:
- Shows active video (status = 'active', most recent) as a play icon/link
- Also includes a simple add form and table (similar to `add_video.php`)

---

## 11. Background Management

### 11.1 `home_background.php` — Manage Carousel Backgrounds
**Access**: Authenticated (no header include)

**Purpose**: Manage homepage carousel background images.

**Features**:
- Add form: image_path (text) + status (Active/Pending)
- Table lists all backgrounds with Edit/Delete actions
- Delete: removes from `home_backgrounds` table

**Database**: `home_backgrounds` table — `id`, `image_path`, `status`, `created_at`

### 11.2 `edit_background.php` — Edit Background
**Access**: Authenticated (no header include)

**Flow**:
1. Fetches background by `id` from `home_backgrounds`
2. On POST: updates `image_path` and `status`
3. Simple text-based form (no file upload)

---

## 12. Utility Pages

### 12.1 `profile.php` — User Profile View
**Access**: Authenticated

**Purpose**: Displays the current user's profile information in a read-only layout.

**Displays**: Avatar, name, role, email, phone, gender, role name

### 12.2 `logout.php` — Session Destroy
**Access**: Authenticated

**Flow**:
1. Starts session
2. Calls `session_destroy()`
3. Redirects to `index.php`

### 12.3 `404_error.php` — Error Page
**Access**: Public

**Purpose**: Custom 404 error page.

**Notes**:
- Based on Pluto Admin Template
- Contains hardcoded "PAGE NOT FOUND" message
- "Go To Home Page" link points to `index-2.html` (broken — should point to `index.php`)
- Title still says "Pluto - Responsive Bootstrap Admin Panel Templates"

---

## 13. Email Handlers (Root Directory)

### 13.1 `contactEmail.php` — Contact Form Handler
**Location**: `C:\xampp\htdocs\first-legal-service\contactEmail.php` (root, not in `data/`)

**Trigger**: POST from public `contact.php` page

**SMTP Configuration**:
- Host: `mail.fairlawfirmltd.com`
- Port: 465 (SSL)
- Username: `info@fairlawfirmltd.com`
- Password: `2RxJfCkKA(jx` (hardcoded — security risk)

**Emails Sent**:
1. **To Customer** (HTML):
   - Subject: "Thank You for Contacting Fair Law Firm"
   - Content: Company branding, contact details, response timeline

2. **To Owner** (HTML):
   - Recipients: `fairlawfirmltd@gmail.com`, `info@fairlawfirmltd.com`
   - Content: Customer name, email, phone, subject, message

### 13.2 `bookingEmail.php` — Booking Form Handler
**Location**: `C:\xampp\htdocs\first-legal-service\bookingEmail.php` (root, not in `data/`)

**Trigger**: POST from public `property_detail.php` page

**SMTP Configuration**: Same as above

**Emails Sent**:
1. **To Customer** (HTML):
   - Subject: "Property Booking Confirmation"
   - Content: Property ID, duration, contact details

2. **To Owner** (HTML):
   - Content: Customer name, property ID, duration, message/comments

---

## 14. Database Schema Summary

### Tables Used by Admin Pages

| Table | Used By | Purpose |
|-------|---------|---------|
| `users` | Login, Dashboard, User Mgmt | User profile data |
| `login` | Login, Register, User Mgmt | Authentication (email, password, usertype) |
| `roles` | Register, Edit User, Profile | Role definitions |
| `add_property` | Dashboard, Property Mgmt | Simple property showcase (image, location, title) |
| `properties` | Dashboard, Rental Mgmt, Property Details | Detailed rental/sale listings |
| `property_images` | Dashboard, Property Images, Property Details | Multiple images per property with featured flag |
| `blog` | Dashboard, Blog Mgmt | Blog posts |
| `blog_attachments` | Blog Mgmt | File attachments per blog post |
| `about_content` | Dashboard, About Mgmt | Single-row CMS for About Us page |
| `videos` | Dashboard, Video Mgmt | Embedded video links |
| `home_backgrounds` | Dashboard, Background Mgmt | Carousel background images |
| `password_reset` | Forgot/Reset Password | Password reset tokens with expiry |

### Key Relationships
- `users.email` ↔ `login.email` (one-to-one, via email)
- `users.role_id` → `roles.role_id` (many-to-one)
- `properties.id` ↔ `property_images.property_id` (one-to-many)
- `blog.id` ↔ `blog_attachments.blog_id` (one-to-many)

---

## 15. Complete Page Inventory

### Authentication & Session
| File | Access | Purpose |
|------|--------|---------|
| `index.php` | Public | Login page with split-screen design |
| `register.php` | Auth | User registration form |
| `register_process.php` | Public | Registration handler with image upload |
| `forgot_password.php` | Public | Password reset request with email |
| `reset_password.php` | Public | Password reset form with token validation |
| `display_reset_link.php` | Dev only | Shows reset link for local development |
| `logout.php` | Auth | Session destroy + redirect |

### Dashboard
| File | Access | Purpose |
|------|--------|---------|
| `dashboard.php` | Auth | Statistics, charts, recent activity, quick actions |

### User Management
| File | Access | Purpose |
|------|--------|---------|
| `manage_users.php` | Admin | User list with view/edit/delete |
| `edit_user.php` | Admin | Edit user form with image upload |
| `profile.php` | Auth | Current user profile view |

### Property Management (Showcase)
| File | Access | Purpose |
|------|--------|---------|
| `manage_property.php` | Auth | Add property form (image, location, title) |
| `add_property.php` | Auth | Add property handler |
| `display_properties.php` | Auth | Property list with delete |
| `edit_property.php` | Auth | Edit property with image replacement |
| `property_details.php` | Auth | Detailed property view (from `properties` table) |

### Property Management (Rental)
| File | Access | Purpose |
|------|--------|---------|
| `add_rental_property.php` | Auth | Detailed rental property form |
| `edit_rental.php` | Auth | Edit rental property |
| `display_rental.php` | Auth | Rental property list with bulk delete |
| `property_images.php` | Auth | Bulk image upload with featured flag |
| `delete_property_image.php` | Auth | Delete single property image |

### Blog Management
| File | Access | Purpose |
|------|--------|---------|
| `add_blog.php` | Auth | Create blog post with image + attachments |
| `edit_blog.php` | Auth | Edit blog post, manage attachments |
| `display_blog.php` | Auth | Blog list with delete |
| `view_blog.php` | Auth | View single blog with attachments |
| `delete_attachment.php` | Auth | Delete single blog attachment |

### About Content
| File | Access | Purpose |
|------|--------|---------|
| `add_about.php` | Auth | Add about CMS content |
| `edit_about.php` | Auth | Edit about CMS content |
| `display_about.php` | Auth | About content list |
| `view_about.php` | Auth | View about content |

### Video Management
| File | Access | Purpose |
|------|--------|---------|
| `add_video.php` | Auth | Add/edit video links (combined page) |
| `edit_video.php` | Auth | Standalone video edit form |
| `display_video.php` | None | Display active video (public component) |

### Background Management
| File | Access | Purpose |
|------|--------|---------|
| `home_background.php` | Auth | Manage carousel backgrounds |
| `edit_background.php` | Auth | Edit background image path |

### Error Page
| File | Access | Purpose |
|------|--------|---------|
| `404_error.php` | Public | Custom 404 error page |

---

## 16. Security & Code Quality Notes

### Implemented
- PDO prepared statements (SQL injection prevention)
- bcrypt password hashing (`password_hash()` / `password_verify()`)
- `filter_var()` for email sanitization
- `htmlspecialchars()` for output encoding
- Session-based authentication
- File upload validation (extension, size, `getimagesize()`)
- PDO transactions for multi-step operations
- Token-based password reset with expiry

### Concerns
- **Hardcoded credentials**: Database credentials in `config.php`, SMTP credentials in root email handlers
- **No CSRF protection**: All forms lack CSRF tokens
- **No rate limiting**: Login, contact, booking forms vulnerable to brute force
- **Session fixation**: No `session_regenerate_id()` after login
- **Error disclosure**: Database errors displayed to users
- **Inconsistent auth**: Some pages use `include/header.php`, others start session directly
- **Duplicate code**: File upload logic repeated across handlers
- **Mixed Bootstrap versions**: CDN 4.5.2 in some pages, local 5.0.2 in others
- **No foreign keys**: Referential integrity handled in application code only
- **0777 permissions**: Upload directories created with world-writable permissions
- **Broken links**: `404_error.php` home link points to `index-2.html`

---

## 17. Navigation Structure (Sidebar)

The sidebar in `include/header.php` provides the following navigation groups:

| Group | Pages | Access |
|-------|-------|--------|
| Dashboard | `dashboard.php` | All |
| Users | `manage_users.php`, `register.php`, `edit_user.php` | Admin only |
| Properties | `display_properties.php`, `add_property.php`, `edit_property.php`, `manage_property.php`, `property_details.php`, `property_images.php` | All |
| Rentals | `display_rental.php`, `add_rental_property.php`, `edit_rental.php` | All |
| Blog | `display_blog.php`, `add_blog.php`, `edit_blog.php`, `view_blog.php` | All |
| About | `display_about.php`, `add_about.php`, `edit_about.php`, `view_about.php` | All |
| Profile | `profile.php` | All |

Active page is highlighted with `.active` class. Notification bell shows pending blogs + pending users count.

---

*Document generated: 2026-08-25*
*Project Location: C:\xampp\htdocs\first-legal-service\*
*Data Directory: C:\xampp\htdocs\first-legal-service\data\*
