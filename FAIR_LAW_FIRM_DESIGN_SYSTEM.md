# FAIR LAW FIRM LTD — The Dossier Interface
## Design System Specification
**Public Marketing Platform + Internal Content Management System**
**Fair Law Firm LTD · Legal Services & Property Management · Kigali, Rwanda**

*This document is the single source of truth for every visual and interaction decision in the application. It follows the same studio workflow as any serious design engagement: study → design system → critique → implementation mapping. It is written so it can be handed directly to an AI coding agent (OpenCode / Claude Code) as a build brief — see §5 at the end for the ready-to-paste prompt.*

> **Scope note:** this document redesigns the **visual system and templates only**. It does not change the PHP logic, database schema, routes, business rules, email flows, or any functionality described in the existing project documentation (`Fair Law Firm LTD - Complete Project Documentation`). Where that document flags security or architecture debt (hardcoded SMTP credentials, no CSRF tokens, procedural structure, etc.), those remain **out of scope** for this UI pass and should be tracked separately.

---

## Table of Contents

1. [Phase 1 — Project Study](#phase-1--project-study)
2. [Phase 2 — Design System](#phase-2--design-system)
3. [Phase 3 — Self-Critique](#phase-3--self-critique)
4. [Phase 4 — Implementation Mapping](#phase-4--implementation-mapping)
5. [Build Prompt — for OpenCode / Claude Code](#5--build-prompt--for-opencode--claude-code)

---

# PHASE 1 — Project Study

## 1.1 The System Today

From the project documentation, Fair Law Firm LTD is a **dual-purpose procedural PHP application** (PHP 8.4, MySQL/MariaDB, Bootstrap 5.0.2 on the **Firdip** template — originally built for a fire department and repurposed):

- A **public marketing site**: homepage, about, legal services, property services, property listings + detail + booking, blog, contact.
- An **internal admin dashboard** (`/data/`): role-scoped (admin/employer), managing properties, rentals, blog posts, about content, videos, homepage backgrounds, and users.
- No formal MVC, no design system, template-inherited CSS (`firdip.css`) whose SEO metadata and component classes still trace back to a fire-department starter kit.

The UI is a **stock Bootstrap shell with someone else's fingerprints still on it.** Functionally the dual-practice model (Legal + Property) is real and complete; visually nothing distinguishes it from any other Bootstrap template site, and nothing in the interface communicates that this is a firm handling **legal representation and property assets** — two domains where visual trust is the product.

## 1.2 Users, Goals, Pain Points, Business Goals

| Audience | Goal | Today's Pain Point | Design Opportunity |
|---|---|---|---|
| **Prospective legal client** (individual/corporate) | Understand whether this firm handles their matter, and how to start | Generic services grid, no sense of credibility, stock template look undermines trust in a *legal* provider | A credible, document-grade visual language (seals, case-file structure) that signals institutional trust before a word of copy is read |
| **Property seeker / owner** | Browse or list a rental/sale property, book a viewing | Property cards look like a generic real-estate template; booking flow has no confirmation cues | A property "dossier" card system with clear status, and a booking flow that visibly confirms what happens next |
| **Returning site visitor / blog reader** | Read firm updates, download attached documents | Blog reads like a filler CMS page bolted onto a real-estate template | Blog treated as **firm publications** — same document-grade type system as the legal pages |
| **Admin / content editor** (`data/` dashboard) | Add/update properties, posts, users quickly and correctly | Dashboard is a bare Bootstrap admin skin; no visual hierarchy for what needs attention today | A dashboard that answers "what's live, what's pending, what needs me today" in one glance |
| **Firm principals / examiners of the brand** | The site should read as a real, licensed Rwandan legal + property firm, not a hobby project | A repurposed fire-department template does the opposite | A named, documented design system with a signature element and a defensible rationale |

**Business goal:** more qualified inquiries (legal + property), faster admin content turnaround, a site that reads as credible enough to hand a client its URL in a first meeting.

**Design opportunity in one sentence:** *turn two different practices — legal and property — into one coherent firm, using the shared idea every lawyer and every property manager already lives by: everything of value becomes a file.*

---

# PHASE 2 — Design System

## 2.1 Product Identity

### Name — **The Dossier Interface**

A *dossier* is the one object both practices already produce: a legal case file and a property file are both, structurally, a dossier — a cover, a status, a set of documents, a history of who touched it and when. The interface is built around that shared unit.

**Fair Law Firm LTD** — the brand name and copy do not change. "The Dossier Interface" is the *design system's* internal name, the way "Material Design" names Google's system without renaming Gmail.

### Concept — **Cover → Contents → Seal**

Every screen follows the physical logic of a case file:

- **The cover is at the top** — a dark chambers-navy bar holding the firm identity, like the cover sheet of a dossier.
- **Navigation is the tab index** — a left rail whose sections read like the tabbed dividers inside a physical file (Practice, Property, Publications, Firm).
- **Content is the contents page** — panels that collect information the way a case file collects exhibits, never scattered cards with no relationship to each other.
- **The decision is the seal** — every screen resolves in one clear primary action (Book, Enquire, Publish, Approve), rendered as a stamped, deliberate button. Nothing ornamental sits between the visitor and that action.

Ledger rules texture backgrounds where restraint is needed; a wax-seal motif marks anything verified, trusted, or primary; the whole application should read as **institutional paperwork done well** — not a website template, not a real-estate marketplace clone.

### Mark (logo concept)

A **balance scale whose base line is a rooftop silhouette** — justice standing on property. Rendered inside a rounded-square seal tile. The mark doubles as a literal wax-seal stamp graphic used for "verified," "published," and "booking confirmed" states — so the mark isn't just a logo, it's a working UI element.

## 2.2 Design Principles

1. **Credibility before decoration.** A law firm's website is evaluated the way its letterhead is: is this a serious operation? Every choice is judged against that bar first.
2. **Legal and Property are equals, not a main feature and an add-on.** Neither practice gets the good template and the other the leftover grid. Shared components, distinct accent colors.
3. **Every document has provenance.** Dates, statuses, and authorship are always visible — on a blog post, a property listing, a booking confirmation. Nothing floats without a timestamp.
4. **Cover → Contents → Seal.** Navigation, content, and action follow one direction on every page; the visitor never has to re-learn the layout.
5. **Warm, not corporate-cold.** This is a Rwandan firm, not a generic multinational template. Warm parchment neutrals over sterile gray-on-white.
6. **Motion is confirmation, not decoration.** A stamp lands, a status changes, a form submits — motion marks the moment something became true. No idle animation, no autoplay carousels with more than three calm slides.
7. **Accessible enough for a courtroom, simple enough for a first-time property tenant.** WCAG AA is the floor for both audiences the firm actually serves.

## 2.3 Moodboard (described)

| Board | Character | Source material |
|---|---|---|
| *Chambers* | Leather-bound registers, brass fittings, ink-on-paper case files | Law office interiors, legal ledgers, notarial seals |
| *The Plot* | Kigali rooftops, surveyed plots, hand-drawn property sketches | Rwandan residential/commercial architecture, land survey plans |
| *The Register* | Ruled ledger paper, stamped receipts, filing tabs | Registries of deeds, notary stamps, index cards |
| *The Meeting* | A warm consultation room, not a boardroom stock photo | Real client-meeting settings, not generic "handshake in suits" photography |

The synthesis: **navy ink on warm parchment**, with **brass/seal gold** reserved for what's verified or primary, and **terracotta clay** for the property practice — never "generic law-firm navy-and-gold-on-white-with-Playfair-Display."

## 2.4 Typography

### The Pairing

| Role | Font | Rationale |
|---|---|---|
| **Display / Headings** | **Source Serif 4** (600/700) | A serif built for long-form reading and editorial gravitas without the "wedding invitation" softness of Playfair Display — the font every generic law-firm template reaches for. Source Serif 4 reads as *drafted*, not *decorated*: it belongs on a contract cover page, not a menu. |
| **Body / UI** | **Public Sans** (400/500/600) | Built for the U.S. Web Design System — designed explicitly for civic, government-adjacent trust and long-session readability. It gives the interface the same "this is a real institution" register a legal and property-registry firm needs, without slipping into "SaaS product" territory (avoid Inter, Poppins, Nunito — every one signals "startup template"). |
| **Data / Reference** | **IBM Plex Mono** (400/500) | Case-adjacent reference numbers — property IDs, prices, dates, phone numbers, blog publish dates — set in Plex Mono read as **filed and indexed**, not marketing copy. It is the "docket number" voice of the interface. |

### Scale

| Token | Rem / Clamp | Face · Weight | Use |
|---|---|---|---|
| `display-1` | `clamp(2.4rem, 5vw, 3.2rem)` | Source Serif 4 700 | Homepage hero, page-level hero statements |
| `display-2` | `clamp(1.9rem, 4vw, 2.4rem)` | Source Serif 4 700 | Section heroes (Services, Properties, Blog) |
| `h1` | `clamp(1.5rem, 2.4vw, 1.9rem)` | Source Serif 4 600 | Page titles |
| `h2` | `1.25rem` | Source Serif 4 600 | Panel / card group titles |
| `h3` | `1.02rem` | Public Sans 600 | Sub-panels, form legends |
| `kicker` | `0.7rem`, `letter-spacing .13em`, uppercase | Public Sans 600 | Eyebrows, section labels, "FILE / DOSSIER" tab labels |
| `body` | `0.9375rem` (15px) · line-height 1.65 | Public Sans 400 | Default text |
| `body-sm` | `0.8125rem` | Public Sans 400 | Metadata, table secondary cells |
| `data` | `0.875rem` | IBM Plex Mono 400 | Prices, dates, IDs, phone numbers |
| `data-lg` | `1.4rem` | IBM Plex Mono 500 | Dashboard KPI values, price heroes on property detail |

**Table typography:** 15px Public Sans; header cells `kicker` style; numeric/price/date columns in mono, right-aligned, so tables read like a ledger.

**Property cards:** price and location in mono (small), title in Source Serif 4, status as a chip — never all in one weight.

## 2.5 Color System

### Philosophy

The palette is named for the file, not for a hex-picker. Neutrals carry a faint **warm parchment undertone** so surfaces feel like paper in good light, never sterile SaaS gray. Two accents exist for a reason: **chambers navy** is the firm's own color (used for links, primary structure, legal content), **clay terracotta** is reserved for the property practice, and **seal gold** is reserved for anything verified, official, or primary-CTA — never used as a background wash.

### Raw Palette

| Token | Value | Named for |
|---|---|---|
| `chambers-950` | `#0B1520` | midnight ledger |
| `chambers-900` | `#101E2E` | closed chambers |
| `chambers-800` | `#16293D` | oak panelling |
| `chambers-700` | `#1D3650` | the bench, deep |
| `chambers-600` | **`#244365`** | **primary · the bench** |
| `chambers-500` | `#2F567F` | reading lamp navy |
| `chambers-400` | `#5478A0` | ink wash |
| `chambers-300` | `#8AA3C0` | window light |
| `chambers-200` | `#BFD0E2` | vellum shadow |
| `chambers-100` | `#E1E9F1` | pale wash |
| `chambers-50` | `#F3F6FA` | letterhead white |
| `seal-700` | `#7A5A12` | old brass |
| `seal-600` | `#9C7818` | **the seal · verified / primary CTA accent** |
| `seal-500` | `#BB9330` | polished brass |
| `seal-400` | `#D2B563` | candlelight |
| `seal-100` | `#F5EAD2` | wax residue |
| `seal-50` | `#FBF5E9` | parchment glow |
| `clay-700` | `#7A3018` | fired brick |
| `clay-600` | `#A3401F` | **property practice · for sale** |
| `clay-500` | `#C15A32` | Kigali rooftop tile |
| `clay-400` | `#DC8760` | terracotta wash |
| `clay-100` | `#F7E2D6` | dust |
| `clay-50` | `#FCF1EA` | |
| `sage-700` | `#245C3A` | forest ledger |
| `sage-600` | `#2F7A4C` | **active / available / functional** |
| `sage-500` | `#49985F` | new stamp ink |
| `sage-100` | `#DDEFE3` | approval wash |
| `sage-50` | `#F1F8F3` | |
| `crit-700` | `#7A1F1B` | sealing wax red |
| `crit-600` | `#A32B25` | **error / rejected / withdrawn** |
| `crit-500` | `#C34038` | |
| `crit-100` | `#F7E1DE` | |
| `crit-50` | `#FCF0EF` | |
| `ink-900` | `#201A14` | basalt ink |
| `ink-800` | `#2C241C` | dry ink |
| `ink-700` | `#423527` | walnut |
| `ink-600` | `#5B4C3B` | leather |
| `ink-500` | `#776450` | **secondary text** |
| `ink-400` | `#94826C` | |
| `ink-300` | `#B3A28D` | |
| `ink-200` | `#D3C6B4` | |
| `ink-100` | `#E9E0D2` | **hairline border** |
| `ink-50` | `#F7F2E9` | **page background · parchment** |
| `surface` | `#FFFFFF` | panel / card surface |

### Semantic Mapping (single source of truth)

| Meaning | Surface | Foreground | Border | Used for |
|---|---|---|---|---|
| **Active / For Rent / Published** | `sage-50` | `sage-700` | `sage-100` | property status, blog status, user status |
| **For Sale** | `clay-50` | `clay-700` | `clay-100` | property status chip |
| **Pending / Under Review** | `seal-50` | `seal-700` | `seal-100` | property, blog, user "Pending" |
| **Not Available / Inactive / Withdrawn** | `ink-100` | `ink-600` | `ink-200` | property, blog, user inactive states |
| **Error / Rejected** | `crit-50` | `crit-700` | `crit-100` | form errors, deletion confirmations |
| **Info / Legal** | `chambers-50` | `chambers-700` | `chambers-100` | legal-services content, informational banners |
| **Verified / Primary CTA** | `seal-600` fill | white | `seal-700` | "Book Now," "Send Enquiry," "Publish," confirmed bookings |

**Contrast (verify with a contrast checker before shipping, these are the design targets):** `chambers-600` on white ≈ 8:1; `sage-700` on `sage-50` ≈ 8.5:1; `clay-700` on `clay-50` ≈ 8:1; `crit-700` on `crit-50` ≈ 9:1; `seal-700` on `seal-50` ≈ 7:1 — all comfortably **WCAG AA**, most **AAA** for normal text. Status text never renders in the pale chip tint — always the 700-level ink.

### Why this is not "navy-and-gold-on-white"

Every third law-firm template is navy, gold, and Playfair Display on stark white. Here, gold (`seal`) is rationed to verified/primary moments only — it never washes a whole section — and the neutral is warm parchment, not clinical white. Property gets its **own** accent (`clay`) so the two practices are visually distinguishable at a glance without either looking like an afterthought.

## 2.6 Space, Grid, Radius, Elevation, Borders

### Spacing Scale (4px base)

| Token | Rem | Use |
|---|---|---|
| `sp-1` | 0.25rem | icon-to-text gap |
| `sp-2` | 0.5rem | chip padding, dense gaps |
| `sp-3` | 0.75rem | form gaps |
| `sp-4` | 1rem | card padding (comfort) |
| `sp-5` | 1.5rem | panel padding, section gaps |
| `sp-6` | 2rem | between major regions |
| `sp-8` | 3rem | page section rhythm |

### Grid

- **Content column:** max `1160px`, tuned for ~72ch reading width at 15px body — legal copy is read, not skimmed.
- **Panel grid:** 12-col fluid; property cards = 3-up desktop / 2-up tablet / 1-up phone; services grid = 3-up (Legal has 7 cards, Property has 7 — always render as 3-up so both grids visually match, never a lopsided count-driven layout).
- **Property detail layout:** gallery + details 7/5 split desktop, stacked mobile.

### Radius

| Token | Value | Use |
|---|---|---|
| `r-sm` | 2px | chips, inputs, small tiles — deliberately sharp, "cut paper" |
| `r-md` | 6px | cards, panels, dialogs |
| `r-lg` | 10px | hero panels, seal tile, gallery lightbox |
| `r-full` | 999px | pills, avatars, the seal badge |

Radius stays small and rectilinear on purpose — a case file does not have rounded corners. **6px is the loudest radius in the system.**

### Elevation

| Token | Use |
|---|---|
| `e-0` | none — default, flat |
| `e-1` | `0 1px 0 rgba(32,26,20,.05)` + `0 1px 3px rgba(32,26,20,.09)` — resting panels (warm-tinted shadow, not cool gray) |
| `e-2` | hover, dropdowns, toasts |
| `e-3` | modals, the booking confirmation dialog, image lightbox |

### Borders

- Hairline `1px` `ink-100` is the default panel edge.
- **Status never relies on color alone** — every chip pairs tint + 700-ink text + a small icon/dot.

## 2.7 Signature Design Element — **The Seal & Ledger**

One unforgettable feature, present on the homepage hero and the admin dashboard, that only makes sense for a firm running both a legal practice and a property portfolio.

### Anatomy

1. **The Seal** — a circular wax-seal badge. On the public homepage it reads `EST. 2021 · FAIR LAW FIRM`, a trust mark next to the hero headline. On the admin dashboard it becomes a live instrument: the seal fills proportionally with `sage` as the share of *healthy* content (active properties + published posts + active users) rises, read as `REGISTRY HEALTH 82%` beneath it in mono.
2. **The Ledger Bar** — a horizontal split bar directly under the dashboard KPI band, divided into two channels: **Legal** (blog/publications, inquiries) in `chambers`, and **Property** (listings, bookings) in `clay`. It visually proves the firm is running two practices, not one with a side hustle. Segment widths are driven by the dashboard's existing `GetCount()` statistics (§ Phase 4).
3. **Registry Watch** — a compact instrument strip: last content update, number of Pending items awaiting approval, new bookings/enquiries this week. This replaces the "AI Watch" concept from data-driven systems — Fair Law Firm has no ML component, so the equivalent trust signal is **content and registry integrity**, not model health.

### Why it is the identity

- **Readable in three seconds:** one glance answers "is the site healthy, and which practice needs attention."
- **Domain-true:** a wax seal is the actual object that makes a legal document official; a ledger is the actual object a property manager keeps.
- **It balances the two practices visually**, which is the single biggest content risk in this project (Property currently dominates the codebase; Legal must not read as an afterthought).
- **It names the brand:** the seal mark reappears on the login screen, empty states, and booking-confirmation dialogs, so the signature element *is* the logo doing double duty.

### Reduced motion

With `prefers-reduced-motion: reduce`, the ledger bar renders as static segments (no shimmer/sweep), and the seal fill animates once on load (a single 250ms ease) rather than continuously.

## 2.8 Iconography

No custom glyph font — Font Awesome 6 (already in the stack) is used inside a fixed **token-tile** treatment: every glyph sits in a rounded-square tile with a tinted wash and a hairline, at 20/24/32px, from a **curated, fixed concept → glyph map**. Consistency comes from the tile system and the mapping, not the vendor icon set itself.

### Concept → Glyph Map (curated, fixed)

| Concept | Glyph | Tile tint |
|---|---|---|
| Legal Advisory | `fa-comments` | `chambers-100` |
| Court Representation | `fa-gavel` | `chambers-100` |
| Mediation | `fa-handshake` | `chambers-100` |
| Contract Drafting | `fa-file-signature` | `chambers-100` |
| Business Transactions | `fa-briefcase` | `chambers-100` |
| Internal Regulations | `fa-scale-balanced` | `chambers-100` |
| Legal Consultation | `fa-user-tie` | `chambers-100` |
| Property (general) | `fa-house` | `clay-100` |
| Rental Management | `fa-key` | `clay-100` |
| Sales Management | `fa-tag` | `clay-100` |
| Rent Recovery | `fa-hand-holding-dollar` | `clay-100` |
| Marketing | `fa-bullhorn` | `clay-100` |
| Compliance | `fa-clipboard-check` | `clay-100` |
| Maintenance | `fa-screwdriver-wrench` | `clay-100` |
| Booking | `fa-calendar-check` | `seal-100` |
| Verified / Published | `fa-stamp` | `seal-100` |
| Blog / Publication | `fa-newspaper` | `chambers-100` |
| Attachment / Download | `fa-paperclip` | `ink-100` |
| Image gallery | `fa-images` | `ink-100` |
| Video | `fa-circle-play` | `ink-100` |
| Location / District | `fa-location-dot` | `clay-100` |
| Contact / Email | `fa-envelope` | `chambers-100` |
| Phone | `fa-phone` | `chambers-100` |
| WhatsApp | `fa-whatsapp` (brand) | `sage-100` |
| Search / Filter | `fa-magnifying-glass` | `ink-100` |
| Admin / User | `fa-user-tie` | `chambers-100` |
| Dashboard | `fa-gauge-high` | `chambers-100` |

*(Where a Font Awesome glyph is a compromise, it's flagged in the component library so it can be swapped for a custom SVG later without touching layout.)*

## 2.9 Component System

### Buttons

| Variant | Style | Use |
|---|---|---|
| **Primary (Seal)** | `seal-600` fill, white text, `r-sm`, hairline `seal-700` | the single decision action: Book Now, Send Enquiry, Publish, Save |
| **Secondary (Chambers)** | `chambers-600` outline, `chambers-700` text, white surface | navigation-grade actions, "View Details" |
| **Tertiary / Ghost** | transparent, `ink-600` text, underline on hover | inline, dense tables/admin rows |
| **Danger** | `crit-600` fill | destructive only (Delete user/property/post) |

Rectilinear, tiny radius (2–4px), height 40px (38px secondary), Public Sans 500, 15px. Icon + label always on primary actions; icon-only gets `aria-label`. **Seal gold is reserved for primary buttons only** — this is what keeps it feeling official instead of decorative. Focus ring: 2px `chambers-400`, offset 2px.

### Inputs

Rectilinear, `ink-100` hairline, transparent surface, `r-sm`, 40px height. Labels are `kicker` style (uppercase, spaced) so forms read like intake sheets, not a SaaS signup. Mono for phone/price/ID fields, Public Sans otherwise. States: idle / hover (`ink-200`) / focus (2px `chambers-400`) / error (`crit-600` hairline + `crit-50` wash) / disabled.

### Cards & Panels

`e-1`, hairline `ink-100`, `r-md`, padding `sp-5`. Every panel carries a `kicker` eyebrow and, where relevant, a tile-icon. Property and blog cards always show: image, `kicker` status chip, Source Serif title, mono price/date, one-line excerpt.

### Status Chips

Chip = tint wash + 700-level text + small dot/icon + label:
- Property: **For Rent** (sage), **For Sale** (clay), **Not Available** (ink)
- Blog / Users: **Active** (sage), **Pending** (seal gold)
- Bookings: **Confirmed** (sage), **Awaiting Response** (seal gold)

### Tables (admin)

Header = `kicker` uppercase; rows 46px, hairline separators, hover `ink-50`; numeric/date columns in mono, right-aligned; status column uses chips. Pagination uses square bordered buttons; current page = `chambers-600` fill.

### Dialogs

`r-lg`, `e-3`, 600px default, backdrop 40% `chambers-950`. Booking confirmation and delete-confirmation dialogs use this. Title in Source Serif `h3`. Focus trapped, `Esc` closes, `aria-labelledby` required.

### Filters

A single-row **filter rail** (status, location/district, price, search) above property and blog listings, ending in Apply/Reset. Stacks on mobile. Filters never float — they sit at the top of the panel they govern.

### Empty States, Loading

- **Empty state:** centered tile-icon in a `chambers-50` rounded square, one plain-language line ("No properties listed for this filter yet"), and the primary next action.
- **Loading:** a **seal-stamping** indicator (the seal outline fills/lands), never a generic spinner.

## 2.10 Data Visualization (Admin Dashboard)

The existing dashboard statistics (active employers, total properties, rental properties — via `GetCount()`) are restyled, not re-architected:

- **KPI band** — 3–4 dense tiles, not oversized hero cards: Active Users, Total Properties, Rentals Available, Pending Content.
- **The Ledger Bar** (§2.7) — the signature visual replacing a plain stat row.
- If Chart.js is introduced later for trend views, it uses the domain palette (`chambers`/`clay`/`seal`/`sage`), never Chart.js defaults; ticks in Public Sans 12px, values in mono.

Rule: **a number on the dashboard must be able to answer "what should I do next," or it doesn't belong there.**

## 2.11 Property & Booking Experience

The property system is the site's most "product-like" surface and deserves the same rigor a GIS map gets in a data platform:

- **Listing (`property.php`):** filter rail (status, price, location) above a 3-up card grid; each card = image + status chip + Source Serif title + mono price/location + "View Details."
- **Detail (`property_detail.php`):** image gallery (lightbox, `r-lg`, `e-3`) on the left/top, a details panel (bedrooms, bathrooms, floor, size, months) on the right/below using `kicker` labels + mono values, and a booking form that ends in a **Primary (Seal)** "Book Now" button.
- **Booking confirmation:** on submit, a dialog stamps a visual seal ("Booking Received") before the page returns to normal state — this is the one place the seal *animates* meaningfully, matching the "motion is confirmation" principle.
- **Showcase grid (`manage_property.php`):** same card system, no filter rail — a lighter public browse view.

## 2.12 Motion

| Purpose | Motion | Duration / Ease |
|---|---|---|
| Seal fill (dashboard health / booking confirm) | radial fill + single settle | 250–300ms ease-out |
| Ledger bar segment change | width crossfade | 220ms |
| Status chip change (admin) | color crossfade | 180ms |
| Panel reveal | translateY 8px + fade | 260ms `cubic-bezier(.2,.7,.2,1)` |
| Modal / dialog | scale .98 → 1 + fade | 180ms |
| Hero carousel (3 slides max) | crossfade, 6s dwell | 600ms ease, no autoplay-forever pattern that fights reading |
| Focus | 2px ring | instant (a11y) |

**All motion disabled** under `prefers-reduced-motion: reduce`. No parallax, no bounce, no idle animation, no infinite-loop carousels beyond the three-slide hero.

## 2.13 Accessibility (WCAG AA)

- Contrast: every text/UI pairing in §2.5 targets ≥ 4.5:1 (text) / ≥ 3:1 (UI) — verify with a contrast tool before shipping.
- **Keyboard:** full tab order; visible 2px focus rings everywhere; modal focus trap; skip-to-content link on both public and admin shells.
- **Screen readers:** `aria-label` on icon-only buttons and the WhatsApp floating button; `role="status"` on the dashboard seal readout; property/blog images get real `alt` text, not filenames.
- **Color-blind safe:** status = dot/icon + word + color, never color alone (matches §2.9 chip spec).
- **Semantic HTML:** one real `<h1>` per page, real `<table>` for admin data, real `<button>`/`<a>` — no clickable `<div>`s.
- **Forms:** every input has a real `<label>`, error text is programmatically associated, not just colored red.

## 2.14 Responsive Rules

| Breakpoint | Behavior |
|---|---|
| ≥ 1200px | full admin rail, content max 1160px, 3-up property/services grid |
| 960–1199px | admin rail compressed to icons |
| 640–959px | admin rail → drawer (hamburger); property/services grid 2-up |
| < 640px | grid 1-up; admin tables → horizontal scroll; filter rail stacks; property gallery becomes a single swipeable strip |

## 2.15 Information Architecture & Wireframes

### IA

```
PUBLIC                          ADMIN (/data/)
Home                            OPERATIONS
About Us                          Dashboard
Legal Services                    Manage Properties (+ Rentals, Images)
Property Services                 Manage Blog
Properties (list/detail)          About Content
Blog (list/detail)                Videos / Home Backgrounds
Contact                         ADMINISTRATION
                                   Manage Users
                                   Register (new admin/employer)
                                 ACCOUNT
                                   Profile
                                   Logout
```

The most frequent public path (find a property or a service, then contact/book) is reachable in 1–2 clicks from the homepage. The most frequent admin path (see what needs attention, act on it) is answered on `dashboard.php` before any click.

### ASCII Wireframes

**Homepage**

```
┌──────────────────────────────────────────────────────────────┐
│ ⚖ FAIR LAW FIRM        Home  Services  Properties  Blog  ☎ Contact │
├──────────────────────────────────────────────────────────────┤
│  HERO CAROUSEL (3 slides, calm crossfade)                     │
│  "Legal clarity. Property confidence."      (◉ Est. 2021 seal)│
│  [ Book a Consultation ]  [ Browse Properties ]                │
├──────────────────────────────────────────────────────────────┤
│ ABOUT PREVIEW           │ MISSION / VISION cards               │
├──────────────────────────────────────────────────────────────┤
│ SERVICES                                                       │
│  LEGAL (chambers)            PROPERTY (clay)                  │
│  ⚖ Advisory  ⚖ Court Rep     🏠 Rental Mgmt  🏷 Sales Mgmt      │
│  ⚖ Mediation ⚖ Contracts     💰 Rent Recovery 📣 Marketing      │
├──────────────────────────────────────────────────────────────┤
│ LATEST PUBLICATIONS (blog, 3 cards, Source Serif titles)       │
├──────────────────────────────────────────────────────────────┤
│ CTA — "Have a matter or a property to discuss?" [Contact Us]   │
└──────────────────────────────────────────────────────────────┘
```

**Property Listing**

```
┌──────────────────────────────────────────────────────────────┐
│ PROPERTIES  [status ▾] [district ▾] [price ▾] [search ⦿]       │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐                        │
│ │ [image]  │ │ [image]  │ │ [image]  │                        │
│ │ FOR RENT │ │ FOR SALE │ │ FOR RENT │  ← status chips         │
│ │ Kacyiru   │ │ Kimihurura│ │ Nyarutarama│                       │
│ │ RWF 450K │ │ RWF 85M  │ │ RWF 600K │  ← mono price            │
│ │[View]    │ │[View]    │ │[View]    │                        │
│ └──────────┘ └──────────┘ └──────────┘                        │
└──────────────────────────────────────────────────────────────┘
```

**Property Detail**

```
┌──────────────────────────────────────────────────────────────┐
│ [ FOR RENT ]  4-Bedroom House, Kacyiru                        │
│ ┌ GALLERY ─────────────┐  ┌ DETAILS ─────────────────────┐   │
│ │  [main image]         │  │ TYPE   Residential            │   │
│ │  [thumb][thumb][thumb]│  │ SIZE   200sqm  BEDS 4  BATH 3 │   │
│ └────────────────────────┘  │ PRICE  RWF 450,000 / month    │   │
│                             │ [ ⌾ Book Now ]                 │   │
│                             └────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────┘
```

**Admin Dashboard**

```
┌──────────────────────────────────────────────────────────────┐
│ FAIR LAW FIRM · ADMIN        [profile ▾]                       │
├──────────┬───────────────────────────────────────────────────┤
│OPERATIONS│  ╔═ The Seal & Ledger ══════════════════════════╗  │
│ ● Dashbrd│  ║ (◉) SEAL          LEDGER ━━━━╦━━━━   WATCH    ║  │
│ ○ Propert│  ║ REGISTRY 82%      Legal│Property  Pending: 3  ║  │
│ ○ Rentals│  ╚════════════════════════════════════════════════╝ │
│ ○ Blog   │  ┌ USERS 12 ┐ ┌ PROPERTIES 34 ┐ ┌ RENTALS 21 ┐      │
│ ○ About  │  └──────────┘ └────────────────┘ └────────────┘      │
│ ○ Media  │  ┌ RECENT ACTIVITY ───────────────────────────────┐ │
│ADMIN.    │  │ New booking — WP-014, Kacyiru — 2h ago          │ │
│ ○ Users  │  │ Blog "Contract Basics" published — 1d ago       │ │
│ACCOUNT   │  └──────────────────────────────────────────────────┘ │
│ ○ Profile│                                                       │
└──────────┴───────────────────────────────────────────────────┘
```

---

# PHASE 3 — Self-Critique

A deliberate pass to kill every "template" instinct before it ships.

| Artifact I found in the first draft | Why it was generic | Replacement |
|---|---|---|
| Navy + gold + Playfair Display | The default "prestige law firm" template combo, used by thousands of sites | Source Serif 4 (not Playfair) + navy rationed to structure + gold rationed to **primary buttons and the seal only**, never a background wash |
| Real-estate card grid with heart/favorite icon | Zillow-clone habit that doesn't fit a firm-managed portfolio, not a marketplace | Booking-first cards: status chip, price, one clear action — no favoriting mechanic that implies a marketplace this isn't |
| Stock "gavel and scales in a suit" hero photography | Cliché stock imagery every law-firm template uses | Hero built around the Seal mark + real firm/property imagery, not stock gavel photos |
| Bootstrap default rounded-xl cards + soft drop shadows | Generic admin-template softness | 6px max radius, warm-tinted `e-1` shadows — "cut paper," not "app store" |
| Firdip's inherited fire-department SEO metadata and leftover CSS classes | Literal template leftovers, undermines credibility instantly if noticed | Explicit purge task in Phase 4 §4 |
| Auto-playing 5+ slide hero carousel | Vendor-template habit, fights reading, no purpose | Calm 3-slide hero, 6s dwell, crossfade only |
| Generic spinner for loading states | Vendor-generic | **Seal-stamp** loading indicator |
| One undifferentiated visual treatment for Legal and Property | Risks the property practice visually swallowing the legal one (property already dominates the codebase) | Two accent colors (`chambers` / `clay`) and the Ledger Bar explicitly split 50/50 by design, not by content volume |
| Plain colored badges for property status | Color-only status, fails color-blind users, looks like every listing site | Chip = tint + icon + word, same discipline everywhere |

**Resulting rule:** if a component could be found unmodified in any random Bootstrap law-firm or real-estate template, it must be either removed or re-made so it only makes sense for a firm running *both* a legal practice and a property portfolio.

---

# PHASE 4 — Implementation Mapping

This maps every design-system item to the **actual files** in the existing codebase, so implementation is additive (new CSS + template edits), never a rewrite.

| Design System Item | Implementation |
|---|---|
| Tokens (colors, type, space, radius) | New file `assets/css/fairlaw-tokens.css`, loaded **after** `firdip.css` in every public `<head>`, and a matching `data/css/fairlaw-tokens.css` for the admin shell — CSS custom properties under `:root`, so nothing in Bootstrap/Firdip has to be torn out, only overridden |
| Typography | Google Fonts (or self-hosted) — Source Serif 4, Public Sans, IBM Plex Mono — linked once in `include/header.php` and `data/include/header.php` |
| Signature element | New partial `include/seal-ledger.php` (public homepage) and `data/include/seal-ledger.php` (dashboard), styled via `.seal`, `.ledger-bar`, `.registry-watch` classes in the tokens stylesheet |
| Header / Nav / Footer | `include/header.php`, `include/footer.php` (public); `data/include/header.php`, `data/include/footer.php` (admin) — restyle only, keep all existing links/session logic untouched |
| Homepage | `index.php` — hero, about preview, services grid, blog preview, CTA sections restyled per §2.15 wireframe |
| About | `about_us.php` — restyle using `about_content` fields already rendered, no query changes |
| Legal Services | `legal_services.php` — 7 service cards restyled with `chambers` accent + concept-glyph tiles |
| Property Services | `property_service.php` — 7 service cards restyled with `clay` accent + concept-glyph tiles |
| Property Listing | `property.php` — filter rail + card grid restyle; pagination controls restyled per §2.9 |
| Property Detail | `property_detail.php` — gallery + details panel + booking form restyle; booking confirmation dialog added per §2.11 |
| Property Showcase | `manage_property.php` (public) — lighter card grid, no filter rail |
| Blog | `blog.php`, `blog_details.php` — Source Serif titles, mono publish dates, attachment list styled with `fa-paperclip` tiles |
| Contact | `contact.php` — form restyled per §2.9 input spec; map/embed untouched |
| Admin Dashboard | `data/dashboard.php` — KPI band + Seal & Ledger + recent activity, fed by existing `GetCount()` calls, no new queries required |
| Property Management (admin) | `data/manage_property.php`, `data/add_rental_property.php`, `data/display_properties.php`, `data/display_rental.php`, `data/property_images.php` — form and table restyle only |
| Blog Management (admin) | `data/add_blog.php`, `data/display_blog.php` — form and table restyle only |
| About / Media (admin) | `data/add_about.php`, `data/add_video.php`, `data/home_background.php` — form restyle only |
| Users (admin) | `data/manage_users.php`, `data/register.php`, `data/profile.php` — table + form + status chip restyle only |
| Login | `data/index.php` — restyled as a calm split screen: firm identity + seal mark on one side, login form on the other |
| Email templates | `contactEmail.php`, `bookingEmail.php` HTML bodies — optional follow-up pass to match the token palette, **credentials/logic untouched** |
| Icons | Font Awesome 6.2.1 (already loaded) wrapped in the `.icon-tile` component from §2.8 — no new icon library |
| Legacy cleanup | Purge fire-department-inherited SEO `<meta>` tags and unused Firdip CSS classes flagged in the original audit — cosmetic/content only, no route changes |

**Explicitly not touched by this pass:** database schema, PDO queries, session/auth logic, PHPMailer/SMTP configuration, file upload handling, roles/permissions, and every item listed under "Security Vulnerabilities & Concerns" and "Known Issues & Technical Debt" in the original project documentation — those are backend/security work tracked separately from this UI design system.

---

# 5 — Build Prompt — for OpenCode / Claude Code

Copy everything in the box below into OpenCode (or any Claude Code session) as the task instruction. It references this document as the spec of record.

```
You are implementing a visual redesign of Fair Law Firm LTD, an existing
procedural PHP + Bootstrap 5.0.2 (Firdip template) web application with a
public marketing site and an internal /data/ admin dashboard.

SPEC OF RECORD: read FAIR_LAW_FIRM_DESIGN_SYSTEM.md in full before writing
any code. It defines the design tokens, typography, color system, spacing,
components, iconography, and the Phase 4 file-by-file implementation map.
Follow it exactly — do not invent a different palette, font pairing, or
component style.

HARD CONSTRAINTS — do not violate these:
1. Do NOT change any PHP business logic, PDO queries, database schema,
   session/auth logic, routes/filenames, or form field names/POST keys.
   This is a visual/template-and-CSS task only.
2. Do NOT introduce a JS framework, a CSS framework replacement, a build
   step (webpack/vite/npm), or remove Bootstrap/jQuery/Firdip — the design
   system layers NEW CSS on top of the existing stack via override files
   (assets/css/fairlaw-tokens.css and data/css/fairlaw-tokens.css), per
   Phase 4 of the spec.
3. Do NOT touch contactEmail.php / bookingEmail.php SMTP config, PHPMailer
   setup, config.php database credentials, or anything under Security
   Vulnerabilities in the original project documentation — out of scope.
4. Preserve every existing route, every include/require_once, every
   session variable name, and every table/column reference exactly as-is.
5. Keep the Lang/lang.php translation function __() wrapping intact
   everywhere it currently appears; do not hardcode over translated strings.
6. Work incrementally, one file (or file group) at a time, in this order —
   confirm each step renders correctly before moving to the next:

   Step 1 — Foundations
     - Create assets/css/fairlaw-tokens.css and data/css/fairlaw-tokens.css
       with the full token set from Design System §2.4–2.6 (colors,
       typography, spacing, radius, elevation) as CSS custom properties.
     - Link Source Serif 4, Public Sans, and IBM Plex Mono (Google Fonts)
       in include/header.php and data/include/header.php.
     - Link the new token stylesheets AFTER firdip.css in both headers.

   Step 2 — Shell
     - Restyle include/header.php, include/footer.php (public nav, footer,
       WhatsApp button) and data/include/header.php, data/include/footer.php
       (admin rail/topbar) per §2.15 wireframes. Do not change any links,
       session checks, or PHP logic inside these files — CSS/markup classes
       only, and only where needed to apply the new component styles.

   Step 3 — Signature element
     - Build the Seal & Ledger component (§2.7) as a reusable partial and
       add it to index.php (as the "Est. 2021" seal near the hero) and
       data/dashboard.php (as the live registry-health instrument, wired
       to the existing GetCount() values — do not add new queries, only
       consume what dashboard.php already computes).

   Step 4 — Public marketing pages
     - index.php, about_us.php, legal_services.php, property_service.php,
       contact.php — restyle per §2.9 components and §2.15 wireframes.

   Step 5 — Property system
     - property.php, property_detail.php, manage_property.php — filter
       rail, card grid, gallery, booking form and confirmation dialog per
       §2.9 and §2.11. Keep booking POST logic and bookingEmail.php
       untouched.

   Step 6 — Blog system
     - blog.php, blog_details.php — restyle per §2.4/§2.9, attachment list
       using the icon-tile component.

   Step 7 — Admin dashboard pages
     - data/dashboard.php, data/manage_property.php,
       data/add_rental_property.php, data/display_properties.php,
       data/display_rental.php, data/property_images.php,
       data/add_blog.php, data/display_blog.php, data/add_about.php,
       data/add_video.php, data/home_background.php,
       data/manage_users.php, data/register.php, data/profile.php,
       data/index.php (login) — restyle tables, forms, and status chips
       per §2.9. Keep every PDO call, validation rule, and redirect as-is.

   Step 8 — QA pass
     - Check every breakpoint in §2.14, keyboard tab order and focus
       rings per §2.13, and that status is never conveyed by color alone
       anywhere in the app.

7. If any existing markup structure makes a spec component impossible to
   implement without touching PHP logic, stop and report the conflict
   instead of guessing — do not silently change behavior to make a style
   work.

Confirm you have read FAIR_LAW_FIRM_DESIGN_SYSTEM.md, then begin at Step 1.
```

---

*© Fair Law Firm LTD. Design rationale, tokens, and implementation share one vocabulary: the dossier.*
