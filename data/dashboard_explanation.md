# Admin Dashboard Explanation

## 1. Overview

The admin dashboard (`dashboard.php`) serves as the landing page for authenticated administrators and users of the Fair Law Firm LTD system. It provides an at-a-glance statistics overview, interactive charts, recent user activity, and quick action shortcuts.

## 2. Access Control

Access is enforced through `include/header.php` which:

```php
session_start();
if(!isset($_SESSION["email"])){
    header("location: index.php");
    exit();
}
```

Only authenticated sessions with a valid `email` key can access the dashboard. Unauthenticated users are redirected to `index.php` (login page).

## 3. Statistics Engine

### 3.1 Helper Function: `getCount()`

```php
function getCount($conn, $table, $condition = null) {
    try {
        $sql = "SELECT COUNT(*) as count FROM $table";
        if ($condition) {
            $sql .= " WHERE $condition";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}
```

- Uses PDO prepared statements for SQL injection prevention
- Returns `0` on database errors to prevent dashboard crashes
- Accepts optional `WHERE` conditions for filtered counts

### 3.2 Dashboard Metrics

| Metric | Query Source | Table | Condition |
|--------|-------------|-------|-----------|
| Total Employers | `users` | `users` | `status = 'Active'` |
| Total Properties | `add_property` | `add_property` | None (all records) |
| Rental Properties | `properties` | `properties` | `property_status = 'For Rent'` |
| Blog Posts | `blog` | `blog` | None (all records) |

### 3.3 Supporting Queries

**Recent Users** — Last 5 registered users ordered by `created_at DESC`:
```php
SELECT first_name, last_name, email, profile_image, status, created_at
FROM users
ORDER BY created_at DESC
LIMIT 5
```

**Property Status Distribution** — Aggregated counts by `property_status`:
```php
SELECT property_status AS label, COUNT(*) AS cnt
FROM properties
GROUP BY property_status
ORDER BY cnt DESC
```

**User Growth** — Monthly new user counts for the last 12 months:
```php
SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c
FROM users
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
GROUP BY ym ORDER BY ym
```

## 4. Database Connection

The dashboard connects to MySQL via `propertyMgt/config.php`:

```php
$host = "localhost";
$dbname = "helloshi_faird";
$charset = "utf8";
$username = "helloshi_fairUser";
$password = "Allin@12345";

$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

- **Database**: `helloshi_faird`
- **Error Mode**: Exceptions enabled (`PDO::ERRMODE_EXCEPTION`)

## 5. UI Layout & Design

### 5.1 Stat Cards

Four responsive stat cards displayed using Bootstrap's grid system:

```html
<div class="row">
    <div class="col-sm-6 col-xl-3">...</div>  <!-- Total Employers -->
    <div class="col-sm-6 col-xl-3">...</div>  <!-- Total Properties -->
    <div class="col-sm-6 col-xl-3">...</div>  <!-- Rental Properties -->
    <div class="col-sm-6 col-xl-3">...</div>  <!-- Blog Posts -->
</div>
```

### 5.2 Charts Section

Two equal-width panels in a row:

```html
<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <!-- Property Status Distribution (Doughnut) -->
    </div>
    <div class="col-lg-6 mb-4">
        <!-- User Growth - Last 12 Months (Line) -->
    </div>
</div>
```

### 5.3 Activity & Actions Section

Two columns in a row:

```html
<div class="row">
    <div class="col-lg-7 mb-4">
        <!-- Recent Activity Feed -->
    </div>
    <div class="col-lg-5 mb-4">
        <!-- Quick Actions Grid -->
    </div>
</div>
```

### 5.4 Card Styling

- **Border Radius**: 10px
- **Shadow**: `0 4px 8px rgba(0,0,0,0.1)`
- **Hover Effect**: Lifts 5px with increased shadow
- **Transition**: 0.3s ease
- **Color Coding**:
  - Employers: navy/gold theme
  - Properties: blue theme
  - Rentals: green theme
  - Blog Posts: red theme

### 5.5 Responsive Behavior

Cards stack vertically on mobile screens with reduced icon and heading sizes.

## 6. Charts Implementation

### 6.1 Property Status Distribution (Doughnut Chart)

- Renders a doughnut chart in `<canvas id="flf_property_chart">`
- Palette: navy, gold, blue soft, gold light, navy dark, muted blue, bronze
- Center plugin displays total listings count
- Legend positioned at bottom with point-style indicators
- Empty state: "No property listings available yet."

### 6.2 User Growth — Last 12 Months (Line Chart)

- Renders a line chart in `<canvas id="flf_growth_chart">`
- Gold gradient fill (`rgba(200, 169, 81, 0.30)` to `rgba(200, 169, 81, 0.02)`)
- Navy border with gold point markers
- Tension: 0.4 (smooth curves)
- X-axis: month labels (e.g., "Aug 25")
- Y-axis: integer precision, begins at zero
- Zero-fill for months with no registrations

### 6.3 Chart Tooltip Style

- Background: navy (`#01166A`)
- Title: gold (`#C8A951`)
- Body: white
- Border: gold, width 1px, corner radius 8px
- Font family: DM Sans

