# Fair Law Firm LTD — Admin CMS Design System

**Document type:** Design System specification (single source of truth)
**Scope:** `data/` admin Content Management System (procedural PHP + Bootstrap)
**Design version:** 1.0
**Last updated:** 2026-09-01

> This document defines every visual decision that makes the admin CMS look and feel
> like a single, professional product. It is written to the same standard used for
> "Amazi — RWB Water Intelligence": tokens first, then components, then the information
> architecture and wireframes. Any CSS you write that is not described here is out of scope.
>
> **Hard constraint:** this document is **visual/template only**. It changes zero PHP
> logic, zero database queries, zero schema, zero session/auth code, and zero
> POST field names. Any class-name alterization below is additive or cosmetic.

---

## 0. How to read this document

The entire system is driven by **CSS custom properties** (variables) declared once on
`:root` in a single shared stylesheet (`css/theme.css`). Every value used across every
page must be referenced through a variable. If it isn't a variable, it isn't part of the
system and should be considered a one-off that is likely to drift.

**The three layers of CSS, loaded in order:**

```
1. Bootstrap 5        (framework — layout/grid/utilities only)
2. theme.css          (OUR system: tokens + components + shell overrides)
3. page-level <style> (only for genuinely page-specific layout, must still use tokens)
```

No component styling happens with raw hex values, raw pixel type, or hard-coded colors
inside page files. Page-level `<style>` blocks are allowed for page-specific layout and
MUST reference the tokens in `theme.css`.

---

## 1. Behavior & Accessibility Principles

1. **Credibility before decoration.** This is a law firm's internal tool. Restraint, order,
   and legibility outperform ornament.
2. **Consistency is the product.** One Bootstrap version, one font link, one token file —
   the same shell on every page.
3. **Status is never color alone.** Every status shows a label / icon + a color, so the
   system is colour-blind safe.
4. **WCAG AA is the floor.** Every text/background pairing targets ≥ 4.5:1 (normal text)
   and ≥ 3:1 for UI components. Visible 2px focus rings on every interactive element.
5. **Motion confirms, it does not decorate.** Short, purposeful transitions only; all
   motion disabled under `prefers-reduced-motion: reduce`.
6. **Real semantic HTML.** One `<h1>` per page, real `<table>` for data, real
   `<button>`/`<a>`, a real `<label>` for every input.

---

## 2. Color System

### 2.1 Brand palette

The firm keeps its identity but gains a refined, consistent palette. Two main accents
plus a full neutral ramp with a warm undertone.

| Token | Value | Use |
|---|---|---|
| `--fl-primary` | `#1D3650` | Chambers navy — primary brand, main structure, primary buttons |
| `--fl-primary-dark` | `#14273C` | Hover / active variant of primary |
| `--fl-primary-light` | `#E9EEF5` | Tinted wash, icon-tile background, table row hover |
| `--fl-accent` | `#B08D3E` | Brass gold — verified / highlight / secondary emphasis (rationed) |
| `--fl-accent-soft` | `#F5EBD6` | Accent tint for chips and icon tiles |
| `--fl-ink` | `#2C241E` | Primary text (warm near-black) |
| `--fl-ink-secondary` | `#5C5348` | Secondary text |
| `--fl-ink-muted` | `#8C8174` | Muted / placeholder text |
| `--fl-line` | `#E4DfD6` | Hairline border (warm) |
| `--fl-surface` | `#FFFFFF` | Cards, panels, topbar |
| `--fl-surface-muted` | `#F7F5F1` | Page background, alternate rows |
| `--fl-danger` | `#B3403A` | Errors, destructive actions |
| `--fl-success` | `#2E7D4F` | Active / success / available |
| `--fl-warning` | `#C99B2E` | Pending / attention |
| `--fl-info` | `#3A6B8F` | Informational / legal context |

### 2.2 Semantic status mapping (single source of truth)

| Meaning | Background | Foreground | Border | Used for |
|---|---|---|---|---|
| **Active / For Rent / Published** | `--fl-success` tint `#EAF4EE` | `--fl-success` | `#D2E8DB` | property status, blog status, user status |
| **Pending / Under review** | `--fl-warning` tint `#FBF3DE` | `#8A6A16` | `#EFDFB0` | pending blog / user / property |
| **Inactive / Not available** | `#EFEDE8` | `--fl-ink-muted` | `#DCD8D0` | inactive states |
| **For Sale** | `--fl-accent-soft` | `--fl-accent` dark `#8A6A16` | `#EADBB5` | property "for sale" |
| **Error / Rejected** | `#FBEEED` | `--fl-danger` | `#F0D5D2` | form errors, deletion |
| **Info / Legal** | `--fl-primary-light` | `--fl-primary` | `#D3DEEB` | informational banners |

