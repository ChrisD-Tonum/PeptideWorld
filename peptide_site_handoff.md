# Claude Code Handoff
## Peptide Education Website — WordPress Custom Theme + Supabase

---

## 1. Project Overview

Build a custom WordPress theme for a peptide education website. The theme should be lightweight and modern, serving as a clean foundation. Elementor will handle drag-and-drop page building for static content pages. A custom PHP template will handle the dynamic Peptides page, which pulls data from Supabase and renders a filterable card grid.

---

## 2. Tech Stack

| Component | Technology |
|---|---|
| CMS | WordPress (self-hosted) |
| Theme | Custom theme built from scratch |
| Page Builder | Elementor plugin (drag-and-drop for static pages) |
| Database | Supabase (PostgreSQL via Supabase REST API) |
| Hosting | WP Engine, Kinsta, or SiteGround (TBD) |
| Local Dev | Local by WP Engine |

---

## 3. Site Structure & Page Map

Research Library has been intentionally removed — all peptide research is embedded within individual peptide cards on the Peptides page to avoid redundancy.

| Page | Type | Template | Notes |
|---|---|---|---|
| Home | Static | Elementor | Hero, intro content, featured peptides or CTAs |
| Peptides | Dynamic | Custom PHP (peptide-database.php) | Supabase-connected filterable card grid |
| Hormone Therapy | Static | Elementor | Educational content, no dynamic data |
| Educational Guides | Static | Elementor | Articles, guides — no dynamic data |
| Consultation | Static | Elementor | External booking tool link/button |

---

## 4. Theme Requirements

Must support:
- Elementor compatibility (critical — requires setup in functions.php)
- Gutenberg block editor support as fallback
- Custom page templates for Supabase-connected pages
- Responsive / mobile-friendly layouts
- Clean, modern design aesthetic appropriate for a science/education site
- SEO-friendly HTML structure

---

## 5. File Structure to Scaffold

```
my-peptide-theme/
├── style.css                  ← required: theme metadata + base styles
├── index.php                  ← main fallback template
├── header.php                 ← site header, nav menu (5 pages)
├── footer.php                 ← site footer
├── functions.php              ← enqueue scripts, register features, Elementor support
├── page.php                   ← static page template (Elementor pages)
├── single.php                 ← single post/article template
├── archive.php                ← archive/category listing
├── search.php                 ← search results page
├── 404.php                    ← not found page
├── assets/
│   ├── css/main.css           ← custom styles incl. peptide card grid
│   ├── js/main.js             ← filter + search logic for peptide cards
│   └── images/
└── templates/
    └── peptide-database.php   ← Supabase-connected filterable card grid
```

---

## 6. functions.php Requirements

```php
add_theme_support('elementor');         // Elementor compatibility
add_theme_support('title-tag');         // Dynamic page titles
add_theme_support('post-thumbnails');   // Featured images
add_theme_support('menus');             // Navigation menus
add_theme_support('wp-block-styles');   // Gutenberg support
add_theme_support('align-wide');        // Wide block alignment
wp_enqueue_scripts();                   // Enqueue CSS and JS (incl. main.js for filters)
register_nav_menus();                   // Register primary navigation menu
```

---

## 7. Peptides Page — Filterable Card Grid

This is the most important custom template. It fetches all peptide records from Supabase and renders them as a responsive card grid. Users can search by keyword and filter by category. All research information is embedded within each card — there is no separate Research Library page.

### Card Grid Requirements
- Search bar at top — filters cards in real-time by peptide name or keyword
- Category filter buttons or dropdown — filters cards by peptide category/type
- Responsive grid layout — 3 columns desktop, 2 tablet, 1 mobile
- Each card displays: peptide name, category, short description, key benefits, and research notes
- Cards should be clean, scannable, and consistent in height
- Clicking a card can expand it inline or link to a detail view (TBD)
- Show a loading state while Supabase data is being fetched
- Show a user-friendly message if no results match the filter/search

### Supabase Fetch Pattern

```php
$response = wp_remote_get('https://YOUR-PROJECT.supabase.co/rest/v1/peptides', [
    'headers' => [
        'apikey'        => SUPABASE_ANON_KEY,
        'Authorization' => 'Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type'  => 'application/json'
    ]
]);

if (is_wp_error($response)) {
    // Handle error gracefully
}

$peptides = json_decode(wp_remote_retrieve_body($response), true);
```

### Expected Supabase Fields (update to match your schema)
- `id` — unique identifier
- `name` — peptide name
- `category` — used for filter buttons (e.g. 'Recovery', 'Cognitive', 'Hormonal')
- `description` — short summary shown on card
- `benefits` — key benefits (array or comma-separated string)
- `research` — research notes or study references embedded in card

### Filter & Search Logic (main.js)

```js
// On search input or filter button click:
// 1. Read current search string and active category filter
// 2. Loop through all rendered cards
// 3. Show card if name/description matches search AND category matches filter
// 4. Hide card otherwise
// No page reload — all filtering happens client-side in the browser
```

---

## 8. Consultation Page

Built with Elementor. Simple layout with a headline, brief description, and a prominent call-to-action button that links to an external booking tool (URL to be provided). No custom PHP template required.

---

## 9. Navigation Structure

Register a primary nav menu in functions.php. Menu order:

1. Home
2. Peptides
3. Hormone Therapy
4. Educational Guides
5. Consultation

> Note: Research Library is intentionally excluded from navigation.

---

## 10. Design Guidelines

| Element | Guideline |
|---|---|
| Aesthetic | Clean, scientific, modern — research/journal feel, not marketing |
| Typography | Readable sans-serif body text, clear heading hierarchy |
| Color Palette | Neutral base (white/light grey) + 1-2 accent colors |
| Layout | Generous whitespace, easy to scan, good for long-form content |
| Cards | Consistent height, subtle shadow, clear visual hierarchy within card |
| Mobile | Fully responsive — all templates must work on phone and tablet |

---

## 11. Deployment Workflow

1. Build and test theme locally using Local by WP Engine
2. Install Elementor plugin in local WordPress
3. Activate custom theme and verify Elementor compatibility
4. Create all 5 pages in WordPress admin with correct titles
5. Assign `peptide-database.php` template to the Peptides page
6. Build Home, Hormone Therapy, Educational Guides, and Consultation with Elementor
7. Set up primary navigation menu with the 5 pages in correct order
8. When ready, deploy to live host via FTP/SSH or host migration tool
9. Use GitHub for version control of all theme files

---

## 12. Notes for Claude Code

- Keep the custom theme as lightweight as possible — Elementor adds its own CSS/JS, avoid duplicating
- Use WordPress coding standards for all PHP
- Use `wp_remote_get()` for all external API calls — never raw curl
- Store the Supabase API key in `wp-config.php` as a constant (`SUPABASE_ANON_KEY`), not hardcoded in templates
- The peptide card filter and search must work client-side (JavaScript) — no page reloads
- Add inline comments throughout all files for maintainability
- Flag anywhere that requires user-specific configuration (Supabase URL, API key, table name, booking tool URL)

---

*Update Supabase project URL, anon key, peptide table schema, and external booking tool URL before deploying.*
