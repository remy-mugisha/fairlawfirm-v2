# Admin Dashboard Explanation

## 1. Overview

The admin dashboard (`data/dashboard.php`) serves as the landing page for authenticated administrators and users of the Fair Law Firm LTD system. It provides an at-a-glance statistics overview of the platform's key metrics.

## 2. Access Control

Access is enforced through `data/include/header.php` which:

```php
session_start();
if(!isset($_SESSION["email"])){
    header("location: index.php");
    exit();
}
```

Only authenticated sessions with a valid `email` key can access the dashboard. Unauthenticated users are redirected to `data/index.php` (login page).

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

## 4. Database Connection

The dashboard connects to MySQL via `data/propertyMgt/config.php`:

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

Three responsive stat cards are displayed using Bootstrap's grid system:

```html
<div class="row">
    <div class="col-md-4">...</div>  <!-- Total Employers -->
    <div class="col-md-4">...</div>  <!-- Total Properties -->
    <div class="col-md-4">...</div>  <!-- Rental Properties -->
</div>
```

### 5.2 Card Styling

- **Border Radius**: 10px
- **Shadow**: `0 4px 8px rgba(0,0,0,0.1)`
- **Hover Effect**: Lifts 5px with increased shadow
- **Transition**: 0.3s ease
- **Color Coding**:
  - Employers: `#17a2b8` (cyan)
  - Total Properties: `#ffc107` (amber)
  - Rental Properties: `#065f46` (green)

### 5.3 Responsive Behavior

```css
@media (max-width: 768px) {
    .dashboard_card { height: 120px; }
    .icon_holder i { font-size: 24px; }
    .stat-card h2 { font-size: 28px; }
}
```

Cards stack vertically on mobile screens with reduced icon and heading sizes.

## 6. Sidebar Navigation

The dashboard includes a persistent sidebar (`data/include/header.php`) with the following navigation items:

| Link | Icon | Access |
|------|------|--------|
| Dashboard | `fa-dashboard` | All users |
| Manage Properties | `fa-building` | All users |
| Rental House | `fa-home` | All users |
| Blog | `fa-newspaper-o` | All users |
| About | `fa-info-circle` | All users |
| Images (collapsible) | `fa-image` | All users |
| Manage Users | `fa-users` | Admin only |
| Settings | `fa-cog` | All users |

- Active page is highlighted with `.active` class
- Admin-only items use conditional rendering:
  ```php
  <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'): ?>
      <li><a href="manage_users.php">...</a></li>
  <?php endif; ?>
  ```

## 7. Top Bar

The top bar includes:
- **Sidebar Toggle**: Collapses/expands sidebar on mobile
- **User Profile Dropdown**: Shows profile image (or default avatar) and user name
- **Dropdown Actions**: "My Profile" and "Log Out"

## 8. Assets & Dependencies

Loaded in `data/include/footer.php`:

| Asset | Purpose |
|-------|---------|
| `jquery.min.js` | jQuery 3.x |
| `popper.min.js` | Bootstrap tooltip positioning |
| `bootstrap.min.js` | Bootstrap JS components |
| `animate.js` | WOW animation library |
| `bootstrap-select.js` | Enhanced select dropdowns |
| `owl.carousel.js` | Carousel (loaded but unused on dashboard) |
| `Chart.min.js` / `Chart.bundle.min.js` | Chart.js (loaded but unused on dashboard) |
| `perfect-scrollbar.min.js` | Custom scrollbar for sidebar |
| `custom.js` | Dashboard custom JS |
| `chart_custom_style1.js` | Chart styling (unused on dashboard) |

## 9. Template Origin

The dashboard layout is based on the **Pluto Admin Template** (by ThemeWagon), as evidenced by:
- HTML comment: `<!-- Mirrored from themewagon.github.io/pluto/index -->`
- CSS classes: `dashboard_1`, `full_container`, `inner_container`
- Structure: sidebar + topbar + content area pattern

## 10. Current Limitations

1. **No Charts**: Chart.js is loaded but no charts are rendered on the dashboard
2. **Static Metrics**: Only 3 hardcoded statistics; no recent activity, pending approvals, or trend data
3. **No Date Filtering**: All-time counts with no time range selector
4. **No Drill-down**: Cards are not clickable to view underlying data
5. **Minimal Interactivity**: No AJAX calls or dynamic updates

## 11. File Structure

```
data/
├── dashboard.php              # Main dashboard page
├── include/
│   ├── header.php             # Session check + sidebar + topbar
│   └── footer.php             # Scripts + footer
├── propertyMgt/
│   └── config.php             # PDO database connection
└── style.css                  # Dashboard base styles
```