Status text **always** renders at the dark 700-level, never in the pale tint.

### 2.3 Contrast

- `--fl-primary` (#1D3650) on white ≈ 11:1 — AAA
- `--fl-ink` (#2C241E) on white ≈ 14:1 — AAA
- `--fl-ink-secondary` (#5C5348) on white ≈ 7:1 — AAA
- `--fl-ink-muted` (#8C8174) on white ≈ 4.5:1 — AA (large/UI text)
- `--fl-accent` (#B08D3E) on white ≈ 3.1:1 — UI components only (never body text at small size)

---

## 3. Typography

### 3.1 Font family — loaded once in `include/header.php`

| Role | Family | Weights loaded |
|---|---|---|
| Body / UI | **Inter** | 400, 500, 600, 700 |
| Headings / display | **Playfair Display** | 500, 600, 700 |
| Data / reference | **IBM Plex Mono** | 400, 500 |

Rationale: **Inter** is a legible, civic-grade sans-serif built for dense admin interfaces
and long reading sessions. **Playfair Display** is a high-contrast, elegant serif that
conveys institutional prestige and legal gravitas — the classic "prestige law firm"
heading voice, paired with the workhorse Inter body to keep the interface dense-friendly.
**IBM Plex Mono** is reserved for IDs, prices, dates, and phone numbers so they read as
"filed and indexed," not marketing copy.

```css
--font-body:    'Inter', system-ui, -apple-system, 'Segoe UI', Arial, sans-serif;
--font-heading: 'Playfair Display', 'Georgia', 'Times New Roman', serif;
--font-mono:    'IBM Plex Mono', 'SFMono-Regular', Consolas, monospace;
```

Google Fonts link — added **once** in `include/header.php` (never per-page):

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
```

### 3.2 Type scale

Font sizes are a clear, deliberate 14px-base scale. All sizes use `rem`.

| Token | Value | Weight | Line-height | Letter-spacing | Use |
|---|---|---|---|---|---|
| `--fs-h1` | `2.25rem` (36px) | 700 | 1.2 | -0.02em | Page title (top-level) |
| `--fs-h2` | `1.5rem` (24px) | 700 | 1.28 | -0.01em | Panel / section titles |
| `--fs-h3` | `1.25rem` (20px) | 600 | 1.32 | 0 | Sub-panel / form-section titles |
| `--fs-h4` | `1.125rem` (18px) | 600 | 1.35 | 0 | Card titles, modal titles |
| `--fs-h5` | `1rem` (16px) | 600 | 1.4 | 0 | Small card titles |
| `--fs-h6` | `0.875rem` (14px) | 600 | 1.45 | 0.01em | Widget labels |
| `--fs-lead` | `1rem` (16px) | 400 | 1.6 | 0 | Lead/intro paragraphs |
| `--fs-body` | `0.875rem` (14px) | 400 | 1.55 | 0 | Default body text |
| `--fs-small` | `0.75rem` (12px) | 400 | 1.5 | 0 | Table secondary cells, help text |
| `--fs-caption` | `0.6875rem` (11px) | 500 | 1.4 | 0.08em · uppercase | Kickers, eyebrow labels, table headers |

### 3.3 Font weight tokens

| Token | Value | Use |
|---|---|---|
| `--fw-regular` | 400 | Body text |
| `--fw-medium` | 500 | Labels, buttons, table headers |
| `--fw-semibold` | 600 | Sub-headings, key highlights |
| `--fw-bold` | 700 | Headings, numbers/KPIs |

### 3.4 Headings vs body rule

Headings (h1–h3, `.fl-h1`–`.fl-h3`) use **Playfair Display**. Body, labels, buttons,
tables, and UI use **Inter**. Data values (KPIs, prices, IDs) use **IBM Plex Mono**.

---

## 4. Spacing, Radius, Shadows

### 4.1 Spacing scale — 4px base grid (8px rhythm)

| Token | Value | Use |
|---|---|---|
| `--sp-1` | `4px` | Icon-to-text gap |
| `--sp-2` | `8px` | Chip padding, dense gaps |
| `--sp-3` | `12px` | Form gaps, input padding |
| `--sp-4` | `16px` | Card padding (comfort), small gaps |
| `--sp-5` | `20px` | Panel padding |
| `--sp-6` | `24px` | Between major regions, panel padding |
| `--sp-8` | `32px` | Page section rhythm |
| `--sp-10` | `40px` | Large section rhythm |

### 4.2 Border radius

| Token | Value | Use |
|---|---|---|
| `--radius-sm` | `6px` | Buttons, inputs, chips, small tiles |
| `--radius-md` | `10px` | Cards, panels, select dropdowns |
| `--radius-lg` | `14px` | Modal content, large containers |
| `--radius-full` | `999px` | Pills, avatars, notification badge |

Radius stays soft but disciplined — the max is `14px`. **No 30px+ "app store" rounding.**

### 4.3 Shadows

| Token | Value | Use |
|---|---|---|
| `--shadow-sm` | `0 1px 2px rgba(45,35,20,.06)` | Resting panels, cards |
| `--shadow-md` | `0 4px 12px rgba(45,35,20,.08)` | Hover cards, dropdowns, popovers |
| `--shadow-lg` | `0 12px 32px rgba(45,35,20,.14)` | Modals, focus-overlays, delete dialogs |

Shadows are warm-tinted (brownish), never cool blue-gray, matching the warm ink palette.

### 4.4 Borders

- Default hairline: `1px solid var(--fl-line)`.
- Status never relies on border alone — borders are additive, never the only signal.

---

## 5. Buttons

Height `40px`, padding `0 1.25rem`, `--fs-body` Inter 500, `--radius-sm`, uppercase not
required. Buttons always show an icon slot where relevant.

| Variant | Background | Text | Border | Hover | Active | Disabled |
|---|---|---|---|---|---|---|
| **Primary** | `--fl-primary` | white | `--fl-primary` | `--fl-primary-dark`, shadow-sm | `--fl-primary-dark` inset | 40% opacity, cursor not-allowed |
| **Accent (secondary CTA)** | `--fl-accent` | white | `--fl-accent` | darken `--fl-accent`, shadow-sm | darker | 40% opacity |
| **Secondary / outline** | transparent | `--fl-primary` | `--fl-primary` 1px | `--fl-primary-light` bg | `--fl-primary-light` bg | 40% opacity |
| **Ghost** | transparent | `--fl-ink-secondary` | none | `--fl-surface-muted` bg | `--fl-surface-muted` bg | 40% opacity |
| **Danger** | `--fl-danger` | white | `--fl-danger` | darken `--fl-danger`, shadow-sm | darker | 40% opacity |

**Sizes:** `.btn-sm` → height `34px`, `--fs-small`; default → `40px`; `.btn-lg` → `46px`.

**Focus:** 2px ring in `--fl-primary` (or `--fl-accent` on dark) with 2px offset.

### 5.1 Button states — explicit

- **default** — as per table above.
- **hover** — darken background (one step), lift with `--shadow-sm`.
- **active / pressed** — darken background, no lift, `box-shadow: inset` to feel pressed.
- **disabled** — `opacity: .4`, `cursor: not-allowed`, no hover/active change.

---

## 6. Forms

| Element | Style |
|---|---|
| **Input** | height `40px`, `--fs-body`, `1px solid --fl-line`, `--radius-sm`, transparent/white bg |
| **Input focus** | `border-color: --fl-primary`, `box-shadow: 0 0 0 3px --fl-primary-light` |
| **Input error** | `border-color: --fl-danger`, `background: #FBEEED` |
| **Select** | same as input; custom chevron arrow via background SVG; `appearance:none` |
| **Checkbox / radio** | `accent-color: --fl-primary`; custom `fl-checkbox-card` for floor grids |
| **Label** | `--fs-small`, Inter 500, `--fl-ink-secondary`, uppercase kicker optional |
| **Textarea** | same border/radius; `min-height: 100px`, `resize: vertical` |
| **File upload** | dashed (`2px dashed --fl-line`) drop-zone, `--fl-primary` on hover/dragover |
| **Help text** | `--fs-small`, `--fl-ink-muted` |

Labels are associated via `for`/`id`. Error text is programmatically associated
(`aria-describedby`).

---

## 7. Tables

- Standard data table in a `.table` with `--radius-md` container.
- **Header:** `--fs-caption` (11px, uppercase, tracked), Inter 500, `--fl-ink-muted`,
  background `--fl-surface-muted`, bottom `1px var(--fl-line)`.
- **Row:** `--fs-body`, min height `48px`, bottom `1px var(--fl-line)`.
- **Hover:** background `--fl-primary-light` (subtle).
- **Numeric / price / date / ID columns:** `--font-mono`, right-aligned (read as a ledger).
- **Image thumbnails:** `56×44px`, `object-fit: cover`, `--radius-sm`, `2px --fl-line`.
- **Action icon buttons:** square `34×34px`, `--radius-sm`.
- **Empty state:** centered icon tile + one line of text + primary next action.

---

## 8. Badges / Status Pills

A status pill uses tint + icon + word (never color alone).

| Class | Background | Foreground | Dot |
|---|---|---|---|
| `.badge-active` | `--fl-success` tint | `--fl-success` | `--fl-success` |
| `.badge-pending` | `--fl-warning` tint | `#8A6A16` | `#8A6A16` |
| `.badge-inactive` | `#EFEDE8` | `--fl-ink-muted` | `--fl-ink-muted` |
| `.badge-sale` | `--fl-accent-soft` | `#8A6A16` | `#B08D3E` |
| `.badge-danger` | `#FBEEED` | `--fl-danger` | `--fl-danger` |
| `.badge-info` | `--fl-primary-light` | `--fl-primary` | `--fl-primary` |

Style: `--fs-small`, Inter 500, padding `4px 10px`, `--radius-full`, an 8px leading dot
(`::before`), whitespace `nowrap`.

---

## 9. Cards & Panels

- **Panel (`.fl-panel`):** `--fl-surface` bg, `1px var(--fl-line)`, `--radius-md`,
  `--shadow-sm`, padding `--sp-6`. Header has a kicker eyebrow + Lora title + border bottom.
- **Stat card (`.fl-stat-card`):** `--fl-surface`, `--radius-md`, `--shadow-sm`; top accent
  rule (4px) in `--fl-accent`; big `--font-mono` KPI value; uppercase label in
  `--fl-ink-muted`; hover lift with `--shadow-md`.
- **Icon tile (`.fl-icon-tile`):** `40×40px`, `--radius-sm`, tinted bg + matching icon color.
- **Electric/empty state:** centered, one `<p>`, one action button.

---

## 10. Sidebar Navigation & Topbar

### 10.1 Sidebar

- **Width:** 250px expanded / 72px collapsed (`.active`).
- **Background:** `--fl-primary` (#1D3650) with a subtle dark gradient to `--fl-primary-dark`.
- **Brand lockup:** icon tile (gold accent) + "Fair Law Firm" (Lora, white) + "Admin" kicker
  (gold, uppercase, tracked).
- **Nav items:** Inter 500, 14px, white at 72% opacity; icon + label.
- **Active state:** 3px gold left border, white text, subtle light wash, icon in gold.
- **Hover:** text 100% white, background white at 6%.
- **Logout:** separated group; rendered as a gold filled CTA with navy icon/text.
- **Mobile (<1199px):** collapses to icon rail; off-canvas drawer with overlay shadow.

### 10.2 Topbar

- Height `64px`, `--fl-surface` bg, bottom `1px var(--fl-line)`.
- **Hamburger:** square `40×40px`, `--radius-sm`, hover `--fl-primary-light`.
- **Page title:** `--fs-h3` (20px) Lora 600, `--fl-primary`.
- **Search pill:** `--fl-surface-muted` bg, `--radius-full`, 40px, focus ring primary.
- **Notification bell:** 40px round icon button; badge = 17px `--fl-danger` circle with
  white count in the top-right.
- **User chip:** avatar (36px round, gold ring) + name (14px Inter 600) + caret.
- **Dropdowns (notifications / user):** `--fl-surface`, `--radius-md`, `--shadow-lg`,
  no border or `1px var(--fl-line)`; rows hover `--fl-primary-light`.

---

## 11. Layout & Information Architecture

### 11.1 Shell

```
┌──────────────────────────────────────────────┐
│  .full_container > .inner_container          │
│  ┌─────────┬────────────────────────────────┐ │
│  │ #sidebar│ #content                       │ │
│  │ 250/72px│  .topbar (64px, sticky)        │ │
│  │         │  .padding_infor_info (content) │ │
│  │         │  .fl-footer                    │ │
│  └─────────┴────────────────────────────────┘ │
└──────────────────────────────────────────────┘
```

### 11.2 IA / nav groups

```
OPERATIONS
  Dashboard        dashboard.php
  Properties       display_properties.php (+ add/edit/images/details)
  Rentals          display_rental.php (+ add/edit)
  Blog             display_blog.php (+ add/edit/view)
  About            display_about.php (+ add/edit/view)
ADMINISTRATION  (admin-only)
  Users            manage_users.php (+ add/edit)
ACCOUNT
  Profile          profile.php
  Logout
```

### 11.3 Wireframe — generic list page (e.g. Properties)

```
┌────────┬──────────────────────────────────────────────────────────┐
│ nav    │ TOPBAR  ☰ Properties            [search]  (🔔) (👤)      │
│        ├──────────────────────────────────────────────────────────┤
│        │  PANEL                                                    │
│        │  ┌──────────────────────────────────────────────────┐    │
│        │  │ Property Listings                    [+ Add New]  │    │
│        │  ├──────────────────────────────────────────────────┤    │
│        │  │ #    IMAGE    LOCATION       TITLE      ACTIONS   │    │
│        │  │ 12   [img]    Kacyiru        4-Bed      ⚙ 🗑       │    │
│        │  │ ...          …                          …         │    │
│        │  └──────────────────────────────────────────────────┘    │
│        └──────────────────────────────────────────────────────────┘
```

### 11.4 Wireframe — form page (e.g. Add Blog)

```
┌────────┬──────────────────────────────────────────────────────────┐
│ nav    │ TOPBAR  ☰ Add Blog Post          [search]  (🔔) (👤)     │
│        ├──────────────────────────────────────────────────────────┤
│        │  PANEL                                                    │
│        │  ┌ Property Information ──────────────────────────────┐  │
│        │  │ Title [_________]  Category [_________]              │  │
│        │  │ Status [Active ▾]  Featured Image [↓ drop-zone]      │  │
│        │  ├─ Content ──────────────────────────────────────────┤  │
│        │  │ Description [ textarea ]                             │  │
│        │  │ Details [ textarea ]                                 │  │
│        │  │ Attachments [ multi drop-zone ]                      │  │
│        │  ├─────────────────────────────────────────────────────┤  │
│        │  │ [ Save Post ] [ Cancel ]                             │  │
│        │  └─────────────────────────────────────────────────────┘  │
│        └──────────────────────────────────────────────────────────┘
```

---

## 12. Responsive Rules

| Breakpoint | Behavior |
|---|---|
| ≥ 1200px | full sidebar, content max ~1400px |
| 960–1199px | sidebar → icon rail |
| < 960px | sidebar → off-canvas drawer; tables scroll horizontally; form rows stack to 1 col |
| < 576px | single column everywhere; buttons full-width where `btn-block` |

---

## 13. Motion

| Purpose | Motion | Duration / ease |
|---|---|---|
| Panel reveal | translateY 8px + fade | 220ms ease-out |
| Card hover | translateY -3px + shadow | 200ms ease |
| Status change | color crossfade | 160ms |
| Modal | scale .98→1 + fade | 180ms |
| Focus ring | instant | 0ms |

**All motion set to 0ms under `prefers-reduced-motion: reduce`.**

---

## 14. File map (where this lives)

| Deliverable | File path |
|---|---|
| This document | `data/DESIGN_SYSTEM.md` |
| Shared token + component stylesheet | `data/css/theme.css` |
| Font + token link (once) | `data/include/header.php` |
| Scripts + theme-preserving footer | `data/include/footer.php` |
| Bootstrap (single version) | `data/css/bootstrap.min.css` → **Bootstrap 5** |

---

## 15. Decisions (confirmed)

1. **Bootstrap 5 migration — CONFIRMED.** The `data/` CSS currently loads local
   **Bootstrap 4.0.0**; the public site already ships **Bootstrap 5.3.0** in
   `assets/vendors/bootstrap/`. The admin reuses that same Bootstrap 5.3.0 CSS + bundle JS
   so the whole application runs one framework version. This is a markup-level change
   (`data-toggle` → `data-bs-toggle`, `data-dismiss` → `data-bs-dismiss`, dropdown/`fade`
   markup,`.show` on menus) — **no PHP/database logic changes**.
2. **Consolidating three overlays — CONFIRMED.** The existing `style.css` (Pluto),
   `dashboard.css` (`--flf-*`), and `fairlaw-tokens.css` (`--fl-*`) are consolidated into
   **one** `css/theme.css`. Header/footer drop the competing overrides so pages stop
   fighting the cascade.
3. **Font pair — CONFIRMED: Playfair Display (headings) + Inter (body).** Replaces the
   earlier Source Serif 4 / Public Sans pairing.
4. **Bootstrap version conflict note resolved:** the DATA_EXPLANATION.md mentions CDN
   4.5.2 / local 5.0.2, but the actual `data/` files are BS4.0.0 — resolved by reusing the
   repo's own BS5.3.0 vendored build.

*© Fair Law Firm LTD — Design System v1.0. Tokens, components, and IA share one vocabulary. Use it, don't extend it ad hoc.*