## 7. Recent Activity Feed

Displays the last 5 registered users with:
- Profile avatar (or default avatar if none uploaded)
- Full name and email
- Status badge (Active/Pending — color coded)
- Relative timestamp via `timeAgo()` helper:
  - `< 1 min` → "Just now"
  - `< 1 hr` → "X min ago"
  - `< 1 day` → "X hr ago"
  - `< 7 days` → "X day(s) ago"
  - Otherwise → "M j, Y" format

Empty state: "No recent user activity found."

## 8. Quick Actions

Four action tiles linking to common admin tasks:

| Action | Icon | Link |
|--------|------|------|
| New Blog | `fa-pencil-square-o` | `add_blog.php` |
| Add Property | `fa-building` | `add_property.php` |
| Add Rental | `fa-home` | `add_rental_property.php` |
| Add User | `fa-user-plus` | `register.php` |

## 9. Helper Functions

### 9.1 `timeAgo($datetime)`

Converts a datetime string to a human-readable relative time:

```php
function timeAgo($datetime) {
    $ts = strtotime($datetime);
    if ($ts === false) return '';
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' day(s) ago';
    return date('M j, Y', $ts);
}
```

### 9.2 Welcome Name

Constructed from session variables:
```php
$welcomeName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
```

## 10. Sidebar Navigation

The dashboard includes a persistent sidebar (`include/header.php`) with the following navigation items:

| Link | Icon | Access |
|------|------|--------|
| Dashboard | `fa-th-large` | All users |
| Users | `fa-users` | Admin only |
| Properties | `fa-building` | All users |
| Rentals | `fa-home` | All users |
| Blog | `fa-newspaper-o` | All users |
| About | `fa-info-circle` | All users |
| Profile | `fa-user-circle` | All users |
| Logout | `fa-sign-out` | All users |

- Active page is highlighted with `.active` class
- Admin-only items use conditional rendering:
  ```php
  <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
      <li><a href="manage_users.php">...</a></li>
  <?php endif; ?>
  ```

## 11. Top Bar

The top bar includes:
- **Sidebar Toggle**: Collapses/expands sidebar on mobile (`#sidebarCollapse`)
- **Page Title**: Dynamic based on current page
- **Search Form**: Searches properties (`display_properties.php?search=...`)
- **Notification Bell**: Shows pending blogs + pending users count
- **User Profile Dropdown**: Shows profile image (or default avatar) and user name
- **Dropdown Actions**: "My Profile", "Dashboard", "Log Out"

## 12. Assets & Dependencies

Loaded in `include/footer.php`:

| Asset | Purpose |
|-------|---------|
| `jquery.min.js` | jQuery 3.x |
| `popper.min.js` | Bootstrap tooltip positioning |
| `bootstrap.min.js` | Bootstrap JS components |
| `bootstrap-select.js` | Enhanced select dropdowns |
| `owl.carousel.js` | Carousel (loaded but unused on dashboard) |
| `perfect-scrollbar.min.js` | Custom scrollbar for sidebar |
| `custom.js` | Dashboard custom JS (sidebar toggle) |
| `Chart.min.js` / `Chart.bundle.min.js` | Chart.js (used for both dashboard charts) |
| `utils.js` / `analyser.js` | Chart.js helpers (loaded but unused on dashboard) |
| `chart_custom_style1.js` | Chart styling (loaded but unused on dashboard) |

## 13. Template Origin

The dashboard layout is based on the **Pluto Admin Template** (by ThemeWagon), as evidenced by:
- HTML comment: `<!-- Mirrored from themewagon.github.io/pluto/index -->`
- CSS classes: `dashboard_1`, `full_container`, `inner_container`
- Structure: sidebar + topbar + content area pattern
- Custom brand styles override the base template with Fair Law Firm LTD branding

## 14. Current Limitations

1. **Static Metrics**: All-time counts with no time range selector
2. **No Drill-down**: Stat cards are not clickable to view underlying data
3. **No AJAX**: Charts and stats are server-rendered on page load
4. **No Date Filtering**: Growth chart always shows last 12 months
5. **No Export**: Charts cannot be exported as images or data

## 15. File Structure

```
data/
├── dashboard.php              # Main dashboard page
├── include/
│   ├── header.php             # Session check + sidebar + topbar + notifications
│   └── footer.php             # Scripts + footer
├── propertyMgt/
│   └── config.php             # PDO database connection
└── style.css                  # Dashboard base styles
```
